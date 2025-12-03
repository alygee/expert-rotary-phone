<?php
/**
 * Функции для фильтрации данных
 */

/**
 * Проверяет, соответствует ли количество сотрудников в строке заданному фильтру
 * 
 * @param array $row Строка данных
 * @param int|null $employeesCount Количество сотрудников для проверки
 * @return bool true если соответствует, false если нет
 */
function matchesEmployeeCountFilter(array $row, ?int $employeesCount): bool {
    if ($employeesCount === null) {
        return true;
    }
    
    $count = trim($row['Кол-во_сотрудников'] ?? '');
    if (!preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
        return false;
    }
    
    $min = (int)$m[1];
    $max = (int)$m[2];
    return $employeesCount >= $min && $employeesCount <= $max;
}

/**
 * Проверяет, соответствует ли уровень в строке заданному фильтру
 * 
 * @param array $row Строка данных
 * @param array $levels Массив уровней для проверки
 * @return bool true если соответствует, false если нет
 */
function matchesLevelFilter(array $row, array $levels): bool {
    if (empty($levels)) {
        return true;
    }
    
    $level = trim($row['Уровень'] ?? '');
    return in_array($level, $levels, true);
}

/**
 * Проверяет, соответствует ли город в строке заданному фильтру
 * 
 * @param array $row Строка данных
 * @param array $cities Массив городов для проверки
 * @return bool true если соответствует, false если нет
 */
function matchesCityFilter(array $row, array $cities): bool {
    if (empty($cities)) {
        return true;
    }
    
    $city = trim($row['Город'] ?? '');
    return in_array($city, $cities, true);
}

/**
 * Проверяет, соответствует ли строка данных всем заданным фильтрам
 * 
 * @param array $row Строка данных
 * @param array $cities Массив городов для фильтрации
 * @param array $levels Массив уровней для фильтрации
 * @param int|null $employeesCount Количество сотрудников для фильтрации
 * @return bool true если соответствует всем фильтрам
 */
function matchesFilters(array $row, array $cities, array $levels, ?int $employeesCount): bool {
    return matchesLevelFilter($row, $levels)
        && matchesEmployeeCountFilter($row, $employeesCount)
        && matchesCityFilter($row, $cities);
}

/**
 * Фильтрует массив данных по заданным критериям и группирует по городам
 * 
 * @param array $data Исходные данные
 * @param array $cities Массив городов для фильтрации
 * @param array $levels Массив уровней для фильтрации
 * @param int|null $employeesCount Количество сотрудников для фильтрации
 * @return array Массив данных, сгруппированный по городам
 */
function filterDataByCriteria(array $data, array $cities, array $levels, ?int $employeesCount): array {
    $grouped = [];
    
    foreach ($data as $row) {
        if (!matchesFilters($row, $cities, $levels, $employeesCount)) {
            continue;
        }
        
        $city = trim($row['Город'] ?? '');
        $grouped[$city][] = $row;
    }
    
    return $grouped;
}

/**
 * Получает данные fallback (записи "Другой город") когда не найдено данных по запрошенным городам
 * 
 * @param array $data Исходные данные
 * @param array $levels Массив уровней для фильтрации
 * @param int|null $employeesCount Количество сотрудников для фильтрации
 * @return array Массив записей для fallback
 */
function getFallbackData(array $data, array $levels = [], ?int $employeesCount = null): array {
    $fallback_rows = [];
    
    foreach ($data as $row) {
        $city = trim($row['Город'] ?? '');
        
        // Берём только записи с городом "Другой город"
        if ($city !== 'Другой город') continue;
        
        // Проверяем остальные фильтры
        if (!matchesLevelFilter($row, $levels) || !matchesEmployeeCountFilter($row, $employeesCount)) {
            continue;
        }

        $fallback_rows[] = $row;
    }
    
    return $fallback_rows;
}

function filterInsuranceData(array $data, $cities = [], $levels = [], ?int $employeesCount = null): array {
    // Нормализация параметров
    if (!is_array($cities)) $cities = [$cities];
    if (!is_array($levels)) $levels = [$levels];

    $cities = array_values(array_filter(array_map('trim', $cities), fn($v) => $v !== ''));
    $levels = array_values(array_filter(array_map('trim', $levels), fn($v) => $v !== ''));

    // Фильтруем данные по критериям
    $grouped = filterDataByCriteria($data, $cities, $levels, $employeesCount);

    // Если ничего не найдено по городам → берём "Цены по соседним городам"
    if (empty($grouped) && !empty($cities)) {
        $fallback_rows = getFallbackData($data, $levels, $employeesCount);
        if (!empty($fallback_rows)) {
            $grouped['fallback'] = $fallback_rows;
        }
    }

    // 🔹 Сортируем результат в порядке переданных городов
    $sorted = [];
    if (!empty($cities)) {
        foreach ($cities as $city) {
            if (isset($grouped[$city])) {
                $sorted[$city] = $grouped[$city];
            }
        }
    }

    // 🔹 Добавляем остальные города, если есть (например, "Другой город")
    foreach ($grouped as $city => $rows) {
        if (!isset($sorted[$city])) {
            $sorted[$city] = $rows;
        }
    }

    // 🔹 Определяем города, для которых не было найдено данных
    $not_found_cities = [];
    if (!empty($cities)) {
        foreach ($cities as $city) {
            // Проверяем, что город не найден и это не fallback
            if (!isset($sorted[$city])) {
                $not_found_cities[] = $city;
            }
        }
    }

    // Возвращаем структуру с данными и информацией о не найденных городах
    return [
        'data' => $sorted,
        'not_found_cities' => $not_found_cities,
    ];
}

function get_insurer_logo(string $insurer): void {
  $array_logo = ['Зетта', 'Ингос', 'РГС', 'СБЕР', 'пари', 'ресо', 'Капитал Лайф', 'Ренессанс', 'Согласие', 'Т-страхование', 'АльфаСтрахование', 'Allianz', 'СОГАЗ'];
  $insurer_lower = mb_strtolower(trim($insurer), 'UTF-8');
  $array_logo_lower = array_map(fn($v) => mb_strtolower($v, 'UTF-8'), $array_logo);

  $index = array_search($insurer_lower, $array_logo_lower, true);

  if ($index !== false) {
      $img_index = $index + 1;
      echo '<img src="' . get_bloginfo('template_url') . '/assets/img/logotypes/logotypes' . $img_index . '.svg" alt="">';
  }
}

