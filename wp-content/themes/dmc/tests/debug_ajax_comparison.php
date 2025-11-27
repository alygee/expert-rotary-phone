<?php
/**
 * Сравнение AJAX запросов: debug_ajax.php vs front-page.php
 * 
 * Этот скрипт помогает найти различия между тестовым запросом и реальным
 */

require_once('../../../wp-load.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Сравнение AJAX запросов</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .test-btn { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin: 5px; }
        .test-btn:hover { background: #005a87; }
        .comparison { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .comparison-item { border: 1px solid #ddd; padding: 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Сравнение AJAX запросов</h1>

    <div class="section">
        <h2>Проблема: debug_ajax.php работает, front-page.php - нет</h2>
        <p>Этот скрипт поможет найти различия между запросами.</p>
    </div>

    <div class="section">
        <h2>1. Проверка селекторов jQuery</h2>
        <p>Проверьте в консоли браузера (F12), что селекторы находят элементы:</p>
        <pre>
// Выполните в консоли браузера на front-page.php:
console.log('count input:', $('.kviz-wrap .input-wrp2 input').length, $('.kviz-wrap .input-wrp2 input').val());
console.log('level select:', $('.kviz-wrap .input-wrp4 .main-select').length, $('.kviz-wrap .input-wrp4 .main-select').val());
console.log('region select:', $('.kviz-wrap .input-wrp5 .region-select').length, $('.kviz-wrap .input-wrp5 .region-select').val());
console.log('ajaxurl:', $('.footer').attr('data-home'));
        </pre>
        <button class="test-btn" onclick="checkSelectors()">Проверить селекторы на этой странице</button>
        <div id="selectors-result"></div>
    </div>

    <div class="section">
        <h2>2. Сравнение запросов</h2>
        <div class="comparison">
            <div class="comparison-item">
                <h3>Запрос из debug_ajax.php</h3>
                <pre>
POST /wp-admin/admin-ajax.php
Content-Type: application/x-www-form-urlencoded

action=action
count=5
level=Стандарт
region[]=Москва
region[]=Барнаул
                </pre>
                <p class="success">✅ Работает</p>
            </div>
            <div class="comparison-item">
                <h3>Запрос из front-page.php</h3>
                <pre>
POST /wp-admin/admin-ajax.php
Content-Type: application/x-www-form-urlencoded

action=action
count=<?php echo isset($_GET['test_count']) ? $_GET['test_count'] : '[значение из формы]'; ?>
level=<?php echo isset($_GET['test_level']) ? $_GET['test_level'] : '[значение из формы]'; ?>
region=<?php echo isset($_GET['test_region']) ? $_GET['test_region'] : '[значение из формы]'; ?>
                </pre>
                <p class="warning">⚠️ Может не работать</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>3. Возможные причины</h2>
        <ul>
            <li><strong>Пустые значения:</strong> Селекторы не находят элементы или возвращают пустые значения</li>
            <li><strong>Неправильный формат region:</strong> Может отправляться как строка вместо массива</li>
            <li><strong>Проблемы с tokenize2:</strong> Плагин может изменять способ получения значений</li>
            <li><strong>Разные URL:</strong> ajaxurl может формироваться неправильно</li>
            <li><strong>Кодировка:</strong> Проблемы с кириллицей в запросе</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Тест с реальными значениями из формы</h2>
        <p>Откройте front-page.php, заполните форму, затем откройте консоль (F12) и посмотрите логи.</p>
        <p>Или используйте кнопку ниже для симуляции запроса:</p>
        <button class="test-btn" onclick="simulateRealRequest()">Симуляция запроса из front-page.php</button>
        <div id="simulation-result"></div>
    </div>

    <div class="section">
        <h2>5. Проверка обработки в PHP</h2>
        <p>Проверьте логи WordPress (wp-content/debug.log) после отправки запроса из front-page.php</p>
        <p>В логах должно быть видно:</p>
        <pre>
=== filter_callback DEBUG ===
$_POST содержимое: Array(...)
count: ...
level: ...
region: ...
        </pre>
    </div>

    <script>
    function checkSelectors() {
        const resultDiv = document.getElementById('selectors-result');
        resultDiv.innerHTML = '<p>Проверка селекторов...</p>';
        
        if (typeof jQuery === 'undefined') {
            resultDiv.innerHTML = '<p class="error">❌ jQuery не загружен</p>';
            return;
        }
        
        const $ = jQuery;
        const results = [];
        
        // Проверяем count
        const countEl = $('.kviz-wrap .input-wrp2 input');
        results.push({
            name: 'count input',
            found: countEl.length > 0,
            value: countEl.val(),
            selector: '.kviz-wrap .input-wrp2 input'
        });
        
        // Проверяем level
        const levelEl = $('.kviz-wrap .input-wrp4 .main-select');
        results.push({
            name: 'level select',
            found: levelEl.length > 0,
            value: levelEl.val(),
            selector: '.kviz-wrap .input-wrp4 .main-select'
        });
        
        // Проверяем region
        const regionEl = $('.kviz-wrap .input-wrp5 .region-select');
        const regionVal = regionEl.val();
        results.push({
            name: 'region select',
            found: regionEl.length > 0,
            value: regionVal,
            isArray: Array.isArray(regionVal),
            selector: '.kviz-wrap .input-wrp5 .region-select'
        });
        
        // Проверяем ajaxurl
        const footer = $('.footer');
        const dataHome = footer.attr('data-home');
        results.push({
            name: 'ajaxurl',
            found: footer.length > 0,
            value: dataHome,
            fullUrl: dataHome ? dataHome + 'wp-admin/admin-ajax.php' : 'не найден'
        });
        
        // Выводим результаты
        let html = '<h3>Результаты проверки:</h3><ul>';
        results.forEach(r => {
            const status = r.found ? '✅' : '❌';
            html += `<li>${status} <strong>${r.name}</strong>: `;
            html += r.found ? `найден, значение: ${JSON.stringify(r.value)}` : 'не найден';
            if (r.isArray !== undefined) {
                html += `, массив: ${r.isArray ? 'да' : 'нет'}`;
            }
            if (r.fullUrl) {
                html += `<br>Полный URL: ${r.fullUrl}`;
            }
            html += '</li>';
        });
        html += '</ul>';
        
        resultDiv.innerHTML = html;
    }
    
    function simulateRealRequest() {
        const resultDiv = document.getElementById('simulation-result');
        resultDiv.innerHTML = '<p>Симуляция запроса...</p>';
        
        if (typeof jQuery === 'undefined') {
            resultDiv.innerHTML = '<p class="error">❌ jQuery не загружен. Этот скрипт должен работать на странице с jQuery.</p>';
            return;
        }
        
        const $ = jQuery;
        const ajaxurl = $('.footer').attr('data-home') + 'wp-admin/admin-ajax.php';
        const count = $('.kviz-wrap .input-wrp2 input').val() || '5';
        const level = $('.kviz-wrap .input-wrp4 .main-select').val() || 'Стандарт';
        const region = $('.kviz-wrap .input-wrp5 .region-select').val() || ['Москва'];
        
        resultDiv.innerHTML += `<p>Отправка запроса с данными:</p><pre>count: ${count}\nlevel: ${level}\nregion: ${JSON.stringify(region)}\nurl: ${ajaxurl}</pre>`;
        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: "html",
            data: {
                'count': count,
                'level': level,
                'region': region,
                action: 'action'
            },
            traditional: false,
            success: function(data) {
                resultDiv.innerHTML += `<p class="success">✅ Успешный ответ</p>`;
                resultDiv.innerHTML += `<p>Длина ответа: ${data.length} символов</p>`;
                if (data.length === 0) {
                    resultDiv.innerHTML += `<p class="error">❌ Получен пустой ответ!</p>`;
                } else {
                    resultDiv.innerHTML += `<pre>${data.substring(0, 500)}${data.length > 500 ? '... (обрезано)' : ''}</pre>`;
                }
            },
            error: function(xhr, status, error) {
                resultDiv.innerHTML += `<p class="error">❌ Ошибка: ${error}</p>`;
                resultDiv.innerHTML += `<p>Status: ${status}, Code: ${xhr.status}</p>`;
                resultDiv.innerHTML += `<pre>${xhr.responseText.substring(0, 500)}</pre>`;
            }
        });
    }
    
    // Автоматически проверяем селекторы при загрузке
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function() {
            console.log('Скрипт диагностики загружен. Используйте функции checkSelectors() и simulateRealRequest()');
        });
    }
    </script>
</body>
</html>


