<?php
/**
 * Быстрое обновление ACF поля csv_file
 * 
 * Использование:
 * php update-csv.php
 * или
 * wp eval-file wp-content/themes/dmc/update-csv.php
 */

require_once(__DIR__ . '/../../../wp-load.php');
require_once(__DIR__ . '/inc/update-csv-field.php');

// Выполняем обновление
$result = update_csv_field();

// Выводим результат
echo "=== Обновление ACF поля csv_file ===\n\n";

if ($result['success']) {
    echo "✓ Успешно обновлено!\n\n";
    echo "Файл: " . $result['file_path'] . "\n";
    echo "URL: " . $result['file_url'] . "\n";
    echo "Время модификации: " . $result['file_time'] . "\n";
    if (isset($result['attachment_id'])) {
        echo "ID вложения: " . $result['attachment_id'] . "\n";
    }
} else {
    echo "✗ Ошибка: " . $result['message'] . "\n\n";
    if (isset($result['file_path'])) {
        echo "Найденный файл: " . $result['file_path'] . "\n";
    }
}

echo "\n";

