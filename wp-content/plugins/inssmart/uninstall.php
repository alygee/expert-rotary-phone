<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Inssmart_Form
 */

// Если файл вызван напрямую, выходим
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Удаление опций плагина из базы данных
delete_option('inssmart_settings');

// Удаление опций из всех сайтов (для мультисайта)
if (is_multisite()) {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        delete_option('inssmart_settings');
        restore_current_blog();
    }
}

