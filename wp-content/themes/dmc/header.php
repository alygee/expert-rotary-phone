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

        <?php $logo = get_field('logo',2); ?>

        <a class="header__logo" href="<?php bloginfo('url'); ?>">

          <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>">

        </a>

        <span class="header__logo-txt"><?php the_field('logo_txt',2); ?></span>

      </div>

      <?php 

        $tel = get_field('tel', 2); 

        $tel_in = preg_replace('# |\-|\(|\)#', '', $tel);

      ?>

      <?php $mail = get_field('mail', 2); ?>

      <div class="header__right d-flex d-m">

        <a class="mail" href="mailto:<?php echo $mail; ?>">

          <span class="ico">

            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">

              <path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50" />

              <path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50" />

            </svg>

          </span>

           <?php echo $mail; ?>

        </a>

        <a class="tel" href="tel:<?php echo $tel_in; ?>">

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

          <?php echo $tel; ?>

        </a>

        <a class="btn active-modal" href="#modal-window">Обратный звонок</a>

      </div>

      <a href="tel:<?php echo $tel_in; ?>" class="tel-mob"></a>

      <div class="mob-menu">

        <a class="click-menu" href="#">

          <span></span>

          <span></span>

          <span></span>

        </a>

        <div class="mob-menu-wrap">

            <?php wp_nav_menu(array('theme_location' => 'm1','container' => '','menu_class' => 'menu-top-mob')); ?>

            <!-- <div class="search-block">

              <form action="#" method="get" accept-charset="utf-8">

                <input type="text" placeholder="Поиск по сайту">

                <input type="submit" value="">

              </form>

            </div> -->
            <a target="_blank" class="url-avtorize" href="https://lk.kubiki.pro/">
              <img src="<?php bloginfo('template_url'); ?>/img/login.svg" alt="">
            </a>

            <a class="mail" href="mailto:<?php echo $mail; ?>">

            <span class="ico">

              <svg width="14" height="14" viewBox="0 0 14 14" fill="none">

                <path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50"></path>

                <path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50"></path>

              </svg>

            </span>

              <?php echo $mail; ?>

          </a>

        </div>

      </div>

    </div>

    <nav class="wrap nav-wrp d-flex d-jm">

        <?php wp_nav_menu(array('theme_location' => 'm1','container' => '','menu_class' => 'menu-top d-flex')); ?>

        <!-- <div class="search-block">

          <form action="#" method="get" accept-charset="utf-8">

            <input type="text" placeholder="Поиск по сайту">

            <input type="submit" value="">

          </form>

        </div> -->

        <a target="_blank" class="url-avtorize" href="https://lk.kubiki.pro/">
          <img src="<?php bloginfo('template_url'); ?>/img/login.svg" alt="">
        </a>

    </nav>

  </header>