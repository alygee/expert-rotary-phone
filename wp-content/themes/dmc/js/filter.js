/**
 * Логика работы с фильтрами формы
 */
jQuery(document).ready(function ($) {
  /**
   * Обновляет URL с параметрами фильтров
   */
  function updateURLParams(count, insurer, region) {
    const url = new URL(window.location);

    // Удаляем старые параметры фильтров
    url.searchParams.delete('count');
    url.searchParams.delete('level');
    url.searchParams.delete('cities');
    url.searchParams.delete('employees_count');
    url.searchParams.delete('insurer');
    url.searchParams.delete('city');

    // Добавляем новые параметры (для обратной совместимости сохраняем старые названия)
    if (count && count !== '') {
      url.searchParams.set('count', count);
      url.searchParams.set('employees_count', count);
    }

    if (insurer) {
      if (Array.isArray(insurer) && insurer.length > 0) {
        url.searchParams.set('insurer', insurer.join(','));
      } else if (typeof insurer === 'string' && insurer !== '') {
        url.searchParams.set('insurer', insurer);
      }
    } else {
      url.searchParams.delete('insurer');
    }

    if (region) {
      if (Array.isArray(region) && region.length > 0) {
        url.searchParams.set('cities', region.join(','));
        url.searchParams.set('city', region[0]); // Первый город для нового API
      } else if (typeof region === 'string' && region !== '') {
        url.searchParams.set('cities', region);
        url.searchParams.set('city', region);
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

    // Заполняем количество сотрудников (поддержка обоих параметров для обратной совместимости)
    const count = urlParams.get('employees_count') || urlParams.get('count');
    if (count) {
      $('.kviz-wrap .input-wrp2 input').val(count);
      hasParams = true;
    }

    // Заполняем страховщика (было level, теперь insurer) - поддержка множественного выбора
    const insurer = urlParams.get('insurer');
    if (insurer) {
      const insurerArray = insurer.split(',').map((i) => i.trim()).filter((i) => i !== '');
      if (insurerArray.length > 0) {
        hasParams = true;
        // Для tokenize2 нужно установить значения через val
        $('.kviz-wrap .insurer-select').val(insurerArray);
        // Если tokenize2 уже инициализирован, обновляем токены
        setTimeout(function () {
          const $select = $('.insurer-select');
          if ($select.length && $select.data('tokenize2')) {
            // Очищаем существующие токены
            $select.trigger('tokenize:clear');
            // Добавляем новые токены
            insurerArray.forEach(function (insurerName) {
              $select.tokenize2().trigger('tokenize:tokens:add', [insurerName, insurerName, true]);
            });
          }
        }, 100);
      }
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
        if (validateCount() && validateRegionSelect() && validateInsurerSelect()) {
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
    //displayNoResultsMessage: true,
    //searchMinLength: 0,
    //dropdownMaxItems: 9999
  });

  // Инициализация multiselect для страховщиков
  $('.insurer-select').tokenize2({
    tokensAllowCustom: false,
    placeholder: 'Выберите страховщика...',
    //displayNoResultsMessage: true,
    //searchMinLength: 0,
    //dropdownMaxItems: 9999
  });

  $('.tokenize').prepend('<span class="tokenize-gl"></span>');

  $('.region-select').on('tokenize:focus', function (e) {
    $(this).trigger('tokenize:search', ['']);
  });

  $('.insurer-select').on('tokenize:focus', function (e) {
    $(this).trigger('tokenize:search', ['']);
  });

  $('.tokenize-gl').on('click', function () {
    let $input = $('.region-select, .insurer-select').siblings('.tokenize').find('input');
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

  // Валидация страховщика
  function validateInsurerSelect() {
    const selected = $('.insurer-select').val();
    if (!selected || selected.length === 0) {
      $('.insurer-error').show();
      $('.input-wrp4 .tokenize').addClass('error');
      return false;
    } else {
      $('.insurer-error').hide();
      $('.input-wrp4 .tokenize').removeClass('error');
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
    if (!validateInsurerSelect()) {
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
    let insurer = $('.kviz-wrap .insurer-select').val(); // Может быть массивом для множественного select
    let region = $('.kviz-wrap .input-wrp5 .region-select').val(); // Может быть массивом для множественного select

    // Получаем состояние чекбоксов услуг
    let polyclinic = $('.kviz-wrap input[name="service-polyclinic"]').is(
      ':checked'
    ); // Поликлиника (disabled, но можно проверить)
    let dentistry = $('.kviz-wrap input[name="service-dentistry"]').is(
      ':checked'
    ); // Стоматология
    let ambulance = $('.kviz-wrap input[name="service-ambulance"]').is(
      ':checked'
    ); // Скорая помощь
    let hospitalization = $(
      '.kviz-wrap input[name="service-hospitalization"]'
    ).is(':checked'); // Госпитализация
    let doctor_home = $('.kviz-wrap input[name="service-doctor-home"]').is(
      ':checked'
    ); // Вызов врача на дом

    // Обновляем URL с параметрами фильтров (для обратной совместимости)
    updateURLParams(count, insurer, region);

    // Отладка: выводим в консоль что получаем из формы
    console.log('REST API Debug - Полученные значения:');
    console.log('count:', count, typeof count);
    console.log('insurer:', insurer, typeof insurer);
    console.log('region:', region, typeof region, Array.isArray(region));
    console.log(
      'Услуги - Поликлиника:',
      polyclinic,
      'Стоматология:',
      dentistry,
      'Скорая помощь:',
      ambulance,
      'Госпитализация:',
      hospitalization,
      'Вызов врача:',
      doctor_home
    );

    // Проверяем, что значения не пустые
    if (!count || count === '' || count === null || count === undefined) {
      console.warn('⚠️ count пустой или не определен');
    }
    
    // Нормализуем массив страховщиков
    let insurersArray = [];
    if (insurer) {
      if (Array.isArray(insurer) && insurer.length > 0) {
        insurersArray = insurer;
      } else if (typeof insurer === 'string' && insurer !== '') {
        insurersArray = [insurer];
      }
    }
    
    if (insurersArray.length === 0) {
      console.warn('⚠️ insurer пустой или не определен');
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

    // Подготавливаем базовые параметры для нового REST API (filter-advanced)
    let baseParams = {
      format: 'html',
    };

    // Добавляем employees_count если есть
    if (count && count !== '') {
      baseParams.employees_count = parseInt(count);
    }

    // Добавляем boolean параметры услуг
    // Поликлиника и Стоматология всегда включены (обязательные услуги)
    baseParams.polyclinic = true;
    baseParams.dentistry = true;

    // Остальные услуги добавляем только если выбраны
    if (ambulance) {
      baseParams.ambulance = true;
    }
    if (hospitalization) {
      baseParams.hospitalization = true;
    }
    if (doctor_home) {
      baseParams.doctor_home = true;
    }

    // Нормализуем массив городов
    let citiesArray = [];
    if (region) {
      if (Array.isArray(region) && region.length > 0) {
        citiesArray = region;
      } else if (typeof region === 'string' && region !== '') {
        citiesArray = [region];
      }
    }

    // Если городов нет, выходим с ошибкой
    if (citiesArray.length === 0) {
      console.error('❌ Не выбран ни один город');
      control = true;
      $('.block-process').css('display', 'none');
      alert('Пожалуйста, выберите хотя бы один регион обслуживания');
      return false;
    }

    // Если страховщиков нет, выходим с ошибкой
    if (insurersArray.length === 0) {
      console.error('❌ Не выбран ни один страховщик');
      control = true;
      $('.block-process').css('display', 'none');
      alert('Пожалуйста, выберите хотя бы одного страховщика');
      return false;
    }

    // Формируем URL для нового REST API
    let apiUrl = baseUrl + '/wp-json/dmc/v1/filter-advanced';

    // Отладка: выводим данные которые будут отправлены
    console.log('REST API Debug - Базовые параметры:', baseParams);
    console.log('REST API Debug - Города для запросов:', citiesArray);
    console.log('REST API Debug - Страховщики для запросов:', insurersArray);

    // Показываем индикатор загрузки
    $('.block-process').css('display', 'block');

    // Функция для выполнения запроса для одной комбинации город+страховщик
    function makeRequestForCityAndInsurer(city, insurer, params) {
      return new Promise(function (resolve, reject) {
        let requestParams = $.extend({}, params, { city: city, insurer: insurer });

        $.ajax({
          type: 'GET',
          url: apiUrl,
          dataType: 'json',
          data: requestParams,
          success: function (response) {
            // Проверяем, не является ли ответ ошибкой WordPress REST API
            if (response && response.code && response.message) {
              console.warn(
                '⚠️ Ошибка для города ' + city + ', страховщик ' + insurer + ':',
                response.code,
                response.message
              );
              resolve({ city: city, insurer: insurer, html: '', error: response.message });
              return;
            }

            // Извлекаем HTML из ответа
            let htmlData = '';
            if (response && response.success && response.data) {
              htmlData = response.data;
            } else if (response && response.data) {
              htmlData = response.data;
            }

            resolve({ city: city, insurer: insurer, html: htmlData, error: null });
          },
          error: function (xhr, status, error) {
            console.error('❌ Ошибка запроса для города ' + city + ', страховщик ' + insurer + ':', error);
            let errorMessage = 'Ошибка при запросе данных';
            try {
              if (xhr.responseText) {
                let errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse && errorResponse.message) {
                  errorMessage = errorResponse.message;
                }
              }
            } catch (e) {
              // Игнорируем ошибки парсинга
            }
            resolve({ city: city, insurer: insurer, html: '', error: errorMessage });
          },
        });
      });
    }

    // Выполняем запросы для всех комбинаций город+страховщик параллельно
    let requests = [];
    citiesArray.forEach(function (city) {
      insurersArray.forEach(function (insurer) {
        requests.push(makeRequestForCityAndInsurer(city, insurer, baseParams));
      });
    });

    // Ждем завершения всех запросов
    Promise.all(requests)
      .then(function (results) {
        console.log('REST API Debug - Результаты всех запросов:', results);

        // Объединяем HTML результаты
        let combinedHtml = '';
        let hasResults = false;
        let errors = [];

        results.forEach(function (result) {
          if (result.error) {
            errors.push(result.city + (result.insurer ? ' (' + result.insurer + ')' : '') + ': ' + result.error);
          }
          if (result.html && result.html.length > 0) {
            // Проверяем, не является ли это сообщением об отсутствии результатов
            if (
              !result.html.includes('Нет результатов') &&
              !result.html.includes('Ошибка')
            ) {
              combinedHtml += result.html;
              hasResults = true;
            }
          }
        });

        // Если есть ошибки, выводим их в консоль
        if (errors.length > 0) {
          console.warn('⚠️ Ошибки при запросах для некоторых городов:', errors);
        }

        // Если нет результатов, показываем сообщение
        if (!hasResults || combinedHtml.length === 0) {
          combinedHtml = 'Нет результатов по заданным критериям';
        }

        // Отображаем результаты
        setTimeout(function () {
          control = true;
          $('.click-step1').removeClass('active');
          $('.block-process').css('display', 'none');
          $('.block-rezult__grid').html('');
          $('.block-rezult__grid').html(combinedHtml);
          $('.block-rezult').css('display', 'block');
          $('html, body')
            .stop()
            .animate(
              {
                scrollTop: $('#block-rezult').offset().top,
              },
              500
            );
        }, 10);
      })
      .catch(function (error) {
        console.error('❌ Критическая ошибка при выполнении запросов:', error);
        control = true;
        $('.block-process').css('display', 'none');
        alert('Произошла ошибка при получении данных. Попробуйте позже.');
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
    if (!validateInsurerSelect()) {
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
    // Для множественного выбора страховщика берем массив значений
    let insurerValue = $('.kviz-wrap .insurer-select').val();
    if (Array.isArray(insurerValue) && insurerValue.length > 0) {
      $('.coverage-level input').val(insurerValue.join(', '));
    } else if (insurerValue) {
      $('.coverage-level input').val(insurerValue);
    }
    $('.service-region input').val($('.kviz-wrap .input-wrp5 select').val());
    $('.fio input').val($('.kviz-wrap .input-wrp6 input').val());
    $('.email-kv input').val($('.kviz-wrap .input-wrp7 input').val());
    $('.phone-kv input').val($('.kviz-wrap .input-wrp8 input').val());
    //return false;
  });
});
