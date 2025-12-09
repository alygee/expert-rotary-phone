<?php
/**
 * Functions and Definitions
 * 
 * Этот файл содержит только подключения модулей и регистрацию хуков WordPress.
 * Вся логика функций вынесена в отдельные файлы в директории inc/
 */

// Подключаем модули темы
require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/body-classes.php';
require_once get_template_directory() . '/inc/images.php';
require_once get_template_directory() . '/inc/scripts.php';
require_once get_template_directory() . '/inc/uploads.php';
require_once get_template_directory() . '/inc/editor.php';
require_once get_template_directory() . '/inc/csv-functions.php';
require_once get_template_directory() . '/inc/filter-functions.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/api-endpoints.php';
require_once get_template_directory() . '/inc/cf7-database.php';
require_once get_template_directory() . '/inc/jivo-webhook.php';

// ============================================
// Регистрация хуков WordPress
// ============================================

// Body classes
add_filter( 'body_class', 'twentysixteen_body_classes' );

// Изображения
add_filter('wp_get_attachment_image_attributes', 'unset_attach_srcset_attr', 99 );
remove_action( 'wp_head', 'wp_resource_hints', 2 );

// Скрипты и стили
add_action( 'wp_enqueue_scripts', 'twentyfifteen_scripts' );
add_action( 'wp_enqueue_scripts', 'jquery_noconflikt' );
add_action('wp_enqueue_scripts','footer_enqueue_scripts');

// Загрузка файлов
add_filter( 'upload_mimes', 'upload_allow_types' );

// Редактор
add_filter('tiny_mce_before_init', 'my_adds_alls_elements', 20);

// AJAX обработчики
add_action('wp_ajax_action', 'filter_callback');
add_action('wp_ajax_nopriv_action', 'filter_callback');

// Подключаем утилиту для обновления CSV поля (только для админов)
if (is_admin()) {
    require_once get_template_directory() . '/inc/update-csv-field.php';
    add_action('wp_ajax_update_csv_field', 'ajax_update_csv_field');
}

/**
 * AJAX обработчик для обновления CSV поля
 */
function ajax_update_csv_field() {
    check_ajax_referer('update_csv_field_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Недостаточно прав'));
        return;
    }
    
    $result = update_csv_field();
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}

add_filter('upload_dir', function($dirs) {
    // Устанавливаем права на новую директорию
    if (isset($dirs['path'])) {
        @chmod($dirs['path'], 0775);
    }
    return $dirs;
});

// ============================================
// Отключение функций WordPress
// ============================================

// Отключаем Embeds связанные с REST API
remove_action( 'wp_head','wp_oembed_add_host_js');

// В хед убираем скрипт смайликов
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );