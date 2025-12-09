<?php
/**
 * Тестовая страница для проверки вебхука Jivo
 * Доступна только для администраторов
 */

/**
 * Добавляет страницу в админ-меню для тестирования вебхука
 */
function add_jivo_webhook_test_page() {
    add_submenu_page(
        'tools.php', // Родительское меню (Инструменты)
        'Тест Jivo Webhook',
        'Тест Jivo Webhook',
        'manage_options', // Только для администраторов
        'jivo-webhook-test',
        'jivo_webhook_test_page_callback'
    );
}
add_action('admin_menu', 'add_jivo_webhook_test_page');

/**
 * Отображение страницы тестирования вебхука
 */
function jivo_webhook_test_page_callback() {
    // Проверка прав доступа
    if (!current_user_can('manage_options')) {
        wp_die('У вас нет прав для доступа к этой странице.');
    }
    
    $result = null;
    $test_data = null;
    
    // Обработка отправки тестового запроса
    if (isset($_POST['test_webhook']) && check_admin_referer('jivo_webhook_test')) {
        $test_data = array(
            'event_name' => sanitize_text_field($_POST['event_type']),
            'client' => array(
                'name' => sanitize_text_field($_POST['client_name']),
                'phone' => sanitize_text_field($_POST['client_phone']),
                'email' => sanitize_email($_POST['client_email']),
            ),
            'message' => array(
                'text' => sanitize_textarea_field($_POST['message_text']),
            ),
        );
        
        // Отправляем тестовый запрос
        $result = send_test_webhook_request($test_data);
    }
    
    // Получаем URL endpoint
    $webhook_url = rest_url('dmc/v1/jivo-webhook');
    $notification_email = get_field('jivo_notification_email', 'option');
    if (empty($notification_email)) {
        $notification_email = get_option('admin_email');
    }
    if (empty($notification_email)) {
        $notification_email = get_field('mail', 2);
    }
    
    ?>
    <div class="wrap">
        <h1>Тестирование Jivo Webhook</h1>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Информация о настройках</h2>
            <table class="form-table">
                <tr>
                    <th>URL вебхука:</th>
                    <td><code><?php echo esc_url($webhook_url); ?></code></td>
                </tr>
                <tr>
                    <th>Email для уведомлений:</th>
                    <td><strong><?php echo esc_html($notification_email ?: 'Не настроен'); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <?php if ($result): ?>
            <div class="notice notice-<?php echo $result['success'] ? 'success' : 'error'; ?>" style="margin-top: 20px;">
                <p><strong><?php echo $result['success'] ? '✅ Успешно!' : '❌ Ошибка!'; ?></strong></p>
                <p><?php echo esc_html($result['message']); ?></p>
                <?php if (isset($result['response'])): ?>
                    <details style="margin-top: 10px;">
                        <summary>Детали ответа</summary>
                        <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;"><?php echo esc_html(print_r($result['response'], true)); ?></pre>
                    </details>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Отправить тестовый запрос</h2>
            <form method="post" action="">
                <?php wp_nonce_field('jivo_webhook_test'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="event_type">Тип события:</label></th>
                        <td>
                            <select name="event_type" id="event_type" style="width: 100%;">
                                <option value="chat_message">chat_message (Новое сообщение)</option>
                                <option value="chat_started">chat_started (Начало чата)</option>
                                <option value="message">message (Универсальное сообщение)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="client_name">Имя клиента:</label></th>
                        <td>
                            <input type="text" name="client_name" id="client_name" value="Тестовый клиент" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="client_phone">Телефон клиента:</label></th>
                        <td>
                            <input type="text" name="client_phone" id="client_phone" value="+7 (999) 123-45-67" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="client_email">Email клиента:</label></th>
                        <td>
                            <input type="email" name="client_email" id="client_email" value="test@example.com" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="message_text">Текст сообщения:</label></th>
                        <td>
                            <textarea name="message_text" id="message_text" rows="5" class="large-text" required>Это тестовое сообщение для проверки работы вебхука Jivo.</textarea>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="test_webhook" class="button button-primary" value="Отправить тестовый запрос">
                </p>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Тестирование через cURL</h2>
            <p>Вы также можете протестировать вебхук через командную строку или Postman:</p>
            <pre style="background: #f5f5f5; padding: 15px; overflow-x: auto; border: 1px solid #ddd;">curl -X POST <?php echo esc_url($webhook_url); ?> \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "chat_message",
    "client": {
      "name": "Тестовый клиент",
      "phone": "+7 (999) 123-45-67",
      "email": "test@example.com"
    },
    "message": {
      "text": "Это тестовое сообщение"
    }
  }'</pre>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Проверка логов</h2>
            <p>Если включен режим отладки (WP_DEBUG), логи вебхука можно найти в:</p>
            <ul>
                <li><code><?php echo WP_CONTENT_DIR; ?>/debug.log</code></li>
            </ul>
            <?php if (defined('WP_DEBUG') && WP_DEBUG): ?>
                <p style="color: green;">✓ Режим отладки включен</p>
            <?php else: ?>
                <p style="color: orange;">⚠ Режим отладки выключен. Включите WP_DEBUG в wp-config.php для просмотра логов.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Отправляет тестовый запрос к вебхуку
 * 
 * @param array $data Данные для отправки
 * @return array Результат запроса
 */
function send_test_webhook_request($data) {
    $webhook_url = rest_url('dmc/v1/jivo-webhook');
    
    // Используем внутренний вызов WordPress REST API
    $request = new WP_REST_Request('POST', '/dmc/v1/jivo-webhook');
    $request->set_body(json_encode($data));
    $request->set_header('Content-Type', 'application/json');
    
    $response = rest_do_request($request);
    $response_data = $response->get_data();
    $status = $response->get_status();
    
    return array(
        'success' => $status === 200 && isset($response_data['success']) && $response_data['success'],
        'message' => isset($response_data['message']) ? $response_data['message'] : 'Запрос отправлен',
        'status' => $status,
        'response' => $response_data,
    );
}

