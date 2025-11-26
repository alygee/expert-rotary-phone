# Где находятся логи ошибок WordPress

## Основные места логов

### 1. Логи WordPress (если включены)

**Расположение:** `/var/www/kubiki.ai/wp-content/debug.log`

**Как включить:**
Добавьте в `wp-config.php` (перед строкой `/* Это всё, дальше не редактируем */`):

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false); // Не показывать на экране, только в лог
```

**Как просмотреть:**
```bash
# Последние 50 строк
tail -n 50 /var/www/kubiki.ai/wp-content/debug.log

# Следить за логами в реальном времени
tail -f /var/www/kubiki.ai/wp-content/debug.log

# Поиск ошибок
grep -i error /var/www/kubiki.ai/wp-content/debug.log

# Очистить лог
> /var/www/kubiki.ai/wp-content/debug.log
```

### 2. Логи PHP-FPM (системные)

**Расположение:**
```bash
# Ubuntu/Debian
/var/log/php-fpm/error.log
# или
/var/log/php8.x-fpm.log

# CentOS/RHEL
/var/log/php-fpm/error.log

# Общий лог PHP
/var/log/php_errors.log
```

**Как просмотреть:**
```bash
# Последние ошибки
tail -n 100 /var/log/php-fpm/error.log

# Следить в реальном времени
tail -f /var/log/php-fpm/error.log

# Поиск ошибок WordPress
grep -i wordpress /var/log/php-fpm/error.log
```

### 3. Логи веб-сервера

**Apache:**
```bash
# Ошибки
/var/log/apache2/error.log
# или
/var/log/httpd/error_log

# Доступ
/var/log/apache2/access.log
```

**Nginx:**
```bash
# Ошибки
/var/log/nginx/error.log

# Доступ
/var/log/nginx/access.log
```

**Как просмотреть:**
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

### 4. Логи через wp-config.php

Можно настроить свой путь к логам:

```php
// В wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_LOG_FILE', '/var/log/wordpress-custom.log');
```

## Быстрые команды для просмотра логов

### Все логи WordPress
```bash
# Просмотр последних ошибок
tail -n 100 /var/www/kubiki.ai/wp-content/debug.log | grep -i error

# Поиск конкретной ошибки
grep -i "filter_callback" /var/www/kubiki.ai/wp-content/debug.log

# Просмотр за последний час
grep "$(date '+%Y-%m-%d %H')" /var/www/kubiki.ai/wp-content/debug.log
```

### Логи PHP
```bash
# Последние ошибки PHP
tail -n 50 /var/log/php-fpm/error.log

# Ошибки связанные с WordPress
grep -i "wordpress\|wp-" /var/log/php-fpm/error.log | tail -20
```

### Комбинированный просмотр
```bash
# Смотреть все логи одновременно
tail -f /var/www/kubiki.ai/wp-content/debug.log /var/log/php-fpm/error.log
```

## Проверка текущих настроек

### Через диагностический скрипт
Откройте: `http://your-site.com/wp-content/themes/dmc/debug_ajax.php`

### Через wp-config.php
```bash
grep -i "WP_DEBUG" /var/www/kubiki.ai/wp-config.php
```

## Включение логирования для отладки

### Временное включение (для отладки)
Добавьте в `wp-config.php`:

```php
// Временно для отладки
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false); // Не показывать на сайте
@ini_set('display_errors', 0); // Не показывать на сайте
```

### Постоянное включение (только для разработки!)
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

### Отключение (для production)
```php
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
```

## Просмотр логов через веб-интерфейс

Если у вас есть доступ к панели управления сервером (cPanel, Plesk и т.д.), логи обычно доступны там.

## Полезные команды для диагностики

```bash
# Размер лог-файла
du -h /var/www/kubiki.ai/wp-content/debug.log

# Количество ошибок
grep -c "ERROR" /var/www/kubiki.ai/wp-content/debug.log

# Последние 10 ошибок
grep -i error /var/www/kubiki.ai/wp-content/debug.log | tail -10

# Очистка старого лога (создаст новый)
> /var/www/kubiki.ai/wp-content/debug.log

# Поиск по дате
grep "2025-11-25" /var/www/kubiki.ai/wp-content/debug.log
```

## Ротация логов

Чтобы лог не разрастался, можно настроить ротацию:

```bash
# Создайте файл /etc/logrotate.d/wordpress
/var/www/kubiki.ai/wp-content/debug.log {
    daily
    rotate 7
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```

## Важно!

⚠️ **Для production сайта:**
- Отключите `WP_DEBUG` или установите `WP_DEBUG_DISPLAY` в `false`
- Регулярно очищайте или ротируйте логи
- Не храните логи с чувствительной информацией

✅ **Для разработки:**
- Включите все логи
- Регулярно проверяйте их
- Очищайте старые логи

## Диагностика медиафайлов

Если проблема с медиафайлами, используйте:
```
http://your-site.com/wp-content/themes/dmc/debug_media.php
```

Этот скрипт покажет:
- Настройки загрузки файлов
- Файлы в директории uploads
- Записи в базе данных
- Права доступа
- Возможные проблемы

