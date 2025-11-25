	<?php 

		$tel = get_field('tel', 2); 

		$tel_in = preg_replace('# |\-|\(|\)#', '', $tel);

	?>

	<?php $mail = get_field('mail', 2); ?>

	<?php $logo_foot = get_field('logo_foot'); ?>

	<footer data-home="<?php bloginfo('url'); ?>/" id="hed7" class="base-container footer">

		<div class="wrap grid">

			<div class="footer__left">

				<span class="foot-title"><?php the_field('zag_foot',2); ?></span>

				<p>

					<?php the_field('txt_foot',2); ?>

				</p>

				<a class="btn4 btn-style-new active-modal" href="#modal-window">Связаться с нами</a>

			</div>

			<div class="footer__right">

				<div class="foot-top d-flex-wrap">

					<div class="foot-top__item foot-top__item1">

						<a class="foot-top__logo" href="<?php bloginfo('url'); ?>">

							<img src="<?php echo $logo_foot['url']; ?>" alt="<?php echo $logo_foot['alt']; ?>">

						</a>

						<span>

							<?php the_field('logo_txt_foot',2); ?>

						</span>

					</div>

					<div class="foot-bottom foot-bottom-mob d-flex">

						<a class="tel-foot" href="tel:<?php echo $tel_in; ?>">

							<svg width="48" height="49" viewBox="0 0 48 49" fill="none">

								<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>

								<path d="M18.6471 17.834H21.9412L23.5882 21.9516L21.5294 23.1869C22.4114 24.9752 23.8587 26.4226 25.6471 27.3046L26.8824 25.2457L31 26.8928V30.1869C31 30.6238 30.8265 31.0427 30.5176 31.3516C30.2087 31.6605 29.7898 31.834 29.3529 31.834C26.1406 31.6388 23.1107 30.2746 20.835 27.9989C18.5594 25.7233 17.1952 22.6934 17 19.481C17 19.0442 17.1735 18.6253 17.4824 18.3164C17.7913 18.0075 18.2102 17.834 18.6471 17.834Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>

							<?php echo $tel; ?>

						</a>

						<a class="email-foot" href="mailto:<?php echo $mail; ?>">

						<svg width="48" height="48" viewBox="0 0 48 48" fill="none">
							<circle cx="24" cy="24" r="24" fill="#00A24D"/>
							<path d="M32.1818 21.2958L24.4918 26.2399C24.1924 26.4324 23.8076 26.4324 23.5082 26.2399L15.8182 21.2958V29.3711C15.8183 29.9139 16.2847 30.4098 16.9297 30.4098H31.0703C31.7153 30.4098 32.1817 29.9139 32.1818 29.3711V21.2958ZM16.9297 18.5916C16.4634 18.5916 16.0911 18.8511 15.9203 19.1998L24 24.3933L32.0788 19.1998C31.9079 18.8513 31.5364 18.5916 31.0703 18.5916H16.9297ZM34 29.3711C33.9999 30.9798 32.6566 32.228 31.0703 32.228H16.9297C15.3434 32.228 14.0001 30.9798 14 29.3711V19.6303C14.0001 18.0216 15.3434 16.7734 16.9297 16.7734H31.0703C32.6566 16.7734 33.9999 18.0216 34 19.6303V29.3711Z" fill="white"/>
						</svg>

							<?php echo $mail; ?>

						</a>

					</div>

					<div class="foot-top__item foot-top__item2">

						<?php wp_nav_menu(array('theme_location' => 'm2','container' => '','menu_class' => 'footer-menu grid')); ?>

					</div>

				</div>

				<div class="foot-bottom foot-bottom-desc d-flex">

					<a class="tel-foot" href="tel:<?php echo $tel_in; ?>">

						<svg width="48" height="49" viewBox="0 0 48 49" fill="none">

							<circle cx="24" cy="24.5" r="24" fill="#00A24D"/>

							<path d="M18.6471 17.834H21.9412L23.5882 21.9516L21.5294 23.1869C22.4114 24.9752 23.8587 26.4226 25.6471 27.3046L26.8824 25.2457L31 26.8928V30.1869C31 30.6238 30.8265 31.0427 30.5176 31.3516C30.2087 31.6605 29.7898 31.834 29.3529 31.834C26.1406 31.6388 23.1107 30.2746 20.835 27.9989C18.5594 25.7233 17.1952 22.6934 17 19.481C17 19.0442 17.1735 18.6253 17.4824 18.3164C17.7913 18.0075 18.2102 17.834 18.6471 17.834Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

						</svg>

						<?php echo $tel; ?>

					</a>

					<a class="email-foot" href="mailto:<?php echo $mail; ?>">

						<svg width="48" height="48" viewBox="0 0 48 48" fill="none">
							<circle cx="24" cy="24" r="24" fill="#00A24D"/>
							<path d="M32.1818 21.2958L24.4918 26.2399C24.1924 26.4324 23.8076 26.4324 23.5082 26.2399L15.8182 21.2958V29.3711C15.8183 29.9139 16.2847 30.4098 16.9297 30.4098H31.0703C31.7153 30.4098 32.1817 29.9139 32.1818 29.3711V21.2958ZM16.9297 18.5916C16.4634 18.5916 16.0911 18.8511 15.9203 19.1998L24 24.3933L32.0788 19.1998C31.9079 18.8513 31.5364 18.5916 31.0703 18.5916H16.9297ZM34 29.3711C33.9999 30.9798 32.6566 32.228 31.0703 32.228H16.9297C15.3434 32.228 14.0001 30.9798 14 29.3711V19.6303C14.0001 18.0216 15.3434 16.7734 16.9297 16.7734H31.0703C32.6566 16.7734 33.9999 18.0216 34 19.6303V29.3711Z" fill="white"/>
						</svg>


						<?php echo $mail; ?>

					</a>

				</div>

			</div>

		</div>

	</footer>

	<div id="modal-window" class="modal-tn modal-window-form">

		<div class="modal-tn__wrap">

			<div class="modal-tn__wrp modal-bg">

               <button title="Close (Esc)" type="button" class="modal-tn__close">×</button>

				<div class="modal-top">

					<h3>Получите индивидуальную консультацию  <br>по лучшим условиям медицинского страхования</h3>

					<span>Введите корректно свои контактные данные, чтобы наш менеджер мог подобрать оптимальные условия для вас</span>

				</div>

				<div class="form-block">

					<?php echo do_shortcode('[contact-form-7 id="a95b3cc" title="Контактная форма"]'); ?>

				</div>

                

			</div>

		</div>

	</div>

	<?php wp_footer(); ?>

	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/tokenize2.min.css">

	<script src="<?php bloginfo('template_url'); ?>/js/tokenize2.min.js"></script>

	<script src="<?php bloginfo('template_url'); ?>/js/mainselect.js"></script>

	<script src="<?php bloginfo('template_url'); ?>/js/jquery.maskedinput.min.js"></script>

	<script src="<?php bloginfo('template_url'); ?>/js/my-script.js"></script> 

</body>

</html>