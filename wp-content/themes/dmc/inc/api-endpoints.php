<?php
/**
 * REST API endpoints для темы
 */

/**
 * Отключение CORS для эндпоинтов dmc/v1
 */
function disable_cors_for_dmc_endpoints($served, $result, $request, $server) {
    $route = $request->get_route();
    
    // Проверяем, что это запрос к нашему API
    if (strpos($route, '/dmc/v1/') !== false) {
        // Удаляем ограничения CORS - разрешаем все источники
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }
    
    return $served;
}
add_filter('rest_pre_serve_request', 'disable_cors_for_dmc_endpoints', 0, 4);

/**
 * Обработка preflight OPTIONS запросов для CORS
 */
function handle_cors_preflight() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, '/wp-json/dmc/v1/') !== false && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
        http_response_code(200);
        exit;
    }
}
add_action('rest_api_init', 'handle_cors_preflight', 1);
add_action('init', 'handle_cors_preflight', 1);

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

/**
 * Регистрация REST API endpoint для сохранения формы OrderForm
 */
function register_order_form_endpoint() {
    register_rest_route('dmc/v1', '/order-form', array(
        'methods' => 'POST',
        'callback' => 'order_form_callback',
        'permission_callback' => '__return_true', // Публичный доступ
        'args' => array(),
    ));
}
add_action('rest_api_init', 'register_order_form_endpoint');

/**
 * Обработчик REST API endpoint для сохранения формы OrderForm
 * 
 * @param WP_REST_Request $request Объект запроса
 * @return WP_REST_Response|WP_Error Ответ API
 */
function order_form_callback($request) {
    // Проверяем наличие функции из плагина inssmart
    if (!function_exists('inssmart_submit_to_cf7')) {
        return new WP_Error(
            'plugin_not_available',
            'Плагин inssmart не установлен или не активирован',
            array('status' => 500)
        );
    }
    
    // Получаем данные из запроса
    $form_data = $request->get_param('form_data');
    $additional_data = $request->get_param('additional_data') ?: array();
    
    // Проверяем, что form_data передан
    if (empty($form_data)) {
        return new WP_Error(
            'missing_form_data',
            'Не переданы данные формы (form_data)',
            array('status' => 400)
        );
    }
    
    // Преобразуем объект в массив, если необходимо
    if (is_object($form_data)) {
        $form_data = json_decode(json_encode($form_data), true);
    }
    
    if (is_object($additional_data)) {
        $additional_data = json_decode(json_encode($additional_data), true);
    }
    
    // Убеждаемся, что это массивы
    if (!is_array($form_data)) {
        $form_data = array();
    }
    if (!is_array($additional_data)) {
        $additional_data = array();
    }
    
    // Санитизация данных
    $form_data = sanitize_order_form_data($form_data);
    $additional_data = sanitize_order_form_additional_data($additional_data);
    
    // Отправляем данные через функцию плагина inssmart
    $result = inssmart_submit_to_cf7($form_data, 'order', $additional_data);
    
    if ($result['success']) {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => $result['message'] ?? 'Форма успешно сохранена',
            'status' => $result['status'] ?? 'mail_sent',
        ), 200);
    } else {
        $status_code = isset($result['status']) && $result['status'] === 'validation_failed' ? 400 : 500;
        
        return new WP_REST_Response(array(
            'success' => false,
            'message' => $result['message'] ?? 'Ошибка при сохранении формы',
            'status' => $result['status'] ?? 'error',
            'errors' => isset($result['errors']) ? $result['errors'] : array(),
            'invalid_fields' => isset($result['invalid_fields']) ? $result['invalid_fields'] : array(),
        ), $status_code);
    }
}

/**
 * Санитизация данных формы OrderForm
 * 
 * @param array $data Данные формы
 * @return array Санитизированные данные
 */
function sanitize_order_form_data($data) {
    if (!is_array($data)) {
        return array();
    }
    
    $sanitized = array();
    
    // Обрабатываем структуру с step3
    if (isset($data['step3']) && is_array($data['step3'])) {
        $sanitized['step3'] = array();
        
        $fields = array(
            'organizationName',
            'inn',
            'responsiblePerson',
            'workEmail',
            'workPhone',
            'serviceRegion',
            'coverageLevel',
            'numberOfEmployees',
            'insurerName',
            'totalPrice',
            'city',
        );
        
        foreach ($fields as $field) {
            if (isset($data['step3'][$field])) {
                if (is_array($data['step3'][$field])) {
                    $sanitized['step3'][$field] = array_map('sanitize_text_field', $data['step3'][$field]);
                } else {
                    $sanitized['step3'][$field] = sanitize_text_field($data['step3'][$field]);
                }
            }
        }
    }
    
    // Сохраняем другие поля, если они есть
    foreach ($data as $key => $value) {
        if ($key !== 'step3' && !isset($sanitized[$key])) {
            if (is_array($value)) {
                $sanitized[$key] = array_map('sanitize_text_field', $value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
    }
    
    return $sanitized;
}

/**
 * Санитизация дополнительных данных формы OrderForm
 * 
 * @param array $data Дополнительные данные
 * @return array Санитизированные данные
 */
function sanitize_order_form_additional_data($data) {
    if (!is_array($data)) {
        return array();
    }
    
    $sanitized = array();
    
    // Обрабатываем subId и clickId
    if (isset($data['subId']) && !empty($data['subId'])) {
        $sanitized['subId'] = sanitize_text_field($data['subId']);
    } elseif (isset($data['sub_id']) && !empty($data['sub_id'])) {
        $sanitized['subId'] = sanitize_text_field($data['sub_id']);
    }
    
    if (isset($data['clickId']) && !empty($data['clickId'])) {
        $sanitized['clickId'] = sanitize_text_field($data['clickId']);
    } elseif (isset($data['click_id']) && !empty($data['click_id'])) {
        $sanitized['clickId'] = sanitize_text_field($data['click_id']);
    }
    
    // Обрабатываем другие поля
    if (isset($data['coverageLevel'])) {
        $sanitized['coverageLevel'] = sanitize_text_field($data['coverageLevel']);
    }
    
    if (isset($data['selectedCities'])) {
        if (is_array($data['selectedCities'])) {
            $sanitized['selectedCities'] = array_map('sanitize_text_field', $data['selectedCities']);
        } else {
            $sanitized['selectedCities'] = sanitize_text_field($data['selectedCities']);
        }
    }
    
    if (isset($data['numberOfEmployees'])) {
        $sanitized['numberOfEmployees'] = sanitize_text_field($data['numberOfEmployees']);
    }
    
    // Обрабатываем selectedOffer
    if (isset($data['selectedOffer']) && is_array($data['selectedOffer'])) {
        $offer = array();
        
        if (isset($data['selectedOffer']['insurerName'])) {
            $offer['insurerName'] = sanitize_text_field($data['selectedOffer']['insurerName']);
        }
        
        if (isset($data['selectedOffer']['city'])) {
            $offer['city'] = sanitize_text_field($data['selectedOffer']['city']);
        }
        
        if (isset($data['selectedOffer']['record']) && is_array($data['selectedOffer']['record'])) {
            $record = array();
            
            if (isset($data['selectedOffer']['record']['total_price'])) {
                $record['total_price'] = floatval($data['selectedOffer']['record']['total_price']);
            }
            
            $offer['record'] = $record;
        }
        
        $sanitized['selectedOffer'] = $offer;
    }
    
    return $sanitized;
}

