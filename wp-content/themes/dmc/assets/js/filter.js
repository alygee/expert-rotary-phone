/**
 * Логика работы с фильтрами формы
 */
jQuery(document).ready(function ($) {
  /**
   * Обновляет URL с параметрами фильтров
   */
  function updateURLParams(count, level, region) {
    const url = new URL(window.location);

    // Удаляем старые параметры фильтров
    url.searchParams.delete('count');
    url.searchParams.delete('level');
    url.searchParams.delete('cities');

    // Добавляем новые параметры
    if (count && count !== '') {
      url.searchParams.set('count', count);
    }

    if (level) {
      if (Array.isArray(level) && level.length > 0) {
        url.searchParams.set('level', level.join(','));
      } else if (typeof level === 'string' && level !== '') {
        url.searchParams.set('level', level);
      }
    }

    if (region) {
      if (Array.isArray(region) && region.length > 0) {
        url.searchParams.set('cities', region.join(','));
      } else if (typeof region === 'string' && region !== '') {
        url.searchParams.set('cities', region);
      }
    }

    // Обновляем URL без перезагрузки страницы
    window.history.pushState({}, '', url);
  }

  /**
   * Читает параметры из URL и заполняет форму
   * @param {boolean} autoSubmit - автоматически выполнить поиск после загрузки параметров
   */
  function loadParamsFromURL(autoSubmit) {
    const urlParams = new URLSearchParams(window.location.search);
    let hasParams = false;

    // Заполняем количество сотрудников
    const count = urlParams.get('count');
    if (count) {
      $('.kviz-wrap .input-wrp2 input').val(count);
      hasParams = true;
    }

    // Заполняем уровень покрытия
    const level = urlParams.get('level');
    if (level) {
      $('.kviz-wrap .input-wrp4 .main-select').val(level).trigger('change');
      hasParams = true;
    }

    // Заполняем регионы
    const cities = urlParams.get('cities');
    if (cities) {
      const citiesArray = cities
        .split(',')
        .map((c) => c.trim())
        .filter((c) => c !== '');
      if (citiesArray.length > 0) {
        hasParams = true;
        // Для tokenize2 нужно установить значения через val
        $('.kviz-wrap .input-wrp5 .region-select').val(citiesArray);
        // Если tokenize2 уже инициализирован, обновляем токены
        setTimeout(function () {
          const $select = $('.region-select');
          if ($select.length && $select.data('tokenize2')) {
            // Очищаем существующие токены
            $select.trigger('tokenize:clear');
            // Добавляем новые токены
            citiesArray.forEach(function (city) {
              $select
                .tokenize2()
                .trigger('tokenize:tokens:add', [city, city, true]);
            });
          }
        }, 100);
      }
    }

    // Если есть параметры в URL и включен autoSubmit, автоматически выполняем поиск
    if (hasParams && autoSubmit !== false) {
      // Небольшая задержка, чтобы селекты успели инициализироваться
      setTimeout(function () {
        // Проверяем валидность перед отправкой
        if (validateCount() && validateRegionSelect()) {
          $('.click-step1').trigger('click');
        }
      }, 800);
    }
  }

  // Инициализация селектов
  $('.main-select').mySelect();

  $('.region-select').tokenize2({
    tokensAllowCustom: true,
    placeholder: 'Введите название региона...',
  });

  $('.tokenize').prepend('<span class="tokenize-gl"></span>');

  $('.region-select').on('tokenize:focus', function (e) {
    $(this).trigger('tokenize:search', ['']);
  });

  $('.tokenize-gl').on('click', function () {
    let $input = $('.region-select').siblings('.tokenize').find('input');
    $input.focus().trigger(jQuery.Event('keydown', { keyCode: 40 }));
  });

  // Загружаем параметры из URL при загрузке страницы
  // Небольшая задержка, чтобы селекты успели инициализироваться
  setTimeout(function () {
    // Загружаем параметры с автоматическим поиском, если они есть в URL
    loadParamsFromURL(true);
  }, 500);

  // Обрабатываем изменение истории браузера (кнопка "Назад"/"Вперед")
  window.addEventListener('popstate', function (event) {
    // При изменении истории загружаем параметры из URL без автоматического поиска
    // чтобы избежать двойного запроса
    setTimeout(function () {
      loadParamsFromURL(false);
    }, 100);
  });

  // Валидация количество сотрудников
  function validateCount() {
    let value = $('.validate1').val();
    let _this1 = $('.validate1');
    let errorMsg = $('.errorMsg1');
    $('.block-large').css('display', 'none');
    $('.block-large').closest('.block-rezult').removeClass('bg-nb');
    if (value === '') {
      errorMsg.text('Пожалуйста, укажите количество сотрудников').show();
      _this1.addClass('error');
      return false;
    } else if (!/^\d+$/.test(value)) {
      errorMsg.text('Укажите целое число').show();
      _this1.addClass('error');
      return false;
    } else if (value.length > 10) {
      errorMsg.text('Число может быть не более 10 цифр').show();
      _this1.addClass('error');
      return false;
    } else if (value > 299) {
      $('.block-large').css('display', 'block');
      $('.block-large').closest('.block-rezult').addClass('bg-nb');
      errorMsg.hide();
      _this1.removeClass('error');
      return true;
    } else {
      errorMsg.hide();
      _this1.removeClass('error');
      return true;
    }
  }

  // Валидация регионы
  function validateRegionSelect() {
    const selected = $('.region-select').val();
    if (!selected || selected.length === 0) {
      $('.region-error').show();
      $('.input-wrp5 .tokenize').addClass('error');
      return false;
    } else {
      $('.region-error').hide();
      $('.input-wrp5 .tokenize').removeClass('error');
      return true;
    }
  }

  // Валидация ниминование компании
  function validateTextInputCompani() {
    let value = $('.validate2').val().trim();
    let _this2 = $('.validate2');
    let errorMsg2 = $('.errorMsg2');
    let regex = /^[A-Za-zА-Яа-яЁё0-9\s"'\-\.]{2,150}$/;
    if (value === '') {
      errorMsg2.text('Поле обязательно для заполнения').show();
      _this2.addClass('error');
      return false;
    } else if (!regex.test(value)) {
      errorMsg2
        .text('Недопустимые символы а также длинна должна быть(2–150 символов)')
        .show();
      _this2.addClass('error');
      return false;
    } else {
      errorMsg2.hide();
      _this2.removeClass('error');
      return true;
    }
  }

  // Валидация ИИН
  function validateNumberIIN() {
    let value = $('.validate3').val().trim();
    let _this3 = $('.validate3');
    let errorMsg3 = $('.errorMsg3');
    if (value === '') {
      errorMsg3.hide();
      _this3.removeClass('error');
      return true;
    }
    // Проверка: только цифры и длина 10 или 12
    const regex = /^(\d{10}|\d{12})$/;

    if (!regex.test(value)) {
      errorMsg3.text('Введите 10 или 12 цифр').show();
      _this3.addClass('error');
      return false;
    } else {
      errorMsg3.hide();
      _this3.removeClass('error');
      return true;
    }
  }

  // Фио
  function validateNameInput() {
    let value = $('.validate4').val().trim();
    let _this4 = $('.validate4');
    let errorMsg4 = $('.errorMsg4');

    let regex = /^[A-Za-zА-Яа-яЁё\s\-]+$/;

    if (value === '') {
      errorMsg4.text('Поле обязательно для заполнения').show();
      _this4.addClass('error');
      return false;
    } else if (!regex.test(value)) {
      errorMsg4.text('Допускаются только буквы, пробелы и дефисы').show();
      _this4.addClass('error');
      return false;
    } else {
      errorMsg4.hide();
      _this4.removeClass('error');
      return true;
    }
  }

  // Емаил/телефон
  function validateEmailInput() {
    let value = $('.validate5').val().trim();
    let _this5 = $('.validate5');
    let errorMsg5 = $('.errorMsg5');

    let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (value === '') {
      errorMsg5.hide();
      _this5.removeClass('error');
      return true;
    }
    if (!regex.test(value)) {
      errorMsg5.text('Введите корректный e-mail').show();
      _this5.addClass('error');
      return false;
    } else {
      errorMsg5.hide();
      _this5.removeClass('error');
      return true;
    }
  }

  function validatePhoneInput() {
    let value1 = $('.validate5').val().trim();
    let value2 = $('.validate6').val().trim();
    let _this5 = $('.validate5');
    let errorMsg5 = $('.errorMsg5');
    let _this6 = $('.validate6');
    let errorMsg6 = $('.errorMsg6');

    let errorMsg55 = $('.errorMsg55');
    let errorMsg66 = $('.errorMsg66');

    if (value1 == '' && value2 == '') {
      _this5.addClass('error');
      _this6.addClass('error');
      errorMsg55.text('Введите телефон или емаил.').show();
      errorMsg66.text('Введите телефон или емаил.').show();
      return false;
    } else {
      _this5.removeClass('error');
      _this6.removeClass('error');
      errorMsg55.hide();
      errorMsg66.hide();
      return true;
    }
  }

  // Обработчик отправки формы фильтра
  var control = true;
  $(document).on('click', '.click-step1, .click-step2', function (event) {
    if (control === false) return false;
    var error_validate = true;

    // Валидация обязательных полей
    if (!validateCount()) {
      error_validate = false;
    }
    if (!validateRegionSelect()) {
      error_validate = false;
    }
    if (error_validate === false) {
      return false;
    }

    if (
      $('.block-rezult').css('display') == 'block' &&
      !$(this).hasClass('click-step2')
    ) {
      $('.block-rezult').css('display', 'none');
      $('.block-top__wrap').addClass('block-top__wrp-final');
      return false;
    }
    $('.update-rez').css('display', 'block');

    if ($(this).hasClass('click-step1')) {
      $(this).css('display', 'none');
    }

    // Получаем базовый URL сайта
    // Пытаемся получить из атрибута data-home, иначе используем текущий origin
    let baseUrl = $('.footer').attr('data-home') || window.location.origin;
    // Убираем завершающий слэш если есть
    baseUrl = baseUrl.replace(/\/$/, '');

    // Получаем значения из формы
    let count = $('.kviz-wrap .input-wrp2 input').val();
    let level = $('.kviz-wrap .input-wrp4 .main-select').val();
    let region = $('.kviz-wrap .input-wrp5 .region-select').val(); // Может быть массивом для множественного select

    // Обновляем URL с параметрами фильтров
    updateURLParams(count, level, region);

    // Отладка: выводим в консоль что получаем из формы
    console.log('REST API Debug - Полученные значения:');
    console.log('count:', count, typeof count);
    console.log('level:', level, typeof level, Array.isArray(level));
    console.log('region:', region, typeof region, Array.isArray(region));

    // Проверяем, что значения не пустые
    if (!count || count === '' || count === null || count === undefined) {
      console.warn('⚠️ count пустой или не определен');
    }
    if (
      !level ||
      (Array.isArray(level) && level.length === 0) ||
      level === '' ||
      level === null ||
      level === undefined
    ) {
      console.warn('⚠️ level пустой или не определен');
    }
    if (
      !region ||
      (Array.isArray(region) && region.length === 0) ||
      region === '' ||
      region === null ||
      region === undefined
    ) {
      console.warn('⚠️ region пустой или не определен');
    }

    control = false;

    // Подготавливаем параметры для REST API
    // REST API использует: cities (вместо region), levels (вместо level), count, format
    let params = {
      format: 'html',
    };

    // Добавляем count если есть
    if (count && count !== '') {
      params.count = count;
    }

    // Обрабатываем level -> levels
    if (level) {
      if (Array.isArray(level) && level.length > 0) {
        params.levels = level.join(',');
      } else if (typeof level === 'string' && level !== '') {
        params.levels = level;
      }
    }

    // Обрабатываем region -> cities
    if (region) {
      if (Array.isArray(region) && region.length > 0) {
        params.cities = region.join(',');
      } else if (typeof region === 'string' && region !== '') {
        params.cities = region;
      }
    }

    // Формируем URL для REST API
    let apiUrl = baseUrl + '/wp-json/dmc/v1/filter';
    let queryString = $.param(params);
    let fullUrl = apiUrl + '?' + queryString;

    // Отладка: выводим данные которые будут отправлены
    console.log('REST API Debug - URL:', fullUrl);
    console.log('REST API Debug - Параметры:', params);

    // Выполняем запрос к REST API
    $.ajax({
      type: 'GET',
      url: apiUrl,
      dataType: 'json',
      data: params,
      success: function (response) {
        console.log('REST API Debug - Успешный ответ:', response);

        // Проверяем, не является ли ответ ошибкой WordPress REST API
        if (response && response.code && response.message) {
          // Это WP_Error в формате REST API
          console.error(
            '❌ REST API вернул ошибку:',
            response.code,
            response.message
          );
          control = true;
          $('.block-process').css('display', 'none');
          alert('Ошибка: ' + (response.message || response.code));
          return;
        }

        // REST API возвращает JSON объект с полем data, содержащим HTML
        let htmlData = '';
        if (response && response.success && response.data) {
          htmlData = response.data;
        } else if (response && response.data) {
          htmlData = response.data;
        } else {
          console.error('❌ Неожиданный формат ответа от REST API');
          control = true;
          $('.block-process').css('display', 'none');
          alert('Ошибка: не удалось получить данные от сервера');
          return;
        }

        if (htmlData.length === 0) {
          console.error('❌ Получен пустой HTML от сервера!');
          htmlData = 'Нет результатов по заданным критериям';
        } else if (
          htmlData.includes('Нет результатов') ||
          htmlData.includes('Ошибка')
        ) {
          console.warn(
            '⚠️ Сервер вернул сообщение об ошибке или отсутствии результатов'
          );
          console.log('Ответ сервера:', htmlData.substring(0, 200));
        }

        setTimeout(function () {
          control = true;
          $('.click-step1').removeClass('active');
          $('.block-process').css('display', 'none');
          $('.block-rezult__grid').html('');
          $('.block-rezult__grid').html(htmlData);
          $('.block-rezult').css('display', 'block');
          $('html, body')
            .stop()
            .animate({ scrollTop: $('#block-rezult').offset().top }, 500);
        }, 10);
      },
      beforeSend: function () {
        $('.block-process').css('display', 'block');
      },
      error: function (xhr, status, error) {
        console.error('REST API Debug - Ошибка запроса:');
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response:', xhr.responseText);
        console.error('Status Code:', xhr.status);

        // Сбрасываем флаг контроля и скрываем индикатор загрузки
        control = true;
        $('.block-process').css('display', 'none');

        // Пытаемся извлечь сообщение об ошибке из ответа
        let errorMessage = 'Возникла ошибка при отправке запроса';
        try {
          if (xhr.responseText) {
            let errorResponse = JSON.parse(xhr.responseText);
            if (errorResponse && errorResponse.message) {
              errorMessage = errorResponse.message;
            } else if (errorResponse && errorResponse.code) {
              errorMessage =
                errorResponse.code +
                (errorResponse.message ? ': ' + errorResponse.message : '');
            }
          }
        } catch (e) {
          // Если не удалось распарсить JSON, используем стандартное сообщение
          if (xhr.status === 0) {
            errorMessage = 'Ошибка сети. Проверьте подключение к интернету.';
          } else if (xhr.status >= 500) {
            errorMessage = 'Ошибка сервера. Попробуйте позже.';
          } else if (xhr.status === 404) {
            errorMessage = 'Эндпоинт не найден. Обратитесь к администратору.';
          }
        }

        alert(errorMessage);
      },
    });

    return false;
  });

  // Обработчик отправки Contact Form 7
  $(document).on('click', '.kviz-wrap .wpcf7-submit', function (event) {
    var error_validate = true;
    if (!validateCount()) {
      error_validate = false;
    }
    if (!validateRegionSelect()) {
      error_validate = false;
    }
    if (!validateTextInputCompani()) {
      error_validate = false;
    }
    if (!validateNumberIIN()) {
      error_validate = false;
    }
    if (!validateNameInput()) {
      error_validate = false;
    }
    if (!validateEmailInput()) {
      error_validate = false;
    }
    if (!validatePhoneInput()) {
      error_validate = false;
    }
    if (error_validate === false) {
      return false;
    }

    $('.kviz-wrap .wpcf7-form .hidden-group input').val('');

    $('.name-kompani input').val($('.kviz-wrap .input-wrp1 input').val());
    $('.count-employees input').val($('.kviz-wrap .input-wrp2 input').val());
    $('.iin input').val($('.kviz-wrap .input-wrp3 input').val());
    $('.coverage-level input').val($('.kviz-wrap .input-wrp4 select').val());
    $('.service-region input').val($('.kviz-wrap .input-wrp5 select').val());
    $('.fio input').val($('.kviz-wrap .input-wrp6 input').val());
    $('.email-kv input').val($('.kviz-wrap .input-wrp7 input').val());
    $('.phone-kv input').val($('.kviz-wrap .input-wrp8 input').val());
    //return false;
  });
});
