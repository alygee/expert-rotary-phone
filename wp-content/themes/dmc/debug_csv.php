<?php
/**
 * Диагностика проблемы с CSV файлом через get_field
 * 
 * Использование:
 * http://your-site.com/wp-content/themes/dmc/debug_csv.php
 * 
 * ВАЖНО: Удалите этот файл после диагностики!
 */

// Подключаем WordPress
require_once('../../../wp-load.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Диагностика CSV файла</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        code { background: #f0f0f0; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Диагностика CSV файла через get_field</h1>

    <div class="section">
        <h2>1. Проверка ACF (Advanced Custom Fields)</h2>
        <?php
        $acf_active = function_exists('get_field');
        echo '<p><strong>ACF установлен:</strong> ' . ($acf_active ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
        
        if (!$acf_active) {
            echo '<p class="error">❌ Плагин ACF не установлен или не активирован!</p>';
            echo '<p>Установите плагин Advanced Custom Fields или используйте альтернативный способ получения файла.</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>2. Проверка поста с ID=2</h2>
        <?php
        $post_id = 2;
        $post = get_post($post_id);
        
        if ($post) {
            echo '<p class="success">✅ Пост с ID=' . $post_id . ' существует</p>';
            echo '<p><strong>Название:</strong> ' . esc_html($post->post_title) . '</p>';
            echo '<p><strong>Тип:</strong> ' . esc_html($post->post_type) . '</p>';
            echo '<p><strong>Статус:</strong> ' . esc_html($post->post_status) . '</p>';
        } else {
            echo '<p class="error">❌ Пост с ID=' . $post_id . ' не найден!</p>';
            echo '<p>Проверьте, что пост существует в базе данных.</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Проверка поля csv_file</h2>
        <?php
        if ($acf_active) {
            $csv_value = get_field('csv_file', $post_id);
            
            echo '<p><strong>Значение get_field(\'csv_file\', ' . $post_id . '):</strong></p>';
            
            if (empty($csv_value)) {
                echo '<p class="error">❌ Поле пустое или не найдено!</p>';
                echo '<p>Возможные причины:</p>';
                echo '<ul>';
                echo '<li>Поле "csv_file" не настроено в ACF для этого поста</li>';
                echo '<li>Поле не заполнено в админ-панели</li>';
                echo '<li>Неправильное имя поля</li>';
                echo '</ul>';
            } else {
                echo '<pre>' . print_r($csv_value, true) . '</pre>';
                
                // Определяем тип значения
                $value_type = gettype($csv_value);
                echo '<p><strong>Тип значения:</strong> ' . $value_type . '</p>';
                
                // Если это массив (ACF file field)
                if (is_array($csv_value)) {
                    echo '<p class="warning">⚠️ ACF вернул массив (обычно для file field)</p>';
                    
                    // Проверяем разные варианты структуры
                    if (isset($csv_value['url'])) {
                        $file_url = $csv_value['url'];
                        $file_path = $csv_value['path'] ?? '';
                        echo '<p><strong>URL файла:</strong> <code>' . esc_html($file_url) . '</code></p>';
                        echo '<p><strong>Путь к файлу:</strong> <code>' . esc_html($file_path) . '</code></p>';
                        
                        // Преобразуем URL в путь
                        if (empty($file_path) && !empty($file_url)) {
                            $upload_dir = wp_upload_dir();
                            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
                            echo '<p><strong>Вычисленный путь:</strong> <code>' . esc_html($file_path) . '</code></p>';
                        }
                        
                        if (!empty($file_path)) {
                            $file_exists = file_exists($file_path);
                            echo '<p><strong>Файл существует:</strong> ' . ($file_exists ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
                            
                            if ($file_exists) {
                                echo '<p><strong>Размер:</strong> ' . size_format(filesize($file_path)) . '</p>';
                                echo '<p><strong>Права доступа:</strong> ' . substr(sprintf('%o', fileperms($file_path)), -4) . '</p>';
                                echo '<p><strong>Доступен для чтения:</strong> ' . (is_readable($file_path) ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
                            }
                        }
                    } elseif (isset($csv_value['ID'])) {
                        echo '<p><strong>ID вложения:</strong> ' . $csv_value['ID'] . '</p>';
                        $file_path = get_attached_file($csv_value['ID']);
                        echo '<p><strong>Путь через get_attached_file:</strong> <code>' . esc_html($file_path) . '</code></p>';
                        
                        if ($file_path && file_exists($file_path)) {
                            echo '<p class="success">✅ Файл найден через get_attached_file()</p>';
                        }
                    }
                } 
                // Если это строка (URL или путь)
                elseif (is_string($csv_value)) {
                    echo '<p><strong>Значение (строка):</strong> <code>' . esc_html($csv_value) . '</code></p>';
                    
                    // Проверяем, это URL или путь
                    if (filter_var($csv_value, FILTER_VALIDATE_URL) || strpos($csv_value, 'http') === 0) {
                        echo '<p class="warning">⚠️ Это URL, нужно преобразовать в путь к файлу</p>';
                        
                        // Преобразуем URL в путь
                        $upload_dir = wp_upload_dir();
                        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_value);
                        // Также пробуем заменить домен
                        $file_path = str_replace(home_url('/wp-content/uploads/'), $upload_dir['basedir'] . '/', $file_path);
                        
                        echo '<p><strong>Преобразованный путь:</strong> <code>' . esc_html($file_path) . '</code></p>';
                    } else {
                        $file_path = $csv_value;
                    }
                    
                    // Проверяем существование файла
                    if (!empty($file_path)) {
                        $file_exists = file_exists($file_path);
                        echo '<p><strong>Файл существует:</strong> ' . ($file_exists ? '<span class="success">✅ Да</span>' : '<span class="error">❌ Нет</span>') . '</p>';
                        
                        if ($file_exists) {
                            echo '<p><strong>Размер:</strong> ' . size_format(filesize($file_path)) . '</p>';
                            echo '<p><strong>Доступен для чтения:</strong> ' . (is_readable($file_path) . '</p>';
                        } else {
                            // Пробуем найти файл
                            echo '<p class="warning">⚠️ Пробуем найти файл...</p>';
                            $possible_paths = [
                                $file_path,
                                ABSPATH . $file_path,
                                get_template_directory() . '/' . basename($file_path),
                                $upload_dir['basedir'] . '/' . basename($file_path),
                            ];
                            
                            foreach ($possible_paths as $path) {
                                if (file_exists($path)) {
                                    echo '<p class="success">✅ Файл найден по пути: <code>' . esc_html($path) . '</code></p>';
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // Проверяем все поля поста
            echo '<h3>Все ACF поля поста ID=' . $post_id . ':</h3>';
            $all_fields = get_fields($post_id);
            if ($all_fields) {
                echo '<pre>' . print_r($all_fields, true) . '</pre>';
            } else {
                echo '<p class="warning">⚠️ Нет ACF полей для этого поста</p>';
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Альтернативные способы получения файла</h2>
        <?php
        // Проверяем существующие CSV файлы
        $csv_files = [
            get_template_directory() . '/list.csv',
            ABSPATH . 'wp-content/themes/dmc/list.csv',
        ];
        
        $upload_dir = wp_upload_dir();
        $upload_csv = glob($upload_dir['basedir'] . '/*/*.csv');
        if ($upload_csv) {
            $csv_files = array_merge($csv_files, $upload_csv);
        }
        
        echo '<p><strong>Найденные CSV файлы:</strong></p>';
        echo '<ul>';
        foreach ($csv_files as $file) {
            if (file_exists($file)) {
                echo '<li class="success">✅ <code>' . esc_html($file) . '</code> (' . size_format(filesize($file)) . ')</li>';
            }
        }
        echo '</ul>';
        ?>
    </div>

    <div class="section">
        <h2>5. Рекомендации по исправлению</h2>
        <h3>Если ACF возвращает массив:</h3>
        <pre>
// В functions.php замените:
$csv = get_field('csv_file', 2);

// На:
$csv_field = get_field('csv_file', 2);
if (is_array($csv_field)) {
    $csv = $csv_field['path'] ?? $csv_field['url'] ?? '';
    // Если это URL, преобразуем в путь
    if (filter_var($csv, FILTER_VALIDATE_URL)) {
        $upload_dir = wp_upload_dir();
        $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv);
    }
} else {
    $csv = $csv_field;
}
        </pre>

        <h3>Если ACF возвращает URL:</h3>
        <pre>
// Преобразуем URL в путь:
$csv_url = get_field('csv_file', 2);
if (!empty($csv_url)) {
    $upload_dir = wp_upload_dir();
    $csv = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $csv_url);
}
        </pre>

        <h3>Если поле не настроено в ACF:</h3>
        <pre>
// Используйте прямой путь:
$csv = get_template_directory() . '/list.csv';
// или
$csv = ABSPATH . 'wp-content/uploads/2025/11/calc3.csv';
        </pre>
    </div>
</body>
</html>

