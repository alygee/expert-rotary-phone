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

// ============================================
// Отключение функций WordPress
// ============================================

// Отключаем сам REST API
add_filter('rest_enabled', '__return_false');

// Отключаем Embeds связанные с REST API
remove_action( 'wp_head','wp_oembed_add_host_js');

// В хед убираем скрипт смайликов
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );