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

