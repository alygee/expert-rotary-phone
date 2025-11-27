<?php
/**
 * Диагностический скрипт для проверки AJAX на тестовом сервере
 * 
 * Использование:
 * http://your-site.com/wp-content/themes/dmc/debug_ajax.php
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
    <title>Диагностика AJAX</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .test-btn { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin: 5px; }
        .test-btn:hover { background: #005a87; }
        #ajax-result { margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Диагностика AJAX запросов</h1>

    <div class="section">
        <h2>1. Проверка функций WordPress</h2>
        <?php
        $checks = [
            'get_field' => function_exists('get_field'),
            'filterInsuranceData' => function_exists('filterInsuranceData'),
            'rez' => function_exists('rez'),
            'filter_callback' => function_exists('filter_callback'),
        ];
        
        foreach ($checks as $func => $exists) {
            echo '<p>' . ($exists ? '✅' : '❌') . ' <strong>' . $func . '()</strong>: ' . ($exists ? 'существует' : 'не найдена') . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>2. Проверка CSV файла</h2>
        <?php
        $csv = get_field('csv_file', 2);
            wp_die($csv);
        if ($csv) {
            echo '<p class="success">✅ CSV путь: <code>' . esc_html($csv) . '</code></p>';
            
            if (file_exists($csv)) {
                echo '<p class="success">✅ Файл существует</p>';
                echo '<p>Размер файла: ' . filesize($csv) . ' байт</p>';
                echo '<p>Права доступа: ' . substr(sprintf('%o', fileperms($csv)), -4) . '</p>';
                
                // Проверяем, можем ли прочитать файл
                if (is_readable($csv)) {
                    echo '<p class="success">✅ Файл доступен для чтения</p>';
                } else {
                    echo '<p class="error">❌ Файл недоступен для чтения</p>';
                }
            } else {
                echo '<p class="error">❌ Файл не существует по указанному пути</p>';
            }
        } else {
            echo '<p class="error">❌ CSV файл не настроен в ACF (get_field(\'csv_file\', 2) вернул пустое значение)</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Проверка функции rez()</h2>
        <?php
        $data = rez();
        if ($data === false) {
            echo '<p class="error">❌ rez() вернул false</p>';
        } elseif (empty($data)) {
            echo '<p class="warning">⚠️ rez() вернул пустой массив</p>';
        } else {
            echo '<p class="success">✅ rez() вернул ' . count($data) . ' записей</p>';
            echo '<p>Первая запись (пример):</p>';
            echo '<pre>' . print_r(array_slice($data, 0, 1), true) . '</pre>';
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Проверка AJAX хуков</h2>
        <?php
        global $wp_filter;
        $ajax_hooks = [
            'wp_ajax_action' => isset($wp_filter['wp_ajax_action']),
            'wp_ajax_nopriv_action' => isset($wp_filter['wp_ajax_nopriv_action']),
        ];
        
        foreach ($ajax_hooks as $hook => $exists) {
            echo '<p>' . ($exists ? '✅' : '❌') . ' <strong>' . $hook . '</strong>: ' . ($exists ? 'зарегистрирован' : 'не зарегистрирован') . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>5. Проверка URL для AJAX</h2>
        <?php
        $ajax_url = admin_url('admin-ajax.php');
        echo '<p>AJAX URL: <code>' . esc_html($ajax_url) . '</code></p>';
        echo '<p>AJAX URL (полный): <code>' . esc_html(home_url('/wp-admin/admin-ajax.php')) . '</code></p>';
        ?>
    </div>

    <div class="section">
        <h2>6. Тест функции filterInsuranceData</h2>
        <?php
        if (function_exists('filterInsuranceData') && !empty($data)) {
            $test_result = filterInsuranceData($data, ['Москва'], ['Стандарт'], 5);
            if (!empty($test_result)) {
                echo '<p class="success">✅ filterInsuranceData работает корректно</p>';
                echo '<p>Найдено городов: ' . count($test_result) . '</p>';
            } else {
                echo '<p class="warning">⚠️ filterInsuranceData вернул пустой результат (возможно, нет данных по критериям)</p>';
            }
        } else {
            echo '<p class="error">❌ Невозможно протестировать filterInsuranceData</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>7. Тест AJAX запроса</h2>
        <p>Нажмите кнопку для тестового AJAX запроса:</p>
        <button class="test-btn" onclick="testAjax()">Тест AJAX (один город)</button>
        <button class="test-btn" onclick="testAjaxMultiple()">Тест AJAX (массив городов)</button>
        <div id="ajax-result"></div>
    </div>

    <div class="section">
        <h2>8. Информация о сервере</h2>
        <pre>
PHP версия: <?php echo PHP_VERSION; ?>
WordPress версия: <?php echo get_bloginfo('version'); ?>
WP_DEBUG: <?php echo defined('WP_DEBUG') && WP_DEBUG ? 'включен' : 'выключен'; ?>
display_errors: <?php echo ini_get('display_errors') ? 'включен' : 'выключен'; ?>
error_reporting: <?php echo error_reporting(); ?>
        </pre>
    </div>

    <script>
    function testAjax() {
        const resultDiv = document.getElementById('ajax-result');
        resultDiv.innerHTML = '<p>Отправка запроса (один город)...</p>';
        
        const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        
        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'action',
                'count': '5',
                'level': 'Стандарт',
                'region': 'Москва'
            })
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<h3>Результат (один город):</h3><pre>' + data.substring(0, 1000) + (data.length > 1000 ? '... (обрезано)' : '') + '</pre>';
            resultDiv.innerHTML += '<p>Длина ответа: ' + data.length + ' символов</p>';
            
            if (data.length === 0) {
                resultDiv.innerHTML += '<p class="error">❌ Получен пустой ответ!</p>';
            } else {
                resultDiv.innerHTML += '<p class="success">✅ Ответ получен</p>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<p class="error">❌ Ошибка: ' + error.message + '</p>';
        });
    }
    
    function testAjaxMultiple() {
        const resultDiv = document.getElementById('ajax-result');
        resultDiv.innerHTML = '<p>Отправка запроса (массив городов)...</p>';
        
        const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        
        // Создаем FormData для отправки массива
        const formData = new FormData();
        formData.append('action', 'action');
        formData.append('count', '5');
        formData.append('level', 'Стандарт');
        // Добавляем несколько городов как массив
        formData.append('region[]', 'Москва');
        formData.append('region[]', 'Барнаул');
        formData.append('region[]', 'Архангельск');
        
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = '<h3>Результат (массив городов):</h3><pre>' + data.substring(0, 1000) + (data.length > 1000 ? '... (обрезано)' : '') + '</pre>';
            resultDiv.innerHTML += '<p>Длина ответа: ' + data.length + ' символов</p>';
            
            if (data.length === 0) {
                resultDiv.innerHTML += '<p class="error">❌ Получен пустой ответ!</p>';
            } else {
                resultDiv.innerHTML += '<p class="success">✅ Ответ получен</p>';
                // Проверяем, есть ли в ответе несколько городов
                const hasMoscow = data.includes('Москва');
                const hasBarnaul = data.includes('Барнаул');
                const hasArkhangelsk = data.includes('Архангельск');
                resultDiv.innerHTML += '<p>Города в ответе: ' + 
                    (hasMoscow ? '✅ Москва ' : '❌ Москва ') +
                    (hasBarnaul ? '✅ Барнаул ' : '❌ Барнаул ') +
                    (hasArkhangelsk ? '✅ Архангельск' : '❌ Архангельск') + '</p>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<p class="error">❌ Ошибка: ' + error.message + '</p>';
        });
    }
    </script>
</body>
</html>

