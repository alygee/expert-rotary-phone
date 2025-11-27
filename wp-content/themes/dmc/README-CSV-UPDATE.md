# Обновление ACF поля csv_file

## Проблема

Функции `rez()` и `сity()` используют ACF поле `csv_file` на странице с ID 2 для получения пути к CSV файлу. Если вы загрузили новый файл вручную, но не обновили ACF поле, функции будут продолжать использовать старый файл.

## Решение

### Автоматический fallback

Функции `rez()` и `сity()` теперь автоматически ищут последний загруженный файл `list.csv` в директории uploads, если:
- ACF поле пустое
- Файл из ACF поля не найден

### Ручное обновление ACF поля

Есть несколько способов обновить ACF поле:

## Способ 1: Через командную строку (рекомендуется)

```bash
cd /var/www/kubiki.ai/wp-content/themes/dmc
php update-csv.php
```

Или через WP-CLI:
```bash
wp eval-file wp-content/themes/dmc/update-csv.php
```

## Способ 2: Через админ-панель WordPress

1. Перейдите на страницу редактирования страницы с ID 2
2. Найдите поле `csv_file` (ACF)
3. Загрузите или выберите новый файл `list.csv`
4. Сохраните страницу

## Способ 3: Через браузер (только для админов)

Откройте в браузере:
```
http://ваш-сайт.ru/wp-content/themes/dmc/inc/update-csv-field.php?update_csv_field=1
```

## Способ 4: Программно в коде

```php
require_once get_template_directory() . '/inc/update-csv-field.php';
$result = update_csv_field();

if ($result['success']) {
    echo "Обновлено: " . $result['file_path'];
} else {
    echo "Ошибка: " . $result['message'];
}
```

## Что делает функция update_csv_field()

1. Ищет все файлы `list.csv` в директории `wp-content/uploads/`
2. Выбирает самый новый файл (по времени модификации)
3. Создает или находит attachment для этого файла
4. Обновляет ACF поле `csv_file` на странице с ID 2
5. Очищает кеш ACF

## Проверка текущего значения

Чтобы проверить, какой файл сейчас используется:

```bash
php -r "require_once 'wp-load.php'; echo get_field('csv_file', 2);"
```

Или в коде:
```php
$csv_field = get_field('csv_file', 2);
var_dump($csv_field);
```

## Логирование

Если включен `WP_DEBUG`, функции `rez()` и `сity()` логируют:
- Какой файл используется
- Время модификации файла
- Ошибки при чтении файла

Логи можно найти в файле, указанном в `WP_DEBUG_LOG` (обычно `wp-content/debug.log`).

