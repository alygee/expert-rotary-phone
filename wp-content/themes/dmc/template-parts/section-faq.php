<section id="hed6" class="faq text-white">
	<div class="relative flex flex-col pt-10 faq-container md:mx-auto md:flex-row md:flex-wrap">

		<div class="md:w-1/2">
			<div class="flex items-start justify-between px-2.5 md:w-xl md:block md:leading-12">
				<h2 class="font-semibold inline leading-[1.25] tracking-[.035em] text-2xl md:text-5xl md:font-medium md:leading-none">
					Часто задаваемые вопросы
				</h2>
				<img class="h-12 md:h-auto md:float-left md:mr-8" src="<?php bloginfo('template_url'); ?>/img/faq-title.webp" alt="">
			</div>
		</div>

		<?php if(have_rows('faq_list')) { ?>
			<div class="md:w-1/2">
				<ul class="faq-list mt-12 md:mt-0">
					<?php $i=0; while( have_rows('faq_list') ): the_row(); $i++; ?>
					<?php 
						$Faq_vp = get_sub_field('Faq_vp'); 
						$Faq_otv = get_sub_field('Faq_otv'); 
					?>

					<li class="py-10 px-2.5">
						<a class="faq-click tracking-[.06em] leading-6 md:leading-[1.25] md:tracking-wide" href="#">
							<?php echo $Faq_vp; ?>

							<span class="mr-2.5">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
										<path d="M7.66699 6.33203H13.667V7.66602H7.66699V13.666H6.33398V7.66602H0.333984V6.33203H6.33398V0.332031H7.66699V6.33203Z" fill="white" />
								</svg>
								<svg width="14" height="2" viewBox="0 0 14 2" fill="none">
									<path d="M13.667 1.66602H0.333984V0.332031H13.667V1.66602Z" fill="white" />
								</svg>
							</span>
						</a>
						<div class="faq-wrap -mt-5 md:-mt-4 hidden">
							<div class="pt-8 text-sm md:text-lg text-grey-2 leading-[1.38]">
								<?php echo $Faq_otv; ?>
							</div>
						</div>
					</li>
					<?php endwhile; ?>
				</ul>
			</div>
		<?php } ?>

		<div class="pt-16 pb-20 px-2.5 md:pb-24 md:w-1/2 md:absolute bottom-0">
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
</section>

