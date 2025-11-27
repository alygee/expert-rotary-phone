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
				<p>Учитываем специфику вашего бизнеса <br>и команды</p>
			</div>
			<div class="about-list-in__item">
				<h4>Управление изменениями</h4>
				<p>Сокращаем трудозатраты HR по управлению ДМС за счет автоматизации</p>
			</div>
			<div class="about-list-in__item">
				<h4>Экспертиза и оптимизация</h4>
				<p>Оптимизируем существующий договор <br>и предлагаем лучшее наполнение <br>по цене-качеству</p>
			</div>
		</div>
	</div>
</section>

