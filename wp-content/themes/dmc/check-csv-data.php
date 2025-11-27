<?php
/**
 * Утилита для проверки данных в CSV файле
 * 
 * Использование: php check-csv-data.php
 */

require_once(__DIR__ . '/../../../wp-load.php');
require_once(__DIR__ . '/inc/csv-functions.php');
require_once(__DIR__ . '/inc/filter-functions.php');

echo "=== Проверка данных CSV ===\n\n";

// Получаем данные
$data = rez();

if (!$data || empty($data)) {
    echo "✗ Ошибка: не удалось прочитать CSV файл\n";
    exit(1);
}

echo "✓ Файл прочитан успешно\n";
echo "Всего записей: " . count($data) . "\n\n";

// Получаем все уникальные страховщики
$insurers = array_unique(array_column($data, 'Страховщик'));
sort($insurers);

echo "Страховщики в файле (" . count($insurers) . "):\n";
foreach ($insurers as $insurer) {
    $count = count(array_filter($data, function($row) use ($insurer) {
        return $row['Страховщик'] === $insurer;
    }));
    echo "  - {$insurer}: {$count} записей\n";
}

echo "\n";

// Получаем все уникальные города
$cities = array_unique(array_column($data, 'Город'));
sort($cities);
echo "Города в файле (" . count($cities) . "):\n";
foreach (array_slice($cities, 0, 10) as $city) {
    echo "  - {$city}\n";
}
if (count($cities) > 10) {
    echo "  ... и еще " . (count($cities) - 10) . " городов\n";
}

echo "\n";

// Получаем все уникальные уровни
$levels = array_unique(array_column($data, 'Уровень'));
sort($levels);
echo "Уровни в файле (" . count($levels) . "):\n";
foreach ($levels as $level) {
    echo "  - {$level}\n";
}

echo "\n";

// Тестируем фильтрацию
echo "=== Тест фильтрации ===\n\n";

$test_cases = [
    ['Москва', ['Комфорт'], 5],
    ['Санкт-Петербург', ['Стандарт'], 10],
    ['Барнаул', ['Комфорт'], 15],
];

foreach ($test_cases as $test) {
    list($city, $levels, $count) = $test;
    $filter_result = filterInsuranceData($data, [$city], $levels, $count);
    $results = $filter_result['data'];
    $not_found_cities = $filter_result['not_found_cities'];
    
    echo "Город: {$city}, Уровень: " . implode(', ', $levels) . ", Сотрудников: {$count}\n";
    if (empty($results)) {
        echo "  ✗ Нет результатов\n";
    } else {
        foreach ($results as $result_city => $rows) {
            $insurers_in_result = array_unique(array_column($rows, 'Страховщик'));
            echo "  ✓ {$result_city}: " . count($rows) . " записей, страховщики: " . implode(', ', $insurers_in_result) . "\n";
        }
    }
    if (!empty($not_found_cities)) {
        echo "  ⚠ Города без данных: " . implode(', ', $not_found_cities) . "\n";
    }
    echo "\n";
}

// Проверяем ACF поле
echo "=== Информация о ACF поле ===\n\n";
$csv_field = get_field('csv_file', 2);
if ($csv_field) {
    if (is_array($csv_field)) {
        echo "ACF поле (массив):\n";
        echo "  - path: " . ($csv_field['path'] ?? 'не указан') . "\n";
        echo "  - url: " . ($csv_field['url'] ?? 'не указан') . "\n";
        echo "  - ID: " . ($csv_field['ID'] ?? 'не указан') . "\n";
    } else {
        echo "ACF поле: {$csv_field}\n";
    }
} else {
    echo "✗ ACF поле пустое\n";
}

echo "\n";

