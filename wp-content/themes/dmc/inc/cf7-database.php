<?php
/**
 * Сохранение заявок Contact Form 7 в базу данных
 */

/**
 * Создание таблицы для хранения заявок
 * Проверяет существование таблицы и создает её при необходимости
 */
function cf7_create_submissions_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cf7_submissions';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        form_id varchar(20) NOT NULL,
        form_title varchar(255) DEFAULT NULL,
        submitted_data longtext NOT NULL,
        ip_address varchar(45) DEFAULT NULL,
        user_agent text DEFAULT NULL,
        sub_id varchar(255) DEFAULT NULL,
        click_id varchar(255) DEFAULT NULL,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY form_id (form_id),
        KEY submitted_at (submitted_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Проверяем и добавляем колонки, если таблица уже существует, но колонок нет
    $columns = $wpdb->get_col("DESCRIBE $table_name");
    
    if (!in_array('sub_id', $columns)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN sub_id varchar(255) DEFAULT NULL");
    }
    
    if (!in_array('click_id', $columns)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN click_id varchar(255) DEFAULT NULL");
    }
}

/**
 * Сохранение заявки в БД при отправке формы
 * Сохраняет данные независимо от результата отправки email
 */
function cf7_save_submission_to_db($contact_form, $result) {
    global $wpdb;
    
    $submission = WPCF7_Submission::get_instance();
    
    if (!$submission) {
        return;
    }
    
    // Проверяем статус отправки
    $status = $submission->get_status();
    
    // Сохраняем только если форма прошла валидацию
    // Статус может быть: 'init', 'validation_failed', 'acceptance_missing', 'spam', 'mail_sent', 'mail_failed'
    if ($status === 'validation_failed' || $status === 'acceptance_missing') {
        return; // Не сохраняем невалидные формы
    }
    
    // Получаем ID формы
    $form_id = $contact_form->id();
    
    // Получаем все данные формы
    $posted_data = $submission->get_posted_data();
    
    // Извлекаем subId и clickId из данных формы
    $sub_id = null;
    $click_id = null;
    
    // Проверяем различные варианты названий полей
    if (isset($posted_data['subId']) && !empty($posted_data['subId'])) {
        $sub_id = sanitize_text_field($posted_data['subId']);
    } elseif (isset($posted_data['sub_id']) && !empty($posted_data['sub_id'])) {
        $sub_id = sanitize_text_field($posted_data['sub_id']);
    } elseif (isset($_POST['subId']) && !empty($_POST['subId'])) {
        $sub_id = sanitize_text_field($_POST['subId']);
    } elseif (isset($_GET['subId']) && !empty($_GET['subId'])) {
        $sub_id = sanitize_text_field($_GET['subId']);
    }
    
    if (isset($posted_data['clickId']) && !empty($posted_data['clickId'])) {
        $click_id = sanitize_text_field($posted_data['clickId']);
    } elseif (isset($posted_data['click_id']) && !empty($posted_data['click_id'])) {
        $click_id = sanitize_text_field($posted_data['click_id']);
    } elseif (isset($_POST['clickId']) && !empty($_POST['clickId'])) {
        $click_id = sanitize_text_field($_POST['clickId']);
    } elseif (isset($_GET['clickId']) && !empty($_GET['clickId'])) {
        $click_id = sanitize_text_field($_GET['clickId']);
    }
    
    // Временное логирование для отладки (можно удалить после тестирования)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('CF7 Submission Debug - subId: ' . ($sub_id ?? 'не найден') . ', clickId: ' . ($click_id ?? 'не найден'));
        error_log('CF7 Submission Debug - posted_data keys: ' . implode(', ', array_keys($posted_data)));
        if (isset($_POST['subId']) || isset($_POST['clickId'])) {
            error_log('CF7 Submission Debug - $_POST содержит subId или clickId');
        }
        if (isset($_GET['subId']) || isset($_GET['clickId'])) {
            error_log('CF7 Submission Debug - $_GET содержит subId или clickId');
        }
    }
    
    // Подготавливаем данные для сохранения
    $submitted_data = array();
    foreach ($posted_data as $key => $value) {
        // Пропускаем служебные поля
        if (strpos($key, '_') === 0) {
            continue;
        }
        
        // Обрабатываем массивы (например, чекбоксы)
        if (is_array($value)) {
            $submitted_data[$key] = implode(', ', array_filter($value));
        } else {
            $submitted_data[$key] = sanitize_text_field($value);
        }
    }
    
    // Получаем дополнительную информацию
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $form_title = $contact_form->title();
    
    // Добавляем статус отправки в данные
    $submitted_data['_submission_status'] = $status;
    
    // Сохраняем в БД
    $table_name = $wpdb->prefix . 'cf7_submissions';
    
    $insert_data = array(
        'form_id' => $form_id,
        'form_title' => $form_title,
        'submitted_data' => json_encode($submitted_data, JSON_UNESCAPED_UNICODE),
        'ip_address' => $ip_address,
        'user_agent' => $user_agent,
    );
    
    $insert_format = array('%s', '%s', '%s', '%s', '%s');
    
    // Добавляем sub_id и click_id, если они есть
    if ($sub_id !== null) {
        $insert_data['sub_id'] = $sub_id;
        $insert_format[] = '%s';
    }
    
    if ($click_id !== null) {
        $insert_data['click_id'] = $click_id;
        $insert_format[] = '%s';
    }
    
    $wpdb->insert($table_name, $insert_data, $insert_format);
}

/**
 * Добавление страницы в админ-панель для просмотра заявок
 */
function cf7_add_admin_menu() {
    add_menu_page(
        'Заявки форм',
        'Заявки форм',
        'manage_options',
        'cf7-submissions',
        'cf7_display_submissions_page',
        'dashicons-email-alt',
        30
    );
}

/**
 * Отображение страницы с заявками
 */
function cf7_display_submissions_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cf7_submissions';
    
    // Обработка удаления заявки
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('delete_submission_' . $_GET['id']);
        $wpdb->delete($table_name, array('id' => intval($_GET['id'])), array('%d'));
        echo '<div class="notice notice-success"><p>Заявка удалена.</p></div>';
    }
    
    // Обработка экспорта в CSV
    if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
        check_admin_referer('export_submissions');
        cf7_export_submissions_to_csv();
        exit;
    }
    
    // Получаем заявки
    $form_id_filter = isset($_GET['form_id']) ? sanitize_text_field($_GET['form_id']) : '';
    $where_clause = '';
    if ($form_id_filter) {
        $where_clause = $wpdb->prepare("WHERE form_id = %s", $form_id_filter);
    }
    
    $submissions = $wpdb->get_results(
        "SELECT * FROM $table_name $where_clause ORDER BY submitted_at DESC LIMIT 100"
    );
    
    // Получаем список всех форм для фильтра
    $forms = $wpdb->get_results(
        "SELECT DISTINCT form_id, form_title FROM $table_name ORDER BY form_id"
    );
    
    ?>
    <div class="wrap">
        <h1>Заявки форм Contact Form 7</h1>
        
        <div class="tablenav top">
            <form method="get" action="" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="page" value="cf7-submissions">
                <label for="form_id">Фильтр по форме:</label>
                <select name="form_id" id="form_id">
                    <option value="">Все формы</option>
                    <?php foreach ($forms as $form): ?>
                        <option value="<?php echo esc_attr($form->form_id); ?>" <?php selected($form_id_filter, $form->form_id); ?>>
                            <?php echo esc_html($form->form_title . ' (ID: ' . $form->form_id . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button" value="Фильтровать">
            </form>
            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=cf7-submissions&action=export_csv'), 'export_submissions'); ?>" 
               class="button button-primary">
                Экспорт в CSV
            </a>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Форма</th>
                    <th>Данные</th>
                    <th>Sub ID</th>
                    <th>Click ID</th>
                    <th>Статус</th>
                    <th>IP адрес</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($submissions)): ?>
                    <tr>
                        <td colspan="9">Заявки не найдены</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($submissions as $submission): ?>
                        <?php 
                        $data = json_decode($submission->submitted_data, true);
                        $status = isset($data['_submission_status']) ? $data['_submission_status'] : 'unknown';
                        unset($data['_submission_status']); // Убираем статус из отображаемых данных
                        
                        // Перевод статусов
                        $status_labels = array(
                            'mail_sent' => '<span style="color: green;">✓ Отправлено</span>',
                            'mail_failed' => '<span style="color: orange;">⚠ Ошибка отправки</span>',
                            'spam' => '<span style="color: red;">✗ Спам</span>',
                            'validation_failed' => '<span style="color: red;">✗ Ошибка валидации</span>',
                            'acceptance_missing' => '<span style="color: red;">✗ Не принято</span>',
                            'init' => '<span style="color: gray;">Ожидание</span>',
                        );
                        $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
                        ?>
                        <tr>
                            <td><?php echo esc_html($submission->id); ?></td>
                            <td>
                                <strong><?php echo esc_html($submission->form_title); ?></strong><br>
                                <small>ID: <?php echo esc_html($submission->form_id); ?></small>
                            </td>
                            <td>
                                <?php if ($data): ?>
                                    <details>
                                        <summary>Показать данные</summary>
                                        <table style="margin-top: 10px;">
                                            <?php foreach ($data as $key => $value): ?>
                                                <tr>
                                                    <td style="padding: 5px; font-weight: bold;"><?php echo esc_html($key); ?>:</td>
                                                    <td style="padding: 5px;"><?php echo esc_html($value); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </details>
                                <?php endif; ?>
                            </td>
                            <td><?php echo isset($submission->sub_id) ? esc_html($submission->sub_id) : '-'; ?></td>
                            <td><?php echo isset($submission->click_id) ? esc_html($submission->click_id) : '-'; ?></td>
                            <td><?php echo $status_label; ?></td>
                            <td><?php echo esc_html($submission->ip_address); ?></td>
                            <td><?php echo esc_html(date('d.m.Y H:i', strtotime($submission->submitted_at))); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=cf7-submissions&action=delete&id=' . $submission->id), 'delete_submission_' . $submission->id); ?>" 
                                   class="button button-small" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту заявку?');">
                                    Удалить
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Экспорт заявок в CSV
 */
function cf7_export_submissions_to_csv() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cf7_submissions';
    
    // Получаем все заявки
    $form_id_filter = isset($_GET['form_id']) ? sanitize_text_field($_GET['form_id']) : '';
    $where_clause = '';
    if ($form_id_filter) {
        $where_clause = $wpdb->prepare("WHERE form_id = %s", $form_id_filter);
    }
    
    $submissions = $wpdb->get_results(
        "SELECT * FROM $table_name $where_clause ORDER BY submitted_at DESC"
    );
    
    if (empty($submissions)) {
        wp_die('Нет данных для экспорта');
    }
    
    // Определяем все возможные поля из всех заявок
    $all_fields = array();
    foreach ($submissions as $submission) {
        $data = json_decode($submission->submitted_data, true);
        if ($data) {
            $all_fields = array_merge($all_fields, array_keys($data));
        }
    }
    $all_fields = array_unique($all_fields);
    sort($all_fields);
    
    // Заголовок CSV
    $filename = 'cf7_submissions_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Открываем поток вывода
    $output = fopen('php://output', 'w');
    
    // Добавляем BOM для корректного отображения кириллицы в Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Заголовки столбцов
    $headers = array('ID', 'ID формы', 'Название формы', 'Дата отправки', 'IP адрес', 'Sub ID', 'Click ID');
    $headers = array_merge($headers, $all_fields);
    fputcsv($output, $headers, ';');
    
    // Данные
    foreach ($submissions as $submission) {
        $data = json_decode($submission->submitted_data, true);
        $row = array(
            $submission->id,
            $submission->form_id,
            $submission->form_title,
            $submission->submitted_at,
            $submission->ip_address,
            isset($submission->sub_id) ? $submission->sub_id : '',
            isset($submission->click_id) ? $submission->click_id : ''
        );
        
        // Добавляем значения полей
        foreach ($all_fields as $field) {
            $row[] = isset($data[$field]) ? $data[$field] : '';
        }
        
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}

/**
 * Автоматическое добавление скрытых полей subId и clickId в формы CF7
 * Поля будут автоматически заполняться из URL параметров через JavaScript
 */
function cf7_add_url_params_fields($form) {
    // Добавляем скрытые поля в форму, если их еще нет
    // JavaScript будет заполнять их значениями из URL
    $hidden_fields = '<input type="hidden" name="subId" value="" class="cf7-url-param-subid">';
    $hidden_fields .= '<input type="hidden" name="clickId" value="" class="cf7-url-param-clickid">';
    
    // Вставляем поля перед закрывающим тегом формы
    $form = str_replace('</form>', $hidden_fields . '</form>', $form);
    
    return $form;
}

// Хуки
// Создаем таблицу при загрузке админки или при отправке формы
add_action('admin_init', 'cf7_create_submissions_table');
add_action('wpcf7_before_send_mail', 'cf7_create_submissions_table');
// Используем wpcf7_submit, который срабатывает после обработки формы и содержит полный статус
// Это позволяет сохранять данные даже если email не отправился
add_action('wpcf7_submit', 'cf7_save_submission_to_db', 10, 2);
add_action('admin_menu', 'cf7_add_admin_menu');
// Автоматически добавляем скрытые поля в формы CF7
add_filter('wpcf7_form_elements', 'cf7_add_url_params_fields');

