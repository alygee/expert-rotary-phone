<?php
/**
 * Настройки темы
 */

// Регистрация сайдбара
register_sidebar(array(
  'name' => 'Виджет подписки',
  'id' => 'vidget1',
  'description' => '',
  'before_widget' => '',
  'after_widget' => '',
  'before_title'  => '',
  'after_title'  => ''
));

// Регистрация меню
register_nav_menus(
   array(
  'm1' => __('Верхнее меню'),
  'm2' => __('Меню в футере'),
  )
);

// Поддержка миниатюр записей
add_theme_support('post-thumbnails');

// Поддержка тега title
add_theme_support( 'title-tag' );

// Поддержка HTML5
add_theme_support( 'html5', array(
  'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
) );

// ============================================
// ACF Options Page и поля для Jivo
// ============================================

/**
 * Создает ACF Options Page для настроек темы (если ACF активен)
 */
function dmc_add_acf_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Настройки темы',
            'menu_title' => 'Настройки темы',
            'menu_slug' => 'theme-settings',
            'capability' => 'edit_posts',
            'icon_url' => 'dashicons-admin-settings',
        ));
    }
}
add_action('acf/init', 'dmc_add_acf_options_page');

/**
 * Программно добавляет ACF поле для email уведомлений Jivo
 */
function dmc_add_jivo_email_field() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    acf_add_local_field_group(array(
        'key' => 'group_jivo_settings',
        'title' => 'Настройки Jivo',
        'fields' => array(
            array(
                'key' => 'field_jivo_notification_emails',
                'label' => 'Email адреса для уведомлений Jivo',
                'name' => 'jivo_notification_emails',
                'type' => 'textarea',
                'instructions' => 'Укажите email адреса для получения уведомлений о новых сообщениях в Jivo чате. Каждый email с новой строки или через запятую. Если не указаны, будет использован email администратора сайта.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'example1@mail.ru\nexample2@mail.ru\nили через запятую: example1@mail.ru, example2@mail.ru',
                'rows' => 5,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
    ));
}
add_action('acf/init', 'dmc_add_jivo_email_field');

