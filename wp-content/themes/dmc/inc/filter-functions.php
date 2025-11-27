<?php
/**
 * Функции для фильтрации данных
 */

function filterData(array $data, $cities = [], $levels = [], int $employeesCount = null): array {
    // Нормализация параметров (если передана строка вместо массива)
    if (!is_array($cities)) {
        $cities = [$cities];
    }
    if (!is_array($levels)) {
        $levels = [$levels];
    }

    // Убираем пустые элементы и пробелы
    $cities = array_values(array_filter(array_map('trim', $cities), fn($v) => $v !== ''));
    $levels = array_values(array_filter(array_map('trim', $levels), fn($v) => $v !== ''));

    $result = [];

    // 1️⃣ Сначала фильтруем по заданным городам
    foreach ($data as $row) {
        $city = trim($row['Город'] ?? '');
        $level = trim($row['Уровень'] ?? '');
        $count = trim($row['Кол-во_сотрудников'] ?? '');

        // Фильтр по городу
        if (!empty($cities) && !in_array($city, $cities, true)) {
            continue;
        }

        // Фильтр по уровню
        if (!empty($levels) && !in_array($level, $levels, true)) {
            continue;
        }

        // Фильтр по количеству сотрудников (формат "min-max")
        if ($employeesCount !== null) {
            if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                $min = (int)$m[1];
                $max = (int)$m[2];
                if ($employeesCount < $min || $employeesCount > $max) {
                    continue;
                }
            } else {
                continue;
            }
        }

        $result[] = $row;
    }


    if (empty($result) && !empty($cities)) {
        foreach ($data as $row) {
            $city = trim($row['Город'] ?? '');
            $level = trim($row['Уровень'] ?? '');
            $count = trim($row['Кол-во_сотрудников'] ?? '');

            if ($city !== 'Другой город') {
                continue;
            }

            if (!empty($levels) && !in_array($level, $levels, true)) {
                continue;
            }

            if ($employeesCount !== null) {
                if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                    $min = (int)$m[1];
                    $max = (int)$m[2];
                    if ($employeesCount < $min || $employeesCount > $max) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            $result[] = $row;
        }
    }

    return $result;
}


function filterData2(array $data, $cities = [], $levels = [], int $employeesCount = null): array {
    // Нормализация параметров
    if (!is_array($cities)) $cities = [$cities];
    if (!is_array($levels)) $levels = [$levels];

    $cities = array_values(array_filter(array_map('trim', $cities), fn($v) => $v !== ''));
    $levels = array_values(array_filter(array_map('trim', $levels), fn($v) => $v !== ''));

    // Сюда будем складывать результат по городам
    $grouped = [];

    // Сначала фильтруем данные
    foreach ($data as $row) {
        $city = trim($row['Город'] ?? '');
        $level = trim($row['Уровень'] ?? '');
        $count = trim($row['Кол-во_сотрудников'] ?? '');

        // --- фильтр по уровню ---
        if (!empty($levels) && !in_array($level, $levels, true)) {
            continue;
        }

        // --- фильтр по количеству сотрудников ---
        if ($employeesCount !== null) {
            if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                $min = (int)$m[1];
                $max = (int)$m[2];
                if ($employeesCount < $min || $employeesCount > $max) continue;
            } else continue;
        }

        // --- фильтр по городам ---
        if (!empty($cities)) {
            if (!in_array($city, $cities, true)) continue;
        }

        // Добавляем запись в массив по городу
        $grouped[$city][] = $row;
    }

    // Если ничего не найдено по городам → берём "Другой город"
    if (empty($grouped) && !empty($cities)) {
        foreach ($data as $row) {
            $city = trim($row['Город'] ?? '');
            $level = trim($row['Уровень'] ?? '');
            $count = trim($row['Кол-во_сотрудников'] ?? '');

            if ($city !== 'Другой город') continue;
            if (!empty($levels) && !in_array($level, $levels, true)) continue;

            if ($employeesCount !== null) {
                if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                    $min = (int)$m[1];
                    $max = (int)$m[2];
                    if ($employeesCount < $min || $employeesCount > $max) continue;
                } else continue;
            }

            $grouped['fallback'][] = $row;
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

    return $sorted;
}

function get_insurer_logo(string $insurer): void {
  $array_logo = ['Зетта', 'Ингос', 'РГС', 'СБЕР', 'пари', 'ресо', 'Капитал life', 'Ренессанс', 'Согласие', 'Т-страхование', 'АльфаСтрахование', 'Allianz', 'СОГАЗ'
  ];
  $insurer_lower = mb_strtolower(trim($insurer), 'UTF-8');
  $array_logo_lower = array_map(fn($v) => mb_strtolower($v, 'UTF-8'), $array_logo);

  $index = array_search($insurer_lower, $array_logo_lower, true);

  if ($index !== false) {
      $img_index = $index + 1;
      echo '<img src="' . get_bloginfo('template_url') . '/img/logotypes' . $img_index . '.svg" alt="">';
  }
}

