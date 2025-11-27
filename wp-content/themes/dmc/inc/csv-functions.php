<?php
/**
 * Функции для работы с CSV файлами
 */

function сity(){
  $csv_field = get_field('csv_file', 2);
  
  // Обработка разных форматов возвращаемых ACF значений (аналогично функции rez())
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
  return $cities;
}


function rez(){
  //$csv = get_bloginfo('template_url')."/list.csv"; 
  $csv_field = get_field('csv_file', 2);
  
  // Обработка разных форматов возвращаемых ACF значений
  $csv = '';
  
  // Если ACF поле заполнено, используем его
  if (!empty($csv_field)) {
    // Если это массив (ACF file field возвращает массив)
    if (is_array($csv_field)) {
      // Проверяем наличие пути
      if (isset($csv_field['path']) && !empty($csv_field['path'])) {
        $csv = $csv_field['path'];
      }
      // Если есть URL, преобразуем в путь
      elseif (isset($csv_field['url']) && !empty($csv_field['url'])) {
        $upload_dir = wp_upload_dir();
        $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_field['url']);
      }
      // Если есть ID вложения
      elseif (isset($csv_field['ID']) && !empty($csv_field['ID'])) {
        $csv = get_attached_file($csv_field['ID']);
      }
    }
    // Если это строка (URL или путь)
    elseif (is_string($csv_field)) {
      // Проверяем, это URL или путь
      if (filter_var($csv_field, FILTER_VALIDATE_URL) || strpos($csv_field, 'http') === 0) {
        // Преобразуем URL в путь к файлу
        $upload_dir = wp_upload_dir();
        $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_field);
        // Также пробуем заменить домен
        if (!file_exists($csv)) {
          $csv = str_replace(home_url('/wp-content/uploads/'), $upload_dir['basedir'] . '/', $csv_field);
        }
      } else {
        // Это уже путь к файлу
        $csv = $csv_field;
      }
    }
    // Если это число (ID вложения)
    elseif (is_numeric($csv_field)) {
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
      
      // Логирование для отладки
      if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
        error_log('rez() - ACF поле пустое или файл не найден, используем последний загруженный: ' . $csv);
        error_log('rez() - Время модификации: ' . date('Y-m-d H:i:s', filemtime($csv)));
      }
    }
  }
  
  // Проверяем, что файл существует
  if (empty($csv) || !file_exists($csv)) {
    // Логирование для отладки
    if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
      error_log('rez() - CSV файл не найден: ' . $csv);
      error_log('rez() - get_field вернул: ' . print_r($csv_field, true));
    }
    return false;
  }
  
  // Логирование пути к используемому файлу
  if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
    error_log('rez() - Используется CSV файл: ' . $csv);
    error_log('rez() - Время модификации файла: ' . date('Y-m-d H:i:s', filemtime($csv)));
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

  return $rows;
}

