=== Inssmart Form ===
Contributors: kubiki
Tags: form, stepper, multi-step, insurance, react
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Многошаговая форма Inssmart для WordPress с использованием React и Tailwind CSS.

== Description ==

Inssmart Form - это современный плагин WordPress, который предоставляет многошаговую форму для страховых услуг. Форма построена на React с использованием компонентов shadcn/ui и стилизована с помощью Tailwind CSS.

== Features ==

* Многошаговая форма с навигацией между шагами
* Современный дизайн на основе Tailwind CSS
* Адаптивная верстка
* Легкая интеграция через шорткод
* Настраиваемые параметры контейнера

== Installation ==

1. Загрузите папку `inssmart` в директорию `/wp-content/plugins/`
2. Активируйте плагин через меню 'Плагины' в WordPress
3. Соберите файлы формы, выполнив в директории плагина:
   `npm install && npm run build:wordpress`
4. Используйте шорткод `[inssmart_form]` на любой странице или посте

== Frequently Asked Questions ==

= Нужно ли устанавливать зависимости? =

Да, для работы плагина необходимо установить зависимости и собрать файлы:
`npm install && npm run build:wordpress`

= Где найти собранные файлы? =

После сборки файлы будут находиться в папке `dist/`:
* `inssmart-form.js` - JavaScript файл
* `inssmart-form.css` - CSS файл

= Как использовать форму? =

Используйте шорткод на любой странице:
`[inssmart_form]`

Или с кастомными параметрами:
`[inssmart_form container_id="my-form" class="custom-class"]`

== Screenshots ==

1. Многошаговая форма Inssmart

== Changelog ==

= 1.0.0 =
* Первый релиз
* Базовая функциональность формы
* Интеграция через шорткод
* Админ-панель для проверки статуса сборки

== Upgrade Notice ==

= 1.0.0 =
Первая версия плагина. Установите зависимости и соберите файлы перед использованием.

