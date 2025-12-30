# Inssmart Form - Stepper Form для WordPress и iframe

Многошаговая форма на React с использованием shadcn/ui и Tailwind CSS, предназначенная для интеграции в WordPress и встраивания через iframe.

## Быстрый старт через CDN

После публикации в npm пакет автоматически доступен через CDN. **Никакой дополнительной настройки не требуется!**

### Использование iframe версии через CDN

```html
<iframe 
  src="https://unpkg.com/inssmart-form@latest/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none;"
></iframe>
```

### Использование WordPress версии через CDN

```html
<link rel="stylesheet" href="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.css">
<div id="inssmart-form-container"></div>
<script src="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.js"></script>
<script>
  if (typeof window.initInssmartForm === 'function') {
    window.initInssmartForm('inssmart-form-container');
  }
</script>
```

**Доступные CDN:**
- **unpkg**: `https://unpkg.com/inssmart-form@latest/...`
- **jsDelivr**: `https://cdn.jsdelivr.net/npm/inssmart-form@latest/...`

## Установка

### Для разработки

```bash
npm install
```

### Установка из npm

```bash
npm install inssmart-form
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

### Сборка для iframe

```bash
npm run build:iframe
```

Создаст файлы в папке `dist/iframe/` для встраивания через iframe:
- `index.html` - HTML файл с формой
- `assets/inssmart-form.css` - CSS файл
- `assets/inssmart-form-[hash].js` - JavaScript файл

### Сборка всех версий

```bash
npm run build:all
```

Создаст обе версии: WordPress и iframe.

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

## Использование через iframe

После установки пакета из npm или сборки проекта, вы можете встроить форму на любую страницу через iframe.

### ⚠️ Важно для локального тестирования

**Не открывайте HTML файлы напрямую через `file://` протокол!** Браузеры блокируют загрузку модулей и ресурсов через `file://` из соображений безопасности (CORS policy).

Для локального тестирования используйте локальный HTTP сервер:

```bash
# Вариант 1: Использование встроенного скрипта (рекомендуется)
npm run serve

# Вариант 2: Использование Vite preview
npm run preview

# Вариант 3: Python HTTP сервер
python3 -m http.server 8000

# Вариант 4: Node.js serve
npx serve .

# Вариант 5: PHP встроенный сервер
php -S localhost:8000
```

Затем откройте в браузере: `http://localhost:8000/iframe-example.html`

### Вариант 1: Использование из npm пакета (CDN)

После публикации в npm пакет автоматически становится доступен через CDN. Вы можете использовать один из следующих CDN:

#### Unpkg (рекомендуется)

```html
<!-- iframe версия -->
<iframe 
  src="https://unpkg.com/inssmart-form@latest/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none; min-height: 800px;"
></iframe>

<!-- WordPress версия -->
<link rel="stylesheet" href="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.css">
<div id="inssmart-form-container"></div>
<script src="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.js"></script>
<script>
  if (typeof window.initInssmartForm === 'function') {
    window.initInssmartForm('inssmart-form-container');
  }
</script>
```

#### jsDelivr

```html
<!-- iframe версия -->
<iframe 
  src="https://cdn.jsdelivr.net/npm/inssmart-form@latest/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none; min-height: 800px;"
></iframe>

<!-- WordPress версия -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/inssmart-form@latest/dist/inssmart-form.css">
<div id="inssmart-form-container"></div>
<script src="https://cdn.jsdelivr.net/npm/inssmart-form@latest/dist/inssmart-form.js"></script>
<script>
  if (typeof window.initInssmartForm === 'function') {
    window.initInssmartForm('inssmart-form-container');
  }
</script>
```

#### Использование конкретной версии

Вместо `@latest` можно указать конкретную версию для стабильности:

```html
<!-- Использование версии 1.1.0 -->
<iframe 
  src="https://unpkg.com/inssmart-form@1.1.0/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
></iframe>
```

### Вариант 2: Использование локальных файлов

1. Соберите iframe версию: `npm run build:iframe`
2. Загрузите содержимое папки `dist/iframe/` на ваш сервер
3. Встройте iframe на страницу:

```html
<iframe 
  src="/path/to/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none; min-height: 800px;"
></iframe>
```

### Вариант 3: Адаптивный iframe

Для адаптивного встраивания используйте следующий код:

```html
<div style="position: relative; width: 100%; padding-bottom: 100%; height: 0; overflow: hidden;">
  <iframe 
    src="/path/to/iframe/index.html"
    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
    allowfullscreen
  ></iframe>
</div>
```

Или с фиксированной высотой:

```html
<iframe 
  src="/path/to/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none; display: block;"
  scrolling="no"
></iframe>
```

### Настройка высоты iframe

Для динамической высоты iframe можно использовать JavaScript:

```html
<iframe 
  id="inssmart-iframe"
  src="/path/to/iframe/index.html"
  width="100%"
  frameborder="0"
  style="border: none; display: block;"
></iframe>

<script>
  // Автоматическая подстройка высоты iframe
  const iframe = document.getElementById('inssmart-iframe');
  
  window.addEventListener('message', function(event) {
    // Проверяем источник сообщения для безопасности
    if (event.origin !== window.location.origin) return;
    
    if (event.data && event.data.type === 'resize' && event.data.height) {
      iframe.style.height = event.data.height + 'px';
    }
  });
</script>
```

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

## Публикация в npm

Для публикации пакета в npm registry:

1. Убедитесь, что все файлы собраны: `npm run build:all`
2. Проверьте версию в `package.json`
3. Войдите в npm: `npm login`
4. Опубликуйте пакет: `npm publish`

Перед публикацией автоматически выполнится `prepublishOnly` скрипт, который соберет все версии.

### Настройка npm пакета

Пакет настроен для публикации со следующими полями:
- `main`: точка входа для WordPress версии
- `exports`: экспорты для разных вариантов использования
- `files`: только необходимые файлы (dist и README)

## Кастомизация

Вы можете изменить количество шагов, поля формы и валидацию в компоненте `FormStepper.tsx`.

