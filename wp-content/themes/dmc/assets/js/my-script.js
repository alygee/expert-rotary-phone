jQuery(document).ready(function ($) {
  $('.faq-click').click(function (event) {
    if ($(this).closest('li').hasClass('active')) {
      $(this).closest('li').removeClass('active');
      $(this).next('.faq-wrap').slideUp(300);
      return false;
    }
    $('.faq-list li').removeClass('active');
    $(this).closest('li').addClass('active');
    $('.faq-wrap').css('display', 'none');
    $(this).next('.faq-wrap').slideToggle(300);
    return false;
  });

  /* обработка клика на пункт меню */
  $('.menu-top a, .menu-top-mob a, .footer-menu a').bind('click', function (e) {
    var anchor = $(this);
    $('html, body')
      .stop()
      .animate({ scrollTop: $(anchor.attr('href')).offset().top }, 500);
    e.preventDefault();
  });

  function width_scroll() {
    let div = document.createElement('div');
    div.style.overflowY = 'scroll';
    div.style.width = '50px';
    div.style.height = '50px';
    document.body.append(div);
    let scrollWidth = div.offsetWidth - div.clientWidth;
    div.remove();
    return scrollWidth;
  }
  $(document).on('click', '.active-modal', function (event) {
    if ($(this).hasClass('active-modal2')) {
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
      $('.modal-bg2').prepend(
        '<div class="prepend-title">' + $('.block-v1').html() + '</div>'
      );
      $('.modal-bg2').prepend(
        '<button title="Close (Esc)" type="button" class="modal-tn__close">×</button>'
      );
      $('.modal-window2 .input-wrp').css('display', 'block');
      $('.modal-window2 .btn-submit2').css('display', 'flex');
      $('.modal-window2 .click-step1').css('display', 'none');
    }
    var modal = $(this).attr('href');
    $(modal).addClass('popup_show');
    $('body').addClass('popup-show-body');
    $('body').css('padding-right', width_scroll() + 'px');
    $(document).bind('click.myEvent2', function (e) {
      if ($(e.target).closest('.popup_show .modal-tn__wrp').length == 0) {
        if ($('.modal-window2').hasClass('popup_show')) {
          $('.kviz-wrp').each(function () {
            $(this).closest('#modal-window2').replaceWith(this);
          });
          $('.kviz-wrp .input-wrp').removeAttr('style');
          $('.btn-submit2').removeAttr('style');
          //$('.click-step1').removeAttr('style');
          $('.kviz-wrp .wpcf7-form').addClass('init');
          $('.kviz-wrp .wpcf7-form').attr('data-status', 'init');
          $('.kviz-wrp .wpcf7-form').removeClass('sent');
          $('.update-rez').css('display', 'block');
        }
        $(modal).removeClass('popup_show');
        $('body').removeClass('popup-show-body');
        $('body').removeAttr('style');
        $(document).unbind('click.myEvent2');
      }
    });
    $(document).one('click', '.modal-tn__close', function (event) {
      if ($('.modal-window2').hasClass('popup_show')) {
        $('.kviz-wrp').each(function () {
          $(this).closest('#modal-window2').replaceWith(this);
        });
        $('.kviz-wrp .input-wrp').removeAttr('style');
        $('.btn-submit2').removeAttr('style');
        //$('.click-step1').removeAttr('style');

        $('.kviz-wrp .wpcf7-form').addClass('init');
        $('.kviz-wrp .wpcf7-form').attr('data-status', 'init');
        $('.kviz-wrp .wpcf7-form').removeClass('sent');
        $('.update-rez').css('display', 'block');
      }
      $(modal).removeClass('popup_show');
      $('body').removeClass('popup-show-body');
      $('body').removeAttr('style');
      $(document).unbind('click.myEvent');
    });

    return false;
  });

  $('.hamburger-button').click(function (event) {
    $(this).toggleClass('active');
    $(this).next('.collapsible-menu').slideToggle(300);
    return false;
  });

  if ($('.phone-mask').hasClass('phone-mask')) {
    $('.phone-mask').mask('+7 (999) 999-99-99');
  }
  $('.modal-window-form .wpcf7-submit').click(function (event) {
    if (!$(this).closest('form').find('.checkbox-wrp input').prop('checked')) {
      alert('Согласитесь с условиями.');
      return false;
    }
  });
  $('.about-list__item').hover(
    function () {
      if ($(this).find('.about-list-name').hasClass('active')) return false;
      $('.about-list__item').find('.about-list-name').removeClass('active');
      $(this).find('.about-list-name').addClass('active');
      var data_img = $(this).attr('data-img');
      $('.about-img').css('display', 'none');
      $('.about-img' + data_img).fadeIn(100);
    },
    function () {
      /* Stuff to do when the mouse leaves the element */
    }
  );

  $('.wpcf7').on('wpcf7mailsent', function (event) {
    if ($(this).closest('.modal-base-step').hasClass('modal-base-step')) {
      $(this).closest('.modal-base-step').css('display', 'none');
      $(this)
        .closest('.modal-base-step')
        .next('.modal-open')
        .css('display', 'flex');
    }
  });
});
