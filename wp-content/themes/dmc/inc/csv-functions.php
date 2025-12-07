<?php
/**
 * Функции для работы с CSV файлами
 */

/**
 * Получает путь к CSV файлу (общая функция для rez() и сity())
 * 
 * @return string|false Путь к CSV файлу или false если не найден
 */
function get_csv_file_path() {
  // Кэшируем путь к файлу на 1 час
  $cache_key = 'dmc_csv_file_path';
  $cached_path = get_transient($cache_key);
  
  if ($cached_path !== false && file_exists($cached_path)) {
    return $cached_path;
  }
  
  $csv_field = get_field('csv_file', 2);
  $csv = '';
  
  // Если ACF поле заполнено, используем его
  if (!empty($csv_field)) {
    if (is_array($csv_field)) {
      if (isset($csv_field['path']) && !empty($csv_field['path'])) {
        $csv = $csv_field['path'];
      } elseif (isset($csv_field['url']) && !empty($csv_field['url'])) {
        $upload_dir = wp_upload_dir();
        $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_field['url']);
      } elseif (isset($csv_field['ID']) && !empty($csv_field['ID'])) {
        $csv = get_attached_file($csv_field['ID']);
      }
    } elseif (is_string($csv_field)) {
      if (filter_var($csv_field, FILTER_VALIDATE_URL) || strpos($csv_field, 'http') === 0) {
        $upload_dir = wp_upload_dir();
        $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_field);
        if (!file_exists($csv)) {
          $csv = str_replace(home_url('/wp-content/uploads/'), $upload_dir['basedir'] . '/', $csv_field);
        }
      } else {
        $csv = $csv_field;
      }
    } elseif (is_numeric($csv_field)) {
      $csv = get_attached_file($csv_field);
    }
  }
  
  // Если файл из ACF не найден или ACF поле пустое, ищем последний загруженный list.csv
  if (empty($csv) || !file_exists($csv)) {
    $upload_dir = wp_upload_dir();
    $uploads_base = $upload_dir['basedir'];
    
    // Ищем последний файл list.csv в директории uploads
    $csv_files = glob($uploads_base . '/*/list.csv');
    if (empty($csv_files)) {
      // Ищем в поддиректориях (год/месяц)
      $csv_files = glob($uploads_base . '/*/*/list.csv');
    }
    
    if (!empty($csv_files)) {
      // Сортируем по времени модификации (новейший первым)
      usort($csv_files, function($a, $b) {
        return filemtime($b) - filemtime($a);
      });
      $csv = $csv_files[0];
    }
  }
  
  if (empty($csv) || !file_exists($csv) || !is_readable($csv)) {
    return false;
  }
  
  // Кэшируем путь на 1 час
  set_transient($cache_key, $csv, HOUR_IN_SECONDS);
  
  return $csv;
}

function сity(){
  $csv = get_csv_file_path();
  
  if ($csv === false) {
    return false;
  }
  
  // Кэшируем список городов на основе времени модификации файла
  $file_mtime = filemtime($csv);
  $cache_key = 'dmc_cities_list_' . md5($csv . $file_mtime);
  $cached_cities = get_transient($cache_key);
  
  if ($cached_cities !== false) {
    return $cached_cities;
  }
  
  $rows = [];
  if (($handle = fopen($csv, "r")) !== false) {
      $headers = fgetcsv($handle);
      while (($data = fgetcsv($handle, 0, ",")) !== false) {
          $rows[] = array_combine($headers, $data);
      }
      fclose($handle);
  } else {
    return false;
  }
  
  $cities = array_column($rows, "Город");
  $uniqueCities = array_values(array_unique($cities));

  $cities = array_map('trim', $uniqueCities);
  $cities = array_unique($cities);
  $cities = array_values($cities);
  
  // Кэшируем на 1 час, но инвалидация произойдет при изменении файла (через filemtime в ключе)
  set_transient($cache_key, $cities, HOUR_IN_SECONDS);
  
  return $cities;
}


function rez(){
  $csv = get_csv_file_path();
  
  if ($csv === false) {
    // Логирование для отладки
    if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
      error_log('rez() - CSV файл не найден');
    }
    return false;
  }
  
  // Кэшируем данные CSV на основе времени модификации файла
  $file_mtime = filemtime($csv);
  $cache_key = 'dmc_csv_data_' . md5($csv . $file_mtime);
  $cached_rows = get_transient($cache_key);
  
  if ($cached_rows !== false) {
    return $cached_rows;
  }
  
  // Логирование пути к используемому файлу
  if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
    error_log('rez() - Используется CSV файл: ' . $csv);
    error_log('rez() - Время модификации файла: ' . date('Y-m-d H:i:s', $file_mtime));
  }
  
  // Проверяем, что файл доступен для чтения
  if (!is_readable($csv)) {
    if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
      error_log('rez() - CSV файл недоступен для чтения: ' . $csv);
    }
    return false;
  }
  
  // Читаем CSV файл
  $rows = [];
  if (($handle = fopen($csv, "r")) !== false) {
      $headers = fgetcsv($handle);
      while (($data = fgetcsv($handle, 0, ",")) !== false) {
          $rows[] = array_combine($headers, $data);
      }
      fclose($handle);
  } else {
    if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
      error_log('rez() - Не удалось открыть CSV файл: ' . $csv);
    }
    return false;
  }
  
  // Кэшируем на 1 час, но инвалидация произойдет при изменении файла (через filemtime в ключе)
  set_transient($cache_key, $rows, HOUR_IN_SECONDS);

  return $rows;
}

