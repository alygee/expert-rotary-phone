<?php
/**
 * Управление данными страховщиков в базе данных
 */

/**
 * Создание таблицы для хранения данных страховщиков
 */
function insurers_create_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        insurer varchar(255) NOT NULL COMMENT 'Название страховщика',
        city varchar(255) NOT NULL COMMENT 'Город',
        level varchar(255) DEFAULT NULL COMMENT 'Уровень',
        employees_count varchar(50) DEFAULT NULL COMMENT 'Количество сотрудников',
        polyclinic varchar(50) DEFAULT NULL COMMENT 'Поликлиника',
        dentistry varchar(50) DEFAULT NULL COMMENT 'Стоматология',
        ambulance varchar(50) DEFAULT NULL COMMENT 'Скорая помощь',
        hospitalization varchar(50) DEFAULT NULL COMMENT 'Госпитализация',
        doctor_home varchar(50) DEFAULT NULL COMMENT 'Вызов врача на дом',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY insurer (insurer),
        KEY city (city),
        KEY level (level),
        KEY employees_count (employees_count)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Импорт данных страховщиков из CSV файла
 */
function insurers_import_from_csv($file_path) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    
    // Проверяем существование файла
    if (!file_exists($file_path) || !is_readable($file_path)) {
        return array(
            'success' => false,
            'message' => 'Файл не найден или недоступен для чтения'
        );
    }
    
    // Открываем файл
    $handle = fopen($file_path, 'r');
    if ($handle === false) {
        return array(
            'success' => false,
            'message' => 'Не удалось открыть файл'
        );
    }
    
    // Пропускаем BOM, если есть
    $bom = fread($handle, 3);
    if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
        rewind($handle);
    }
    
    // Читаем заголовки
    $headers = fgetcsv($handle, 0, ',', '"', '\\');
    if ($headers === false) {
        fclose($handle);
        return array(
            'success' => false,
            'message' => 'Не удалось прочитать заголовки CSV файла'
        );
    }
    
    // Нормализуем заголовки (убираем пробелы)
    $headers = array_map('trim', $headers);
    
    // Маппинг колонок CSV на поля БД
    $column_mapping = array(
        'Страховщик' => 'insurer',
        'Город' => 'city',
        'Уровень' => 'level',
        'Кол-во_сотрудников' => 'employees_count',
        'Поликлиника' => 'polyclinic',
        'Стоматология' => 'dentistry',
        'Скорая_помощь' => 'ambulance',
        'Госпитализация' => 'hospitalization',
        'Вызов_врача_на_дом' => 'doctor_home'
    );
    
    // Определяем индексы колонок
    $column_indexes = array();
    foreach ($column_mapping as $csv_header => $db_field) {
        $index = array_search($csv_header, $headers);
        if ($index !== false) {
            $column_indexes[$db_field] = $index;
        }
    }
    
    // Проверяем обязательные поля
    if (!isset($column_indexes['insurer']) || !isset($column_indexes['city'])) {
        fclose($handle);
        return array(
            'success' => false,
            'message' => 'В CSV файле отсутствуют обязательные колонки: "Страховщик" или "Город"'
        );
    }
    
    // Очищаем таблицу перед импортом (если выбрано)
    $clear_table = isset($_POST['clear_table']) && $_POST['clear_table'] === '1';
    if ($clear_table) {
        $wpdb->query("TRUNCATE TABLE $table_name");
    }
    
    $imported = 0;
    $skipped = 0;
    $errors = array();
    
    // Читаем данные построчно
    $line_number = 1; // Уже прочитали заголовки
    while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
        $line_number++;
        
        // Пропускаем пустые строки
        if (empty(array_filter($row))) {
            continue;
        }
        
        // Проверяем минимальное количество колонок
        if (count($row) < count($headers)) {
            $errors[] = "Строка {$line_number}: недостаточно колонок";
            $skipped++;
            continue;
        }
        
        // Извлекаем данные
        $insurer = isset($column_indexes['insurer']) && isset($row[$column_indexes['insurer']]) 
            ? sanitize_text_field(trim($row[$column_indexes['insurer']])) 
            : '';
        
        $city = isset($column_indexes['city']) && isset($row[$column_indexes['city']]) 
            ? sanitize_text_field(trim($row[$column_indexes['city']])) 
            : '';
        
        // Проверяем обязательные поля
        if (empty($insurer) || empty($city)) {
            $errors[] = "Строка {$line_number}: отсутствует обязательное поле (Страховщик или Город)";
            $skipped++;
            continue;
        }
        
        // Подготавливаем данные для вставки
        $insert_data = array(
            'insurer' => $insurer,
            'city' => $city
        );
        
        $insert_format = array('%s', '%s');
        
        // Добавляем остальные поля, если они есть
        $optional_fields = array('level', 'employees_count', 'polyclinic', 'dentistry', 'ambulance', 'hospitalization', 'doctor_home');
        foreach ($optional_fields as $field) {
            if (isset($column_indexes[$field]) && isset($row[$column_indexes[$field]])) {
                $value = trim($row[$column_indexes[$field]]);
                if ($value !== '') {
                    $insert_data[$field] = sanitize_text_field($value);
                    $insert_format[] = '%s';
                }
            }
        }
        
        // Вставляем в БД
        $result = $wpdb->insert($table_name, $insert_data, $insert_format);
        
        if ($result === false) {
            $errors[] = "Строка {$line_number}: ошибка при вставке в БД - " . $wpdb->last_error;
            $skipped++;
        } else {
            $imported++;
        }
    }
    
    fclose($handle);
    
    return array(
        'success' => true,
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors,
        'message' => "Импортировано: {$imported}, пропущено: {$skipped}"
    );
}

/**
 * Добавление страницы в админ-панель для управления данными страховщиков
 */
function insurers_add_admin_menu() {
    add_menu_page(
        'Данные страховщиков',
        'Страховщики',
        'manage_options',
        'insurers-data',
        'insurers_display_admin_page',
        'dashicons-building',
        31
    );
}

/**
 * Отображение страницы админки для управления данными страховщиков
 */
function insurers_display_admin_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    
    // Обработка импорта CSV
    if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
        check_admin_referer('import_insurers_csv');
        
        if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['csv_file']['tmp_name'];
            $file_name = sanitize_file_name($_FILES['csv_file']['name']);
            
            // Проверяем расширение файла
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($file_ext !== 'csv') {
                echo '<div class="notice notice-error"><p>Ошибка: загруженный файл должен иметь расширение .csv</p></div>';
            } else {
                // Импортируем файл
                $result = insurers_import_from_csv($file_tmp);
                
                if ($result['success']) {
                    $message = $result['message'];
                    if (!empty($result['errors'])) {
                        $message .= '<br><strong>Ошибки (первые 20):</strong><ul>';
                        foreach (array_slice($result['errors'], 0, 20) as $error) {
                            $message .= '<li>' . esc_html($error) . '</li>';
                        }
                        if (count($result['errors']) > 20) {
                            $message .= '<li>... и еще ' . (count($result['errors']) - 20) . ' ошибок</li>';
                        }
                        $message .= '</ul>';
                    }
                    echo '<div class="notice notice-success"><p>' . $message . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>Ошибка импорта: ' . esc_html($result['message']) . '</p></div>';
                }
            }
        } else {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE => 'Файл превышает максимальный размер, установленный в php.ini',
                UPLOAD_ERR_FORM_SIZE => 'Файл превышает максимальный размер формы',
                UPLOAD_ERR_PARTIAL => 'Файл был загружен частично',
                UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
                UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
                UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
                UPLOAD_ERR_EXTENSION => 'Загрузка файла была остановлена расширением PHP'
            );
            $error_msg = isset($error_messages[$_FILES['csv_file']['error']]) 
                ? $error_messages[$_FILES['csv_file']['error']] 
                : 'Неизвестная ошибка';
            echo '<div class="notice notice-error"><p>Ошибка загрузки файла: ' . $error_msg . '</p></div>';
        }
    }
    
    // Обработка очистки таблицы
    if (isset($_GET['action']) && $_GET['action'] === 'clear_table' && isset($_GET['_wpnonce'])) {
        check_admin_referer('clear_insurers_table');
        $deleted = $wpdb->query("TRUNCATE TABLE $table_name");
        if ($deleted !== false) {
            echo '<div class="notice notice-success"><p>Таблица очищена успешно.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Ошибка при очистке таблицы: ' . $wpdb->last_error . '</p></div>';
        }
    }
    
    // Получаем статистику
    $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $unique_insurers = $wpdb->get_var("SELECT COUNT(DISTINCT insurer) FROM $table_name");
    $unique_cities = $wpdb->get_var("SELECT COUNT(DISTINCT city) FROM $table_name");
    
    ?>
    <div class="wrap">
        <h1>Данные страховщиков</h1>
        
        <!-- Статистика -->
        <div style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;">Статистика</h2>
            <p>
                <strong>Всего записей:</strong> <?php echo esc_html($total_records); ?><br>
                <strong>Уникальных страховщиков:</strong> <?php echo esc_html($unique_insurers); ?><br>
                <strong>Уникальных городов:</strong> <?php echo esc_html($unique_cities); ?>
            </p>
        </div>
        
        <!-- Форма импорта CSV -->
        <div style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;">Импорт из CSV</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('import_insurers_csv'); ?>
                <p>
                    <label for="csv_file"><strong>Выберите CSV файл для импорта:</strong></label><br>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required style="margin-top: 5px; padding: 5px;">
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="clear_table" value="1">
                        <strong>Очистить таблицу перед импортом</strong> (все существующие данные будут удалены)
                    </label>
                </p>
                <p class="description">
                    <strong>Формат файла:</strong><br>
                    • Файл должен быть в формате CSV с разделителем "," (запятая)<br>
                    • Обязательные колонки: <strong>"Страховщик"</strong>, <strong>"Город"</strong><br>
                    • Дополнительные колонки: "Уровень", "Кол-во_сотрудников", "Поликлиника", "Стоматология", "Скорая_помощь", "Госпитализация", "Вызов_врача_на_дом"
                </p>
                <p>
                    <input type="submit" name="import_csv" class="button button-primary button-large" value="📥 Импортировать CSV">
                </p>
            </form>
        </div>
        
        <!-- Кнопка очистки -->
        <div style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;">Очистка данных</h2>
            <p>
                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=insurers-data&action=clear_table'), 'clear_insurers_table'); ?>" 
                   class="button button-secondary" 
                   onclick="return confirm('Вы уверены, что хотите удалить все данные страховщиков? Это действие нельзя отменить!');">
                    🗑️ Очистить таблицу
                </a>
            </p>
        </div>
        
        <!-- Просмотр данных -->
        <?php if ($total_records > 0): ?>
        <div style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0;">Просмотр данных (первые 50 записей)</h2>
            <?php
            $records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 50");
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Страховщик</th>
                        <th>Город</th>
                        <th>Уровень</th>
                        <th>Сотрудников</th>
                        <th>Поликлиника</th>
                        <th>Стоматология</th>
                        <th>Скорая помощь</th>
                        <th>Госпитализация</th>
                        <th>Вызов врача</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo esc_html($record->id); ?></td>
                        <td><?php echo esc_html($record->insurer); ?></td>
                        <td><?php echo esc_html($record->city); ?></td>
                        <td><?php echo esc_html($record->level ?? '-'); ?></td>
                        <td><?php echo esc_html($record->employees_count ?? '-'); ?></td>
                        <td><?php echo esc_html($record->polyclinic ?? '-'); ?></td>
                        <td><?php echo esc_html($record->dentistry ?? '-'); ?></td>
                        <td><?php echo esc_html($record->ambulance ?? '-'); ?></td>
                        <td><?php echo esc_html($record->hospitalization ?? '-'); ?></td>
                        <td><?php echo esc_html($record->doctor_home ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Получение данных страховщиков из базы данных
 * Возвращает данные в том же формате, что и функция rez() из CSV
 * 
 * @return array|false Массив данных или false в случае ошибки
 */
function rez_from_db() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    
    // Проверяем существование таблицы
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Таблица ' . $table_name . ' не существует');
        }
        return false;
    }
    
    // Получаем все записи из БД
    $records = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    
    if ($records === false) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Ошибка при получении данных: ' . $wpdb->last_error);
        }
        return false;
    }
    
    // Преобразуем формат БД в формат CSV (для совместимости с существующим кодом)
    $data = array();
    foreach ($records as $record) {
        $row = array(
            'Страховщик' => $record['insurer'] ?? '',
            'Город' => $record['city'] ?? '',
            'Уровень' => $record['level'] ?? '',
            'Кол-во_сотрудников' => $record['employees_count'] ?? '',
            'Поликлиника' => $record['polyclinic'] ?? '',
            'Стоматология' => $record['dentistry'] ?? '',
            'Скорая_помощь' => $record['ambulance'] ?? '',
            'Госпитализация' => $record['hospitalization'] ?? '',
            'Вызов_врача_на_дом' => $record['doctor_home'] ?? '',
        );
        $data[] = $row;
    }
    
    return $data;
}

/**
 * Фильтрация данных страховщиков из БД по заданным параметрам
 * 
 * @param array $params Параметры фильтрации:
 *   - employees_count (int) - количество сотрудников
 *   - city (string) - регион обслуживания
 *   - insurer (string) - название страховщика
 *   - services (array) - массив boolean значений для услуг:
 *     - polyclinic (bool)
 *     - dentistry (bool)
 *     - ambulance (bool)
 *     - hospitalization (bool)
 *     - doctor_home (bool)
 * @return array Массив отфильтрованных записей в формате для API
 */
function filter_insurers_from_db($params = array()) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    
    // Проверяем существование таблицы
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('filter_insurers_from_db - Таблица не существует');
        }
        return array();
    }
    
    // Начинаем построение SQL запроса
    $where_conditions = array();
    $where_values = array();
    
    // Фильтр по количеству сотрудников
    if (!empty($params['employees_count']) && is_numeric($params['employees_count'])) {
        $employees_count = (int) $params['employees_count'];
        // Формат в БД может быть "5-10" или просто "5"
        // Проверяем оба варианта
        $where_conditions[] = "(
            employees_count IS NOT NULL 
            AND employees_count != '' 
            AND (
                (employees_count REGEXP '^[0-9]+-[0-9]+$'
                 AND CAST(SUBSTRING_INDEX(employees_count, '-', 1) AS UNSIGNED) <= %d
                 AND CAST(SUBSTRING_INDEX(employees_count, '-', -1) AS UNSIGNED) >= %d)
                OR
                (employees_count REGEXP '^[0-9]+$'
                 AND CAST(employees_count AS UNSIGNED) = %d)
            )
        )";
        $where_values[] = $employees_count;
        $where_values[] = $employees_count;
        $where_values[] = $employees_count;
    }
    
    // Фильтр по региону обслуживания
    if (!empty($params['city'])) {
        // Используем LIKE для более гибкого поиска (учитывает возможные пробелы)
        // $where_conditions[] = "city LIKE %s";
        // $where_values[] = '%' . $wpdb->esc_like(trim($params['city'])) . '%';

        // $where_conditions[] = "city LIKE CONCAT('%', %s, '%')";
        // $where_values[] = trim($params['city']);

        $value = $wpdb->esc_like(trim($params['city']));
        $where_conditions[] = "city LIKE CONCAT('%', %s, '%')";
        $where_values[] = $value;
    }
    
    // Фильтр по страховщику
    if (!empty($params['insurer'])) {
        // Используем LIKE для более гибкого поиска
        // $where_conditions[] = "insurer LIKE %s";
        // $where_values[] = '%' . $wpdb->esc_like(trim($params['insurer'])) . '%';
        // $where_conditions[] = "insurer LIKE CONCAT('%', %s, '%')";
        // $where_values[] = trim($params['insurer']);

        $value = $wpdb->esc_like(trim($params['insurer']));
        $where_conditions[] = "insurer LIKE CONCAT('%', %s, '%')";
        $where_values[] = $value;
    }
    
    // Фильтры по услугам (boolean - проверяем, что услуга есть)
    $services = array(
        'polyclinic' => 'polyclinic',
        'dentistry' => 'dentistry',
        'ambulance' => 'ambulance',
        'hospitalization' => 'hospitalization',
        'doctor_home' => 'doctor_home'
    );
    
    foreach ($services as $param_key => $db_field) {
        // Проверяем наличие параметра и его значение
        // Параметр должен быть установлен и равен true (не false, не null, не пустая строка)
        if (isset($params[$param_key]) && $params[$param_key] === true) {
            // Услуга должна быть не пустой и не равна "#Н/Д" или подобным значениям
            $where_conditions[] = "(
                $db_field IS NOT NULL 
                AND $db_field != '' 
                AND $db_field != '#Н/Д'
                AND $db_field != 'Н/Д'
                AND CAST(REPLACE(REPLACE($db_field, ' ', ''), ',', '.') AS DECIMAL(10,2)) > 0
            )";
        }
    }
    
    // Собираем WHERE условие
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Формируем SQL запрос
    $sql = "SELECT * FROM $table_name $where_clause ORDER BY insurer, city, level";
    
    // Выполняем запрос с подготовленными значениями
    if (!empty($where_values)) {
        $sql = $wpdb->prepare($sql, $where_values);
        $sql = $wpdb->remove_placeholder_escape($sql);
    }
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('filter_insurers_from_db - SQL запрос: ' . $sql);
    }
    
    $results = $wpdb->get_results($sql, ARRAY_A);
    
    if ($results === false) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('filter_insurers_from_db() - Ошибка SQL: ' . $wpdb->last_error);
        }
        return array();
    }
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('filter_insurers_from_db - Найдено результатов: ' . count($results));
    }
    
    // Преобразуем в формат для API (совместимый с существующим форматом)
    $data = array();
    foreach ($results as $record) {
        $row = array(
            'Страховщик' => $record['insurer'] ?? '',
            'Город' => $record['city'] ?? '',
            'Уровень' => $record['level'] ?? '',
            'Кол-во_сотрудников' => $record['employees_count'] ?? '',
            'Поликлиника' => $record['polyclinic'] ?? '',
            'Стоматология' => $record['dentistry'] ?? '',
            'Скорая_помощь' => $record['ambulance'] ?? '',
            'Госпитализация' => $record['hospitalization'] ?? '',
            'Вызов_врача_на_дом' => $record['doctor_home'] ?? '',
        );
        $data[] = $row;
    }
    
    return $data;
}

/**
 * Получает fallback данные (записи "Другой город") из БД по заданным параметрам
 * 
 * @param array $params Параметры фильтрации (без city):
 *   - employees_count (int) - количество сотрудников
 *   - insurer (string) - название страховщика
 *   - polyclinic (bool)
 *   - dentistry (bool)
 *   - ambulance (bool)
 *   - hospitalization (bool)
 *   - doctor_home (bool)
 * @return array Массив fallback записей в формате для API
 */
function get_fallback_insurers_from_db($params = array()) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'insurers_data';
    
    // Проверяем существование таблицы
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        return array();
    }
    
    // Начинаем построение SQL запроса
    $where_conditions = array();
    $where_values = array();
    
    // Фильтр по городу - только "Другой город"
    $where_conditions[] = "city = %s";
    $where_values[] = 'Другой город';
    
    // Фильтр по количеству сотрудников
    if (!empty($params['employees_count']) && is_numeric($params['employees_count'])) {
        $employees_count = (int) $params['employees_count'];
        // Формат в БД может быть "5-10" или просто "5"
        $where_conditions[] = "(
            employees_count IS NOT NULL 
            AND employees_count != '' 
            AND (
                (employees_count REGEXP '^[0-9]+-[0-9]+$'
                 AND CAST(SUBSTRING_INDEX(employees_count, '-', 1) AS UNSIGNED) <= %d
                 AND CAST(SUBSTRING_INDEX(employees_count, '-', -1) AS UNSIGNED) >= %d)
                OR
                (employees_count REGEXP '^[0-9]+$'
                 AND CAST(employees_count AS UNSIGNED) = %d)
            )
        )";
        $where_values[] = $employees_count;
        $where_values[] = $employees_count;
        $where_values[] = $employees_count;
    }
    
    // Фильтр по страховщику
    if (!empty($params['insurer'])) {
        $where_conditions[] = "insurer LIKE %s";
        $where_values[] = '%' . $wpdb->esc_like(trim($params['insurer'])) . '%';
    }
    
    // Фильтры по услугам (boolean - проверяем, что услуга есть)
    $services = array(
        'polyclinic' => 'polyclinic',
        'dentistry' => 'dentistry',
        'ambulance' => 'ambulance',
        'hospitalization' => 'hospitalization',
        'doctor_home' => 'doctor_home'
    );
    
    foreach ($services as $param_key => $db_field) {
        if (isset($params[$param_key]) && $params[$param_key] === true) {
            $where_conditions[] = "(
                $db_field IS NOT NULL 
                AND $db_field != '' 
                AND $db_field != '#Н/Д'
                AND $db_field != 'Н/Д'
                AND CAST(REPLACE(REPLACE($db_field, ' ', ''), ',', '.') AS DECIMAL(10,2)) > 0
            )";
        }
    }
    
    // Собираем WHERE условие
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    
    // Формируем SQL запрос
    $sql = "SELECT * FROM $table_name $where_clause ORDER BY insurer, level";
    
    // Выполняем запрос с подготовленными значениями
    $sql = $wpdb->prepare($sql, $where_values);
    $sql = $wpdb->remove_placeholder_escape($sql);
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('get_fallback_insurers_from_db - SQL запрос: ' . $sql);
    }
    
    $results = $wpdb->get_results($sql, ARRAY_A);
    
    if ($results === false) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('get_fallback_insurers_from_db() - Ошибка SQL: ' . $wpdb->last_error);
        }
        return array();
    }
    
    // Преобразуем в формат для API
    $data = array();
    foreach ($results as $record) {
        $row = array(
            'Страховщик' => $record['insurer'] ?? '',
            'Город' => $record['city'] ?? '',
            'Уровень' => $record['level'] ?? '',
            'Кол-во_сотрудников' => $record['employees_count'] ?? '',
            'Поликлиника' => $record['polyclinic'] ?? '',
            'Стоматология' => $record['dentistry'] ?? '',
            'Скорая_помощь' => $record['ambulance'] ?? '',
            'Госпитализация' => $record['hospitalization'] ?? '',
            'Вызов_врача_на_дом' => $record['doctor_home'] ?? '',
        );
        $data[] = $row;
    }
    
    return $data;
}

function get_unique_cities_from_db(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'insurers_data';

    // Проверяем существование таблицы
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Таблица ' . $table_name . ' не существует');
        }
        return false;
    }

    // Получаем все записи из БД
    $records = $wpdb->get_results("SELECT DISTINCT city FROM $table_name", ARRAY_A);

    if ($records === false) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Ошибка при получении данных: ' . $wpdb->last_error);
        }
        return false;
    }

    // Преобразуем формат БД в формат CSV (для совместимости с существующим кодом)
    $data = array();
    foreach ($records as $record) {
        $row = array(
            'Город' => $record['city'] ?? '',
        );
        $data[] = $row;
    }

    return $data;
}

function get_unique_insurers_from_db(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'insurers_data';

    // Проверяем существование таблицы
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    if (!$table_exists) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Таблица ' . $table_name . ' не существует');
        }
        return false;
    }

    // Получаем все записи из БД
    $records = $wpdb->get_results("SELECT DISTINCT insurer FROM $table_name", ARRAY_A);

    if ($records === false) {
        if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
            error_log('rez_from_db() - Ошибка при получении данных: ' . $wpdb->last_error);
        }
        return false;
    }

    // Преобразуем формат БД в формат CSV (для совместимости с существующим кодом)
    $data = array();
    foreach ($records as $record) {
        $row = array(
            'Страховщик' => $record['insurer'] ?? '',
        );
        $data[] = $row;
    }

    return $data;
}

// Хуки
add_action('admin_init', 'insurers_create_table');
add_action('admin_menu', 'insurers_add_admin_menu');
