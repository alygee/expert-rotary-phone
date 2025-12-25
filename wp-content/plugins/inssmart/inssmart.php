<?php
/**
 * Plugin Name: Inssmart Form
 * Plugin URI: https://kubiki.ai
 * Description: Интеграция многошаговой формы Inssmart в WordPress. Плагин предоставляет шорткод для вставки формы на любую страницу или пост.
 * Version: 1.0.0
 * Author: Kubiki.ai
 * Author URI: https://kubiki.ai
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: inssmart
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Предотвращаем прямой доступ
if (!defined('ABSPATH')) {
    exit;
}

// Константы плагина
define('INSSMART_VERSION', '1.0.0');
define('INSSMART_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('INSSMART_PLUGIN_URL', plugin_dir_url(__FILE__));
define('INSSMART_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Подключаем файл интеграции с Contact Form 7
require_once INSSMART_PLUGIN_DIR . 'includes/cf7-integration.php';

/**
 * Основной класс плагина
 */
class Inssmart_Form {
    
    /**
     * Единственный экземпляр класса
     */
    private static $instance = null;
    
    /**
     * Получить экземпляр класса (Singleton)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Конструктор
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Инициализация хуков
     */
    private function init_hooks() {
        // Подключение скриптов и стилей
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Регистрация шорткода
        add_shortcode('inssmart_form', array($this, 'form_shortcode'));
        
        // Админ-панель
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // AJAX обработчики
        add_action('wp_ajax_inssmart_submit_order', array($this, 'ajax_submit_order'));
        add_action('wp_ajax_nopriv_inssmart_submit_order', array($this, 'ajax_submit_order'));
        add_action('wp_ajax_inssmart_submit_callback', array($this, 'ajax_submit_callback'));
        add_action('wp_ajax_nopriv_inssmart_submit_callback', array($this, 'ajax_submit_callback'));
        
        // Диагностический AJAX обработчик (только для отладки)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('wp_ajax_inssmart_diagnostic', array($this, 'ajax_diagnostic'));
            add_action('wp_ajax_nopriv_inssmart_diagnostic', array($this, 'ajax_diagnostic'));
        }
        
        // Локализация
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    /**
     * Подключение скриптов и стилей
     */
    public function enqueue_scripts() {
        $plugin_url = INSSMART_PLUGIN_URL;
        $version = INSSMART_VERSION;
        
        // Проверяем наличие собранных файлов
        $css_path = INSSMART_PLUGIN_DIR . 'dist/inssmart-form.css';
        $js_path = INSSMART_PLUGIN_DIR . 'dist/inssmart-form.js';
        
        // Подключаем CSS только если файл существует
        if (file_exists($css_path)) {
            wp_enqueue_style(
                'inssmart-form',
                $plugin_url . 'dist/inssmart-form.css',
                array(),
                $version
            );
        }
        
        // Подключаем шрифты напрямую, если они не встроены в CSS
        $fonts_dir = INSSMART_PLUGIN_DIR . 'dist/fonts';
        if (is_dir($fonts_dir)) {
            $font_files = array('Jost-Regular.ttf', 'Jost-Medium.ttf');
            foreach ($font_files as $font_file) {
                $font_path = $fonts_dir . '/' . $font_file;
                if (file_exists($font_path)) {
                    // Шрифты будут загружаться через CSS @font-face
                    // Здесь можно добавить preload для оптимизации, если нужно
                }
            }
        }
        
        // Подключаем JS только если файл существует
        if (file_exists($js_path)) {
            wp_enqueue_script(
                'inssmart-form',
                $plugin_url . 'dist/inssmart-form.js',
                array(),
                $version,
                true
            );
            
            // Локализация для AJAX
            $nonce = wp_create_nonce('inssmart_ajax');
            
            // Логирование nonce для отладки (только в режиме отладки)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                inssmart_log('Создан nonce для AJAX: ' . $nonce, 'info');
            }
            
            wp_localize_script('inssmart-form', 'inssmartAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => $nonce,
            ));
        }
    }
    
    /**
     * Диагностическая функция для проверки AJAX настроек
     * Можно вызвать через AJAX для отладки
     */
    public function ajax_diagnostic() {
        // Разрешаем вызов без nonce для диагностики (только в режиме отладки)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $diagnostic = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce_created' => wp_create_nonce('inssmart_ajax'),
                'nonce_received' => isset($_POST['nonce']) ? $_POST['nonce'] : 'не передан',
                'nonce_valid' => isset($_POST['nonce']) ? wp_verify_nonce($_POST['nonce'], 'inssmart_ajax') : false,
                'user_logged_in' => is_user_logged_in(),
                'current_user_id' => get_current_user_id(),
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'неизвестно',
                'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'неизвестно',
                'post_data_keys' => array_keys($_POST),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'неизвестно',
            );
            
            wp_send_json_success($diagnostic);
        } else {
            wp_send_json_error(array('message' => 'Диагностика доступна только в режиме отладки'));
        }
    }
    
    /**
     * Шорткод для вставки формы
     * 
     * @param array $atts Атрибуты шорткода
     * @return string HTML код формы
     */
    public function form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'container_id' => 'inssmart-form-container',
            'class' => ''
        ), $atts, 'inssmart_form');
        
        $container_id = esc_attr($atts['container_id']);
        $class = esc_attr($atts['class']);
        
        ob_start();
        ?>
        <div id="<?php echo $container_id; ?>" class="inssmart-form-wrapper <?php echo $class; ?>"></div>
        <script>
            (function() {
                var containerId = '<?php echo $container_id; ?>';
                var maxAttempts = 50;
                var attempt = 0;
                
                function initForm() {
                    attempt++;
                    
                    // Проверяем несколько способов доступа к функции
                    var initFn = null;
                    if (typeof window.initInssmartForm === 'function') {
                        initFn = window.initInssmartForm;
                    } else if (typeof window.InssmartForm !== 'undefined' && typeof window.InssmartForm.initInssmartForm === 'function') {
                        initFn = window.InssmartForm.initInssmartForm;
                    }
                    
                    if (initFn) {
                        try {
                            initFn(containerId);
                        } catch (error) {
                            console.error('Inssmart Form: Error initializing form', error);
                        }
                    } else if (attempt < maxAttempts) {
                        // Пытаемся еще раз через небольшую задержку
                        setTimeout(initForm, 100);
                    } else {
                        console.warn('Inssmart Form: initInssmartForm function not found after ' + maxAttempts + ' attempts. Make sure the plugin files are built and loaded.');
                    }
                }
                
                // Запускаем инициализацию после загрузки DOM
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(initForm, 100);
                    });
                } else {
                    setTimeout(initForm, 100);
                }
            })();
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Добавление пункта меню в админ-панель
     */
    public function add_admin_menu() {
        add_options_page(
            __('Inssmart Form Settings', 'inssmart'),
            __('Inssmart Form', 'inssmart'),
            'manage_options',
            'inssmart-form',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Регистрация настроек
     */
    public function register_settings() {
        register_setting('inssmart_settings', 'inssmart_settings');
    }
    
    /**
     * Страница настроек в админ-панели
     */
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Сохранение настроек
        if (isset($_POST['inssmart_settings_submit'])) {
            check_admin_referer('inssmart_settings');
            
            $settings = array();
            $settings['order_form_id'] = isset($_POST['order_form_id']) ? sanitize_text_field($_POST['order_form_id']) : '';
            $settings['callback_form_id'] = isset($_POST['callback_form_id']) ? sanitize_text_field($_POST['callback_form_id']) : '';
            
            // Маппинг полей для формы заказа
            $order_mapping = array();
            if (isset($_POST['order_mapping']) && is_array($_POST['order_mapping'])) {
                foreach ($_POST['order_mapping'] as $react_field => $cf7_field) {
                    $order_mapping[sanitize_text_field($react_field)] = sanitize_text_field($cf7_field);
                }
            }
            $settings['field_mapping']['order'] = $order_mapping;
            
            // Маппинг полей для формы обратного звонка
            $callback_mapping = array();
            if (isset($_POST['callback_mapping']) && is_array($_POST['callback_mapping'])) {
                foreach ($_POST['callback_mapping'] as $react_field => $cf7_field) {
                    $callback_mapping[sanitize_text_field($react_field)] = sanitize_text_field($cf7_field);
                }
            }
            $settings['field_mapping']['callback'] = $callback_mapping;
            
            update_option('inssmart_settings', $settings);
            echo '<div class="notice notice-success"><p>' . __('Settings saved.', 'inssmart') . '</p></div>';
        }
        
        $settings = get_option('inssmart_settings', array());
        $order_form_id = isset($settings['order_form_id']) ? $settings['order_form_id'] : '';
        $callback_form_id = isset($settings['callback_form_id']) ? $settings['callback_form_id'] : '';
        $order_mapping = isset($settings['field_mapping']['order']) ? $settings['field_mapping']['order'] : array();
        $callback_mapping = isset($settings['field_mapping']['callback']) ? $settings['field_mapping']['callback'] : array();
        
        // Получаем список форм Contact Form 7
        $cf7_forms = array();
        $order_form_fields = array();
        $callback_form_fields = array();
        
        if (class_exists('WPCF7_ContactForm')) {
            $forms = WPCF7_ContactForm::find();
            foreach ($forms as $form) {
                $cf7_forms[] = array(
                    'id' => $form->id(),
                    'title' => $form->title(),
                );
            }
            
            // Получаем поля выбранных форм для подсказок
            if (!empty($order_form_id)) {
                $order_form = WPCF7_ContactForm::get_instance($order_form_id);
                if ($order_form) {
                    $order_form_fields = $this->get_cf7_form_fields($order_form);
                }
            }
            
            if (!empty($callback_form_id)) {
                $callback_form = WPCF7_ContactForm::get_instance($callback_form_id);
                if ($callback_form) {
                    $callback_form_fields = $this->get_cf7_form_fields($callback_form);
                }
            }
        }
        
        $css_exists = file_exists(INSSMART_PLUGIN_DIR . 'dist/inssmart-form.css');
        $js_exists = file_exists(INSSMART_PLUGIN_DIR . 'dist/inssmart-form.js');
        $cf7_active = class_exists('WPCF7_ContactForm');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if (!$cf7_active): ?>
                <div class="notice notice-error">
                    <p><?php _e('Contact Form 7 is not installed or activated. Please install and activate Contact Form 7 to use this feature.', 'inssmart'); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('inssmart_settings'); ?>
                
                <div class="card">
                    <h2><?php _e('Contact Form 7 Integration', 'inssmart'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="order_form_id"><?php _e('Order Form (CF7 ID)', 'inssmart'); ?></label>
                            </th>
                            <td>
                                <select name="order_form_id" id="order_form_id">
                                    <option value=""><?php _e('-- Select Form --', 'inssmart'); ?></option>
                                    <?php foreach ($cf7_forms as $form): ?>
                                        <option value="<?php echo esc_attr($form['id']); ?>" <?php selected($order_form_id, $form['id']); ?>>
                                            <?php echo esc_html($form['title'] . ' (ID: ' . $form['id'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Select Contact Form 7 form for order submissions.', 'inssmart'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="callback_form_id"><?php _e('Callback Form (CF7 ID)', 'inssmart'); ?></label>
                            </th>
                            <td>
                                <select name="callback_form_id" id="callback_form_id">
                                    <option value=""><?php _e('-- Select Form --', 'inssmart'); ?></option>
                                    <?php foreach ($cf7_forms as $form): ?>
                                        <option value="<?php echo esc_attr($form['id']); ?>" <?php selected($callback_form_id, $form['id']); ?>>
                                            <?php echo esc_html($form['title'] . ' (ID: ' . $form['id'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Select Contact Form 7 form for callback submissions.', 'inssmart'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="card">
                    <h2><?php _e('Field Mapping - Order Form', 'inssmart'); ?></h2>
                    <p class="description"><?php _e('Map React form fields to Contact Form 7 field names.', 'inssmart'); ?></p>
                    <?php if (!empty($order_form_fields)): ?>
                        <div class="notice notice-info inline">
                            <p><strong><?php _e('Available fields in selected CF7 form:', 'inssmart'); ?></strong> 
                            <code><?php echo esc_html(implode(', ', $order_form_fields)); ?></code></p>
                        </div>
                    <?php endif; ?>
                    <table class="form-table">
                        <?php
                        $order_fields = array(
                            'organizationName' => 'Organization Name',
                            'inn' => 'INN',
                            'responsiblePerson' => 'Responsible Person',
                            'workEmail' => 'Work Email',
                            'workPhone' => 'Work Phone',
                            'serviceRegion' => 'Service Region',
                            'coverageLevel' => 'Coverage Level',
                            'numberOfEmployees' => 'Number of Employees',
                            'insurerName' => 'Insurer Name',
                            'totalPrice' => 'Total Price',
                            'city' => 'City',
                        );
                        foreach ($order_fields as $react_field => $label):
                            $cf7_field = isset($order_mapping[$react_field]) ? $order_mapping[$react_field] : '';
                        ?>
                        <tr>
                            <th scope="row">
                                <label for="order_mapping_<?php echo esc_attr($react_field); ?>"><?php echo esc_html($label); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       name="order_mapping[<?php echo esc_attr($react_field); ?>]" 
                                       id="order_mapping_<?php echo esc_attr($react_field); ?>" 
                                       value="<?php echo esc_attr($cf7_field); ?>" 
                                       class="regular-text" 
                                       placeholder="<?php echo esc_attr($react_field); ?>">
                                <p class="description"><?php _e('CF7 field name', 'inssmart'); ?></p>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                
                <div class="card">
                    <h2><?php _e('Field Mapping - Callback Form', 'inssmart'); ?></h2>
                    <p class="description"><?php _e('Map React form fields to Contact Form 7 field names.', 'inssmart'); ?></p>
                    <?php if (!empty($callback_form_fields)): ?>
                        <div class="notice notice-info inline">
                            <p><strong><?php _e('Available fields in selected CF7 form:', 'inssmart'); ?></strong> 
                            <code><?php echo esc_html(implode(', ', $callback_form_fields)); ?></code></p>
                        </div>
                    <?php endif; ?>
                    <table class="form-table">
                        <?php
                        $callback_fields = array(
                            'callbackName' => 'Name',
                            'callbackPhone' => 'Phone',
                            'isAgreed' => 'Agreement',
                        );
                        foreach ($callback_fields as $react_field => $label):
                            $cf7_field = isset($callback_mapping[$react_field]) ? $callback_mapping[$react_field] : '';
                        ?>
                        <tr>
                            <th scope="row">
                                <label for="callback_mapping_<?php echo esc_attr($react_field); ?>"><?php echo esc_html($label); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       name="callback_mapping[<?php echo esc_attr($react_field); ?>]" 
                                       id="callback_mapping_<?php echo esc_attr($react_field); ?>" 
                                       value="<?php echo esc_attr($cf7_field); ?>" 
                                       class="regular-text" 
                                       placeholder="<?php echo esc_attr($react_field); ?>">
                                <p class="description"><?php _e('CF7 field name', 'inssmart'); ?></p>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                
                <?php submit_button(__('Save Settings', 'inssmart'), 'primary', 'inssmart_settings_submit'); ?>
            </form>
            
            <div class="card">
                <h2><?php _e('Plugin Information', 'inssmart'); ?></h2>
                <p>
                    <strong><?php _e('Version:', 'inssmart'); ?></strong> <?php echo INSSMART_VERSION; ?>
                </p>
                <p>
                    <strong><?php _e('Plugin Directory:', 'inssmart'); ?></strong> 
                    <code><?php echo INSSMART_PLUGIN_DIR; ?></code>
                </p>
            </div>
            
            <div class="card">
                <h2><?php _e('Build Status', 'inssmart'); ?></h2>
                <p>
                    <strong><?php _e('CSS File:', 'inssmart'); ?></strong>
                    <?php if ($css_exists): ?>
                        <span style="color: green;">✓ <?php _e('Found', 'inssmart'); ?></span>
                    <?php else: ?>
                        <span style="color: red;">✗ <?php _e('Not Found', 'inssmart'); ?></span>
                        <br><small><?php _e('Run', 'inssmart'); ?> <code>npm run build:wordpress</code> <?php _e('to build the plugin files.', 'inssmart'); ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <strong><?php _e('JS File:', 'inssmart'); ?></strong>
                    <?php if ($js_exists): ?>
                        <span style="color: green;">✓ <?php _e('Found', 'inssmart'); ?></span>
                    <?php else: ?>
                        <span style="color: red;">✗ <?php _e('Not Found', 'inssmart'); ?></span>
                        <br><small><?php _e('Run', 'inssmart'); ?> <code>npm run build:wordpress</code> <?php _e('to build the plugin files.', 'inssmart'); ?></small>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="card">
                <h2><?php _e('Usage', 'inssmart'); ?></h2>
                <p><?php _e('To insert the form on any page or post, use the following shortcode:', 'inssmart'); ?></p>
                <code>[inssmart_form]</code>
                
                <p><?php _e('You can also customize the container ID and add custom classes:', 'inssmart'); ?></p>
                <code>[inssmart_form container_id="my-custom-form" class="custom-class"]</code>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX обработчик для отправки формы заказа
     */
    public function ajax_submit_order() {
        // Логирование для отладки 403 ошибки
        if (defined('WP_DEBUG') && WP_DEBUG) {
            inssmart_log('AJAX запрос получен - action: inssmart_submit_order', 'info');
            inssmart_log('POST данные: ' . print_r($_POST, true), 'info');
            inssmart_log('Nonce получен: ' . (isset($_POST['nonce']) ? $_POST['nonce'] : 'не найден'), 'info');
            inssmart_log('HTTP метод: ' . ($_SERVER['REQUEST_METHOD'] ?? 'неизвестно'), 'info');
        }
        
        // Проверка nonce с более детальной обработкой ошибок
        $nonce_check = check_ajax_referer('inssmart_ajax', 'nonce', false);
        
        if (!$nonce_check) {
            // Детальное логирование ошибки nonce
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $received_nonce = isset($_POST['nonce']) ? $_POST['nonce'] : 'не передан';
                $expected_nonce = wp_create_nonce('inssmart_ajax');
                inssmart_log('ОШИБКА NONCE - Получен: ' . $received_nonce, 'error');
                inssmart_log('ОШИБКА NONCE - Ожидается: ' . $expected_nonce, 'error');
                inssmart_log('ОШИБКА NONCE - Проверка: ' . wp_verify_nonce($received_nonce, 'inssmart_ajax'), 'error');
            }
            
            wp_send_json_error(array(
                'message' => 'Ошибка безопасности: неверный или отсутствующий nonce. Обновите страницу и попробуйте снова.',
                'error_code' => 'nonce_failed',
                'debug_info' => defined('WP_DEBUG') && WP_DEBUG ? array(
                    'received_nonce' => isset($_POST['nonce']) ? 'present' : 'missing',
                    'user_logged_in' => is_user_logged_in(),
                ) : null,
            ), 403);
            return;
        }

        $form_data_raw = isset($_POST['form_data']) ? $_POST['form_data'] : '';
        $additional_data_raw = isset($_POST['additional_data']) ? $_POST['additional_data'] : '';
        
        // Если данные пришли как JSON-строка, декодируем их
        if (is_string($form_data_raw)) {
            $form_data = json_decode(stripslashes($form_data_raw), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $form_data = array();
                inssmart_log('Ошибка декодирования JSON form_data в ajax_submit_order: ' . json_last_error_msg(), 'error');
            }
        } else {
            $form_data = $form_data_raw;
        }
        
        if (is_string($additional_data_raw)) {
            $additional_data = json_decode(stripslashes($additional_data_raw), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $additional_data = array();
                inssmart_log('Ошибка декодирования JSON additional_data в ajax_submit_order: ' . json_last_error_msg(), 'error');
            }
        } else {
            $additional_data = $additional_data_raw;
        }
        
        // Если после декодирования не массивы, создаем пустые массивы
        if (!is_array($form_data)) {
            $form_data = array();
        }
        if (!is_array($additional_data)) {
            $additional_data = array();
        }

        // Валидация и санитизация данных
        $form_data = $this->sanitize_form_data($form_data);
        $additional_data = $this->sanitize_additional_data($additional_data);
        
        // Логирование для отладки
        if (defined('WP_DEBUG') && WP_DEBUG) {
            inssmart_log('ajax_submit_order - additional_data после санитизации: ' . print_r($additional_data, true), 'info');
            inssmart_log('ajax_submit_order - subId в additional_data: ' . (isset($additional_data['subId']) ? $additional_data['subId'] : 'не найден'), 'info');
            inssmart_log('ajax_submit_order - clickId в additional_data: ' . (isset($additional_data['clickId']) ? $additional_data['clickId'] : 'не найден'), 'info');
        }

        // Отправка в Contact Form 7
        $result = inssmart_submit_to_cf7($form_data, 'order', $additional_data);

        if ($result['success']) {
            wp_send_json_success(array(
                'message' => $result['message'],
                'status' => $result['status'],
            ));
        } else {
            wp_send_json_error(array(
                'message' => $result['message'],
                'errors' => isset($result['errors']) ? $result['errors'] : array(),
                'status' => isset($result['status']) ? $result['status'] : null,
            ));
        }
    }

    /**
     * AJAX обработчик для отправки формы обратного звонка
     */
    public function ajax_submit_callback() {
        // Логирование для отладки 403 ошибки
        if (defined('WP_DEBUG') && WP_DEBUG) {
            inssmart_log('AJAX запрос получен - action: inssmart_submit_callback', 'info');
            inssmart_log('POST данные: ' . print_r($_POST, true), 'info');
            inssmart_log('Nonce получен: ' . (isset($_POST['nonce']) ? $_POST['nonce'] : 'не найден'), 'info');
        }
        
        // Проверка nonce с более детальной обработкой ошибок
        $nonce_check = check_ajax_referer('inssmart_ajax', 'nonce', false);
        
        if (!$nonce_check) {
            // Детальное логирование ошибки nonce
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $received_nonce = isset($_POST['nonce']) ? $_POST['nonce'] : 'не передан';
                $expected_nonce = wp_create_nonce('inssmart_ajax');
                inssmart_log('ОШИБКА NONCE - Получен: ' . $received_nonce, 'error');
                inssmart_log('ОШИБКА NONCE - Ожидается: ' . $expected_nonce, 'error');
            }
            
            wp_send_json_error(array(
                'message' => 'Ошибка безопасности: неверный или отсутствующий nonce. Обновите страницу и попробуйте снова.',
                'error_code' => 'nonce_failed',
            ), 403);
            return;
        }

        $form_data_raw = isset($_POST['form_data']) ? $_POST['form_data'] : '';
        $additional_data_raw = isset($_POST['additional_data']) ? $_POST['additional_data'] : '';
        
        // Если данные пришли как JSON-строка, декодируем их
        if (is_string($form_data_raw)) {
            $form_data = json_decode(stripslashes($form_data_raw), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Если не удалось декодировать, пытаемся обработать как обычную строку
                $form_data = array();
                inssmart_log('Ошибка декодирования JSON form_data в ajax_submit_callback: ' . json_last_error_msg(), 'error');
            }
        } else {
            $form_data = $form_data_raw;
        }
        
        if (is_string($additional_data_raw)) {
            $additional_data = json_decode(stripslashes($additional_data_raw), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $additional_data = array();
                inssmart_log('Ошибка декодирования JSON additional_data в ajax_submit_callback: ' . json_last_error_msg(), 'error');
            }
        } else {
            $additional_data = $additional_data_raw;
        }
        
        // Если после декодирования не массивы, создаем пустые массивы
        if (!is_array($form_data)) {
            $form_data = array();
        }
        if (!is_array($additional_data)) {
            $additional_data = array();
        }

        // Валидация и санитизация данных
        $form_data = $this->sanitize_form_data($form_data);
        $additional_data = $this->sanitize_additional_data($additional_data);
        // Отправка в Contact Form 7
        $result = inssmart_submit_to_cf7($form_data, 'callback', $additional_data);

        if ($result['success']) {
            wp_send_json_success(array(
                'message' => $result['message'],
                'status' => $result['status'],
            ));
        } else {
            wp_send_json_error(array(
                'message' => $result['message'],
                'errors' => isset($result['errors']) ? $result['errors'] : array(),
                'status' => isset($result['status']) ? $result['status'] : null,
            ));
        }
    }

    /**
     * Получение списка полей из формы Contact Form 7
     * 
     * @param WPCF7_ContactForm $form Объект формы CF7
     * @return array Массив имен полей
     */
    private function get_cf7_form_fields($form) {
        $fields = array();
        
        if (!$form || !method_exists($form, 'scan_form_tags')) {
            return $fields;
        }
        
        $tags = $form->scan_form_tags();
        foreach ($tags as $tag) {
            if (isset($tag->name) && !empty($tag->name)) {
                $fields[] = $tag->name;
            }
        }
        
        return array_unique($fields);
    }
    
    /**
     * Санитизация ключа массива с сохранением регистра
     * 
     * @param string $key Ключ для санитизации
     * @return string Санитизированный ключ
     */
    private function sanitize_key_preserve_case($key) {
        // Удаляем опасные символы, но сохраняем регистр
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
        return $key;
    }
    
    /**
     * Санитизация данных формы
     */
    private function sanitize_form_data($data) {
        if (!is_array($data)) {
            return array();
        }

        $sanitized = array();
        foreach ($data as $key => $value) {
            // Сохраняем оригинальный регистр ключа
            $sanitized_key = $this->sanitize_key_preserve_case($key);
            
            if (is_array($value)) {
                $sanitized[$sanitized_key] = $this->sanitize_form_data($value);
            } else {
                $sanitized[$sanitized_key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }
    
    /**
     * Санитизация дополнительных данных
     */
    private function sanitize_additional_data($data) {
        if (!is_array($data)) {
            return array();
        }
        
        $sanitized = array();
        
        // Добавляем subId и clickId из URL параметров
        if (isset($data['subId']) && !empty($data['subId'])) {
            $sanitized['subId'] = sanitize_text_field($data['subId']);
        } elseif (isset($data['sub_id']) && !empty($data['sub_id'])) {
            $sanitized['subId'] = sanitize_text_field($data['sub_id']);
        }
        
        if (isset($data['clickId']) && !empty($data['clickId'])) {
            $sanitized['clickId'] = sanitize_text_field($data['clickId']);
        } elseif (isset($data['click_id']) && !empty($data['click_id'])) {
            $sanitized['clickId'] = sanitize_text_field($data['click_id']);
        }
        
        if (isset($data['coverageLevel'])) {
            $sanitized['coverageLevel'] = sanitize_text_field($data['coverageLevel']);
        }
        
        if (isset($data['selectedCities'])) {
            if (is_array($data['selectedCities'])) {
                $sanitized['selectedCities'] = array_map('sanitize_text_field', $data['selectedCities']);
            } else {
                $sanitized['selectedCities'] = sanitize_text_field($data['selectedCities']);
            }
        }
        
        if (isset($data['numberOfEmployees'])) {
            $sanitized['numberOfEmployees'] = sanitize_text_field($data['numberOfEmployees']);
        }
        
        if (isset($data['selectedOffer']) && is_array($data['selectedOffer'])) {
            $offer = array();
            if (isset($data['selectedOffer']['insurerName'])) {
                $offer['insurerName'] = sanitize_text_field($data['selectedOffer']['insurerName']);
            }
            if (isset($data['selectedOffer']['city'])) {
                $offer['city'] = sanitize_text_field($data['selectedOffer']['city']);
            }
            if (isset($data['selectedOffer']['record']) && is_array($data['selectedOffer']['record'])) {
                $record = array();
                if (isset($data['selectedOffer']['record']['total_price'])) {
                    $record['total_price'] = floatval($data['selectedOffer']['record']['total_price']);
                }
                $offer['record'] = $record;
            }
            $sanitized['selectedOffer'] = $offer;
        }
        
        return $sanitized;
    }
    
    /**
     * Загрузка текстового домена для локализации
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'inssmart',
            false,
            dirname(INSSMART_PLUGIN_BASENAME) . '/languages'
        );
    }
}

/**
 * Инициализация плагина
 */
function inssmart_form_init() {
    return Inssmart_Form::get_instance();
}

// Запуск плагина
inssmart_form_init();

