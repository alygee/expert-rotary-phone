<?php
/**
 * Шаблон для HTML вывода результатов фильтрации через API
 * Используется когда format=html
 */

if (empty($results)) {
    echo '<!-- Нет результатов по заданным критериям -->';
    return;
}

$ir = 0;
foreach ($results as $key => $value2) {
    $ir++;
    
    if ($key === 'fallback') {
        if (is_array($cities)) {
            $r = implode(', ', $cities);
            $c = count($cities) > 1 ? 'Для регионов ' : 'Для региона ';
        } else {
            $r = $cities;
            $c = 'Для региона ';
        }
        echo '<div class="text-4xl mt-3 font-semibold tracking-wide">Цены по соседним регионам</div>';
        echo '<div class="mt-3 mb-6 text-xl">' . $c . $r . ' не удалось произвести расчет</div>';
    } else {
        echo '<h3 class="h3-sfd mt-3">' . esc_html($key) . '</h3>';
    }
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
                
                // Заменяем длинное название страховщика на короткое
                $insurer_display_name = $value["Страховщик"];
                if($insurer_display_name == 'ООО «Капитал Лайф Страхование Жизни»'){
                    $insurer_display_name = 'Капитал Лайф';
                }
                ?>
                
                <div class="rezult-top d-flex d-jm">
                    <h5 class="flex-logotypes d-flex<?php echo $cl_w; ?>">
                        <?php get_insurer_logo($value["Страховщик"]); ?>
                        <?php echo esc_html($insurer_display_name); ?>
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
                        
                        if($y1 == 0) $bs1 = 'беспл.';
                        if($y2 == 0) $bs2 = 'беспл.';
                        if($y3 == 0) $bs3 = 'беспл.';
                        if($y4 == 0) $bs4 = 'беспл.';
                        if($y5 == 0) $bs5 = 'беспл.';
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
        <?php if(count($results) == $ir){ ?>
            <div class="block-rezult__item block-rezult__item-last">
                <div class="r-last-wrp">
                    <h2>Оставьте свои <br>контакты</h2>
                    <span class="block-rezult__desc">Не нашли, что хотели? Мы перезвоним вам</span>
                </div>
                <a class="btn2 btn-style active-modal" href="#modal-window">Заказать обратный звонок</a>
            </div>
        <?php } ?>
    </div>
    
    <?php
}

