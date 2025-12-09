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
    
    // Обработка прямой отправки тестового email
    if (isset($_POST['test_email_direct']) && check_admin_referer('jivo_webhook_test')) {
        $test_email = sanitize_email($_POST['test_email']);
        if (!empty($test_email)) {
            $test_result = wp_mail(
                $test_email,
                'Тестовое письмо от ' . get_bloginfo('name'),
                'Это тестовое письмо для проверки работы отправки email на вашем сервере.',
                array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                )
            );
            $email_test_result = array(
                'success' => $test_result,
                'message' => $test_result ? 'Тестовое письмо отправлено на ' . $test_email : 'Ошибка отправки тестового письма',
                'email' => $test_email,
            );
        }
    }
    
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
    
    // Проверяем настройки email
    $admin_email = get_option('admin_email');
    $smtp_configured = false;
    
    // Проверяем наличие плагинов SMTP
    $smtp_plugins = array(
        'wp-mail-smtp/wp_mail_smtp.php',
        'easy-wp-smtp/easy-wp-smtp.php',
        'post-smtp/postman-smtp.php',
    );
    
    foreach ($smtp_plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            $smtp_configured = true;
            break;
        }
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
                    <td>
                        <strong><?php echo esc_html($notification_email ?: 'Не настроен'); ?></strong>
                        <?php if (empty($notification_email)): ?>
                            <span style="color: red;">⚠️ Настройте email в ACF опциях!</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Email администратора:</th>
                    <td><?php echo esc_html($admin_email); ?></td>
                </tr>
                <tr>
                    <th>SMTP плагин:</th>
                    <td>
                        <?php if ($smtp_configured): ?>
                            <span style="color: green;">✓ Настроен</span>
                        <?php else: ?>
                            <span style="color: orange;">⚠ Не обнаружен</span>
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                Рекомендуется установить плагин SMTP (например, WP Mail SMTP) для надежной отправки email.
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>WP_DEBUG:</th>
                    <td>
                        <?php if (defined('WP_DEBUG') && WP_DEBUG): ?>
                            <span style="color: green;">✓ Включен</span>
                            <p style="margin: 5px 0; font-size: 12px; color: #666;">
                                Логи: <code><?php echo WP_CONTENT_DIR; ?>/debug.log</code>
                            </p>
                        <?php else: ?>
                            <span style="color: orange;">⚠ Выключен</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <h2>⚠️ Важно: Проверка отправки email</h2>
            <p>Если тест показывает успех, но email не приходит, проверьте:</p>
            <ol>
                <li><strong>Проверьте папку "Спам"</strong> в почтовом ящике</li>
                <li><strong>Проверьте логи:</strong> <code><?php echo WP_CONTENT_DIR; ?>/debug.log</code></li>
                <li><strong>Установите SMTP плагин</strong> (WP Mail SMTP, Easy WP SMTP и т.д.)</li>
                <li><strong>Проверьте настройки сервера</strong> - возможно, функция mail() отключена</li>
                <li><strong>Проверьте email адрес</strong> - убедитесь, что он правильный</li>
            </ol>
            <p><strong>Быстрый тест:</strong> Попробуйте отправить тестовое письмо через WordPress (Настройки → Письма) или через плагин SMTP.</p>
        </div>
        
        <?php if ($result): ?>
            <div class="notice notice-<?php echo $result['success'] ? 'success' : 'error'; ?>" style="margin-top: 20px;">
                <p><strong><?php echo $result['success'] ? '✅ Успешно!' : '❌ Ошибка!'; ?></strong></p>
                <p><?php echo esc_html($result['message']); ?></p>
                
                <?php if (isset($result['mail_error'])): ?>
                    <div style="background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid #f44336;">
                        <strong>Ошибка отправки email:</strong> <?php echo esc_html($result['mail_error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($result['diagnostics'])): ?>
                    <details style="margin-top: 10px;">
                        <summary><strong>Диагностическая информация</strong></summary>
                        <table class="form-table" style="margin-top: 10px;">
                            <tr>
                                <th>Email получателя:</th>
                                <td><?php echo esc_html($result['diagnostics']['notification_email'] ?: 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Попытка отправки:</th>
                                <td><?php echo $result['diagnostics']['mail_attempted'] ? '✓ Да' : '✗ Нет'; ?></td>
                            </tr>
                            <?php if (isset($result['mail_info'])): ?>
                            <tr>
                                <th>Информация о письме:</th>
                                <td>
                                    <pre style="background: #f5f5f5; padding: 5px; font-size: 11px; overflow-x: auto;"><?php echo esc_html(print_r($result['mail_info'], true)); ?></pre>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </details>
                <?php endif; ?>
                
                <?php if (isset($result['response'])): ?>
                    <details style="margin-top: 10px;">
                        <summary>Полный ответ вебхука</summary>
                        <pre style="background: #f5f5f5; padding: 10px; overflow-x: auto;"><?php echo esc_html(print_r($result['response'], true)); ?></pre>
                    </details>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($email_test_result)): ?>
            <div class="notice notice-<?php echo $email_test_result['success'] ? 'success' : 'error'; ?>" style="margin-top: 20px;">
                <p><strong><?php echo $email_test_result['success'] ? '✅ Тестовое письмо отправлено!' : '❌ Ошибка отправки!'; ?></strong></p>
                <p><?php echo esc_html($email_test_result['message']); ?></p>
                <?php if (!$email_test_result['success']): ?>
                    <p style="color: red;">Проверьте логи или настройте SMTP плагин для отправки email.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>Быстрый тест отправки email</h2>
            <p>Проверьте, работает ли отправка email на вашем сервере:</p>
            <form method="post" action="" style="margin-top: 15px;">
                <?php wp_nonce_field('jivo_webhook_test'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="test_email">Email для теста:</label></th>
                        <td>
                            <input type="email" name="test_email" id="test_email" value="<?php echo esc_attr($notification_email); ?>" class="regular-text" required>
                            <input type="submit" name="test_email_direct" class="button" value="Отправить тестовое письмо">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        
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
    
    // Получаем email для проверки
    $notification_email = get_field('jivo_notification_email', 'option');
    if (empty($notification_email)) {
        $notification_email = get_option('admin_email');
    }
    if (empty($notification_email)) {
        $notification_email = get_field('mail', 2);
    }
    
    // Используем внутренний вызов WordPress REST API
    $request = new WP_REST_Request('POST', '/dmc/v1/jivo-webhook');
    $request->set_body(json_encode($data));
    $request->set_header('Content-Type', 'application/json');
    
    // Перехватываем результат wp_mail для диагностики
    $mail_result = null;
    $mail_error = null;
    
    add_filter('wp_mail', function($args) use (&$mail_result) {
        $mail_result = $args;
        return $args;
    }, 10, 1);
    
    add_filter('wp_mail_failed', function($error) use (&$mail_error) {
        $mail_error = $error->get_error_message();
    });
    
    $response = rest_do_request($request);
    $response_data = $response->get_data();
    $status = $response->get_status();
    
    // Дополнительная диагностика
    $diagnostics = array(
        'webhook_status' => $status,
        'webhook_success' => isset($response_data['success']) ? $response_data['success'] : false,
        'notification_email' => $notification_email,
        'mail_attempted' => !empty($mail_result),
    );
    
    if ($mail_error) {
        $diagnostics['mail_error'] = $mail_error;
    }
    
    return array(
        'success' => $status === 200 && isset($response_data['success']) && $response_data['success'],
        'message' => isset($response_data['message']) ? $response_data['message'] : 'Запрос отправлен',
        'status' => $status,
        'response' => $response_data,
        'diagnostics' => $diagnostics,
        'mail_info' => $mail_result,
        'mail_error' => $mail_error,
    );
}

