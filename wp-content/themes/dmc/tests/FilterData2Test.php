<?php
/**
 * Unit-тесты для функции filterData2
 * 
 * Требования:
 * - PHPUnit установлен (composer require --dev phpunit/phpunit)
 * 
 * Запуск:
 * vendor/bin/phpunit tests/FilterData2Test.php
 */

use PHPUnit\Framework\TestCase;

// Подключаем functions.php с функцией filterData2
require_once __DIR__ . '/../functions.php';

// Проверяем, что функция существует
if (!function_exists('filterData2')) {
    throw new \RuntimeException('Функция filterData2 не найдена в functions.php');
}

class FilterData2Test extends TestCase
{
    private $mockData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Подготавливаем тестовые данные
        $this->mockData = [
            [
                'Город' => 'Москва',
                'Уровень' => 'Стандарт',
                'Кол-во_сотрудников' => '1-10',
                'Страховщик' => 'Сбербанк страхование',
                'Стоматология' => '5000',
                'Скорая_помощь' => '3000',
                'Госпитализация' => '10000',
                'Вызов_врача_на_дом' => '2000',
                'Поликлиника' => '15000'
            ],
            [
                'Город' => 'Москва',
                'Уровень' => 'Комфорт',
                'Кол-во_сотрудников' => '1-10',
                'Страховщик' => 'РГС',
                'Стоматология' => '8000',
                'Скорая_помощь' => '5000',
                'Госпитализация' => '15000',
                'Вызов_врача_на_дом' => '3000',
                'Поликлиника' => '20000'
            ],
            [
                'Город' => 'Санкт-Петербург',
                'Уровень' => 'Стандарт',
                'Кол-во_сотрудников' => '11-50',
                'Страховщик' => 'Ингос',
                'Стоматология' => '6000',
                'Скорая_помощь' => '3500',
                'Госпитализация' => '12000',
                'Вызов_врача_на_дом' => '2500',
                'Поликлиника' => '18000'
            ],
            [
                'Город' => 'Барнаул',
                'Уровень' => 'Комфорт',
                'Кол-во_сотрудников' => '1-10',
                'Страховщик' => 'АльфаСтрахование',
                'Стоматология' => '7000',
                'Скорая_помощь' => '4000',
                'Госпитализация' => '11000',
                'Вызов_врача_на_дом' => '2200',
                'Поликлиника' => '16000'
            ],
            [
                'Город' => 'Другой город',
                'Уровень' => 'Стандарт',
                'Кол-во_сотрудников' => '1-10',
                'Страховщик' => 'СОГАЗ',
                'Стоматология' => '4500',
                'Скорая_помощь' => '2800',
                'Госпитализация' => '9500',
                'Вызов_врача_на_дом' => '1800',
                'Поликлиника' => '14000'
            ],
            [
                'Город' => 'Другой город',
                'Уровень' => 'Комфорт',
                'Кол-во_сотрудников' => '11-50',
                'Страховщик' => 'Ренессанс',
                'Стоматология' => '9000',
                'Скорая_помощь' => '6000',
                'Госпитализация' => '18000',
                'Вызов_врача_на_дом' => '4000',
                'Поликлиника' => '25000'
            ],
        ];
    }

    /**
     * Тест: Фильтрация по одному городу
     */
    public function testFilterBySingleCity()
    {
        $result = filterData2($this->mockData, ['Москва']);
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertCount(2, $result['Москва']);
        $this->assertEquals('Москва', $result['Москва'][0]['Город']);
        $this->assertEquals('Москва', $result['Москва'][1]['Город']);
    }

    /**
     * Тест: Фильтрация по нескольким городам
     */
    public function testFilterByMultipleCities()
    {
        $result = filterData2($this->mockData, ['Москва', 'Барнаул']);
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertArrayHasKey('Барнаул', $result);
        $this->assertCount(2, $result['Москва']);
        $this->assertCount(1, $result['Барнаул']);
    }

    /**
     * Тест: Фильтрация по уровню
     */
    public function testFilterByLevel()
    {
        $result = filterData2($this->mockData, [], ['Комфорт']);
        
        foreach ($result as $city => $rows) {
            foreach ($rows as $row) {
                $this->assertEquals('Комфорт', $row['Уровень']);
            }
        }
    }

    /**
     * Тест: Фильтрация по количеству сотрудников
     */
    public function testFilterByEmployeesCount()
    {
        $result = filterData2($this->mockData, [], [], 5);
        
        foreach ($result as $city => $rows) {
            foreach ($rows as $row) {
                $this->assertEquals('1-10', $row['Кол-во_сотрудников']);
            }
        }
    }

    /**
     * Тест: Комбинированная фильтрация
     */
    public function testCombinedFilter()
    {
        $result = filterData2($this->mockData, ['Москва'], ['Комфорт'], 5);
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertCount(1, $result['Москва']);
        $this->assertEquals('Комфорт', $result['Москва'][0]['Уровень']);
        $this->assertEquals('1-10', $result['Москва'][0]['Кол-во_сотрудников']);
    }

    /**
     * Тест: Fallback на "Другой город" при отсутствии результатов
     */
    public function testFallbackToOtherCity()
    {
        $result = filterData2($this->mockData, ['НесуществующийГород'], ['Стандарт'], 5);
        
        $this->assertArrayHasKey('Другой город', $result);
        $this->assertCount(1, $result['Другой город']);
        $this->assertEquals('Стандарт', $result['Другой город'][0]['Уровень']);
    }

    /**
     * Тест: Город как строка (не массив)
     */
    public function testCityAsString()
    {
        $result = filterData2($this->mockData, 'Москва');
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertCount(2, $result['Москва']);
    }

    /**
     * Тест: Без фильтров (должны вернуться все данные)
     */
    public function testNoFilters()
    {
        $result = filterData2($this->mockData);
        
        $totalRows = 0;
        foreach ($result as $rows) {
            $totalRows += count($rows);
        }
        
        $this->assertEquals(count($this->mockData), $totalRows);
    }

    /**
     * Тест: Сохранение порядка городов
     */
    public function testCityOrderPreservation()
    {
        $result = filterData2($this->mockData, ['Барнаул', 'Москва']);
        
        $cities = array_keys($result);
        $this->assertEquals('Барнаул', $cities[0]);
        $this->assertEquals('Москва', $cities[1]);
    }

    /**
     * Тест: Обрезка пробелов в параметрах
     */
    public function testTrimWhitespace()
    {
        $result = filterData2($this->mockData, [' Москва ', '  Барнаул  ']);
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertArrayHasKey('Барнаул', $result);
    }

    /**
     * Тест: Пустые строки в массивах игнорируются
     */
    public function testEmptyStringsIgnored()
    {
        $result = filterData2($this->mockData, ['Москва', '', '  ']);
        
        $this->assertArrayHasKey('Москва', $result);
        $this->assertCount(2, $result['Москва']);
    }

    /**
     * Тест: Количество сотрудников вне диапазона
     */
    public function testEmployeesCountOutOfRange()
    {
        $result = filterData2($this->mockData, [], [], 100);
        
        $totalRows = 0;
        foreach ($result as $rows) {
            $totalRows += count($rows);
        }
        
        // Должно быть 0 записей, так как 100 не попадает ни в один диапазон
        $this->assertEquals(0, $totalRows);
    }

    /**
     * Тест: Пустой массив данных
     */
    public function testEmptyData()
    {
        $result = filterData2([]);
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Тест: Несколько уровней
     */
    public function testMultipleLevels()
    {
        $result = filterData2($this->mockData, [], ['Стандарт', 'Комфорт']);
        
        $totalRows = 0;
        foreach ($result as $rows) {
            $totalRows += count($rows);
        }
        
        // Должны вернуться все записи, так как все имеют либо Стандарт, либо Комфорт
        $this->assertEquals(count($this->mockData), $totalRows);
    }
}

