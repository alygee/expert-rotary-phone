<?php
/**
 * Функции для подключения скриптов и стилей
 */

function twentyfifteen_scripts() {
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}

function jquery_noconflikt() {
  wp_add_inline_script( 'jquery-core', '$ = jQuery;' );
}

function footer_enqueue_scripts(){ 
    if(!is_admin()){
        wp_dequeue_script('jquery');
        wp_dequeue_script('jquery-core');
        wp_dequeue_script('jquery-migrate');
        wp_enqueue_script('jquery', false, array(), false, true);
        wp_enqueue_script('jquery-core', false, array(), false, true);
        wp_enqueue_script('jquery-migrate', false, array(), false, true);

        wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/assets/css/output.css', array(), '1.0.0');
        
        // Подключаем CSS для tokenize2
        wp_enqueue_style(
            'tokenize2-css',
            get_template_directory_uri() . '/assets/css/tokenize2.min.css',
            array(),
            '1.0.0'
        );
        
        // Подключаем JavaScript библиотеки с зависимостями
        // Tokenize2 - библиотека для множественного выбора
        wp_enqueue_script(
            'tokenize2',
            get_template_directory_uri() . '/assets/js/tokenize2.min.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Mainselect - кастомный селект
        wp_enqueue_script(
            'mainselect',
            get_template_directory_uri() . '/assets/js/mainselect.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Masked input - маска для телефона
        wp_enqueue_script(
            'jquery-maskedinput',
            get_template_directory_uri() . '/assets/js/jquery.maskedinput.min.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Основной скрипт темы
        wp_enqueue_script(
            'dmc-my-script',
            get_template_directory_uri() . '/assets/js/my-script.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Скрипт фильтрации
        wp_enqueue_script(
            'dmc-filter',
            get_template_directory_uri() . '/assets/js/filter.js',
            array('jquery', 'tokenize2', 'mainselect'),
            '1.0.0',
            true
        );
    }
}

/**
 * Добавляет defer атрибут к скриптам для улучшения производительности
 */
function add_defer_to_scripts($tag, $handle, $src) {
    // Список скриптов, которым нужен defer
    $defer_scripts = array(
        'tokenize2',
        'mainselect',
        'jquery-maskedinput',
        'dmc-my-script',
        'dmc-filter'
    );
    
    if (in_array($handle, $defer_scripts)) {
        // Добавляем defer, если его еще нет
        if (strpos($tag, 'defer') === false) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'add_defer_to_scripts', 10, 3);

