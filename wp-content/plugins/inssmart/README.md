# Inssmart Form - Stepper Form для WordPress

Многошаговая форма на React с использованием shadcn/ui и Tailwind CSS, предназначенная для интеграции в WordPress.

## Установка

```bash
npm install
```

## Разработка

### Обычный React режим (для разработки)

```bash
npm run dev
```

Откроет приложение на http://localhost:5173 с hot reload. Приложение автоматически монтируется в элемент `#root` из `index.html`.

### Сборка для WordPress

```bash
npm run build:wordpress
```

Создаст файлы в папке `dist/` для интеграции в WordPress:
- `inssmart-form.js` - JavaScript файл (включает все зависимости)
- `inssmart-form.css` - CSS файл

### Обычная сборка React (для деплоя)

```bash
npm run build
```

Создаст стандартную сборку React приложения в папке `dist/`.

После сборки файлы будут находиться в папке `dist/`:
- `inssmart-form.js` - основной JavaScript файл (включает все зависимости)
- `inssmart-form.css` - файл стилей (Tailwind CSS)
- `inssmart-form.js.map` - source map (опционально, для отладки)

**Важно:** Собранный файл включает React и ReactDOM, поэтому не нужно подключать их отдельно. Не забудьте подключить CSS файл!

## Интеграция в WordPress

### Вариант 1: Использование плагина (рекомендуется)

Плагин уже находится в правильной директории (`wp-content/plugins/inssmart/`).

1. Убедитесь, что файлы собраны: выполните `npm run build:wordpress` в директории плагина
2. Активируйте плагин в админ-панели WordPress (Плагины → Inssmart Form → Активировать)
3. Используйте шорткод на любой странице:
```
[inssmart_form]
```

Или с кастомным ID контейнера:
```
[inssmart_form container_id="my-custom-form"]
```

### Вариант 2: Использование шорткода через functions.php

1. Загрузите файл `dist/inssmart-form.js` на ваш WordPress сервер (например, в `wp-content/themes/your-theme/js/`)
2. Добавьте следующий код в `functions.php` вашей темы:

```php
function enqueue_inssmart_form() {
    // Подключаем CSS
    wp_enqueue_style(
        'inssmart-form',
        get_template_directory_uri() . '/js/inssmart-form.css',
        array(),
        '1.0.0'
    );
    // Подключаем JS
    wp_enqueue_script(
        'inssmart-form',
        get_template_directory_uri() . '/js/inssmart-form.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_inssmart_form');

function inssmart_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'container_id' => 'inssmart-form-container'
    ), $atts);
    
    $container_id = esc_attr($atts['container_id']);
    
    return '<div id="' . $container_id . '"></div>
    <script>
        if (typeof window.initInssmartForm === "function") {
            window.initInssmartForm("' . $container_id . '");
        } else {
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof window.initInssmartForm === "function") {
                    window.initInssmartForm("' . $container_id . '");
                }
            });
        }
    </script>';
}
add_shortcode('inssmart_form', 'inssmart_form_shortcode');
```

3. Используйте шорткод на любой странице:
```
[inssmart_form]
```

### Вариант 3: Прямое встраивание в HTML

1. Загрузите файл `dist/inssmart-form.js` на ваш сервер
2. Добавьте в HTML страницы (через редактор блоков или напрямую):

```html
<link rel="stylesheet" href="/path/to/inssmart-form.css">
<div id="inssmart-form-container"></div>
<script src="/path/to/inssmart-form.js"></script>
<script>
  if (typeof window.initInssmartForm === 'function') {
    window.initInssmartForm('inssmart-form-container');
  } else {
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof window.initInssmartForm === 'function') {
        window.initInssmartForm('inssmart-form-container');
      }
    });
  }
</script>
```

## Настройка дизайна из Figma

### Перенос цветов из Figma

1. Откройте ваш дизайн в Figma
2. Выберите нужные цвета и скопируйте их значения
3. Откройте файл `src/index.css`
4. Замените значения CSS переменных в секции `:root`:

```css
:root {
  --primary: 221.2 83.2% 53.3%; /* Замените на цвет из Figma */
  --primary-foreground: 210 40% 98%;
  /* ... другие переменные ... */
}
```

### Перенос типографики

Настройте шрифты в `tailwind.config.js`:

```js
theme: {
  extend: {
    fontFamily: {
      sans: ['Your Font', 'sans-serif'],
    },
    fontSize: {
      // Добавьте размеры из Figma
    }
  }
}
```

### Перенос компонентов

Компоненты shadcn/ui можно кастомизировать в папке `src/components/ui/`. Каждый компонент использует Tailwind классы, которые легко настроить под дизайн из Figma.

## Структура проекта

```
src/
  components/
    ui/          # Компоненты shadcn/ui
    FormStepper.tsx  # Основной компонент формы
  lib/
    utils.ts     # Утилиты
  App.tsx        # Главный компонент
  main.tsx       # Точка входа
  index.css      # Глобальные стили
```

## Кастомизация

Вы можете изменить количество шагов, поля формы и валидацию в компоненте `FormStepper.tsx`.

