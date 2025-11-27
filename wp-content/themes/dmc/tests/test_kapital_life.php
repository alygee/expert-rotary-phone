<?php
/**
 * Тест для проверки работы с страховщиком "ООО «Капитал Лайф Страхование Жизни»"
 * 
 * Использование:
 * php test_kapital_life.php
 */

// Подключаем WordPress
if (file_exists(__DIR__ . '/../../../wp-load.php')) {
    require_once(__DIR__ . '/../../../wp-load.php');
} else {
    // Альтернативный путь
    require_once('/var/www/kubiki.ai/wp-load.php');
}
require_once(__DIR__ . '/../inc/csv-functions.php');
require_once(__DIR__ . '/../inc/filter-functions.php');

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         ТЕСТ: ООО «Капитал Лайф Страхование Жизни»                          ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Получаем данные из CSV
$data = rez();

if (!$data || empty($data)) {
    die("✗ Ошибка: не удалось прочитать CSV файл\n");
}

echo "✓ Файл прочитан успешно\n";
echo "Всего записей: " . count($data) . "\n\n";

// Ищем все записи с этим страховщиком
$target_insurer = 'ООО «Капитал Лайф Страхование Жизни»';
$kapital_life_records = array_filter($data, function($row) use ($target_insurer) {
    return trim($row['Страховщик'] ?? '') === $target_insurer;
});

echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "АНАЛИЗ ЗАПИСЕЙ ДЛЯ: {$target_insurer}\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

if (empty($kapital_life_records)) {
    echo "✗ Записи с этим страховщиком не найдены!\n";
    echo "\nПроверьте:\n";
    echo "1. Правильность написания названия страховщика в CSV\n";
    echo "2. Что файл был сохранен корректно\n";
    echo "3. Что ACF поле указывает на правильный файл\n\n";
    
    // Показываем все уникальные страховщики для сравнения
    $all_insurers = array_unique(array_column($data, 'Страховщик'));
    echo "Доступные страховщики в файле:\n";
    foreach ($all_insurers as $insurer) {
        echo "  - " . $insurer . "\n";
    }
    exit(1);
}

$kapital_life_records = array_values($kapital_life_records);
echo "✓ Найдено записей: " . count($kapital_life_records) . "\n\n";

// Анализируем данные
$cities = array_unique(array_column($kapital_life_records, 'Город'));
$levels = array_unique(array_column($kapital_life_records, 'Уровень'));
$employee_ranges = array_unique(array_column($kapital_life_records, 'Кол-во_сотрудников'));

echo "Города (" . count($cities) . "):\n";
sort($cities);
foreach (array_slice($cities, 0, 20) as $city) {
    $count = count(array_filter($kapital_life_records, function($r) use ($city) {
        return $r['Город'] === $city;
    }));
    echo "  - {$city}: {$count} записей\n";
}
if (count($cities) > 20) {
    echo "  ... и еще " . (count($cities) - 20) . " городов\n";
}

echo "\nУровни (" . count($levels) . "):\n";
sort($levels);
foreach ($levels as $level) {
    $count = count(array_filter($kapital_life_records, function($r) use ($level) {
        return $r['Уровень'] === $level;
    }));
    echo "  - {$level}: {$count} записей\n";
}

echo "\nДиапазоны сотрудников (" . count($employee_ranges) . "):\n";
sort($employee_ranges);
foreach ($employee_ranges as $range) {
    $count = count(array_filter($kapital_life_records, function($r) use ($range) {
        return $r['Кол-во_сотрудников'] === $range;
    }));
    echo "  - {$range}: {$count} записей\n";
}

echo "\n";

// Тестируем фильтрацию
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "ТЕСТИРОВАНИЕ ФИЛЬТРАЦИИ\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

// Берем первый город из списка для теста
$test_city = $cities[0];
$test_level = $levels[0];
$test_employee_count = 5; // Тестируем с 5 сотрудниками

echo "Тест 1: Фильтрация по городу '{$test_city}'\n";
$result1 = filterData2($data, [$test_city], [], null);
if (isset($result1[$test_city])) {
    $insurers_in_result = array_unique(array_column($result1[$test_city], 'Страховщик'));
    $has_kapital = in_array($target_insurer, $insurers_in_result);
    echo "  Записей для {$test_city}: " . count($result1[$test_city]) . "\n";
    echo "  Страховщики: " . implode(', ', $insurers_in_result) . "\n";
    echo "  " . ($has_kapital ? "✓" : "✗") . " ООО «Капитал Лайф Страхование Жизни» " . ($has_kapital ? "найден" : "НЕ найден") . "\n";
} else {
    echo "  ✗ Нет результатов для города {$test_city}\n";
}
echo "\n";

echo "Тест 2: Фильтрация по городу '{$test_city}' и уровню '{$test_level}'\n";
$result2 = filterData2($data, [$test_city], [$test_level], null);
if (isset($result2[$test_city])) {
    $insurers_in_result = array_unique(array_column($result2[$test_city], 'Страховщик'));
    $has_kapital = in_array($target_insurer, $insurers_in_result);
    echo "  Записей для {$test_city}, {$test_level}: " . count($result2[$test_city]) . "\n";
    echo "  Страховщики: " . implode(', ', $insurers_in_result) . "\n";
    echo "  " . ($has_kapital ? "✓" : "✗") . " ООО «Капитал Лайф Страхование Жизни» " . ($has_kapital ? "найден" : "НЕ найден") . "\n";
} else {
    echo "  ✗ Нет результатов для города {$test_city} и уровня {$test_level}\n";
}
echo "\n";

// Находим подходящий диапазон сотрудников
$suitable_range = null;
foreach ($employee_ranges as $range) {
    if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
        $min = (int)$m[1];
        $max = (int)$m[2];
        if ($test_employee_count >= $min && $test_employee_count <= $max) {
            $suitable_range = $range;
            break;
        }
    }
}

if ($suitable_range) {
    echo "Тест 3: Фильтрация по городу '{$test_city}', уровню '{$test_level}', {$test_employee_count} сотрудников (диапазон: {$suitable_range})\n";
    $result3 = filterData2($data, [$test_city], [$test_level], $test_employee_count);
    if (isset($result3[$test_city])) {
        $insurers_in_result = array_unique(array_column($result3[$test_city], 'Страховщик'));
        $has_kapital = in_array($target_insurer, $insurers_in_result);
        echo "  Записей: " . count($result3[$test_city]) . "\n";
        echo "  Страховщики: " . implode(', ', $insurers_in_result) . "\n";
        echo "  " . ($has_kapital ? "✓" : "✗") . " ООО «Капитал Лайф Страхование Жизни» " . ($has_kapital ? "найден" : "НЕ найден") . "\n";
        
        if ($has_kapital) {
            // Показываем детали записи
            $kapital_record = array_filter($result3[$test_city], function($r) use ($target_insurer) {
                return $r['Страховщик'] === $target_insurer;
            });
            if (!empty($kapital_record)) {
                $record = array_values($kapital_record)[0];
                echo "\n  Детали записи:\n";
                echo "    Город: " . $record['Город'] . "\n";
                echo "    Уровень: " . $record['Уровень'] . "\n";
                echo "    Сотрудников: " . $record['Кол-во_сотрудников'] . "\n";
                echo "    Поликлиника: " . $record['Поликлиника'] . "\n";
                echo "    Стоматология: " . $record['Стоматология'] . "\n";
            }
        }
    } else {
        echo "  ✗ Нет результатов\n";
    }
} else {
    echo "Тест 3: Пропущен (нет подходящего диапазона сотрудников для {$test_employee_count})\n";
}
echo "\n";

// Тест функции get_insurer_logo
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "ТЕСТ: ФУНКЦИЯ get_insurer_logo()\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

echo "Проверка вывода логотипа для: {$target_insurer}\n";
ob_start();
get_insurer_logo($target_insurer);
$logo_output = ob_get_clean();

if (empty($logo_output)) {
    echo "  ✗ Логотип НЕ выводится (страховщик не найден в списке логотипов)\n";
    echo "\n  Доступные страховщики в функции get_insurer_logo():\n";
    $array_logo = ['Зетта', 'Ингос', 'РГС', 'СБЕР', 'пари', 'ресо', 'Капитал life', 'Ренессанс', 'Согласие', 'Т-страхование', 'АльфаСтрахование', 'Allianz', 'СОГАЗ'];
    foreach ($array_logo as $logo) {
        echo "    - {$logo}\n";
    }
    echo "\n  ⚠️  ВАЖНО: Название в CSV: '{$target_insurer}'\n";
    echo "  ⚠️  В функции используется: 'Капитал life'\n";
    echo "  ⚠️  Названия не совпадают! Нужно добавить '{$target_insurer}' в список или изменить название в CSV.\n";
} else {
    echo "  ✓ Логотип выводится:\n";
    echo "  " . $logo_output . "\n";
}

echo "\n";

// Итоговый отчет
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "ИТОГОВЫЙ ОТЧЕТ\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$summary = [
    'Записей в CSV' => count($kapital_life_records),
    'Городов' => count($cities),
    'Уровней' => count($levels),
    'Диапазонов сотрудников' => count($employee_ranges),
    'Логотип выводится' => !empty($logo_output) ? 'Да' : 'Нет'
];

foreach ($summary as $key => $value) {
    echo "  {$key}: {$value}\n";
}

echo "\n";

if (count($kapital_life_records) > 0 && empty($logo_output)) {
    echo "⚠️  РЕКОМЕНДАЦИЯ: Добавьте '{$target_insurer}' в функцию get_insurer_logo()\n";
    echo "   или измените название в CSV на одно из существующих в списке.\n\n";
}

echo "✓ Тестирование завершено\n";

