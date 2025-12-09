<?php
/**
 * Обработчик событий виджета Jivo через Widget API (JavaScript)
 * Принимает события от клиентской стороны и отправляет email уведомления
 */

/**
 * Регистрация REST API endpoint для событий виджета
 */
function register_jivo_widget_event_endpoint() {
    register_rest_route('dmc/v1', '/jivo-widget-event', array(
        'methods' => 'POST',
        'callback' => 'jivo_widget_event_callback',
        'permission_callback' => '__return_true', // Публичный доступ
    ));
}
add_action('rest_api_init', 'register_jivo_widget_event_endpoint');

/**
 * Обработчик событий от виджета Jivo
 * 
 * @param WP_REST_Request $request Объект запроса
 * @return WP_REST_Response Ответ API
 */
function jivo_widget_event_callback($request) {
    // Получаем данные из запроса
    $body = $request->get_body();
    $data = json_decode($body, true);
    
    // Логируем для отладки
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Jivo Widget Event: ' . print_r($data, true));
    }
    
    // Проверяем наличие данных
    if (empty($data) || !isset($data['event_type'])) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Нет данных или тип события не указан'
        ), 400);
    }
    
    // Получаем email для отправки уведомлений
    $notification_email = get_field('jivo_notification_email', 'option');
    if (empty($notification_email)) {
        $notification_email = get_option('admin_email');
    }
    if (empty($notification_email)) {
        $notification_email = get_field('mail', 2);
    }
    
    $event_type = sanitize_text_field($data['event_type']);
    $event_data = isset($data['data']) ? $data['data'] : array();
    $message_sent = false;
    
    // Обрабатываем разные типы событий
    switch ($event_type) {
        case 'client_message':
            // Новое сообщение от клиента
            $client_name = isset($event_data['client']['name']) ? $event_data['client']['name'] : 'Неизвестный клиент';
            $client_phone = isset($event_data['client']['phone']) ? $event_data['client']['phone'] : 'Не указан';
            $client_email = isset($event_data['client']['email']) ? $event_data['client']['email'] : 'Не указан';
            $message_text = isset($event_data['message']['text']) ? $event_data['message']['text'] : '';
            
            if (!empty($message_text)) {
                $subject = 'Новое сообщение в Jivo чате от ' . $client_name;
                $email_body = create_jivo_email_template($client_name, $client_phone, $client_email, $message_text, $event_data);
                $message_sent = send_jivo_notification_email($notification_email, $subject, $email_body);
            }
            break;
            
        case 'chat_started':
            // Чат начат
            $client_name = isset($event_data['client']['name']) ? $event_data['client']['name'] : 'Неизвестный клиент';
            $client_phone = isset($event_data['client']['phone']) ? $event_data['client']['phone'] : 'Не указан';
            $client_email = isset($event_data['client']['email']) ? $event_data['client']['email'] : 'Не указан';
            
            $subject = 'Новый чат начат в Jivo от ' . $client_name;
            $email_body = '<h2>Новый чат начат</h2>';
            $email_body .= '<p><strong>Клиент:</strong> ' . esc_html($client_name) . '</p>';
            $email_body .= '<p><strong>Телефон:</strong> ' . esc_html($client_phone) . '</p>';
            $email_body .= '<p><strong>Email:</strong> ' . esc_html($client_email) . '</p>';
            $email_body .= '<p><strong>Время:</strong> ' . date('d.m.Y H:i:s') . '</p>';
            $email_body .= '<p><strong>Страница:</strong> ' . (isset($data['page_url']) ? esc_html($data['page_url']) : 'Не указана') . '</p>';
            
            $message_sent = send_jivo_notification_email($notification_email, $subject, $email_body);
            break;
            
        case 'chat_accepted':
            // Оператор принял чат
            $client_name = isset($event_data['client']['name']) ? $event_data['client']['name'] : 'Неизвестный клиент';
            $agent_name = isset($event_data['agent']['name']) ? $event_data['agent']['name'] : 'Неизвестный оператор';
            
            $subject = 'Оператор принял чат в Jivo от ' . $client_name;
            $email_body = '<h2>Оператор принял чат</h2>';
            $email_body .= '<p><strong>Клиент:</strong> ' . esc_html($client_name) . '</p>';
            $email_body .= '<p><strong>Оператор:</strong> ' . esc_html($agent_name) . '</p>';
            $email_body .= '<p><strong>Время:</strong> ' . date('d.m.Y H:i:s') . '</p>';
            
            $message_sent = send_jivo_notification_email($notification_email, $subject, $email_body);
            break;
            
        case 'chat_finished':
            // Чат завершен
            $client_name = isset($event_data['client']['name']) ? $event_data['client']['name'] : 'Неизвестный клиент';
            
            $subject = 'Чат завершен в Jivo от ' . $client_name;
            $email_body = '<h2>Чат завершен</h2>';
            $email_body .= '<p><strong>Клиент:</strong> ' . esc_html($client_name) . '</p>';
            $email_body .= '<p><strong>Время:</strong> ' . date('d.m.Y H:i:s') . '</p>';
            
            $message_sent = send_jivo_notification_email($notification_email, $subject, $email_body);
            break;
            
        case 'widget_ready':
            // Виджет загружен (только для логирования, не отправляем email)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Jivo Widget: Виджет готов к работе');
            }
            return new WP_REST_Response(array(
                'success' => true,
                'message' => 'Виджет готов'
            ), 200);
            
        default:
            // Неизвестное событие
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Jivo Widget Event: Неизвестный тип события - ' . $event_type);
            }
            break;
    }
    
    // Возвращаем ответ
    return new WP_REST_Response(array(
        'success' => $message_sent,
        'message' => $message_sent ? 'Email отправлен' : 'Событие обработано',
        'event_type' => $event_type
    ), $message_sent ? 200 : 200); // Всегда возвращаем 200, чтобы не блокировать виджет
}

/**
 * Отправляет email уведомление с логированием
 * 
 * @param string $to Email получателя
 * @param string $subject Тема письма
 * @param string $body Тело письма
 * @return bool Результат отправки
 */
function send_jivo_notification_email($to, $subject, $body) {
    // Логируем попытку отправки
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Jivo Widget Handler: Попытка отправить email на ' . $to);
        error_log('Jivo Widget Handler: Тема - ' . $subject);
    }
    
    // Добавляем фильтр для логирования ошибок wp_mail
    add_filter('wp_mail_failed', 'jivo_log_mail_error');
    
    $result = wp_mail($to, $subject, $body, array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
    ));
    
    // Логируем результат
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Jivo Widget Handler: Результат отправки email - ' . ($result ? 'успешно' : 'ошибка'));
    }
    
    // Удаляем фильтр
    remove_filter('wp_mail_failed', 'jivo_log_mail_error');
    
    return $result;
}

/**
 * Логирует ошибки отправки email
 * 
 * @param WP_Error $error Объект ошибки
 */
function jivo_log_mail_error($error) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Jivo Widget Handler: Ошибка отправки email - ' . $error->get_error_message());
    }
}

/**
 * Создает HTML шаблон для email уведомления
 * 
 * @param string $client_name Имя клиента
 * @param string $client_phone Телефон клиента
 * @param string $client_email Email клиента
 * @param string $message_text Текст сообщения
 * @param array $data Полные данные из события
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

