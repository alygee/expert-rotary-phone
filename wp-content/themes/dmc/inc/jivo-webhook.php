<?php
/**
 * Обработчик вебхуков Jivo для отправки сообщений на email
 */

/**
 * Регистрация endpoint для вебхука Jivo
 */
function register_jivo_webhook_endpoint() {
    register_rest_route('dmc/v1', '/jivo-webhook', array(
        'methods' => 'POST',
        'callback' => 'jivo_webhook_callback',
        'permission_callback' => '__return_true', // Публичный доступ для вебхуков
    ));
}
add_action('rest_api_init', 'register_jivo_webhook_endpoint');

/**
 * Обработчик вебхука Jivo
 * 
 * @param WP_REST_Request $request Объект запроса
 * @return WP_REST_Response Ответ API
 */
function jivo_webhook_callback($request) {
    // Получаем данные из запроса
    $body = $request->get_body();
    $data = json_decode($body, true);
    
    // Логируем для отладки (можно убрать в продакшене)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Jivo Webhook Data: ' . print_r($data, true));
    }
    
    // Проверяем наличие данных
    if (empty($data)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Нет данных в запросе'
        ), 400);
    }
    
    // Получаем email для отправки уведомлений
    // Можно настроить через ACF поле или константу
    $notification_email = get_field('jivo_notification_email', 'option');
    
    // Если нет настройки в ACF, используем email из настроек сайта
    if (empty($notification_email)) {
        $notification_email = get_option('admin_email');
    }
    
    // Если все еще нет email, используем email из header.php
    if (empty($notification_email)) {
        $notification_email = get_field('mail', 2);
    }
    
    // Обрабатываем разные типы событий от Jivo
    $event_type = isset($data['event_name']) ? $data['event_name'] : '';
    $message_sent = false;
    
    switch ($event_type) {
        case 'chat_message':
        case 'message':
            // Обработка нового сообщения от клиента
            if (isset($data['message']) && isset($data['message']['text'])) {
                $message_text = $data['message']['text'];
                $client_name = isset($data['client']) ? $data['client']['name'] : 'Неизвестный клиент';
                $client_phone = isset($data['client']) && isset($data['client']['phone']) ? $data['client']['phone'] : 'Не указан';
                $client_email = isset($data['client']) && isset($data['client']['email']) ? $data['client']['email'] : 'Не указан';
                
                // Отправляем email
                $subject = 'Новое сообщение в Jivo чате от ' . $client_name;
                $email_body = create_jivo_email_template($client_name, $client_phone, $client_email, $message_text, $data);
                
                $message_sent = wp_mail($notification_email, $subject, $email_body, array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                ));
            }
            break;
            
        case 'chat_started':
            // Обработка начала чата
            if (isset($data['client'])) {
                $client_name = isset($data['client']['name']) ? $data['client']['name'] : 'Неизвестный клиент';
                $client_phone = isset($data['client']['phone']) ? $data['client']['phone'] : 'Не указан';
                
                $subject = 'Новый чат начат в Jivo от ' . $client_name;
                $email_body = '<h2>Новый чат начат</h2>';
                $email_body .= '<p><strong>Клиент:</strong> ' . esc_html($client_name) . '</p>';
                $email_body .= '<p><strong>Телефон:</strong> ' . esc_html($client_phone) . '</p>';
                $email_body .= '<p><strong>Время:</strong> ' . date('d.m.Y H:i:s') . '</p>';
                
                $message_sent = wp_mail($notification_email, $subject, $email_body, array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                ));
            }
            break;
            
        default:
            // Обработка других событий или универсальная обработка
            if (isset($data['message']) || isset($data['text'])) {
                $message_text = isset($data['message']['text']) ? $data['message']['text'] : (isset($data['text']) ? $data['text'] : '');
                $client_name = isset($data['client']['name']) ? $data['client']['name'] : (isset($data['visitor_name']) ? $data['visitor_name'] : 'Неизвестный клиент');
                
                if (!empty($message_text)) {
                    $subject = 'Сообщение в Jivo чате от ' . $client_name;
                    $email_body = create_jivo_email_template($client_name, '', '', $message_text, $data);
                    
                    $message_sent = wp_mail($notification_email, $subject, $email_body, array(
                        'Content-Type: text/html; charset=UTF-8',
                        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                    ));
                }
            }
            break;
    }
    
    // Возвращаем ответ для Jivo
    return new WP_REST_Response(array(
        'success' => $message_sent,
        'message' => $message_sent ? 'Email отправлен' : 'Ошибка отправки email',
        'event_type' => $event_type
    ), $message_sent ? 200 : 500);
}

/**
 * Создает HTML шаблон для email уведомления
 * 
 * @param string $client_name Имя клиента
 * @param string $client_phone Телефон клиента
 * @param string $client_email Email клиента
 * @param string $message_text Текст сообщения
 * @param array $data Полные данные из вебхука
 * @return string HTML содержимое email
 */
function create_jivo_email_template($client_name, $client_phone, $client_email, $message_text, $data = array()) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .message-box { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #4CAF50; }
        .info { margin: 10px 0; }
        .info strong { color: #4CAF50; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Новое сообщение в Jivo чате</h2>
        </div>
        <div class="content">
            <div class="info">
                <strong>Клиент:</strong> ' . esc_html($client_name) . '<br>
                <strong>Телефон:</strong> ' . esc_html($client_phone) . '<br>
                <strong>Email:</strong> ' . esc_html($client_email) . '<br>
                <strong>Время:</strong> ' . date('d.m.Y H:i:s') . '
            </div>
            <div class="message-box">
                <strong>Сообщение:</strong><br>
                ' . nl2br(esc_html($message_text)) . '
            </div>
        </div>
        <div class="footer">
            <p>Это автоматическое уведомление от ' . get_bloginfo('name') . '</p>
            <p>Сайт: <a href="' . get_bloginfo('url') . '">' . get_bloginfo('url') . '</a></p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

