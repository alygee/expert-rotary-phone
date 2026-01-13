<?php
/**
 * Plugin Name: Roistat Control
 * Plugin URI: https://kubiki.ai
 * Description: Управление скриптом Roistat Counter Start — включение/выключение через админ-панель
 * Version: 1.0.0
 * Author: Kubiki.ai
 * License: GPL v2 or later
 * Text Domain: roistat-control
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

class Roistat_Control {
    
    private static $instance = null;
    
    /**
     * Получение экземпляра класса (Singleton)
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Конструктор
     */
    private function __construct() {
        // Добавляем страницу настроек в админку
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Регистрируем настройки
        add_action('admin_init', [$this, 'register_settings']);
        
        // Выводим скрипт в footer (перед </body>)
        add_action('wp_footer', [$this, 'output_roistat_script'], 99);
        
        // Добавляем ссылку на настройки в списке плагинов
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);
    }
    
    /**
     * Добавление страницы настроек в меню
     */
    public function add_admin_menu() {
        add_options_page(
            'Roistat Control',           // Заголовок страницы
            'Roistat Control',           // Название в меню
            'manage_options',            // Права доступа
            'roistat-control',           // Slug страницы
            [$this, 'render_settings_page'] // Callback для отрисовки страницы
        );
    }
    
    /**
     * Регистрация настроек
     */
    public function register_settings() {
        register_setting('roistat_control_settings', 'roistat_enabled', [
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ]);
        
        register_setting('roistat_control_settings', 'roistat_script', [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => [$this, 'sanitize_script']
        ]);
        
        // Секция настроек
        add_settings_section(
            'roistat_control_main',
            'Настройки Roistat Counter',
            [$this, 'section_description'],
            'roistat-control'
        );
        
        // Поле включения/выключения
        add_settings_field(
            'roistat_enabled',
            'Включить Roistat',
            [$this, 'render_enabled_field'],
            'roistat-control',
            'roistat_control_main'
        );
        
        // Поле для скрипта
        add_settings_field(
            'roistat_script',
            'Код скрипта Roistat',
            [$this, 'render_script_field'],
            'roistat-control',
            'roistat_control_main'
        );
    }
    
    /**
     * Санитизация скрипта (разрешаем HTML/JS)
     */
    public function sanitize_script($input) {
        // Разрешаем скрипты без изменений для администраторов
        if (current_user_can('unfiltered_html')) {
            return $input;
        }
        return wp_kses_post($input);
    }
    
    /**
     * Описание секции
     */
    public function section_description() {
        echo '<p>Управление скриптом Roistat Counter Start. Вставьте код скрипта и включите/выключите его по необходимости.</p>';
    }
    
    /**
     * Рендер чекбокса включения
     */
    public function render_enabled_field() {
        $enabled = get_option('roistat_enabled', false);
        ?>
        <label>
            <input type="checkbox" name="roistat_enabled" value="1" <?php checked($enabled, true); ?>>
            Вывести скрипт Roistat на сайте
        </label>
        <p class="description">Если включено, скрипт будет добавлен перед закрывающим тегом &lt;/body&gt;</p>
        <?php
    }
    
    /**
     * Рендер поля для скрипта
     */
    public function render_script_field() {
        $script = get_option('roistat_script', '');
        ?>
        <textarea name="roistat_script" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($script); ?></textarea>
        <p class="description">
            Вставьте полный код скрипта Roistat Counter Start, включая теги &lt;script&gt;.<br>
            Пример:
        </p>
        <pre style="background: #f1f1f1; padding: 10px; margin-top: 5px; font-size: 12px;">&lt;script&gt;
(function(w, d, s, h, id) {
    w.roistatProjectId = id; w.roistatHost = h;
    var p = d.location.protocol == "https:" ? "https://" : "http://";
    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init?referrer="+encodeURIComponent(d.location.href);
    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
})(window, document, 'script', 'cloud.roistat.com', 'YOUR_PROJECT_ID');
&lt;/script&gt;</pre>
        <?php
    }
    
    /**
     * Рендер страницы настроек
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Показываем сообщение об успешном сохранении
        if (isset($_GET['settings-updated'])) {
            add_settings_error('roistat_messages', 'roistat_message', 'Настройки сохранены', 'updated');
        }
        
        settings_errors('roistat_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-top: 20px;">
                <h2 style="margin-top: 0;">Статус</h2>
                <?php
                $enabled = get_option('roistat_enabled', false);
                $script = get_option('roistat_script', '');
                
                if ($enabled && !empty($script)) {
                    echo '<p style="color: green; font-weight: bold;">✓ Скрипт Roistat АКТИВЕН</p>';
                } elseif ($enabled && empty($script)) {
                    echo '<p style="color: orange; font-weight: bold;">⚠ Скрипт включен, но код не указан</p>';
                } else {
                    echo '<p style="color: #999;">○ Скрипт Roistat отключен</p>';
                }
                ?>
            </div>
            
            <form action="options.php" method="post" style="margin-top: 20px;">
                <?php
                settings_fields('roistat_control_settings');
                do_settings_sections('roistat-control');
                submit_button('Сохранить настройки');
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Вывод скрипта Roistat в footer
     */
    public function output_roistat_script() {
        // Не выводим в админке
        if (is_admin()) {
            return;
        }
        
        $enabled = get_option('roistat_enabled', false);
        $script = get_option('roistat_script', '');
        
        if ($enabled && !empty($script)) {
            // Выводим скрипт как есть
            echo "\n<!-- Roistat Counter Start -->\n";
            echo $script;
            echo "\n<!-- Roistat Counter End -->\n";
        }
    }
    
    /**
     * Добавление ссылки на настройки в списке плагинов
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=roistat-control">Настройки</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Инициализация плагина
Roistat_Control::get_instance();
