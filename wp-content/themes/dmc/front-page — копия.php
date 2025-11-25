<?php //get_header(); ?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="telephone=no" name="format-detection">
	<meta name="theme-color" content="#F1F1F5">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<header class="header base-container">
		<div class="wrap d-flex d-jm">
			<div class="header__left d-flex d-m">
				<a class="header__logo" href="<?php bloginfo('url'); ?>">
					<img src="<?php bloginfo('template_url'); ?>/img/logo.webp" alt="">
				</a>
				<span class="header__logo-txt">Сервис по подбору медицинской страховки компаниям</span>
			</div>
			<div class="header__right d-flex d-m">
				<a class="mail" href="mailto:info@kubiki.ru">
					<span class="ico">
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
							<path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50" />
							<path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50" />
						</svg>
					</span>
				   info@kubiki.ru
				</a>
				<a class="tel" href="tel:88004458866">
					<span class="ico">
						<svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_8053_747)">
							<path d="M13.6403 9.8875C13.6044 9.85906 11 7.98175 10.2851 8.1165C9.94387 8.17688 9.74875 8.40963 9.35719 8.87556C9.29419 8.95081 9.14281 9.13106 9.02513 9.25925C8.77767 9.17854 8.53629 9.08029 8.30281 8.96525C7.09773 8.37857 6.12406 7.4049 5.53737 6.19981C5.42225 5.96637 5.324 5.72498 5.24337 5.4775C5.372 5.35938 5.55225 5.208 5.62925 5.14325C6.093 4.75387 6.32619 4.55875 6.38656 4.21662C6.51038 3.50787 4.63437 0.8855 4.61469 0.861875C4.52957 0.740281 4.41849 0.639127 4.28948 0.565733C4.16047 0.492338 4.01676 0.448537 3.86875 0.4375C3.10837 0.4375 0.9375 3.25369 0.9375 3.72794C0.9375 3.7555 0.977313 6.55725 4.43225 10.0717C7.94319 13.5227 10.7445 13.5625 10.7721 13.5625C11.2467 13.5625 14.0625 11.3916 14.0625 10.6312C14.0516 10.4838 14.008 10.3405 13.935 10.2119C13.862 10.0833 13.7614 9.97248 13.6403 9.8875Z" fill="#4CAF50" />
							</g>
							<defs>
							<clipPath id="clip0_8053_747">
							  <rect width="14" height="14" fill="white" transform="translate(0.5)" />
							</clipPath>
							</defs>
						</svg>
					</span>
					8 800 445 88 66
				</a>
				<a class="btn active-modal" href="#modal-window">Обратный звонок</a>
			</div>
			<a href="tel:88004458866" class="tel-mob"></a>
			<div class="mob-menu">
				<a class="click-menu" href="#">
					<span></span>
					<span></span>
					<span></span>
				</a>
				<div class="mob-menu-wrap">
				    <ul class="menu-top-mob">
				        <li><a href="#hed1">Что такое ДМС</a></li>
				        <li><a href="#hed2">Партнеры</a></li>
				        <li><a href="#hed3">Условия программ</a></li>
				        <li><a href="#hed4">ДМС поможет</a></li>
				        <li><a href="#hed5">Поддержка</a></li>
				        <li><a href="#hed6">Часто задаваемые вопросы</a></li>
				        <li><a href="#hed7">Контакты</a></li>
				    </ul>
				    <div class="search-block">
				    	<form action="#" method="get" accept-charset="utf-8">
				    		<input type="text" placeholder="Поиск по сайту">
				    		<input type="submit" value="">
				    	</form>
				    </div>
				    <a class="mail" href="mailto:info@kubiki.ru">
						<span class="ico">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
								<path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50"></path>
								<path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50"></path>
							</svg>
						</span>
					   		info@kubiki.ru
					</a>
				</div>
			</div>
		</div>
		<nav class="wrap nav-wrp d-flex d-jm">
		    <ul class="menu-top d-flex">
		        <li><a href="#hed1">Что такое ДМС</a></li>
		        <li><a href="#hed2">Партнеры</a></li>
		        <li><a href="#hed3">Условия программ</a></li>
		        <li><a href="#hed4">ДМС поможет</a></li>
		        <li><a href="#hed5">Поддержка</a></li>
		        <li><a href="#hed6">Часто задаваемые вопросы</a></li>
		        <li><a href="#hed7">Контакты</a></li>
		    </ul>
		    <div class="search-block">
		    	<form action="#" method="get" accept-charset="utf-8">
		    		<input type="text" placeholder="Поиск по сайту">
		    		<input type="submit" value="">
		    	</form>
		    </div>
		</nav>
	</header>

<?php
$csv = get_bloginfo('template_url')."/list.csv"; 
$rows = [];
if (($handle = fopen($csv, "r")) !== false) {
    $headers = fgetcsv($handle); // читаем первую строку (заголовки)
    while (($data = fgetcsv($handle, 0, ",")) !== false) {
        $rows[] = array_combine($headers, $data);
    }
    fclose($handle);
}

//print_r($rows);

//$cities = array_column($rows, "Город"); // достанем только города
//$uniqueCities = array_values(array_unique($cities)); // оставим только уникальные

//var_dump(sity());
	?>


	<section class="base-container block-top">
		<div class="wrap">
			<!-- block-top__wrp-final -->
			<div class="block-top__wrap">
				<div class="block-top__wrp h2-style">
					<div class="block-v1">
						<h2>Подберем самые выгодные  предложения за <span>3 минуты</span></h2>
						<span class="sub-zag">Ответьте на три вопроса, и мы предложим вам уникальный вариант, соответствующий вашим требованиям</span>
					</div>
					<div class="block-v2">
						<h2>Оформление заявки  <br>на медицинское страхование</h2>
						<span>Заполните все поля, чтобы получить самые выгодные условия <br>для вашей компании</span>
					</div>
					<div class="kviz-wrap">
						<form action="#" method="get" accept-charset="utf-8">
							<h3 class="kviz-title">Расскажите о коллективе</h3>
							<div class="kviz-wrp d-flex-wrap d-j">
								<div class="input-wrp input-wrp1">
									<div class="label label1">Наименование компании</div>
									<input type="text" placeholder="Наименование компании">
								</div>
								<div class="input-wrp input-wrp2 style-bl">
									<div class="label label-k1">Количество сотрудников</div>
									<input type="text" placeholder="128">
								</div>
								<div class="input-wrp input-wrp3 style-bl">
									<div class="label label-kt1">ИНН</div>
									<input type="text" placeholder="ИНН">
								</div>
								<div class="input-wrp input-wrp4">
									<div class="label label-k2">Уровень покрытия</div>
									<select class="main-select" name="nothing" >
										<option value="Эконом">Эконом</option>
										<option value="Стандарт">Стандарт</option>
										<option value="Бизнес">Бизнес</option>
									</select> 
								</div>
								<div class="input-wrp input-wrp5">
									<div class="label label-k3">Регион обслуживания</div>
									<select class="region-select" multiple>
										<?php 
											if(!empty(sity())){
												foreach (sity() as $value) {
													echo '<option value="'.$value.'">'.$value.'</option>';
												} 
											}	
										?>
									</select>
								</div>
								<div class="input-wrp input-wrp6 style-bl">
									<div class="label label-kt2">ФИО ответственного</div>
									<input type="text" placeholder="Введите ФИО ответственного">
								</div>
								<div class="input-wrp input-wrp7 style-bl">
									<div class="label label-kt4">Введите рабочую почту</div>
									<input type="email" placeholder="Рабочая почта">
								</div>
								<div class="input-wrp input-wrp8 style-bl">
									<div class="label label-kt3">Телефон</div>
									<input type="tel" placeholder="Введите номер телефона">
								</div>
								<div class="input-wrp input-wrp9">
									<div class="btn-submit-wrp">
										<button class="btn-submit btn-submit1 click-step1" type="button">
										    Дальше
											<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
											    <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
											</svg>
									    </button>
										<button class="btn-submit btn-submit2" type="submit">
										    Отправить
											<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
											    <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
											</svg>
									    </button>
							    	</div>
								</div>
							</div>
						</form>
					</div>

				</div>
			</div>
			<!-- <div class="block-process d-flex d-m">
				<div class="block-process__wrp">
					<h3>Подождите, ведем расчет</h3>
					<span>Сравните предложения от страховых компаний, выберите лучшее и оформите полис без визита в офис</span>
					<img class="loader" src="img/loader.svg" alt="">
				</div>
			</div> -->

			<div class="block-rezult">
				<div class="block-rezult__top d-flex d-m">
					<h3>Предложение для вас</h3>
					<div>Уникальное предложение, соответствующее вашим параметрам, доступно по спецусловиям в течение <span>2 часов</span></div>
				</div>
				<div class="block-rezult__grid grid">

					<!-- <div class="block-rezult__item">
						<div class="rating">
							<div class="rating-in d-flex d-m">
								<div class="rating-wrp d-flex">
									<i class="active"></i>
									<i></i>
									<i></i>
									<i></i>
									<i></i>
								</div>
								<div class="tarif-name">Тариф «Эконом»</div>
							</div>
						</div>
						<div class="rezult-top d-flex d-jm">
							<h5>Ингосстрах</h5>
							<div class="rezult-top__price">
								<span class="price-r">6 200 ₽</span>
								<span class="desc-r">в год за человека</span>
							</div>
						</div>	
						<div class="rezult-data">
							<ul>
								<li>
									Онлайн-консультации врачей “Лучи”
									<div class="li-val">
										<i class="li-val__hover"></i>
										<div class="li-val__wrp">
											<span>6 200 ₽</span>
										</div>
									</div>
								</li>
								<li>
									Онлайн-консультации врачей “Лучи”
									<div class="li-val">
										<i class="li-val__hover"></i>
										<div class="li-val__wrp">
											<span>6 200 ₽</span>
										</div>
									</div>
								</li>
								<li>
									Онлайн-консультации врачей “Лучи”
									<div class="li-val">
										<i class="li-val__hover"></i>
										<div class="li-val__wrp">
											<span>6 200 ₽</span>
										</div>
									</div>
								</li>
								<li class="no-r">
									Онлайн-консультации врачей “Лучи”
									<div class="li-val">
										<i class="li-val__hover"></i>
										<div class="li-val__wrp">
											<span>6 200 ₽</span>
										</div>
									</div>
								</li>
							</ul>
						</div>
						<a class="btn4 btn-style-new" href="#">Оформить</a>
					</div> -->

				</div>
			</div>

		</div>
	</section>
	<section id="hed1" class="control base-container">	
		<div class="wrap d-flex d-j">
			<div class="control__left">
				<h3>Если <span>вы управляете бизнесом</span>, вы точно сталкивались с этим:</h3>
				<div class="pl-wrap pl-wrap1 d-flex">
					<div class="pl-item pl-item1">
						<img src="<?php bloginfo('template_url'); ?>/img/ico1.svg" alt="">
						<span class="pl-item__title">Сотрудники уходят  за «плюшками»  в крупные компании</span>
					</div>
					<div class="pl-item pl-item2">
						<img src="<?php bloginfo('template_url'); ?>/img/ico2.svg" alt="">
						<span class="pl-item__title">Зарплатную вилку уже некуда расширять</span>
					</div>
				</div>
			</div>
			<div class="control__right">			
				<div class="pl-wrap pl-wrap2 d-flex">
					<div class="pl-item pl-item3">
						<img src="<?php bloginfo('template_url'); ?>/img/ico3.svg" alt="">
						<span class="pl-item__title">HR сложно понять, где найти самую выгодную ДМС</span>
					</div>
					<div class="pl-item pl-item4">
						<img src="<?php bloginfo('template_url'); ?>/img/ico4.svg" alt="">
						<span class="pl-item__title">Налоги растут — а как их оптимизировать неясно</span>
					</div>
					<div class="pl-item pl-item5">
						<img src="<?php bloginfo('template_url'); ?>/img/ico5.svg" alt="">
						<span class="pl-item__title">Коммуникация со страховыми сложная,  а покупка ДМС непрозрачная</span>
					</div>
				</div>
				<div class="txt-int">
					Проблемы
					<span>Бизнеса</span>
				</div>
			</div>
		</div>
		<img class="control-img control-img-desc" src="<?php bloginfo('template_url'); ?>/img/img1.webp" alt="">
		<div class="control-img-mob">
			<img class="control-img" src="<?php bloginfo('template_url'); ?>/img/img1-mob.png" alt="">
		</div>
	</section>
	<section id="hed2" class="base-container insurance">
		<div class="wrap h2-style">
			<h2>Добровольное медицинское страхование / ДМС- это</h2>
			<div class="insurance__row grid">
				<div class="insurance__item li-style">
					
					<span class="number">01</span>
					<div class="insurance__item-wrp">
					    <h3>Медицинская помощь для ваших сотрудников</h3>
					    <ul>
						    <li><b>Лечение в частных клиниках</b> <br>с повышенным комфортом </li>
						    <li><b>Отсутствие очередей</b> в записи <br>к нужному специалисту</li>
					    </ul>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/f1-img.webp" alt="">
				</div>
				<div class="insurance__item li-style">
					<span class="number">02</span>
					<div class="insurance__item-wrp">
					    <h3>Налоговые <br>льготы</h3>
					    <ul>
						    <li>Расходы на корпоративное <br>ДМС помогают <b>снизить налогооблагаемую базу</b></li>
					    </ul>
					</div>
				</div>
				<div class="insurance__item li-style">
					<span class="number">03</span>
					<div class="insurance__item-wrp">
					    <h3>Имидж надежного работодателя</h3>
					    <ul>
						   <li>Наличие ДМС повышает привлекательность вакансий компании и <b>увеличивает лояльность сотрудников</b></li>
					    </ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="hed3" class="about base-container">
		<div class="wrap h2-style">
			<div class="about__top d-flex">
				<span class="st-btn">О компании</span>
				<h2>Мы – первый цифровой продукт по рассчету, подключению и управлению ДМС</h2>
			</div>
			<div class="about-list">
				<img class="about-img" src="<?php bloginfo('template_url'); ?>/img/about.webp" alt="">
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name active">Цены на ДМС</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Показываем, сколько реально стоит ДМС и что он включает	
						</p>
						<span class="list-number">01</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob1.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Помогаем с выбором</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Сравниваем предложения  от проверенных страховщиков
						</p>
						<span class="list-number">02</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob2.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Индивидуальный подход</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Учитываем специфику вашей команды
						</p>
						<span class="list-number">03</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob3.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Помогаем с налогами</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Снижаем налогооблагаемую базу
						</p>
						<span class="list-number">04</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob4.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Оптимизация</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Оптимизируем существующий договор и предлагаем лучшее наполнение по цене-качеству
						</p>
						<span class="list-number">05</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob5.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Экспертиза</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Помогаем бизнесу подключить самый выгодный ДМС с учетом особенностей вашей команды
						</p>
						<span class="list-number">06</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob6.webp" alt="">
				</div>
				<div class="about-list__item d-flex d-jm">
					<span class="about-list-name">Цифровизация</span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							Простой и понятный сервис для hr  по управлению ДМС
						</p>
						<span class="list-number">07</span>
					</div>
					<img src="<?php bloginfo('template_url'); ?>/img/mob7.webp" alt="">
				</div>
				
			</div>
		</div>
	</section>
	<section id="hed4" class="base-container selection">
		<div class="wrap">
			<div class="selection__top">
				<h3>
					Получите подборку ДМС-программ <br>c понятными условиями от ведущих страховых компаний 
				</h3>
				<span>Переходите в наш Telegram-чат или оставьте свой телефон, и мы перезвоним вам в течение 5 минут, чтобы все рассказать!</span>
				<div class="group-btn d-m d-c d-flex">
					<a class="btn1 btn-style" href="#">Написать в Telegram</a>
					<a class="btn2 btn-style" href="#">Заказать обратный звонок</a>
				</div>
			</div>
			<div class="cases h2-style">
				<h2>Кейсы и клиенты</h2>
				<div class="cases__row grid">
					<div class="cases__item cases__item1">
						<img class="ico-cases" src="<?php bloginfo('template_url'); ?>/img/cases-ico1.svg" alt="">
						<span class="mob-span">Снижение текучки  <br>на 15–30%,  где уже внедрили ДМС</span>
					</div>
					<div class="cases__item cases__item2">
						<img class="ico-cases" src="<?php bloginfo('template_url'); ?>/img/cases-ico2.svg" alt="">
						<span>Премия FinNext IVI</span>
					</div>
					<div class="cases__item cases__item3">
						<img class="bg-cases" src="<?php bloginfo('template_url'); ?>/img/cases-img1.webp" alt="">
						<span>Оптимизация <br>и улучшение покрытия  <br>по текущему договору ДМС</span>
					</div>
					<div class="cases__item cases__item4">
						<img class="ico-cases" src="<?php bloginfo('template_url'); ?>/img/cases-ico3.svg" alt="">
					</div>
					<div class="cases__item cases__item5">
						<span>Снижение текучки  <br>на 15–30%, где  <br>уже внедрили ДМС</span>
					</div>
					<div class="cases__item cases__item6">
						<img class="bg-cases" src="<?php bloginfo('template_url'); ?>/img/cases-img2.webp" alt="">
						<span>Экономия  <br>и оптимизация бюджета <br>на 10%-15%</span>
					</div>
					<div class="cases__item cases__item7">
						<img class="ico-cases" src="<?php bloginfo('template_url'); ?>/img/cases-ico4.svg" alt="">
						<span>Без головной боли — <br>мы на стороне клиента, <br>а не страховщика</span>
					</div>
					<div class="cases__item cases__item8">
						<span>Прозрачность  <br>и контроль — понимаете <br>из чего состоит ДМС  <br>и можете им управлять</span>
					</div>
				</div>
				<a href="#" class="btn3">
					Показать все
					<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
						<path d="M1 17L9 9L0.999999 0.999999" stroke="white" />
					</svg>
				</a>
			</div>
		</div>
	</section>
	<section id="hed5" class="base-container news">
		<div class="wrap h2-style">
			<h2>Новости компании</h2>
			<div class="news__row d-flex">
				<div class="news__left">
					<a href="#"><img src="<?php bloginfo('template_url'); ?>/img/img2.webp" alt=""></a>
				</div>
				<div class="news__right">
					<time datetime="2011-01-12">21.января в 21:00</time>
					<a class="title-url" href="#">Оптимизация  <br>и улучшение покрытия  <br>по текущему договору ДМС</a>
					<p>
						В современном мире автомобиль уже давно перестал быть просто средством передвижения. Для многих он стал способом выражения себя, своего стиля  и статуса. Отсюда вытекает стремление к идеальному внешнему виду авто
					</p>
					<div class="group-btn-new d-m d-flex">
						<a class="btn4 btn-style-new" href="#">Дальше</a>
						<a class="btn5 btn-style-new" href="#">
						    Показать
						<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
						    <path d="M1 17L9 9L0.999999 0.999999" stroke="#01A24D" />
						</svg>
					</a>
					</div>
				</div>
			</div>
			<div class="news__list grid">
				<div class="news__item">
					<a class="img-new-wrp" href="#"><img src="<?php bloginfo('template_url'); ?>/img/img3.webp" alt=""></a>
					<time datetime="2011-01-12">21.января в 21:00</time>
					<a class="news__item-more" href="#">Оптимизация  <br>и улучшение покрытия  <br>по текущему договору ДМС</a>
				</div>
				<div class="news__item">
					<a class="img-new-wrp" href="#"><img src="<?php bloginfo('template_url'); ?>/img/img4.webp" alt=""></a>
					<time datetime="2011-01-12">21.января в 21:00</time>
					<a class="news__item-more" href="#">Оптимизация  <br>и улучшение покрытия  <br>по текущему договору ДМС</a>
				</div>
				<div class="news__item">
					<a class="img-new-wrp" href="#"><img src="<?php bloginfo('template_url'); ?>/img/img5.webp" alt=""></a>
					<time datetime="2011-01-12">21.января в 21:00</time>
					<a class="news__item-more" href="#">Оптимизация  <br>и улучшение покрытия  <br>по текущему договору ДМС</a>
				</div>
				<div class="news__item">
					<a class="img-new-wrp" href="#"><img src="<?php bloginfo('template_url'); ?>/img/img6.webp" alt=""></a>
					<time datetime="2011-01-12">21.января в 21:00</time>
					<a class="news__item-more" href="#">Оптимизация  <br>и улучшение покрытия  <br>по текущему договору ДМС</a>
				</div>
			</div>
		</div>
	</section>
	<section id="hed6" class="base-container faq">
		<div class="wrap d-flex-wrap">
			<div class="faq__left">
				<div class="mdb">
					<h2>
						Часто задаваемые<br>
						<span><img src="<?php bloginfo('template_url'); ?>/img/faq-title.webp" alt=""> вопросы</span>
	       			</h2>
       			</div>
       			<div class="faq__left-bt">
       				<span class="span-d">Не нашли ответ?</span>
       				<span class="span-k">Задайте вопрос, заполнив форму</span>
	       			<a href="#" class="btn3">
						Связаться с нами
						<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
							<path d="M1 17L9 9L0.999999 0.999999" stroke="white"></path>
						</svg>
					</a>
       			</div>
			</div>
			<div class="faq__right">
				<ul class="faq-list">
					<li class="active">
						<a class="faq-click" href="#">
						    Исключения и ограничения
							<span>
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
								    <path d="M7.66699 6.33203H13.667V7.66602H7.66699V13.666H6.33398V7.66602H0.333984V6.33203H6.33398V0.332031H7.66699V6.33203Z" fill="white" />
								</svg>
								<svg width="14" height="2" viewBox="0 0 14 2" fill="none">
									<path d="M13.667 1.66602H0.333984V0.332031H13.667V1.66602Z" fill="white" />
								</svg>
							</span>
						</a>
						<div style="display: block;" class="faq-wrap">
							<div class="faq-wrap-in">
							<p>
								В случае возникновения дефектов и получения обоснованной претензии в течение Гарантийного периода, компания в рамках, установленных законом, <span>произведет бесплатный ремонт аппаратного оборудования с использованием новых запчастей или восстановленных запчастей, эквивалентных новым</span> по производительности и надежности.
							</p>
							<ul>
								<li>После замены изделия или аксессуара любой заменяющий элемент становится Вашей собственностью, а замененный элемент становится собственностью сервисного центра.</li>
								<li>Запчасти, предоставляемые сервисным центром в рамках исполнения гарантийных обязательств, должны использоваться в изделиях, на которые оформлено гарантийное обслуживание.</li>
							</ul>
							</div>
						</div>
					</li>
					<li>
						<a class="faq-click" href="#">
						    Резервные копии
							<span>
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
								    <path d="M7.66699 6.33203H13.667V7.66602H7.66699V13.666H6.33398V7.66602H0.333984V6.33203H6.33398V0.332031H7.66699V6.33203Z" fill="white" />
								</svg>
								<svg width="14" height="2" viewBox="0 0 14 2" fill="none">
									<path d="M13.667 1.66602H0.333984V0.332031H13.667V1.66602Z" fill="white" />
								</svg>
							</span>
						</a>
						<div class="faq-wrap">
							<div class="faq-wrap-in">
							<p>
								В случае возникновения дефектов и получения обоснованной претензии в течение Гарантийного периода, компания в рамках, установленных законом, <span>произведет бесплатный ремонт аппаратного оборудования с использованием новых запчастей или восстановленных запчастей, эквивалентных новым</span> по производительности и надежности.
							</p>
							<ul>
								<li>После замены изделия или аксессуара любой заменяющий элемент становится Вашей собственностью, а замененный элемент становится собственностью сервисного центра.</li>
								<li>Запчасти, предоставляемые сервисным центром в рамках исполнения гарантийных обязательств, должны использоваться в изделиях, на которые оформлено гарантийное обслуживание.</li>
							</ul>
							</div>
						</div>
					</li>
					<li>
						<a class="faq-click" href="#">
						    Исключения и ограничения
							<span>
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
								    <path d="M7.66699 6.33203H13.667V7.66602H7.66699V13.666H6.33398V7.66602H0.333984V6.33203H6.33398V0.332031H7.66699V6.33203Z" fill="white" />
								</svg>
								<svg width="14" height="2" viewBox="0 0 14 2" fill="none">
									<path d="M13.667 1.66602H0.333984V0.332031H13.667V1.66602Z" fill="white" />
								</svg>
							</span>
						</a>
						
						<div class="faq-wrap">
							<div class="faq-wrap-in">
							<p>
								В случае возникновения дефектов и получения обоснованной претензии в течение Гарантийного периода, компания в рамках, установленных законом, <span>произведет бесплатный ремонт аппаратного оборудования с использованием новых запчастей или восстановленных запчастей, эквивалентных новым</span> по производительности и надежности.
							</p>
							<ul>
								<li>После замены изделия или аксессуара любой заменяющий элемент становится Вашей собственностью, а замененный элемент становится собственностью сервисного центра.</li>
								<li>Запчасти, предоставляемые сервисным центром в рамках исполнения гарантийных обязательств, должны использоваться в изделиях, на которые оформлено гарантийное обслуживание.</li>
							</ul>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>
	<footer data-home="<?php bloginfo('url'); ?>/" id="hed7" class="base-container footer">
		<div class="wrap grid">
			<div class="footer__left">
				<span class="foot-title">Нужна помощь в расчете?</span>
				<p>
					Оставьте заявку, и наш менеджер проконсультирует вас по самым лучшим и подходящим условиям медицинского страхования  <br>для вашей компании
				</p>
				<a class="btn4 btn-style-new" href="#">Связаться с нами</a>
			</div>
			<div class="footer__right">
				<div class="foot-top d-flex-wrap">
					<div class="foot-top__item foot-top__item1">
						<a class="foot-top__logo" href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('template_url'); ?>/img/logo-foot.svg" alt=""></a>
						<span>
							Сервис по подбору медицинской <br>страховки компаниям
						</span>
					</div>
					<div class="foot-bottom foot-bottom-mob d-flex">
						<a class="tel-foot" href="tel:88004458866">
							<svg width="48" height="49" viewBox="0 0 48 49" fill="none">
								<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>
								<path d="M18.6471 17.834H21.9412L23.5882 21.9516L21.5294 23.1869C22.4114 24.9752 23.8587 26.4226 25.6471 27.3046L26.8824 25.2457L31 26.8928V30.1869C31 30.6238 30.8265 31.0427 30.5176 31.3516C30.2087 31.6605 29.7898 31.834 29.3529 31.834C26.1406 31.6388 23.1107 30.2746 20.835 27.9989C18.5594 25.7233 17.1952 22.6934 17 19.481C17 19.0442 17.1735 18.6253 17.4824 18.3164C17.7913 18.0075 18.2102 17.834 18.6471 17.834Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							8 800 445 88 66
						</a>
						<a class="email-foot" href="mailto:info@kubiki.ru">
							<svg width="48" height="49" viewBox="0 0 48 49" fill="none">
								<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>
								<path d="M30.6668 22.3161C30.6668 21.7737 30.0035 21.334 29.1853 21.334H18.815C17.9968 21.334 17.3335 21.7737 17.3335 22.3161M30.6668 22.3161V27.2268C30.6668 27.7693 30.0035 28.209 29.1853 28.209H18.815C17.9968 28.209 17.3335 27.7693 17.3335 27.2268V22.3161M30.6668 22.3161L24.0002 25.2626L17.3335 22.3161" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							info@kubiki.ru
						</a>
					</div>
					<div class="foot-top__item foot-top__item2">
						<ul class="footer-menu grid">
							<li><a href="#">О нас</a></li>
							<li><a href="#">Услуги</a></li>

							<li><a href="#">Вопросы</a></li>
							<li><a href="#">Контакты</a></li>

							<li><a href="#">Калькулятор</a></li>
							<li><a href="#">Новости</a></li>
						</ul>
					</div>
				</div>
				<div class="foot-bottom foot-bottom-desc d-flex">
					<a class="tel-foot" href="tel:88004458866">
						<svg width="48" height="49" viewBox="0 0 48 49" fill="none">
							<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>
							<path d="M18.6471 17.834H21.9412L23.5882 21.9516L21.5294 23.1869C22.4114 24.9752 23.8587 26.4226 25.6471 27.3046L26.8824 25.2457L31 26.8928V30.1869C31 30.6238 30.8265 31.0427 30.5176 31.3516C30.2087 31.6605 29.7898 31.834 29.3529 31.834C26.1406 31.6388 23.1107 30.2746 20.835 27.9989C18.5594 25.7233 17.1952 22.6934 17 19.481C17 19.0442 17.1735 18.6253 17.4824 18.3164C17.7913 18.0075 18.2102 17.834 18.6471 17.834Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						8 800 445 88 66
					</a>
					<a class="email-foot" href="mailto:info@kubiki.ru">
						<svg width="48" height="49" viewBox="0 0 48 49" fill="none">
							<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>
							<path d="M30.6668 22.3161C30.6668 21.7737 30.0035 21.334 29.1853 21.334H18.815C17.9968 21.334 17.3335 21.7737 17.3335 22.3161M30.6668 22.3161V27.2268C30.6668 27.7693 30.0035 28.209 29.1853 28.209H18.815C17.9968 28.209 17.3335 27.7693 17.3335 27.2268V22.3161M30.6668 22.3161L24.0002 25.2626L17.3335 22.3161" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						info@kubiki.ru
					</a>
				</div>
			</div>
		</div>
	</footer>

	<div id="modal-window2" class="modal-tn">
		<div class="modal-tn__wrap">
			<div class="modal-tn__wrp modal-bg">
               <button title="Close (Esc)" type="button" class="modal-tn__close">×</button>
				<div class="modal-top">
					<h3>Получите индивидуальную консультацию  <br>по лучшим условиям медицинского страхования</h3>
					<span>Введите корректно свои контактные данные, чтобы наш менеджер мог подобрать оптимальные условия для вас</span>
				</div>
				<div class="form-block form-block2">
					
				</div>
                
			</div>
		</div>
	</div>
	<div id="modal-window" class="modal-tn">
		<div class="modal-tn__wrap">
			<div class="modal-tn__wrp modal-bg">
               <button title="Close (Esc)" type="button" class="modal-tn__close">×</button>
				<div class="modal-top">
					<h3>Получите индивидуальную консультацию  <br>по лучшим условиям медицинского страхования</h3>
					<span>Введите корректно свои контактные данные, чтобы наш менеджер мог подобрать оптимальные условия для вас</span>
				</div>
				<div class="form-block">
					<form action="#" method="get" accept-charset="utf-8">
						<div class="form-wrap d-flex-wrap d-jm">
							<div class="input-wrp">
								<div class="label label1">Ваше имя</div>
								<input type="text" placeholder="Введите данные">
							</div>
							<div class="input-wrp">
								<div class="label label2">Ваш номер телефона</div>
								<input type="tel" placeholder="Введите данные">
							</div>
							<div class="input-wrp">
								<div class="btn-submit-wrp">
									<button class="btn-submit" type="submit">
									    Отправить
										<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
										    <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white" />
										</svg>
								    </button>
							    </div>
							    <div class="checkbox-wrp">
							    	<input checked="checked" type="checkbox">
							    	<div class="checkbox-wrp-txt">
							    		Я согласен(а) на обработку персональных данных согласно политике конфиденциальности
							    	</div>
							    </div>
							</div>
						</div>
					</form>
				</div>
                
			</div>
		</div>
	</div>

	<?php wp_footer(); ?>
	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/tokenize2.min.css">
	<!-- <script src="js/jquery-3.6.4.min.js"></script> -->
	<script src="<?php bloginfo('template_url'); ?>/js/swiper-bundle.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/js/tokenize2.min.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/js/mainselect.js"></script>
	<script src="<?php bloginfo('template_url'); ?>/js/my-script.js"></script> 
</body>
</html>

<?php //get_footer(); ?>


