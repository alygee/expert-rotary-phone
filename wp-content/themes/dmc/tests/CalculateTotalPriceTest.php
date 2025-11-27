<?php
/**
 * Unit-тесты для функции calculate_total_price
 * 
 * Требования:
 * - PHPUnit установлен (composer require --dev phpunit/phpunit)
 * 
 * Запуск:
 * vendor/bin/phpunit tests/CalculateTotalPriceTest.php
 */

use PHPUnit\Framework\TestCase;

// Подключаем functions.php с функцией calculate_total_price
require_once __DIR__ . '/../functions.php';

// Проверяем, что функция существует
if (!function_exists('calculate_total_price')) {
    throw new \RuntimeException('Функция calculate_total_price не найдена в functions.php');
}

class CalculateTotalPriceTest extends TestCase
{
    /**
     * Тест: Базовое суммирование без пробелов
     */
    public function testBasicSumWithoutSpaces()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '3000',
            'Госпитализация' => '10000',
            'Вызов_врача_на_дом' => '2000',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 3000 + 10000 + 2000 + 15000; // 35000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Суммирование с пробелами в значениях
     */
    public function testSumWithSpaces()
    {
        $row = [
            'Стоматология' => '5 000',
            'Скорая_помощь' => '3 000',
            'Госпитализация' => '10 000',
            'Вызов_врача_на_дом' => '2 000',
            'Поликлиника' => '15 000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 3000 + 10000 + 2000 + 15000; // 35000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Суммирование с запятыми вместо точек
     */
    public function testSumWithCommas()
    {
        $row = [
            'Стоматология' => '5000,50',
            'Скорая_помощь' => '3000,25',
            'Госпитализация' => '10000,75',
            'Вызов_врача_на_дом' => '2000,10',
            'Поликлиника' => '15000,40'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000.50 + 3000.25 + 10000.75 + 2000.10 + 15000.40; // 35002.0
        
        $this->assertEquals($expected, $result, '', 0.01);
    }

    /**
     * Тест: Суммирование с пробелами и запятыми
     */
    public function testSumWithSpacesAndCommas()
    {
        $row = [
            'Стоматология' => '5 000,50',
            'Скорая_помощь' => '3 000,25',
            'Госпитализация' => '10 000,75',
            'Вызов_врача_на_дом' => '2 000,10',
            'Поликлиника' => '15 000,40'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000.50 + 3000.25 + 10000.75 + 2000.10 + 15000.40; // 35002.0
        
        $this->assertEquals($expected, $result, '', 0.01);
    }

    /**
     * Тест: Смешанные форматы (с пробелами и без)
     */
    public function testMixedFormats()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '3 000',
            'Госпитализация' => '10000,50',
            'Вызов_врача_на_дом' => '2 000,25',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 3000 + 10000.50 + 2000.25 + 15000; // 35000.75
        
        $this->assertEquals($expected, $result, '', 0.01);
    }

    /**
     * Тест: Пустые значения игнорируются
     */
    public function testEmptyValuesIgnored()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '',
            'Госпитализация' => '10000',
            'Вызов_врача_на_дом' => '',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 10000 + 15000; // 30000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Нулевые значения игнорируются
     */
    public function testZeroValuesIgnored()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '0',
            'Госпитализация' => '10000',
            'Вызов_врача_на_дом' => '0',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 10000 + 15000; // 30000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Отрицательные значения игнорируются (так как проверка > 0)
     */
    public function testNegativeValuesIgnored()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '-1000',
            'Госпитализация' => '10000',
            'Вызов_врача_на_дом' => '2000',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 10000 + 2000 + 15000; // 32000 (отрицательное значение игнорируется)
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Нечисловые значения игнорируются
     */
    public function testNonNumericValuesIgnored()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => 'abc',
            'Госпитализация' => '10000',
            'Вызов_врача_на_дом' => '#Н/Д',
            'Поликлиника' => '15000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 10000 + 15000; // 30000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Отсутствующие поля игнорируются
     */
    public function testMissingFieldsIgnored()
    {
        $row = [
            'Стоматология' => '5000',
            'Госпитализация' => '10000',
            'Поликлиника' => '15000'
            // Скорая_помощь и Вызов_врача_на_дом отсутствуют
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 10000 + 15000; // 30000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Большие числа с пробелами
     */
    public function testLargeNumbersWithSpaces()
    {
        $row = [
            'Стоматология' => '50 000',
            'Скорая_помощь' => '30 000',
            'Госпитализация' => '100 000',
            'Вызов_врача_на_дом' => '20 000',
            'Поликлиника' => '150 000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 50000 + 30000 + 100000 + 20000 + 150000; // 350000
        
        $this->assertEquals($expected, $result);
    }

    /**
     * Тест: Все поля пустые
     */
    public function testAllFieldsEmpty()
    {
        $row = [
            'Стоматология' => '',
            'Скорая_помощь' => '',
            'Госпитализация' => '',
            'Вызов_врача_на_дом' => '',
            'Поликлиника' => ''
        ];
        
        $result = calculate_total_price($row);
        
        $this->assertEquals(0, $result);
    }

    /**
     * Тест: Один валидный элемент
     */
    public function testSingleValidValue()
    {
        $row = [
            'Стоматология' => '5000',
            'Скорая_помощь' => '',
            'Госпитализация' => '',
            'Вызов_врача_на_дом' => '',
            'Поликлиника' => ''
        ];
        
        $result = calculate_total_price($row);
        
        $this->assertEquals(5000, $result);
    }

    /**
     * Тест: Значения с множественными пробелами
     */
    public function testMultipleSpaces()
    {
        $row = [
            'Стоматология' => '5   000',
            'Скорая_помощь' => '3  000',
            'Госпитализация' => '10   000',
            'Вызов_врача_на_дом' => '2 000',
            'Поликлиника' => '15 000'
        ];
        
        $result = calculate_total_price($row);
        $expected = 5000 + 3000 + 10000 + 2000 + 15000; // 35000
        
        $this->assertEquals($expected, $result);
    }
}

