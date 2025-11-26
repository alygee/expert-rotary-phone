# Диагностика: почему debug_ajax.php работает, а front-page.php - нет

## Проблема
Запрос из `debug_ajax.php` выполняется успешно, но запрос из `front-page.php` возвращает "нет результатов".

## Что было добавлено

### 1. Расширенное логирование в JavaScript (`my-script.js`)
- ✅ Вывод в консоль всех значений перед отправкой
- ✅ Проверка на пустые значения
- ✅ Детальная информация об ошибках
- ✅ Логирование ответа сервера

### 2. Расширенное логирование в PHP (`functions.php`)
- ✅ Полное содержимое `$_POST`
- ✅ Типы всех переменных
- ✅ Информация о результатах фильтрации
- ✅ Детали при отсутствии результатов

### 3. Диагностический скрипт (`debug_ajax_comparison.php`)
- ✅ Проверка селекторов jQuery
- ✅ Сравнение запросов
- ✅ Симуляция реального запроса

## Пошаговая диагностика

### Шаг 1: Откройте консоль браузера (F12)

На странице `front-page.php`:
1. Заполните форму
2. Нажмите кнопку отправки
3. Откройте вкладку **Console** в DevTools
4. Найдите логи, начинающиеся с `AJAX Debug`

**Что искать:**
```
AJAX Debug - Полученные значения:
count: 5 number
level: "Стандарт" string false
region: ["Москва"] object true
```

**Возможные проблемы:**
- ❌ `count: undefined` - селектор не находит input
- ❌ `level: undefined` - селектор не находит select
- ❌ `region: undefined` - селектор не находит select или tokenize2 не работает
- ❌ `region: ""` - пустая строка вместо массива

### Шаг 2: Проверьте вкладку Network

1. Откройте вкладку **Network** в DevTools
2. Найдите запрос к `admin-ajax.php`
3. Откройте его и проверьте:

**Вкладка Headers:**
- URL должен быть правильным
- Method: POST
- Content-Type: application/x-www-form-urlencoded

**Вкладка Payload (или Form Data):**
```
action: action
count: 5
level: Стандарт
region[]: Москва
region[]: Барнаул
```

**Возможные проблемы:**
- ❌ `region: Москва,Барнаул` (строка) вместо `region[]: Москва, region[]: Барнаул` (массив)
- ❌ Пустые значения: `count: `, `level: `, `region: `
- ❌ Отсутствует параметр `action`

**Вкладка Response:**
- Проверьте, что возвращает сервер
- Если пусто - проблема на сервере
- Если "Нет результатов" - проблема с данными или фильтрацией

### Шаг 3: Проверьте логи PHP

Включите отладку в `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Проверьте `wp-content/debug.log`:
```
=== filter_callback DEBUG ===
$_POST содержимое: Array
(
    [action] => action
    [count] => 5
    [level] => Стандарт
    [region] => Array
        (
            [0] => Москва
        )
)
```

**Возможные проблемы:**
- ❌ `$_POST['region']` - строка вместо массива
- ❌ `$_POST['count']` - пустое значение
- ❌ `$_POST['level']` - пустое значение

### Шаг 4: Используйте диагностический скрипт

Откройте `debug_ajax_comparison.php`:
1. Нажмите "Проверить селекторы" - проверяет, находятся ли элементы
2. Нажмите "Симуляция запроса" - отправляет тестовый запрос

## Наиболее частые причины

### 1. Селекторы не находят элементы
**Симптомы:** В консоли `undefined` для всех значений

**Решение:**
```javascript
// Проверьте в консоли:
console.log($('.kviz-wrap .input-wrp2 input').length); // Должно быть > 0
console.log($('.kviz-wrap .input-wrp4 .main-select').length); // Должно быть > 0
console.log($('.kviz-wrap .input-wrp5 .region-select').length); // Должно быть > 0
```

### 2. region отправляется как строка вместо массива
**Симптомы:** В Network видно `region: Москва,Барнаул` вместо `region[]: Москва`

**Решение:**
- Проверьте, что tokenize2 правильно инициализирован
- Убедитесь, что `.val()` возвращает массив для множественного select

### 3. Пустые значения
**Симптомы:** В логах `count: null`, `level: []`, `region: []`

**Решение:**
- Проверьте, что форма заполнена
- Проверьте валидацию перед отправкой

### 4. Неправильный ajaxurl
**Симптомы:** Ошибка 404 или неправильный URL

**Решение:**
```javascript
// Проверьте в консоли:
console.log($('.footer').attr('data-home')); // Должен быть правильный URL
```

### 5. Проблемы с кодировкой
**Симптомы:** Кириллица отображается как `???`

**Решение:**
- Убедитесь, что страница в UTF-8
- Проверьте заголовки запроса

## Быстрое решение

Если нужно быстро исправить, добавьте проверку в JavaScript:

```javascript
// В my-script.js перед отправкой AJAX:
let count = $('.kviz-wrap .input-wrp2 input').val();
let level = $('.kviz-wrap .input-wrp4 .main-select').val();
let region = $('.kviz-wrap .input-wrp5 .region-select').val();

// Принудительно преобразуем region в массив, если это строка
if (typeof region === 'string' && region.includes(',')) {
    region = region.split(',').map(r => r.trim());
} else if (typeof region === 'string') {
    region = [region];
}

// Проверяем, что значения не пустые
if (!count || !level || !region || (Array.isArray(region) && region.length === 0)) {
    alert('Заполните все поля формы');
    return false;
}
```

## После исправления

**ВАЖНО:** Удалите или отключите логирование в production:

1. В `wp-config.php` установите:
```php
define('WP_DEBUG', false);
```

2. Удалите или закомментируйте `console.log` в `my-script.js`

3. Удалите диагностические скрипты:
```bash
rm wp-content/themes/dmc/debug_ajax.php
rm wp-content/themes/dmc/debug_ajax_comparison.php
```

