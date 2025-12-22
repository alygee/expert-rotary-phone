<?php
/**
 * REST API endpoints для темы
 */

/**
 * Регистрация REST API endpoint для фильтрации данных
 */
function register_filter_api_endpoint() {
    register_rest_route('dmc/v1', '/filter', array(
        'methods' => 'GET',
        'callback' => 'filter_api_callback',
        'permission_callback' => '__return_true', // Публичный доступ
        'args' => array(
            'cities' => array(
                'required' => false,
                'type' => 'string',
                'description' => 'Города для фильтрации (через запятую или массив)',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'levels' => array(
                'required' => false,
                'type' => 'string',
                'description' => 'Уровни для фильтрации (через запятую или массив)',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'count' => array(
                'required' => false,
                'type' => 'integer',
                'description' => 'Количество сотрудников',
                'sanitize_callback' => 'absint',
            ),
            'format' => array(
                'required' => false,
                'type' => 'string',
                'default' => 'json',
                'enum' => array('json', 'html'),
                'description' => 'Формат ответа: json или html',
            ),
        ),
    ));
}
add_action('rest_api_init', 'register_filter_api_endpoint');

/**
 * Обработчик REST API endpoint для фильтрации
 * 
 * @param WP_REST_Request $request Объект запроса
 * @return WP_REST_Response|WP_Error Ответ API
 */
function filter_api_callback($request) {
    // Получаем параметры
    $cities_param = $request->get_param('cities');
    $levels_param = $request->get_param('levels');
    $count_param = $request->get_param('count');
    $format = $request->get_param('format') ?: 'json';
    
    // Обрабатываем города
    $cities = [];
    if (!empty($cities_param)) {
        if (is_string($cities_param)) {
            $cities = array_filter(array_map('trim', explode(',', $cities_param)));
        } elseif (is_array($cities_param)) {
            $cities = array_filter(array_map('trim', $cities_param));
        }
    }
    $cities = array_values($cities);
    
    // Обрабатываем уровни
    $levels = [];
    if (!empty($levels_param)) {
        if (is_string($levels_param)) {
            $levels = array_filter(array_map('trim', explode(',', $levels_param)));
        } elseif (is_array($levels_param)) {
            $levels = array_filter(array_map('trim', $levels_param));
        }
    }
    $levels = array_values($levels);
    
    // Обрабатываем количество сотрудников
    $count = null;
    if (!empty($count_param)) {
        $count = (int) $count_param;
    }
    
    // Получаем данные из CSV
    $data = rez();
    
    if (empty($data) || $data === false) {
        return new WP_Error(
            'no_data',
            'Не удалось загрузить данные из CSV',
            array('status' => 500)
        );
    }
    
    // Фильтруем данные
    $filter_result = filterInsuranceData($data, $cities, $levels, $count);
    $results = $filter_result['data'];
    $not_found_cities = $filter_result['not_found_cities'];
    
    // Убираем fallback из результатов, если он там есть (он будет показан отдельно для not_found_cities)
    if (isset($results['fallback'])) {
        unset($results['fallback']);
    }
    
    // Получаем fallback данные для городов, для которых не найдено данных
    $fallback_data = [];
    if (!empty($not_found_cities)) {
        $fallback_rows = getFallbackData($data, $levels, $count);
        if (!empty($fallback_rows)) {
            $fallback_data = $fallback_rows;
        }
    }
    
    // Формируем ответ
    if ($format === 'html') {
        // HTML формат (как в AJAX обработчике)
        ob_start();
        include get_template_directory() . '/inc/api-filter-output.php';
        $html = ob_get_clean();
        
        return new WP_REST_Response(array(
            'success' => true,
            'data' => $html,
            'count' => count($results),
            'not_found_cities' => $not_found_cities,
        ), 200);
    } else {
        // JSON формат
        $response_data = array(
            'success' => true,
            'count' => count($results),
            'cities_count' => count($results),
            'not_found_cities' => $not_found_cities,
            'filters' => array(
                'cities' => $cities,
                'levels' => $levels,
                'count' => $count,
            ),
            'results' => array(),
            'fallback' => null, // Добавляем поле для fallback данных
        );
        
        // Преобразуем результаты в удобный формат
        foreach ($results as $city => $rows) {
            $city_data = array(
                'city' => $city,
                'count' => count($rows),
                'insurers' => array(),
            );
            
            // Группируем по страховщикам
            $insurers = array();
            foreach ($rows as $row) {
                $insurer = $row['Страховщик'];
                if (!isset($insurers[$insurer])) {
                    $insurers[$insurer] = array(
                        'name' => $insurer,
                        'count' => 0,
                        'records' => array(),
                    );
                }
                $insurers[$insurer]['count']++;
                $insurers[$insurer]['records'][] = array(
                    'level' => $row['Уровень'] ?? '',
                    'employees' => $row['Кол-во_сотрудников'] ?? '',
                    'prices' => array(
                        'polyclinic' => $row['Поликлиника'] ?? '',
                        'dentistry' => $row['Стоматология'] ?? '',
                        'ambulance' => $row['Скорая_помощь'] ?? '',
                        'hospitalization' => $row['Госпитализация'] ?? '',
                        'doctor_home' => $row['Вызов_врача_на_дом'] ?? '',
                    ),
                    'total_price' => calculate_total_price($row),
                );
            }
            
            $city_data['insurers'] = array_values($insurers);
            $response_data['results'][] = $city_data;
        }
        
        // Добавляем fallback данные, если они есть
        if (!empty($fallback_data) && !empty($not_found_cities)) {
            $fallback_insurers = array();
            
            foreach ($fallback_data as $row) {
                $insurer = $row['Страховщик'];
                if (!isset($fallback_insurers[$insurer])) {
                    $fallback_insurers[$insurer] = array(
                        'name' => $insurer,
                        'count' => 0,
                        'records' => array(),
                    );
                }
                $fallback_insurers[$insurer]['count']++;
                $fallback_insurers[$insurer]['records'][] = array(
                    'level' => $row['Уровень'] ?? '',
                    'employees' => $row['Кол-во_сотрудников'] ?? '',
                    'prices' => array(
                        'polyclinic' => $row['Поликлиника'] ?? '',
                        'dentistry' => $row['Стоматология'] ?? '',
                        'ambulance' => $row['Скорая_помощь'] ?? '',
                        'hospitalization' => $row['Госпитализация'] ?? '',
                        'doctor_home' => $row['Вызов_врача_на_дом'] ?? '',
                    ),
                    'total_price' => calculate_total_price($row),
                );
            }
            
            $cities_text = is_array($not_found_cities) ? implode(', ', $not_found_cities) : $not_found_cities;
            $cities_label = is_array($not_found_cities) && count($not_found_cities) > 1 ? 'Для регионов ' : 'Для региона ';
            
            $response_data['fallback'] = array(
                'title' => 'Цены по соседним регионам',
                'description' => $cities_label . $cities_text . ' не удалось произвести расчет',
                'not_found_cities' => $not_found_cities,
                'count' => count($fallback_data),
                'insurers' => array_values($fallback_insurers),
            );
        }
        
        return new WP_REST_Response($response_data, 200);
    }
}

/**
 * Вычисляет общую стоимость программы
 * 
 * @param array $row Строка данных
 * @return float Общая стоимость
 */
function calculate_total_price($row) {
    $fields = ["Стоматология", "Скорая_помощь", "Госпитализация", "Вызов_врача_на_дом", "Поликлиника"];
    $total = 0;
    
    foreach ($fields as $field) {
        if (isset($row[$field]) && $row[$field] !== '') {
            // Убираем пробелы и заменяем запятую на точку
            $cleaned = str_replace([' ', ','], ['', '.'], $row[$field]);
            $num = (float) $cleaned;
            if (is_numeric($num) && $num > 0) {
                $total += $num;
            }
        }
    }
    
    return $total;
}

/**
 * Получить информацию о доступных фильтрах
 */
function register_filter_info_endpoint() {
    register_rest_route('dmc/v1', '/filter-info', array(
        'methods' => 'GET',
        'callback' => 'filter_info_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_filter_info_endpoint');

/**
 * Возвращает информацию о доступных фильтрах
 */
function filter_info_callback($request) {
    $data = rez();
    
    if (empty($data) || $data === false) {
        return new WP_Error(
            'no_data',
            'Не удалось загрузить данные из CSV',
            array('status' => 500)
        );
    }
    
    $cities = array_unique(array_column($data, 'Город'));
    $levels = array_unique(array_column($data, 'Уровень'));
    $insurers = array_unique(array_column($data, 'Страховщик'));
    $employee_ranges = array_unique(array_column($data, 'Кол-во_сотрудников'));
    
    sort($cities);
    sort($levels);
    sort($insurers);
    sort($employee_ranges);
    
    return new WP_REST_Response(array(
        'success' => true,
        'total_records' => count($data),
        'available_filters' => array(
            'cities' => array_values($cities),
            'levels' => array_values($levels),
            'insurers' => array_values($insurers),
            'employee_ranges' => array_values($employee_ranges),
        ),
    ), 200);
}

