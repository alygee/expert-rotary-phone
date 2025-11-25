jQuery(document).ready(function($) {
	$('.faq-click').click(function(event) {
		if($(this).closest('li').hasClass('active')){
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
	$('.menu-top a, .menu-top-mob a, .footer-menu a').bind("click", function(e) {
	  	var anchor = $(this);
	  	$('html, body').stop().animate({
	  		scrollTop: $(anchor.attr('href')).offset().top
	  	}, 500);
	  	e.preventDefault();
	});

	function width_scroll(){
		let div = document.createElement('div');
		div.style.overflowY = 'scroll';
		div.style.width = '50px';
		div.style.height = '50px';
		document.body.append(div);
		let scrollWidth = div.offsetWidth - div.clientWidth;
		div.remove();
		return scrollWidth;
	}
	$(document).on('click', '.active-modal', function(event) {
		if($(this).hasClass('active-modal2')){
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
			$('.modal-bg2').prepend('<div class="prepend-title">'+$('.block-v1').html()+'</div>');
			$('.modal-bg2').prepend('<button title="Close (Esc)" type="button" class="modal-tn__close">×</button>');
			$('.modal-window2 .input-wrp').css('display', 'block');
			$('.modal-window2 .btn-submit2').css('display', 'block');
			$('.modal-window2 .click-step1').css('display', 'none');

		}
		var modal = $(this).attr('href'); 
		$(modal).addClass('popup_show');
		$('body').addClass('popup-show-body');
		$('body').css('padding-right', width_scroll()+'px');
	 	$(document).bind('click.myEvent2', function(e) {
		    if ($(e.target).closest('.popup_show .modal-tn__wrp').length == 0) { 
				if($('.modal-window2').hasClass('popup_show')){
					$('.kviz-wrp').each(function() {
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
	    $(document).one('click', '.modal-tn__close', function(event) {
			if($('.modal-window2').hasClass('popup_show')){
				$('.kviz-wrp').each(function() {
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
	$('.click-menu').click(function(event) {
		$(this).toggleClass('active');
		$(this).next('.mob-menu-wrap').slideToggle(300);
		return false;
	});
	$('.main-select').mySelect();

	$('.region-select').tokenize2({
		tokensAllowCustom: true,
		placeholder: 'Введите название региона...',
		//displayNoResultsMessage: true,
		//searchMinLength: 0,
		//dropdownMaxItems: 9999
	});

	$('.tokenize').prepend('<span class="tokenize-gl"></span>');

	$('.region-select').on('tokenize:focus', function(e){
	    $(this).trigger('tokenize:search', ['']);
	});




	$('.tokenize-gl').on('click', function () {
        let $input = $('.region-select').siblings('.tokenize').find('input');
        $input.focus().trigger(jQuery.Event('keydown', { keyCode: 40 }));
    });


	//***

	// Валидация количество сотрудников
	function validateCount() {
		let value = $('.validate1').val();
		let _this1  = $('.validate1');
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
		let _this2  = $('.validate2');
		let errorMsg2 = $('.errorMsg2');
        let regex = /^[A-Za-zА-Яа-яЁё0-9\s"'\-\.]{2,150}$/;
        if (value === '') {
            errorMsg2.text('Поле обязательно для заполнения').show();
            _this2.addClass('error');
            return false;
        } else if (!regex.test(value)) {
            errorMsg2.text('Недопустимые символы а также длинна должна быть(2–150 символов)').show();
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
		let _this3  = $('.validate3');
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
		let _this4  = $('.validate4');
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
		let _this5  = $('.validate5');
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
		let _this5  = $('.validate5');
		let errorMsg5 = $('.errorMsg5');
		let _this6  = $('.validate6');
		let errorMsg6 = $('.errorMsg6');

		let errorMsg55 = $('.errorMsg55');
		let errorMsg66 = $('.errorMsg66');

    	if(value1 == '' && value2 == ''){
    		_this5.addClass('error');
    		_this6.addClass('error');
    		errorMsg55.text('Введите телефон или емаил.').show();
    		errorMsg66.text('Введите телефон или емаил.').show();
    		return false;
    	}else{
    		_this5.removeClass('error');
    		_this6.removeClass('error');
    		errorMsg55.hide();
    		errorMsg66.hide();
    		return true;
    	}
    }

var control = true;
	$(document).on('click', '.click-step1, .click-step2', function(event) {
		if(control === false) return false;
		var error_validate = true;

		// ***
        if (!validateCount()) {
           error_validate = false;
        }
        if (!validateRegionSelect()) {
           error_validate = false;
        }
		if(error_validate === false){
			return false;
		}

		if($('.block-rezult').css('display') == 'block' && !$(this).hasClass('click-step2')){
			$('.block-rezult').css('display', 'none');
			$('.block-top__wrap').addClass('block-top__wrp-final');
			return false;
		}
		$('.update-rez').css('display', 'block');

		if($(this).hasClass('click-step1')){
			$(this).css('display', 'none');
		}
		let ajaxurl = $('.footer').attr('data-home')+'wp-admin/admin-ajax.php';
		let count = $('.kviz-wrap .input-wrp2 input').val();
		let level = $('.kviz-wrap .input-wrp4 .main-select').val();
		let region  = $('.kviz-wrap .input-wrp5 .region-select').val();
		control = false;
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: "html",
			data: {
				'count': count,
				'level': level,
				'region': region,
				action: 'action'
			},
			success: function(data) {
				//alert(data)
				
				setTimeout(function() {
					control = true;
					$('.click-step1').removeClass('active');
					$('.block-process').css('display', 'none');
					$('.block-rezult__grid').html('');
					$('.block-rezult__grid').html(data);
					$('.block-rezult').css('display', 'block');
					var anchor = $(this);
				  	$('html, body').stop().animate({
				  		scrollTop: $('#block-rezult').offset().top
				  	}, 500);

				}, 10);
			},
			beforeSend: function(data) {
				$('.block-process').css('display', 'block');
			},
			error: function() {
				alert("Возникла ошибка при отправке");
			}
		});

		return false;
	});

	$(document).on('click', '.kviz-wrap .wpcf7-submit', function(event) {
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
		if(error_validate === false){
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
	if($('.phone-mask').hasClass('phone-mask')){
		$('.phone-mask').mask("+7 (999) 999-99-99");
	}
	$('.modal-window-form .wpcf7-submit').click(function(event) {
		if(!$(this).closest('form').find('.checkbox-wrp input').prop('checked')){
			alert('Согласитесь с условиями.');
			return false;
		}
	});
	$('.about-list__item').hover(function() {
		if($(this).find('.about-list-name').hasClass('active')) return false;
		$('.about-list__item').find('.about-list-name').removeClass('active');
		$(this).find('.about-list-name').addClass('active');
		var data_img = $(this).attr('data-img');
		$('.about-img').css('display', 'none');
		$('.about-img'+data_img).fadeIn(100);
	}, function() {
		/* Stuff to do when the mouse leaves the element */
	});

	$(".wpcf7").on('wpcf7mailsent', function(event){
		if($(this).closest('.modal-base-step').hasClass('modal-base-step')){
			$(this).closest('.modal-base-step').css('display', 'none');
			$(this).closest('.modal-base-step').next('.modal-open').css('display', 'flex');
		}
	}); 
});