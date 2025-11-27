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

        wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/css/output.css', array(), '1.0.0');
    }
}

