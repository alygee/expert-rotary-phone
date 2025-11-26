# Обработка массива городов в filter_callback

## Поддерживаемые форматы

Функция `filter_callback` теперь поддерживает несколько форматов передачи городов в параметре `region`:

### 1. Массив городов (рекомендуется)
```javascript
// JavaScript
let region = ['Москва', 'Барнаул', 'Архангельск'];
// jQuery автоматически сериализует как: region[]=Москва&region[]=Барнаул&region[]=Архангельск
```

### 2. Строка через запятую
```javascript
// JavaScript
let region = 'Москва,Барнаул,Архангельск';
// Отправляется как: region=Москва,Барнаул,Архангельск
```

### 3. Один город (строка)
```javascript
// JavaScript
let region = 'Москва';
// Отправляется как: region=Москва
```

## Как это работает

### В PHP (functions.php):

```php
// Обработка region (может быть массивом, строкой или region[])
if(isset($_POST['region'])){
    if(is_array($_POST['region'])){
      // Массив городов (например, из множественного select)
      $region = $_POST['region'];
    } elseif(!empty($_POST['region'])) {
      // Если строка, разбиваем по запятой
      $region = array_filter(array_map('trim', explode(',', $_POST['region'])));
    }
}

// Очищаем массивы от пустых значений
$region = array_values(array_filter(array_map('trim', $region), function($v) { 
    return $v !== ''; 
}));
```

### В JavaScript (my-script.js):

```javascript
// Для множественного select .val() возвращает массив
let region = $('.region-select').val(); // ['Москва', 'Барнаул']

// jQuery автоматически сериализует массив при отправке
$.ajax({
    data: {
        'region': region, // Поддерживается как массив, так и строка
        // ...
    },
    traditional: false // false = region[]=value, true = region=value&region=value
});
```

## Примеры использования

### Пример 1: Множественный select (массив)
```html
<select class="region-select" multiple>
    <option value="Москва">Москва</option>
    <option value="Барнаул">Барнаул</option>
    <option value="Архангельск">Архангельск</option>
</select>
```

```javascript
// .val() вернет массив выбранных значений
let region = $('.region-select').val(); // ['Москва', 'Барнаул']
// Отправится как: region[]=Москва&region[]=Барнаул
```

### Пример 2: Строка через запятую
```javascript
let region = 'Москва,Барнаул,Архангельск';
// Отправится как: region=Москва,Барнаул,Архангельск
// В PHP будет разбито на массив: ['Москва', 'Барнаул', 'Архангельск']
```

## Обработка в filterData2

Функция `filterData2` также поддерживает массив городов:

```php
function filterData2(array $data, $cities = [], $levels = [], int $employeesCount = null): array {
    // Нормализация параметров
    if (!is_array($cities)) $cities = [$cities];
    // ...
}
```

## Отладка

Для отладки включите `WP_DEBUG` в `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

В логах будет видно:
```
filter_callback - region: Array
(
    [0] => Москва
    [1] => Барнаул
    [2] => Архангельск
)
filter_callback - $_POST[region] type: array
```

## Тестирование

Используйте диагностический скрипт `debug_ajax.php`:
- Кнопка "Тест AJAX (один город)" - тестирует один город
- Кнопка "Тест AJAX (массив городов)" - тестирует массив городов

## Важные замечания

1. ✅ **Массивы поддерживаются** - можно передавать массив городов
2. ✅ **Строки поддерживаются** - можно передавать строку через запятую
3. ✅ **Автоматическая нормализация** - пустые значения удаляются
4. ✅ **Обрезка пробелов** - все значения автоматически обрезаются
5. ⚠️ **jQuery traditional: false** - по умолчанию массивы отправляются как `param[]=value`

## Совместимость

- ✅ Работает с множественным select (tokenize2)
- ✅ Работает с обычным select (один выбор)
- ✅ Работает со строкой через запятую
- ✅ Работает с массивом JavaScript
- ✅ Работает с `region[]` из POST

