<?php
/**
 * AJAX обработчики
 */

function filter_callback() {
  // Включаем отображение ошибок для отладки (на тестовом сервере)
  if (defined('WP_DEBUG') && WP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
  }
  
  // Инициализируем переменные
  $count = null;
  $level = [];
  $region = [];
  
  // Получаем данные из POST
  if(isset($_POST['count']) && !empty($_POST['count'])){
    $count = (int) $_POST['count'];
  }
  
  // Обработка level (может быть массивом или строкой)
  if(isset($_POST['level']) && !empty($_POST['level'])){
    if(is_array($_POST['level'])){
      $level = $_POST['level'];
    } else {
      // Если строка, разбиваем по запятой
      $level = array_filter(array_map('trim', explode(',', $_POST['level'])));
    }
  }
  
  // Обработка region (может быть массивом, строкой или region[])
  if(isset($_POST['region'])){
    if(is_array($_POST['region'])){
      // Массив городов (например, из множественного select)
      $region = $_POST['region'];
    } elseif(!empty($_POST['region'])) {
      // Если строка, разбиваем по запятой (например, "Москва,СПб,Барнаул")
      $region = array_filter(array_map('trim', explode(',', $_POST['region'])));
    }
  }
  
  // Очищаем массивы от пустых значений и нормализуем
  $level = array_values(array_filter(array_map('trim', $level), function($v) { return $v !== ''; }));
  $region = array_values(array_filter(array_map('trim', $region), function($v) { return $v !== ''; }));
  
  // Логирование для отладки (только если WP_DEBUG включен)
  if (defined('WP_DEBUG') && WP_DEBUG && function_exists('error_log')) {
    error_log('=== filter_callback DEBUG ===');
    error_log('$_POST содержимое: ' . print_r($_POST, true));
    error_log('count: ' . ($count ?? 'null') . ' (type: ' . gettype($count) . ')');
    error_log('level: ' . print_r($level, true) . ' (type: ' . gettype($level) . ', is_array: ' . (is_array($level) ? 'yes' : 'no') . ')');
    error_log('region: ' . print_r($region, true) . ' (type: ' . gettype($region) . ', is_array: ' . (is_array($region) ? 'yes' : 'no') . ')');
    error_log('$_POST[region] type: ' . (isset($_POST['region']) ? (is_array($_POST['region']) ? 'array' : 'string') : 'not set'));
    error_log('$_POST[level] type: ' . (isset($_POST['level']) ? (is_array($_POST['level']) ? 'array' : 'string') : 'not set'));
    error_log('$_POST[count] value: ' . (isset($_POST['count']) ? $_POST['count'] : 'not set'));
  }

  // Получаем данные из CSV
  $data = rez();
  
  // Проверяем, что данные получены
  if(empty($data) || $data === false){
    // Логируем ошибку для отладки
    if (function_exists('error_log')) {
      error_log('filter_callback: rez() вернул пустой результат или false');
      $csv_field = get_field('csv_file', 2);
      if (is_array($csv_field)) {
        $csv = $csv_field['path'] ?? $csv_field['url'] ?? 'массив без path/url';
      } else {
        $csv = $csv_field ?: 'не установлен';
      }
      error_log('filter_callback: CSV значение: ' . (is_array($csv_field) ? print_r($csv_field, true) : $csv));
    }
    echo '<!-- Ошибка: данные не загружены -->';
    wp_die('Ошибка загрузки данных');
  }

  // Фильтруем данные
  $filter_result = filterInsuranceData(
      $data,
      $region,
      $level,
      $count
  );
  $results = $filter_result['data'];
  $not_found_cities = $filter_result['not_found_cities'];
  
  // Убираем fallback из результатов, если он там есть (он будет показан отдельно для not_found_cities)
  if (isset($results['fallback'])) {
    unset($results['fallback']);
  }
  
  // Получаем fallback данные для городов, для которых не найдено данных
  $fallback_data = [];
  if (!empty($not_found_cities)) {
    $fallback_rows = getFallbackData($data, $level, $count);
    if (!empty($fallback_rows)) {
      $fallback_data = $fallback_rows;
    }
  }
  
  // Проверяем результат фильтрации
  if(empty($results) && empty($fallback_data)) {
    echo '<!-- Нет результатов по заданным критериям -->';
    wp_die('Нет результатов');
  }

  // Подсчитываем общее количество секций для отображения последнего блока
  $total_sections = count($results) + (!empty($fallback_data) ? 1 : 0);
  $current_section = 0;
  ?>
  
  <?php 
  // Выводим отфильтрованные данные
  if (!empty($results)) {
    foreach ($results as $key => $value2) {
      $current_section++;
      echo '<h3 class="h3-sfd mt-3">' . esc_html($key) . '</h3>';
    ?>

    <div class="block-rezult__grid grid ghd-grid">
      <?php foreach ($value2 as $value) { ?>
        <div class="block-rezult__item">

          <?php 
              // Используем функцию calculate_total_price из api-endpoints.php
              $suma_price = function_exists('calculate_total_price') 
                  ? calculate_total_price($value) 
                  : 0;
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

              <?php if($y5 != '' && $y5 != '#Н/Д'){ ?>
                <li> 
                  Поликлиника
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs5 == ''){ ?>
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Поликлиника"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Стоматология"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Скорая_помощь"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Госпитализация"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Вызов_врача_на_дом"]), 0, ' ', ' '); ?> ₽
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
    </div>

    <?php
    }
  }

  // Выводим fallback данные для городов, для которых не найдено данных
  if (!empty($fallback_data) && !empty($not_found_cities)) {
    $current_section++;
    $cities_text = is_array($not_found_cities) ? implode(', ', $not_found_cities) : $not_found_cities;
    $cities_label = is_array($not_found_cities) && count($not_found_cities) > 1 ? 'Для регионов ' : 'Для региона ';
    echo '<div class="text-4xl mt-3 font-semibold tracking-wide">Цены по соседним регионам</div>';
    echo '<div class="mt-3 mb-6 text-xl">' . esc_html($cities_label . $cities_text . ' не удалось произвести расчет') . '</div>';
    ?>
    
    <div class="block-rezult__grid grid ghd-grid">
      <?php foreach ($fallback_data as $value) { ?>
        <div class="block-rezult__item">

          <?php 
              // Используем функцию calculate_total_price из api-endpoints.php
              $suma_price = function_exists('calculate_total_price') 
                  ? calculate_total_price($value) 
                  : 0;
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

              <?php if($y5 != '' && $y5 != '#Н/Д'){ ?>
                <li> 
                  Поликлиника
                  <div class="li-val">
                    <i class="li-val__hover"></i>
                    <div class="li-val__wrp">
                      <span>
                        <?php if($bs5 == ''){ ?>
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Поликлиника"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Стоматология"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Скорая_помощь"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Госпитализация"]), 0, ' ', ' '); ?> ₽
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
                        <?php echo number_format((float) str_replace([' ', ','], ['', '.'], $value["Вызов_врача_на_дом"]), 0, ' ', ' '); ?> ₽
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
    </div>
    
    <?php
  }

  // Выводим последний блок с контактами, если это последняя секция
  if ($current_section === $total_sections) {
    ?>
    <div class="block-rezult__grid grid ghd-grid">
      <div class="block-rezult__item block-rezult__item-last">
        <div class="r-last-wrp">
          <h2>Оставьте свои <br>контакты</h2>
          <span class="block-rezult__desc">Не нашли, что хотели? Мы перезвоним вам</span>
        </div>
        <a class="btn2 btn-style active-modal" href="#modal-window">Заказать обратный звонок</a>
      </div>
    </div>
    <?php
  }
  ?>

  <?php
  wp_die(); // Правильный способ завершения AJAX запроса в WordPress
}

