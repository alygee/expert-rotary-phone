/**
 * Автоматическое заполнение полей subId и clickId в формах Contact Form 7
 * из URL параметров
 */

(function($) {
    'use strict';

    /**
     * Получает параметры из URL
     */
    function getUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            subId: urlParams.get('subId') || urlParams.get('sub_id') || null,
            clickId: urlParams.get('clickId') || urlParams.get('click_id') || null
        };
    }

    /**
     * Заполняет скрытые поля в форме CF7
     */
    function fillCf7Fields($form, params) {
        if (!params.subId && !params.clickId) {
            return; // Нет параметров для заполнения
        }

        // Ищем существующие скрытые поля
        if (params.subId) {
            let $subIdField = $form.find('input[name="subId"], input[name="sub_id"]');
            if ($subIdField.length === 0) {
                // Если поля нет, создаем его
                $subIdField = $('<input>', {
                    type: 'hidden',
                    name: 'subId',
                    value: params.subId
                });
                $form.append($subIdField);
            } else {
                // Если поле есть, обновляем значение
                $subIdField.val(params.subId);
            }
        }

        if (params.clickId) {
            let $clickIdField = $form.find('input[name="clickId"], input[name="click_id"]');
            if ($clickIdField.length === 0) {
                // Если поля нет, создаем его
                $clickIdField = $('<input>', {
                    type: 'hidden',
                    name: 'clickId',
                    value: params.clickId
                });
                $form.append($clickIdField);
            } else {
                // Если поле есть, обновляем значение
                $clickIdField.val(params.clickId);
            }
        }
    }

    /**
     * Обработка всех форм CF7 на странице
     */
    function processAllCf7Forms() {
        const params = getUrlParams();
        
        if (!params.subId && !params.clickId) {
            return; // Нет параметров в URL
        }

        // Обрабатываем все формы CF7 на странице
        $('form.wpcf7-form').each(function() {
            fillCf7Fields($(this), params);
        });

        // Также обрабатываем формы, которые могут быть добавлены динамически
        // через MutationObserver
        const observer = new MutationObserver(function(mutations) {
            $('form.wpcf7-form').each(function() {
                const $form = $(this);
                // Проверяем, не обработали ли мы уже эту форму
                if (!$form.data('url-params-filled')) {
                    fillCf7Fields($form, params);
                    $form.data('url-params-filled', true);
                }
            });
        });

        // Наблюдаем за изменениями в DOM
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Инициализация при загрузке страницы
     */
    function init() {
        // Обрабатываем формы сразу при загрузке
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                // Небольшая задержка для загрузки форм CF7
                setTimeout(processAllCf7Forms, 500);
            });
        } else {
            // DOM уже загружен
            setTimeout(processAllCf7Forms, 500);
        }

        // Также обрабатываем формы после AJAX загрузки (для CF7)
        $(document).on('wpcf7mailsent wpcf7invalid wpcf7spam wpcf7mailfailed', function() {
            // После отправки формы CF7 может перезагрузиться
            setTimeout(processAllCf7Forms, 100);
        });

        // Обрабатываем формы при их появлении через AJAX
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).is('form.wpcf7-form') || $(e.target).find('form.wpcf7-form').length > 0) {
                setTimeout(processAllCf7Forms, 100);
            }
        });
    }

    // Запускаем инициализацию
    init();

})(jQuery);

