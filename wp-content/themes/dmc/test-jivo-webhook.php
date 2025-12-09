<?php
/**
 * Тестовый скрипт для проверки вебхука Jivo через командную строку
 * 
 * Использование:
 * php test-jivo-webhook.php
 * 
 * Или через curl:
 * curl -X POST http://ваш-сайт.ru/wp-json/dmc/v1/jivo-webhook \
 *   -H "Content-Type: application/json" \
 *   -d @test-data.json
 */

// Загружаем WordPress
require_once(__DIR__ . '/../../wp-load.php');

// Тестовые данные
$test_data = array(
    'event_name' => 'chat_message',
    'client' => array(
        'name' => 'Тестовый клиент',
        'phone' => '+7 (999) 123-45-67',
        'email' => 'test@example.com',
    ),
    'message' => array(
        'text' => 'Это тестовое сообщение для проверки работы вебхука Jivo. Время: ' . date('Y-m-d H:i:s'),
    ),
);

echo "=== Тестирование Jivo Webhook ===\n\n";
echo "URL: " . rest_url('dmc/v1/jivo-webhook') . "\n";
echo "Email для уведомлений: ";

// Получаем email
$notification_email = get_field('jivo_notification_email', 'option');
if (empty($notification_email)) {
    $notification_email = get_option('admin_email');
}
if (empty($notification_email)) {
    $notification_email = get_field('mail', 2);
}

echo ($notification_email ?: 'Не настроен') . "\n\n";

echo "Отправка тестового запроса...\n\n";

// Создаем запрос
$request = new WP_REST_Request('POST', '/dmc/v1/jivo-webhook');
$request->set_body(json_encode($test_data));
$request->set_header('Content-Type', 'application/json');

// Выполняем запрос
$response = rest_do_request($request);
$response_data = $response->get_data();
$status = $response->get_status();

// Выводим результат
echo "Статус: " . $status . "\n";
echo "Успешно: " . ($response_data['success'] ? 'Да' : 'Нет') . "\n";
echo "Сообщение: " . (isset($response_data['message']) ? $response_data['message'] : 'N/A') . "\n";
echo "Тип события: " . (isset($response_data['event_type']) ? $response_data['event_type'] : 'N/A') . "\n\n";

if ($response_data['success']) {
    echo "✅ Тест пройден! Проверьте почту: " . $notification_email . "\n";
} else {
    echo "❌ Тест не пройден. Проверьте настройки.\n";
}

echo "\nПолный ответ:\n";
print_r($response_data);

