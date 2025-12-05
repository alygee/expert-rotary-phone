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
  <header class="max-w-6xl mx-auto bg-white 2xl:bg-transparent px-4 pt-4 2xl:px-2.5 6xl:px-0 pb-9">
    <div class="flex justify-between items-center">

      <?php // logo + текст ?>
      <div class="flex items-center gap-6">
        <?php $logo = get_field('logo',2); ?>
        <a href="<?php bloginfo('url'); ?>">
          <img class="2xl:h-16" src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>">
        </a>
        <span class="hidden text-secondary-blck text-base leading-[22px] max-w-[280px] 3xl:max-w-[325px] sm:block 2xl:text-lg"><?php the_field('logo_txt',2); ?></span>
      </div>

      <?php 
        $tel = get_field('tel', 2); 
        $tel_in = preg_replace('# |\-|\(|\)#', '', $tel);
      ?>
      <?php $mail = get_field('mail', 2); ?>

      <div class="hidden 2xl:flex items-center gap-6">
        <a class="font-semibold text-lg leading-[1.22] text-[var(--green-1)] flex items-center" href="mailto:<?php echo $mail; ?>">
          <span class="rounded-full w-12 h-12 flex justify-center items-center mr-2.5 bg-white">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
              <path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50"></path>
              <path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50"></path>
            </svg>
          </span>
          <span class="border-b-2 border-[var(--green-1)] hover:border-none">
            <?php echo $mail; ?>
          </span>
        </a>

        <a class="font-semibold text-lg leading-[1.22] text-[var(--green-1)] flex items-center" href="tel:<?php echo $tel_in; ?>">
          <span class="rounded-full w-12 h-12 flex justify-center items-center mr-2.5 bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
              <g clip-path="url(#clip0_8736_4063)">
                <path d="M14.0789 10.5938C14.0405 10.5633 11.25 8.55188 10.4841 8.69625C10.1184 8.76094 9.90938 9.01031 9.48984 9.50953C9.42234 9.59016 9.26016 9.78328 9.13406 9.92062C8.86894 9.83415 8.61031 9.72888 8.36016 9.60563C7.06899 8.97704 6.02578 7.93382 5.39719 6.64266C5.27384 6.39254 5.16857 6.13391 5.08219 5.86875C5.22 5.74219 5.41313 5.58 5.49563 5.51062C5.9925 5.09344 6.24234 4.88437 6.30703 4.51781C6.43969 3.75844 4.42969 0.94875 4.40859 0.923438C4.3174 0.793158 4.19838 0.684779 4.06016 0.606142C3.92194 0.527505 3.76796 0.480576 3.60938 0.46875C2.79469 0.46875 0.46875 3.48609 0.46875 3.99422C0.46875 4.02375 0.511406 7.02562 4.21312 10.7911C7.97484 14.4886 10.9762 14.5312 11.0058 14.5312C11.5144 14.5312 14.5312 12.2053 14.5312 11.3906C14.5196 11.2326 14.4729 11.0791 14.3947 10.9413C14.3165 10.8035 14.2086 10.6848 14.0789 10.5938Z" fill="#00A24D"/>
              </g>
              <defs>
                <clipPath id="clip0_8736_4063">
                  <rect width="15" height="15" fill="white"/>
                </clipPath>
              </defs>
            </svg>
          </span>
          <span class="text-secondary-blck">
            <?php echo $tel; ?>
          </span>
        </a>

        <a class="btn active-modal text-center" href="#modal-window">Обратный звонок</a>
      </div>

      <?php // кнопки для показа мобильного меню ?>
      <div class="flex gap-2 2xl:hidden">
        <a href="tel:<?php echo $tel_in; ?>" class="tel-mob block border border-green-1 rounded-[10px] w-12 h-12 2xl:hidden"></a>

        <div class="mob-menu">
          <?php // кнопка для показа мобильного меню ?>
          <button type="button" class="hamburger-button flex flex-col items-center justify-center w-12 h-12 bg-[var(--green-1)] rounded-[10px] cursor-pointer transition-background duration-300 ease-linear 2xl:hidden">
            <span></span>
            <span></span>
            <span></span>
          </button>

          <div class="collapsible-menu bg-white absolute z-10 w-full left-0 pt-9 px-6 pb-10 hidden">

            <?php // вывод пунктов меню ?>
            <?php wp_nav_menu(array('theme_location' => 'm1','container' => '','menu_class' => 'menu-top-mob')); ?>


            <div class="flex flex-col gap-3 2xl:hidden">
              <?php // ссылка на email ?>
              <a class="font-semibold text-lg leading-[1.22] text-[var(--green-1)] flex items-center" href="mailto:<?php echo $mail; ?>">
                <span class="rounded-full w-12 h-12 flex justify-center items-center mr-2.5 bg-[var(--grey-1)]">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M8.17001 8.57729C7.82171 8.8095 7.41713 8.93224 7 8.93224C6.5829 8.93224 6.17832 8.8095 5.83002 8.57729L0.0932148 4.75264C0.0613867 4.73142 0.0303789 4.7093 0 4.68655V10.9537C0 11.6722 0.583105 12.2424 1.28879 12.2424H12.7112C13.4297 12.2424 14 11.6593 14 10.9537V4.68652C13.9695 4.70933 13.9385 4.7315 13.9066 4.75275L8.17001 8.57729Z" fill="#4CAF50"></path>
                    <path d="M0.548242 4.07088L6.28504 7.89556C6.50221 8.04034 6.75109 8.11272 6.99997 8.11272C7.24888 8.11272 7.49779 8.04031 7.71496 7.89556L13.4518 4.07088C13.7951 3.84214 14 3.45933 14 3.04617C14 2.33575 13.422 1.75781 12.7116 1.75781H1.28836C0.577965 1.75784 0 2.33578 0 3.04685C0 3.45933 0.204969 3.84214 0.548242 4.07088Z" fill="#4CAF50"></path>
                  </svg>
                </span>
                <span class="border-b-2 border-[var(--green-1)] hover:border-none">
                  <?php echo $mail; ?>
                </span>
              </a>

              <?php // ссылка на телефон ?>
              <a class="font-semibold text-lg leading-[1.22] text-[var(--green-1)] flex items-center" href="tel:<?php echo $tel_in; ?>">
                <span class="rounded-full w-12 h-12 flex justify-center items-center mr-2.5 bg-[var(--grey-1)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                    <g clip-path="url(#clip0_8736_4063)">
                      <path d="M14.0789 10.5938C14.0405 10.5633 11.25 8.55188 10.4841 8.69625C10.1184 8.76094 9.90938 9.01031 9.48984 9.50953C9.42234 9.59016 9.26016 9.78328 9.13406 9.92062C8.86894 9.83415 8.61031 9.72888 8.36016 9.60563C7.06899 8.97704 6.02578 7.93382 5.39719 6.64266C5.27384 6.39254 5.16857 6.13391 5.08219 5.86875C5.22 5.74219 5.41313 5.58 5.49563 5.51062C5.9925 5.09344 6.24234 4.88437 6.30703 4.51781C6.43969 3.75844 4.42969 0.94875 4.40859 0.923438C4.3174 0.793158 4.19838 0.684779 4.06016 0.606142C3.92194 0.527505 3.76796 0.480576 3.60938 0.46875C2.79469 0.46875 0.46875 3.48609 0.46875 3.99422C0.46875 4.02375 0.511406 7.02562 4.21312 10.7911C7.97484 14.4886 10.9762 14.5312 11.0058 14.5312C11.5144 14.5312 14.5312 12.2053 14.5312 11.3906C14.5196 11.2326 14.4729 11.0791 14.3947 10.9413C14.3165 10.8035 14.2086 10.6848 14.0789 10.5938Z" fill="#00A24D"/>
                    </g>
                    <defs>
                      <clipPath id="clip0_8736_4063">
                        <rect width="15" height="15" fill="white"/>
                      </clipPath>
                    </defs>
                  </svg>
                </span>
                <?php echo $tel; ?>
              </a>

              <?php // ссылка на личный кабинет ?>
              <a target="_blank" href="https://lk.kubiki.pro/" class="font-semibold text-lg leading-[1.22] text-[var(--green-1)] flex items-center">
                <span class="rounded-full w-12 h-12 flex justify-center items-center mr-2.5 bg-[var(--grey-1)]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M3.67641 15.9478C3.30284 15.9478 3 15.6516 3 15.2861V13.8889C3.00004 12.973 3.37107 12.0942 4.03179 11.446C4.69261 10.7978 5.58942 10.4333 6.52475 10.4333H10.7253C11.6606 10.4333 12.5574 10.7977 13.2182 11.446C13.8789 12.0942 14.25 12.973 14.25 13.8889V15.2861C14.25 15.6515 13.9471 15.9477 13.5736 15.9478C8.94128 15.9478 4.75499 15.9478 3.67641 15.9478ZM12.1494 5.50695C12.1493 7.41372 10.5729 8.96247 8.62467 8.96247C6.6766 8.96234 5.10074 7.41364 5.10058 5.50695C5.10058 3.60013 6.6765 2.05091 8.62467 2.05078C10.5729 2.05078 12.1494 3.60005 12.1494 5.50695Z" fill="#00A24D"/>
                  </svg>
                </span>
                <span class="text-black">Авторизация</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <nav class="hidden justify-between items-center box-border py-[10px] px-7 bg-white rounded-[10px] mt-5 2xl:flex">
      <?php wp_nav_menu(array('theme_location' => 'm1','container' => '','menu_class' => 'menu-top flex items-center text-center')); ?>
      <a class="ml-7" target="_blank" class="" href="https://lk.kubiki.pro/">
        <span class="rounded-full w-12 h-12 flex justify-center items-center bg-[var(--grey-1)]">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.4557 21.5V19.3887C18.4557 18.5333 18.1091 17.7134 17.4935 17.1094C16.8778 16.5054 16.0431 16.167 15.1737 16.167H8.82629C7.95691 16.167 7.12215 16.5054 6.50652 17.1094C5.89084 17.7134 5.54433 18.5333 5.54427 19.3887V21.5C5.54427 22.0523 5.08665 22.5 4.52214 22.5C3.95763 22.5 3.5 22.0523 3.5 21.5V19.3887C3.50006 18.0046 4.06073 16.6768 5.05916 15.6973C6.05772 14.7177 7.41291 14.167 8.82629 14.167H15.1737C16.5871 14.167 17.9423 14.7176 18.9408 15.6973C19.9392 16.6768 20.4999 18.0046 20.5 19.3887V21.5C20.5 22.0522 20.0423 22.4999 19.4779 22.5C18.9134 22.5 18.4557 22.0523 18.4557 21.5ZM15.2815 6.72266C15.2815 4.94045 13.8092 3.5 11.9995 3.5C10.19 3.5002 8.71848 4.94058 8.71848 6.72266C8.71872 8.50454 10.1901 9.94414 11.9995 9.94434C13.8091 9.94434 15.2813 8.50466 15.2815 6.72266ZM17.3258 6.72266C17.3255 9.60399 14.9434 11.9443 11.9995 11.9443C9.05575 11.9441 6.67445 9.60387 6.67421 6.72266C6.67421 3.84124 9.0556 1.5002 11.9995 1.5C14.9436 1.5 17.3258 3.84112 17.3258 6.72266Z" fill="#1A1A1A"/>
          </svg>
        </span>
      </a>
    </nav>
  </header>
