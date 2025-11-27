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

