jQuery(document).ready(function ($) {
  /**
   * Обработка клика на элементы FAQ (часто задаваемые вопросы)
   * Реализует аккордеон: при клике открывается/закрывается ответ на вопрос
   */
  $('.faq-click').click(function (event) {
    // Если текущий элемент уже активен, закрываем его
    if ($(this).closest('li').hasClass('active')) {
      $(this).closest('li').removeClass('active');
      $(this).next('.faq-wrap').slideUp(300);
      return false;
    }
    // Убираем активный класс со всех элементов FAQ
    $('.faq-list li').removeClass('active');
    // Добавляем активный класс текущему элементу
    $(this).closest('li').addClass('active');
    // Скрываем все ответы
    $('.faq-wrap').css('display', 'none');
    // Показываем ответ для текущего вопроса с анимацией
    $(this).next('.faq-wrap').slideToggle(300);
    return false;
  });

  /**
   * Обработка клика на пункт меню
   * Реализует плавную прокрутку к якорным ссылкам
   */
  $('.menu-top a, .menu-top-mob a, .footer-menu a').bind('click', function (e) {
    var anchor = $(this);
    // Плавная прокрутка к элементу, указанному в href
    $('html, body')
      .stop()
      .animate({ scrollTop: $(anchor.attr('href')).offset().top }, 500);
    e.preventDefault();
  });

  /**
   * Функция для вычисления ширины скроллбара браузера
   * Используется для компенсации сдвига контента при открытии модального окна
   * @returns {number} Ширина скроллбара в пикселях
   */
  function width_scroll() {
    let div = document.createElement('div');
    div.style.overflowY = 'scroll';
    div.style.width = '50px';
    div.style.height = '50px';
    document.body.append(div);
    // Разница между offsetWidth и clientWidth равна ширине скроллбара
    let scrollWidth = div.offsetWidth - div.clientWidth;
    div.remove();
    return scrollWidth;
  }

  /**
   * Обработка открытия модальных окон
   * Управляет показом/скрытием модальных окон и предотвращает прокрутку фона
   */
  $(document).on('click', '.active-modal', function (event) {
    // Специальная обработка для модального окна с классом active-modal2 (квиз)
    if ($(this).hasClass('active-modal2')) {
      // Обертываем квиз в структуру модального окна
      $('.kviz-wrp').wrap(`
        <div id="modal-window2" class="modal-tn modal-window2">
          <div class="modal-tn__wrap">
            <div class="modal-tn__wrp modal-base-step modal-bg modal-bg2"></div>
            <div class="modal-open modal-tn__wrp">
              <button title="Close (Esc)" type="button" class="modal-tn__close"></button>
              <div class="modal-open-wrp">
                <h2>Спасибо! Заявка успешно отправлена</h2>
                <span>Наш менеджер свяжется с вами в течение 30 минут</span>
              </div>
            </div>
          </div>
        </div>
      `);
      // Добавляем заголовок и кнопку закрытия в модальное окно квиза
      $('.modal-bg2').prepend(
        '<div class="prepend-title">' + $('.block-v1').html() + '</div>'
      );
      $('.modal-bg2').prepend(
        '<button title="Close (Esc)" type="button" class="modal-tn__close">×</button>'
      );
      // Настраиваем видимость элементов формы в модальном окне
      $('.modal-window2 .input-wrp').css('display', 'block');
      $('.modal-window2 .btn-submit2').css('display', 'flex');
      $('.modal-window2 .click-step1').css('display', 'none');
    }
    // Получаем селектор модального окна из атрибута href
    var modal = $(this).attr('href');
    // Показываем модальное окно
    $(modal).addClass('popup_show');
    $('body').addClass('popup-show-body');
    // Компенсируем сдвиг контента из-за скрытия скроллбара
    $('body').css('padding-right', width_scroll() + 'px');

    /**
     * Обработка клика вне модального окна для его закрытия
     */
    // $(document).bind('click.myEvent2', function (e) {
    //   // Если клик был вне области модального окна
    //   if ($(e.target).closest('.popup_show .modal-tn__wrp').length == 0) {
    //     // Если закрывается модальное окно квиза, восстанавливаем его исходное состояние
    //     if ($('.modal-window2').hasClass('popup_show')) {
    //       // Убираем обертку модального окна
    //       $('.kviz-wrp').each(function () {
    //         $(this).closest('#modal-window2').replaceWith(this);
    //       });
    //       // Сбрасываем стили элементов формы
    //       $('.kviz-wrp .input-wrp').removeAttr('style');
    //       $('.btn-submit2').removeAttr('style');
    //       //$('.click-step1').removeAttr('style');
    //       // Сбрасываем состояние формы к начальному
    //       $('.kviz-wrp .wpcf7-form').addClass('init');
    //       $('.kviz-wrp .wpcf7-form').attr('data-status', 'init');
    //       $('.kviz-wrp .wpcf7-form').removeClass('sent');
    //       $('.update-rez').css('display', 'block');
    //     }
    //     // Закрываем модальное окно
    //     $(modal).removeClass('popup_show');
    //     $('body').removeClass('popup-show-body');
    //     $('body').removeAttr('style');
    //     $(document).unbind('click.myEvent2');
    //   }
    // });

    /**
     * Обработка клика на кнопку закрытия модального окна
     */
    $(document).one('click', '.modal-tn__close', function (event) {
      // Если закрывается модальное окно квиза, восстанавливаем его исходное состояние
      if ($('.modal-window2').hasClass('popup_show')) {
        // Убираем обертку модального окна
        $('.kviz-wrp').each(function () {
          $(this).closest('#modal-window2').replaceWith(this);
        });
        // Сбрасываем стили элементов формы
        $('.kviz-wrp .input-wrp').removeAttr('style');
        $('.btn-submit2').removeAttr('style');
        //$('.click-step1').removeAttr('style');

        // Сбрасываем состояние формы к начальному
        $('.kviz-wrp .wpcf7-form').addClass('init');
        $('.kviz-wrp .wpcf7-form').attr('data-status', 'init');
        $('.kviz-wrp .wpcf7-form').removeClass('sent');
        $('.update-rez').css('display', 'block');
      }
      // Закрываем модальное окно
      $(modal).removeClass('popup_show');
      $('body').removeClass('popup-show-body');
      $('body').removeAttr('style');
      $(document).unbind('click.myEvent');
    });

    return false;
  });

  /**
   * Обработка клика на кнопку гамбургер-меню
   * Открывает/закрывает мобильное меню с анимацией
   */
  $('.hamburger-button').click(function (event) {
    $(this).toggleClass('active');
    $(this).next('.collapsible-menu').slideToggle(300);
    return false;
  });

  /**
   * Применение маски ввода для полей телефона
   * Форматирует ввод номера телефона в формате +7 (999) 999-99-99
   */
  if ($('.phone-mask').hasClass('phone-mask')) {
    $('.phone-mask').mask('+7 (999) 999-99-99');
  }

  /**
   * Валидация формы перед отправкой
   * Проверяет, что пользователь согласился с условиями (чекбокс отмечен)
   */
  $('.modal-window-form .wpcf7-submit').click(function (event) {
    if (!$(this).closest('form').find('.checkbox-wrp input').prop('checked')) {
      alert('Согласитесь с условиями.');
      return false;
    }
  });

  /**
   * Обработка наведения на элементы списка "О нас"
   * При наведении меняет активный элемент и показывает соответствующее изображение
   */
  $('.about-list__item').hover(
    function () {
      // Если элемент уже активен, ничего не делаем
      if ($(this).find('.about-list-name').hasClass('active')) return false;
      // Убираем активный класс со всех элементов
      $('.about-list__item').find('.about-list-name').removeClass('active');
      // Добавляем активный класс текущему элементу
      $(this).find('.about-list-name').addClass('active');
      // Получаем значение атрибута data-img для определения нужного изображения
      var data_img = $(this).attr('data-img');
      // Скрываем все изображения
      $('.about-img').css('display', 'none');
      // Показываем нужное изображение с плавным появлением
      $('.about-img' + data_img).fadeIn(100);
    },
    function () {
      /* Действия при уходе курсора с элемента (не реализовано) */
    }
  );

  /**
   * Обработка успешной отправки формы Contact Form 7
   * После отправки формы скрывает форму и показывает сообщение об успехе
   */
  $('.wpcf7').on('wpcf7mailsent', function (event) {
    // Если форма находится внутри модального окна
    if ($(this).closest('.modal-base-step').hasClass('modal-base-step')) {
      // Скрываем форму
      $(this).closest('.modal-base-step').css('display', 'none');
      // Показываем сообщение об успешной отправке
      $(this)
        .closest('.modal-base-step')
        .next('.modal-open')
        .css('display', 'flex');
    }
  });
});
