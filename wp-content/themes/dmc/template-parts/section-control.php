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

