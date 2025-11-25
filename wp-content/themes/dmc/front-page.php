<?php
/*
Template Name: Homepage Template
*/
?>
<?php get_header(); ?>
	<section id="block-top" class="base-container block-top">
		<div class="wrap">
			<!-- block-top__wrp-final -->
			<div class="block-top__wrap">
				<div class="block-top__wrp h2-style">
					<div class="block-v1">
						<h2>Подберем самые выгодные  предложения за <span>3 минуты</span></h2>
						<span class="sub-zag">Укажите свои данные, и мы предложим вам уникальный вариант, соответствующий вашим требованиям</span>
					</div>
					<div class="block-v2">
						<h2>Оформление заявки  <br>на медицинское страхование</h2>
						<span>Заполните все поля, чтобы получить самые выгодные условия <br>для вашей компании</span>
					</div>
					<div class="kviz-wrap">
						<h3 class="kviz-title">Расскажите о коллективе</h3>
						<div class="kviz-wrp d-flex-wrap d-j">
							<div class="input-wrp input-wrp1">
								<div class="label label1">Наименование компании</div>
								<input name="1" class="validate2" type="text" placeholder="Наименование компании">
								<span class="errorMsg errorMsg2"></span>
							</div>
							<div class="input-wrp input-wrp2">
								<div class="label label-k1">Количество сотрудников</div>
								<input name="2" class="validate1" type="text" placeholder="128">
								<span class="errorMsg errorMsg1"></span>
							</div>
							<div class="input-wrp input-wrp3">
								<div class="label label-kt1">ИНН</div>
								<input name="3" class="validate3" type="text" placeholder="ИНН">
								<span class="errorMsg errorMsg3"></span>
							</div>
							<div class="input-wrp input-wrp4">
								<div class="label label-k2">
									Уровень покрытия
									<div class="label-hover">
										<a class="label-hover-hover" href="#">
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M14.4668 8C14.4668 4.42856 11.5714 1.5332 8 1.5332C4.42856 1.5332 1.5332 4.42856 1.5332 8C1.5332 11.5714 4.42856 14.4668 8 14.4668C11.5714 14.4668 14.4668 11.5714 14.4668 8ZM8 7.4668C8.29455 7.4668 8.5332 7.70545 8.5332 8V10.5775H8.778C9.07245 10.5776 9.3112 10.8168 9.3112 11.1113C9.31108 11.4057 9.07237 11.6444 8.778 11.6445H8C7.70552 11.6445 7.46691 11.4058 7.4668 11.1113V8.5332H7.22201C6.92755 8.53309 6.6888 8.29448 6.6888 8C6.6888 7.70552 6.92755 7.46691 7.22201 7.4668H8ZM8.00781 4.35547C8.30228 4.35549 8.5409 4.59423 8.54102 4.88867C8.54102 5.18321 8.30235 5.42251 8.00781 5.42253H8C7.70545 5.42253 7.4668 5.18322 7.4668 4.88867C7.46691 4.59422 7.70552 4.35547 8 4.35547H8.00781ZM15.5332 8C15.5332 12.1605 12.1605 15.5332 8 15.5332C3.83946 15.5332 0.466797 12.1605 0.466797 8C0.466797 3.83946 3.83946 0.466797 8 0.466797C12.1605 0.466797 15.5332 3.83946 15.5332 8Z" fill="#1A1A1A" />
											</svg>
										</a>
										<div class="label-hover-wrap">
											<b>Уровень покрытия</b>
											<ul>
												<li>Базовый: основное покрытие, включая обязательные медицинские услуги</li>
												<li>Комфорт: Расширенная помощь, включая амбулаторное лечение и доп. услуги</li>
												<li>Премиум: Полный пакет с приоритетным обслуживанием и доп. опциями</li>
											</ul>
										</div>
									</div>
								</div>
								<select class="main-select" name="4">
									<option value="Базовый">Базовый</option>
									<option value="Комфорт">Комфорт</option>
									<option value="Премиум">Премиум</option>
								</select> 
							</div>
							<div class="input-wrp input-wrp5">
								<div class="label label-k3">Регион обслуживания</div>
								<select class="region-select" multiple name="5">
									<?php 
										if(!empty(сity())){
											foreach (сity() as $value) {
												echo '<option value="'.$value.'">'.$value.'</option>';
											} 
										}	
									?>
								</select>
								<span class="region-error" style="color:red; display:none;">Выберите хотя бы один регион</span>
							</div>
							<div class="input-wrp input-wrp6">
								<div class="label label-kt2">ФИО ответственного</div>
								<input name="6" class="validate4" type="text" placeholder="Введите ФИО ответственного">
								<span class="errorMsg errorMsg4"></span>
							</div>
							<div class="input-wrp input-wrp7">
								<div class="label label-kt4">Введите рабочую почту</div>
								<input name="7" class="validate5" type="email" placeholder="Рабочая почта">
								<span class="errorMsg errorMsg5"></span>
								<span class="errorMsg errorMsg55"></span>
							</div>
							<div class="input-wrp input-wrp8">
								<div class="label label-kt3">Телефон</div>
								<input name="8" class="validate6 phone-mask" type="tel" placeholder="Введите номер телефона">
								<span class="errorMsg errorMsg6"></span>
								<span class="errorMsg errorMsg66"></span>
							</div>
							<div class="input-wrp input-wrp9">
								<div class="btn-submit-wrp">
									<button class="btn-submit btn-submit1 click-step1" type="button">
									    Дальше
										<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
										    <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
										</svg>
								    </button>

									<div class="update-rez">
										<button class="btn-submit click-step2" type="button">
										    Обновить
											<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
											    <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
											</svg>
									    </button>
									</div>
									<?php echo do_shortcode( '[contact-form-7 id="363f513" title="Контактная форма 1"]'); ?>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div style="display: none;" class="block-process d-flex d-m">
				<div class="block-process__wrp">
					<h3>Подождите, ведем расчет</h3>
					<span>Сравните предложения от страховых компаний, выберите лучшее и оформите полис без визита в офис</span>
					<img class="loader" src="<?php bloginfo('template_url'); ?>/img/loader.svg" alt="">
				</div>
			</div>
			<div id="block-rezult" style="display: none;" class="block-rezult">
				<!-- <div class="block-rezult__top d-flex d-m">
					<h3>Предложение для вас</h3>
					<div>Уникальное предложение, соответствующее вашим параметрам, доступно по спецусловиям в течение <span>2 часов</span></div>
				</div> -->
				<h3 class="prdl-dlvs">Предложения для вас</h3>
				<div class="block-large">
					<h2>Оу, вы крупная рыба! Для вас <br>индивидуальный расчет</h2>
					<span>Оставьте свои контактные данные, и наш менеджер вернется  <br>с коммерческим предложением</span>
					<div class="block-large__forma">
						<div class="form-block">
							<?php echo do_shortcode('[contact-form-7 id="3dfc896" title="Контактная форма-2"]'); ?>
						</div>
					</div>
				</div>
				<div class="block-rezult__grid grids">
				</div>
			</div>
		</div>
	</section>
	<section id="hed1" class="control base-container">	
		<div class="wrap d-flex d-j">
			<div class="control__left">
				<h3><?php the_field('zag1'); ?></h3>
				<div class="pl-wrap pl-wrap1 d-flex">
					<?php $i=0; while( have_rows('yp_list') ): the_row(); $i++; ?>
					<?php 
						$yp_list_ico = get_sub_field('yp_list_ico'); 
						$yp_list_zag = get_sub_field('yp_list_zag'); 
						if($i==3) break;
					?>
					<div class="pl-item pl-item<?php echo $i; ?>">
						<img src="<?php echo $yp_list_ico['url']; ?>" alt="<?php echo $yp_list_ico['alt']; ?>">
						<span class="pl-item__title"><?php echo $yp_list_zag; ?></span>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
			<div class="control__right">			
				<div class="pl-wrap pl-wrap2 d-flex">
					<?php reset_rows(); $k=0; while( have_rows('yp_list') ): the_row();  ?>
					<?php 
						$k++;
						$yp_list_ico = get_sub_field('yp_list_ico'); 
						$yp_list_zag = get_sub_field('yp_list_zag'); 
						if($k<3) continue;
					?>

					<div class="pl-item pl-item<?php echo $k; ?>">
						<img src="<?php echo $yp_list_ico['url']; ?>" alt="<?php echo $yp_list_ico['alt']; ?>">
						<span class="pl-item__title"><?php echo $yp_list_zag; ?></span>
					</div>
					<?php endwhile; ?>
				</div>
				<!-- <div class="txt-int">
					<?php //the_field('nd_yp'); ?>
				</div> -->
			</div>
		</div>
		<?php $img_yp = get_field('img_yp'); ?>
		<?php $img_yp_mob = get_field('img_yp_mob'); ?>
		<img class="control-img control-img-desc" src="<?php echo $img_yp['url']; ?>" alt="<?php echo $img_yp['alt']; ?>">
		<div class="control-img-mob">
			<img class="control-img" src="<?php echo $img_yp_mob['url']; ?>" alt="<?php echo $img_yp_mob['alt']; ?>">
		</div>
	</section>
	<?php if(have_rows('str_list')){ ?>
	<section id="hed2" class="base-container insurance">
		<div class="wrap h2-style">
			<h2><?php the_field('zag2'); ?></h2>
			<div class="insurance__row grid">
				<?php $i=0; while( have_rows('str_list') ): the_row(); $i++; ?>
				<?php 
					$str_list_zag = get_sub_field('str_list_zag'); 
					$str_list_data = get_sub_field('str_list_data'); 
					$str_list_img = get_sub_field('str_list_img'); 
				?>
				<div class="insurance__item li-style">
					<span class="number">0 <?php echo $i; ?></span>
					<div class="insurance__item-wrp">
					    <h3><?php echo $str_list_zag; ?></h3>
					    <?php echo $str_list_data; ?>
					</div>
					<?php if(!empty($str_list_img)){ ?>
						<!-- <img src="<?php echo $str_list_img['url']; ?>" alt="<?php echo $str_list_img['alt']; ?>"> -->
					<?php } ?>
				</div>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php } ?>
	<section id="hed3" class="about base-container">
		<div class="wrap h2-style">
			<div class="about__top d-flex">
				<!-- <span class="st-btn">О компании</span> -->
				<h2><?php the_field('zag3'); ?></h2>
			</div>
			<?php //$about_img = get_field('about_img'); ?>
			<!-- <div class="about-list">
				<?php $i=0; while( have_rows('about_list') ): the_row(); $i++; ?>
				<?php 
					$about_list_zag = get_sub_field('about_list_zag'); 
					$about_list_txt = get_sub_field('about_list_txt'); 
					$about_list_img = get_sub_field('about_list_img'); 
					if($i < 10){
						$rez = '0'.$i;
					}else{
						$rez = $i;
					}
					if($i==1){
						$cl = ' active';
					}else{
						$cl = '';
					}
				?>
				
				<div data-img="<?php echo $i; ?>" class="about-list__item d-flex d-jm">
					
					<span class="about-list-name<?php echo $cl; ?>"><?php echo $about_list_zag; ?></span>
					<div class="about-list__desc d-flex d-jm">
						<p>
							<?php echo $about_list_txt; ?>
						</p>
						<span class="list-number"><?php echo $rez; ?></span>
					</div>
					<img class="about-img about-img<?php echo $i; ?>" src="<?php echo $about_list_img['url']; ?>" alt="<?php echo $about_list_img['alt']; ?>">
				</div>
				<?php endwhile; ?>
			</div> -->
			<div class="about-list-in grid">
				<div class="about-list-in__item">
					<h4>Сравниваем цены на ДМС</h4>
					<p>Сравниваем предложения от проверенных страховщиков и выбираем лучшее <br>по цене-качеству</p>
				</div>
				<div class="about-list-in__item">
					<h4>Индивидуальный подход</h4>
					<p>Учитываем специфику вашего бизнеса  <br>и команды</p>
				</div>
				<div class="about-list-in__item">
					<h4>Управление изменениями</h4>
					<p>Сокращаем трудозатраты HR по управлению ДМС за счет автоматизации</p>
				</div>
				<div class="about-list-in__item">
					<h4>Экспертиза и оптимизация</h4>
					<p>Оптимизируем существующий договор  <br>и предлагаем лучшее наполнение  <br>по цене-качеству</p>
				</div>
			</div>
		</div>
	</section>
	<section id="hed4" class="base-container selection">
		<div class="wrap">
			<div class="selection__top">
				<h3>
					<?php the_field('zag4'); ?>
				</h3>
				<span><?php the_field('sub_zag4'); ?></span>
				<div class="group-btn d-m d-c d-flex">
					<a target="_blank" class="btn1 btn-style" href="<?php the_field('tg_url'); ?>">Написать в Telegram</a>
					<a class="btn2 btn-style active-modal" href="#modal-window">Заказать обратный звонок</a>
				</div>
			</div>
<!-- 			<div class="cases h2-style">
				<h2><?php the_field('zag5'); ?></h2>
				<?php if(have_rows('cases_list')){ ?>
				<div class="cases__row grid">
					<?php $i=0; while( have_rows('cases_list') ): the_row(); $i++; ?>
					<?php 
						$cases_list_zag = get_sub_field('cases_list_zag'); 
						$cases_list_ico = get_sub_field('cases_list_ico'); 
						$cases_list_bg = get_sub_field('cases_list_bg'); 
					?>

					<div class="cases__item cases__item<?php echo $i; ?>">
						<?php if(!empty($cases_list_ico)){ ?>
							<img class="ico-cases" src="<?php echo $cases_list_ico['url']; ?>" alt="<?php echo $cases_list_ico['alt']; ?>">
						<?php } ?>
						<?php if(!empty($cases_list_bg)){ ?>
							<img class="bg-cases" src="<?php echo $cases_list_bg['url']; ?>" alt="<?php echo $cases_list_bg['alt']; ?>">
						<?php } ?>
						<?php if(!empty($cases_list_zag)){ ?>
							<span class="mob-span"><?php echo $cases_list_zag; ?></span>
						<?php } ?>
					</div>
					<?php endwhile; ?>
				</div>
				<?php } ?>
				<a href="#" class="btn3">
					Показать все
					<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
						<path d="M1 17L9 9L0.999999 0.999999" stroke="white" />
					</svg>
				</a>
			</div> -->
		</div>
	</section>
<!-- 	<section id="hed5" class="base-container news">
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
	</section> -->
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
	       			<a href="#modal-window" class="btn3 active-modal">
						Связаться с нами
						<svg width="10" height="18" viewBox="0 0 10 18" fill="none">
							<path d="M1 17L9 9L0.999999 0.999999" stroke="white"></path>
						</svg>
					</a>
       			</div>
			</div>
			<?php if(have_rows('faq_list')){ ?>
			<div class="faq__right">
				<ul class="faq-list">
					<?php $i=0; while( have_rows('faq_list') ): the_row(); $i++; ?>
					<?php 
						$Faq_vp = get_sub_field('Faq_vp'); 
						$Faq_otv = get_sub_field('Faq_otv'); 
						if($i==1){
							$clas = ' class="active"';
						}else{
							$clas = '';
						}
						if($i==1){
							$st = ' style="display: block;"';
						}else{
							$st = '';
						}
					?>

					<li<?php echo $clas; ?>>
						<a class="faq-click" href="#">
						    <?php echo $Faq_vp; ?>
							<span>
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
								    <path d="M7.66699 6.33203H13.667V7.66602H7.66699V13.666H6.33398V7.66602H0.333984V6.33203H6.33398V0.332031H7.66699V6.33203Z" fill="white" />
								</svg>
								<svg width="14" height="2" viewBox="0 0 14 2" fill="none">
									<path d="M13.667 1.66602H0.333984V0.332031H13.667V1.66602Z" fill="white" />
								</svg>
							</span>
						</a>
						<div<?php echo $st; ?> class="faq-wrap">
							<div class="faq-wrap-in">
								<?php echo $Faq_otv; ?>
							</div>
						</div>
					</li>
					<?php endwhile; ?>
				</ul>
			</div>
			<?php } ?>
		</div>
	</section>
<?php get_footer(); ?>