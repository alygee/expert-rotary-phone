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

