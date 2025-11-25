<?php
function twentysixteen_body_classes( $classes ) { 
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to sites with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of no-sidebar to sites without active sidebar.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
  if($classes[0] == 'home' && $classes[1] == 'blog'){
    unset($classes[1]);
  }
	return $classes;
}
add_filter( 'body_class', 'twentysixteen_body_classes' );

register_sidebar(array(
  'name' => 'Виджет подписки',
  'id' => 'vidget1',
  'description' => '',
  'before_widget' => '',
  'after_widget' => '',
  'before_title'  => '',
  'after_title'  => ''
));

register_nav_menus(
   array(
  'm1' => __('Верхнее меню'),
  'm2' => __('Меню в футере'),
  )
);
//show_admin_bar(false);

add_theme_support('post-thumbnails');
add_theme_support( 'title-tag' );

add_filter('wp_get_attachment_image_attributes', 'unset_attach_srcset_attr', 99 );
function unset_attach_srcset_attr( $attr ){
foreach( array('sizes','srcset') as $key )
    if( isset($attr[ $key ]) ) unset($attr[ $key ]);
    return $attr;
}
remove_action( 'wp_head', 'wp_resource_hints', 2 );

//add_image_size('img_min', 223, 312, true);

// Отключаем сам REST API
add_filter('rest_enabled', '__return_false');

// Отключаем фильтры REST API
/*remove_action( 'xmlrpc_rsd_apis',            'rest_output_rsd' );
remove_action( 'wp_head',                    'rest_output_link_wp_head', 10, 0 );
remove_action( 'template_redirect',          'rest_output_link_header', 11, 0 );
remove_action( 'auth_cookie_malformed',      'rest_cookie_collect_status' );
remove_action( 'auth_cookie_expired',        'rest_cookie_collect_status' );
remove_action( 'auth_cookie_bad_username',   'rest_cookie_collect_status' );
remove_action( 'auth_cookie_bad_hash',       'rest_cookie_collect_status' );
remove_action( 'auth_cookie_valid',          'rest_cookie_collect_status' );
remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );*/

// Отключаем события REST API
/*remove_action( 'init',          'rest_api_init' );
remove_action( 'rest_api_init', 'rest_api_default_filters', 10, 1 );
remove_action( 'parse_request', 'rest_api_loaded' );*/
// Отключаем Embeds связанные с REST API
/*remove_action( 'rest_api_init',          'wp_oembed_register_route'              );
remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );*/
// если собираетесь выводить вставки из других сайтов на своем, то закомментируйте след. строку.
remove_action( 'wp_head','wp_oembed_add_host_js');

//в хед убираем скрипт смайликов:
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

function twentyfifteen_scripts() {
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_scripts' );
add_theme_support( 'html5', array(
  'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
) );

//Отключаем обновление плагинов WordPress:
/*remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_plugins' );*/

//Отключаем обновления шаблонов WordPress:
/*remove_action('load-update-core.php','wp_update_themes');
add_filter('pre_site_transient_update_themes',create_function('$a', "return null;")); wp_clear_scheduled_hook('wp_update_themes');*/

function jquery_noconflikt() {
  wp_add_inline_script( 'jquery-core', '$ = jQuery;' );
}
add_action( 'wp_enqueue_scripts', 'jquery_noconflikt' );

add_filter( 'upload_mimes', 'upload_allow_types' );
function upload_allow_types( $mimes ) {
  $mimes['svg']  =  'image/svg+xml';
  return $mimes;
}
//add_filter('show_admin_bar', '__return_false');


// ***
function footer_enqueue_scripts(){ 
    if(!is_admin()){
        wp_dequeue_script('jquery');
        wp_dequeue_script('jquery-core');
        wp_dequeue_script('jquery-migrate');
        wp_enqueue_script('jquery', false, array(), false, true);
        wp_enqueue_script('jquery-core', false, array(), false, true);
        wp_enqueue_script('jquery-migrate', false, array(), false, true);

        wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/css/output.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts','footer_enqueue_scripts');

add_filter('tiny_mce_before_init', 'my_adds_alls_elements', 20);
  function my_adds_alls_elements($init) {
  if(current_user_can('unfiltered_html')) {
    $init['extended_valid_elements'] = 'span[*]';
  }
  return $init;
}

// ***

/*function сity() {
    $csv = get_field('csv_file', 2);
    if (empty($csv)) return false;
    $rows = [];
    if (($handle = fopen($csv, "r")) !== false) {
        $headers = fgetcsv($handle);
        while (($data = fgetcsv($handle, 0, ",")) !== false) {
            $rows[] = array_combine($headers, $data);
        }
        fclose($handle);
    }

    $cities = array_column($rows, "Город");
    $filtered = array_filter($cities, function ($city) {
        $city = trim($city); 
        return strpos($city, ' ') === false && $city !== '';
    });
    $uniqueCities = array_values(array_unique($filtered));

    $cities = array_map('trim', $uniqueCities);
    $cities = array_unique($cities);
    $cities = array_values($cities);
    return $cities;
}*/

function сity(){
  $csv = get_field('csv_file',2);
  if(empty($csv)) return false;
  $rows = [];
  if (($handle = fopen($csv, "r")) !== false) {
      $headers = fgetcsv($handle);
      while (($data = fgetcsv($handle, 0, ",")) !== false) {
          $rows[] = array_combine($headers, $data);
      }
      fclose($handle);
  }
  $cities = array_column($rows, "Город");
  $uniqueCities = array_values(array_unique($cities));

  $cities = array_map('trim', $uniqueCities);
  $cities = array_unique($cities);
  $cities = array_values($cities);
  return $cities;
}


function rez(){
  //$csv = get_bloginfo('template_url')."/list.csv"; 
  $csv = get_field('csv_file',2);
  if(empty($csv)) return false;
  $rows = [];
  if (($handle = fopen($csv, "r")) !== false) {
      $headers = fgetcsv($handle);
      while (($data = fgetcsv($handle, 0, ",")) !== false) {
          $rows[] = array_combine($headers, $data);
      }
      fclose($handle);
  }

  return $rows;
}



function filterData(array $data, $cities = [], $levels = [], int $employeesCount = null): array {
    // Нормализация параметров (если передана строка вместо массива)
    if (!is_array($cities)) {
        $cities = [$cities];
    }
    if (!is_array($levels)) {
        $levels = [$levels];
    }

    // Убираем пустые элементы и пробелы
    $cities = array_values(array_filter(array_map('trim', $cities), fn($v) => $v !== ''));
    $levels = array_values(array_filter(array_map('trim', $levels), fn($v) => $v !== ''));

    $result = [];

    // 1️⃣ Сначала фильтруем по заданным городам
    foreach ($data as $row) {
        $city = trim($row['Город'] ?? '');
        $level = trim($row['Уровень'] ?? '');
        $count = trim($row['Кол-во_сотрудников'] ?? '');

        // Фильтр по городу
        if (!empty($cities) && !in_array($city, $cities, true)) {
            continue;
        }

        // Фильтр по уровню
        if (!empty($levels) && !in_array($level, $levels, true)) {
            continue;
        }

        // Фильтр по количеству сотрудников (формат "min-max")
        if ($employeesCount !== null) {
            if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                $min = (int)$m[1];
                $max = (int)$m[2];
                if ($employeesCount < $min || $employeesCount > $max) {
                    continue;
                }
            } else {
                continue;
            }
        }

        $result[] = $row;
    }


    if (empty($result) && !empty($cities)) {
        foreach ($data as $row) {
            $city = trim($row['Город'] ?? '');
            $level = trim($row['Уровень'] ?? '');
            $count = trim($row['Кол-во_сотрудников'] ?? '');

            if ($city !== 'Другой город') {
                continue;
            }

            if (!empty($levels) && !in_array($level, $levels, true)) {
                continue;
            }

            if ($employeesCount !== null) {
                if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                    $min = (int)$m[1];
                    $max = (int)$m[2];
                    if ($employeesCount < $min || $employeesCount > $max) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            $result[] = $row;
        }
    }

    return $result;
}


function filterData2(array $data, $cities = [], $levels = [], int $employeesCount = null): array {
    // Нормализация параметров
    if (!is_array($cities)) $cities = [$cities];
    if (!is_array($levels)) $levels = [$levels];

    $cities = array_values(array_filter(array_map('trim', $cities), fn($v) => $v !== ''));
    $levels = array_values(array_filter(array_map('trim', $levels), fn($v) => $v !== ''));

    // Сюда будем складывать результат по городам
    $grouped = [];

    // Сначала фильтруем данные
    foreach ($data as $row) {
        $city = trim($row['Город'] ?? '');
        $level = trim($row['Уровень'] ?? '');
        $count = trim($row['Кол-во_сотрудников'] ?? '');

        // --- фильтр по уровню ---
        if (!empty($levels) && !in_array($level, $levels, true)) {
            continue;
        }

        // --- фильтр по количеству сотрудников ---
        if ($employeesCount !== null) {
            if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                $min = (int)$m[1];
                $max = (int)$m[2];
                if ($employeesCount < $min || $employeesCount > $max) continue;
            } else continue;
        }

        // --- фильтр по городам ---
        if (!empty($cities)) {
            if (!in_array($city, $cities, true)) continue;
        }

        // Добавляем запись в массив по городу
        $grouped[$city][] = $row;
    }

    // Если ничего не найдено по городам → берём "Другой город"
    if (empty($grouped) && !empty($cities)) {
        foreach ($data as $row) {
            $city = trim($row['Город'] ?? '');
            $level = trim($row['Уровень'] ?? '');
            $count = trim($row['Кол-во_сотрудников'] ?? '');

            if ($city !== 'Другой город') continue;
            if (!empty($levels) && !in_array($level, $levels, true)) continue;

            if ($employeesCount !== null) {
                if (preg_match('/^(\d+)-(\d+)$/', $count, $m)) {
                    $min = (int)$m[1];
                    $max = (int)$m[2];
                    if ($employeesCount < $min || $employeesCount > $max) continue;
                } else continue;
            }

            $grouped['Другой город'][] = $row;
        }
    }

    // 🔹 Сортируем результат в порядке переданных городов
    $sorted = [];
    if (!empty($cities)) {
        foreach ($cities as $city) {
            if (isset($grouped[$city])) {
                $sorted[$city] = $grouped[$city];
            }
        }
    }

    // 🔹 Добавляем остальные города, если есть (например, "Другой город")
    foreach ($grouped as $city => $rows) {
        if (!isset($sorted[$city])) {
            $sorted[$city] = $rows;
        }
    }

    return $sorted;
}




//var_dump(filterData(rez(), ['test'], ['Стандарт'], 12));

//var_dump(filterData2(rez(), ['Барнаул', 'Архангельск'], ['Комфорт'], 12));


function get_insurer_logo(string $insurer): void {
  $array_logo = ['Зетта', 'Ингос', 'РГС', 'СБЕР', 'пари', 'ресо', 'Капитал life', 'Ренессанс', 'Согласие', 'Т-страхование', 'АльфаСтрахование', 'Allianz', 'СОГАЗ'
  ];
  $insurer_lower = mb_strtolower(trim($insurer), 'UTF-8');
  $array_logo_lower = array_map(fn($v) => mb_strtolower($v, 'UTF-8'), $array_logo);

  $index = array_search($insurer_lower, $array_logo_lower, true);

  if ($index !== false) {
      $img_index = $index + 1;
      echo '<img src="' . get_bloginfo('template_url') . '/img/logotypes' . $img_index . '.svg" alt="">';
  }
}

add_action('wp_ajax_action', 'filter_callback');
add_action('wp_ajax_nopriv_action', 'filter_callback');
function filter_callback(){
  if(empty(rez())){
    return false;
  }
 if(isset($_POST['count'])){
    $count = $_POST['count'];
  }
  if(isset($_POST['level'])){
    $level = $_POST['level'];
    $level = explode(',', $level);

  }
  if(isset($_POST['region'])){
    $region = $_POST['region'];
  }

  $results = filterData2(
      rez(),
      $region,
      $level,
      $count
  );
  $ir=0;
  ?>
  <?php foreach ($results as $key=>$value2) { $ir++ ?>

    <?php echo '<h3 class="h3-sfd">'.$key.'</h3>'; ?>
    <div class="block-rezult__grid grid ghd-grid">
      <?php foreach ($value2 as $value) { ?>
        <div class="block-rezult__item">

          <?php 
              $fields = ["Стоматология", "Скорая_помощь", "Госпитализация", "Вызов_врача_на_дом", "Поликлиника"];
              $suma_price = 0;
              foreach ($fields as $field) {
                $num = (float) str_replace(',', '.', $value[$field]);
                  if (isset($num) && is_numeric($num) && $num > 0) {
                      
                      $suma_price += $value[$field];
                  }
              }
              $cl_w = '';
              if($value["Страховщик"] == 'Сбербанк страхование'){
                $cl_w = ' cl-width';
              }
          ?>

          <div class="rezult-top d-flex d-jm">
            <h5 class="flex-logotypes d-flex<?php echo $cl_w; ?>">
              <?php get_insurer_logo($value["Страховщик"]); ?>
              <?php echo $value["Страховщик"]; ?>
            </h5>
            <div class="rezult-top__price">
              <span class="price-r"><?php echo number_format($suma_price, 0, ' ', ' '); ?> ₽</span>
              <span class="desc-r">в год за человека</span>
            </div>
          </div>  
          <span class="program-composition">Состав программы:</span>
          <div class="rezult-data">
            <ul>
              <?php 
                $clas1 = '';
                $clas2 = '';
                $clas3 = '';
                $clas4 = '';
                $clas5 = '';
                $bs1 = '';
                $bs2 = '';
                $bs3 = '';
                $bs4 = '';
                $bs5 = '';
                $y1 = $value["Стоматология"];
                $y2 = $value["Скорая_помощь"];
                $y3 = $value["Госпитализация"];
                $y4 = $value["Вызов_врача_на_дом"];
                $y5 = $value["Поликлиника"];

                /*if($y1 == 0 || empty($y1)){
                  $clas1 = ' class="no-r"';
                }
                if($y2 == 0 || empty($y2)){
                  $clas2 = ' class="no-r"';
                }
                if($y3 == 0 || empty($y3)){
                  $clas3 = ' class="no-r"';
                }
                if($y4 == 0 || empty($y4)){
                  $clas4 = ' class="no-r"';
                }
                if($y5 == 0 || empty($y5)){
                  $clas5 = ' class="no-r"';
                }*/
                if($y1 == 0){
                  $bs1 = 'беспл.';
                }
                if($y2 == 0){
                  $bs2 = 'беспл.';
                }
                if($y3 == 0){
                  $bs3 = 'беспл.';
                }
                if($y4 == 0){
                  $bs4 = 'беспл.';
                }
                if($y5 == 0){
                  $bs5 = 'беспл.';
                }

              ?>

              <?php //echo $value["Город"]; ?>
              <?php if($y5 != '' && $y5 != '#Н/Д'){ ?>
                <li> 
                  Поликлиника
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs5 == ''){ ?>
                        <?php echo number_format((float) str_replace(',', '.', $value["Поликлиника"]), 0, ' ', ' '); ?> ₽
                        <?php }else{ echo $bs5; } ?>
                      </span>
                    </div>
                  </div>
                </li>
              <?php } ?>
              <?php if($y1 != '' && $y1 != '#Н/Д'){ ?>
                <li>
                  Стоматология
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs1 == ''){ ?>
                        <?php echo number_format((float) str_replace(',', '.', $value["Стоматология"]), 0, ' ', ' '); ?> ₽
                        <?php }else{ echo $bs1; } ?>
                      </span>
                    </div>
                  </div>
                </li>
              <?php } ?>
              <?php if($y2 != '' && $y2 != '#Н/Д'){ ?>
                <li>
                  Скорая помощь
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs2 == ''){ ?>
                        <?php echo number_format((float) str_replace(',', '.', $value["Скорая_помощь"]), 0, ' ', ' '); ?> ₽
                        <?php }else{ echo $bs2; } ?>
                      </span>
                    </div>
                  </div>
                </li>
              <?php } ?>
              <?php if($y3 != '' && $y3 != '#Н/Д'){ ?>
                <li>
                  Госпитализация
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs3 == ''){ ?>
                        <?php echo number_format((float) str_replace(',', '.', $value["Госпитализация"]), 0, ' ', ' '); ?> ₽
                        <?php }else{ echo $bs3; } ?>
                      </span>
                    </div>
                  </div>
                </li>
              <?php } ?>
              <?php if($y4 != '' && $y4 != '#Н/Д'){ ?>
                <li>
                  Вызов врача на дом
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs4 == ''){ ?>
                        <?php echo number_format((float) str_replace(',', '.', $value["Вызов_врача_на_дом"]), 0, ' ', ' '); ?> ₽
                        <?php }else{ echo $bs4; } ?>
                      </span>
                    </div>
                  </div>
                </li>
              <?php } ?>
             

            </ul>
          </div>
          <a class="btn4 btn-style-new active-modal active-modal2" href="#modal-window2">Оформить</a>
        </div>
      <?php } ?>
      <?php if(count($results) == $ir){ ?>
        <div class="block-rezult__item block-rezult__item-last">
          <div class="r-last-wrp">
            <h2>Оставьте свои  <br>контакты</h2>
            <span class="block-rezult__desc">Не нашли, что хотели? Мы перезвоним вам</span>
          </div>
          <a class="btn2 btn-style active-modal" href="#modal-window">Заказать обратный звонок</a>
        </div>
      <?php } ?>
    </div>

  <?php } ?>

  <?php
  exit();
}
