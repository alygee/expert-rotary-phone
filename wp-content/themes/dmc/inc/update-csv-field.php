<?php
/**
 * Утилита для обновления ACF поля csv_file на странице с ID 2
 * 
 * Использование:
 * 1. Через WP-CLI: wp eval-file wp-content/themes/dmc/inc/update-csv-field.php
 * 2. Через браузер: добавить ?update_csv_field=1 в URL (только для админов)
 * 3. Вызвать функцию update_csv_field() программно
 */

// Защита от прямого доступа (только через WordPress)
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

/**
 * Обновляет ACF поле csv_file на странице с ID 2
 * Ищет последний загруженный файл list.csv в директории uploads
 * 
 * @return array|false Массив с результатом обновления или false при ошибке
 */
function update_csv_field() {
    // Проверяем права доступа (только для веб-интерфейса, для CLI пропускаем)
    $is_cli = (php_sapi_name() === 'cli' || defined('WP_CLI'));
    if (!$is_cli && !current_user_can('manage_options')) {
        return array(
            'success' => false,
            'message' => 'Недостаточно прав для обновления поля'
        );
    }
    
    $page_id = 2; // ID страницы с ACF полем
    $field_name = 'csv_file'; // Имя ACF поля
    
    // Проверяем, что страница существует
    $page = get_post($page_id);
    if (!$page) {
        return array(
            'success' => false,
            'message' => "Страница с ID {$page_id} не найдена"
        );
    }
    
    // Получаем директорию uploads
    $upload_dir = wp_upload_dir();
    $uploads_base = $upload_dir['basedir'];
    
    // Ищем файлы list.csv
    $csv_files = glob($uploads_base . '/*/list.csv');
    if (empty($csv_files)) {
        // Ищем в поддиректориях (год/месяц)
        $csv_files = glob($uploads_base . '/*/*/list.csv');
    }
    
    if (empty($csv_files)) {
        return array(
            'success' => false,
            'message' => 'Файл list.csv не найден в директории uploads'
        );
    }
    
    // Сортируем по времени модификации (новейший первым)
    usort($csv_files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    $latest_csv = $csv_files[0];
    $csv_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $latest_csv);
    
    // Пытаемся найти attachment ID для этого файла
    $attachment_id = null;
    $attachment = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => 1,
        'post_status' => 'inherit',
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'value' => str_replace($uploads_base . '/', '', $latest_csv),
                'compare' => 'LIKE'
            )
        )
    ));
    
    if (!empty($attachment)) {
        $attachment_id = $attachment[0]->ID;
    } else {
        // Если attachment не найден, создаем его
        $filename = basename($latest_csv);
        $filetype = wp_check_filetype($filename, null);
        
        $attachment_data = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment_data, $latest_csv, $page_id);
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attachment_id, $latest_csv);
            wp_update_attachment_metadata($attachment_id, $attach_data);
        }
    }
    
    // Обновляем ACF поле
    // Пробуем разные варианты в зависимости от типа поля
    $update_result = false;
    
    // Вариант 1: Если поле принимает attachment ID
    if ($attachment_id) {
        $update_result = update_field($field_name, $attachment_id, $page_id);
    }
    
    // Вариант 2: Если поле принимает URL (как строка)
    if (!$update_result) {
        $update_result = update_field($field_name, $csv_url, $page_id);
    }
    
    // Вариант 3: Если поле принимает массив (file field)
    if (!$update_result && $attachment_id) {
        $file_array = array(
            'ID' => $attachment_id,
            'url' => $csv_url,
            'path' => $latest_csv
        );
        $update_result = update_field($field_name, $file_array, $page_id);
    }
    
    if ($update_result) {
        // Очищаем кеш ACF
        if (function_exists('acf_get_store')) {
            acf_get_store('values')->reset();
        }
        
        return array(
            'success' => true,
            'message' => 'ACF поле успешно обновлено',
            'file_path' => $latest_csv,
            'file_url' => $csv_url,
            'attachment_id' => $attachment_id,
            'file_time' => date('Y-m-d H:i:s', filemtime($latest_csv))
        );
    } else {
        return array(
            'success' => false,
            'message' => 'Не удалось обновить ACF поле. Проверьте тип поля и права доступа.',
            'file_path' => $latest_csv,
            'file_url' => $csv_url
        );
    }
}

// Если скрипт запущен напрямую через браузер (для админов)
if (isset($_GET['update_csv_field']) && current_user_can('manage_options')) {
    $result = update_csv_field();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

