<?php
/**
 * Шаблон для HTML вывода результатов фильтрации через API
 * Используется когда format=html
 */

/**
 * Получает отображаемое имя страховщика
 */
function get_insurer_display_name(string $insurer): string {
    $name_mapping = [
        'ООО «Капитал Лайф Страхование Жизни»' => 'Капитал Лайф',
    ];
    return $name_mapping[$insurer] ?? $insurer;
}

/**
 * Форматирует цену услуги
 */
function format_service_price($value, string $field_key): string {
    $price = $value[$field_key] ?? '';

    // Проверка на пустое значение или Н/Д
    if ($price === '' || $price === '#Н/Д') {
        return '';
    }

    // Проверка на бесплатную услугу
    if ($price == 0) {
        return 'беспл.';
    }

    // Форматирование цены
    $cleaned = str_replace([' ', ','], ['', '.'], $price);
    return number_format((float) $cleaned, 0, ' ', ' ') . ' ₽';
}

/**
 * Выводит элемент услуги
 */
function render_service_item(string $label, string $price_html): void {
    if (empty($price_html)) {
        return;
    }
    ?>
    <li>
        <?php echo esc_html($label); ?>
        <div class="li-val">
            <i class="li-val__hover"></i>
            <div class="li-val__wrp">
                <span><?php echo esc_html($price_html); ?></span>
            </div>
        </div>
    </li>
    <?php
}

// Конфигурация услуг
$services = [
    ['label' => 'Поликлиника', 'field' => 'Поликлиника'],
    ['label' => 'Стоматология', 'field' => 'Стоматология'],
    ['label' => 'Скорая помощь', 'field' => 'Скорая_помощь'],
    ['label' => 'Госпитализация', 'field' => 'Госпитализация'],
    ['label' => 'Вызов врача на дом', 'field' => 'Вызов_врача_на_дом'],
];

// Подсчитываем общее количество секций для отображения последнего блока
$total_sections = count($results) + (!empty($fallback_data) ? 1 : 0);
$current_section = 0;

// Выводим отфильтрованные данные
if (!empty($results)) {
    foreach ($results as $key => $value2) {
        $current_section++;
        echo '<h3 class="text-center text-base mb-1.5 xl:font-semibold xl:text-4xl">' . esc_html($key) . '</h3>';
        ?>
        <div class="block-rezult__grid grid ghd-grid">
            <?php foreach ($value2 as $value) { 
                $suma_price = function_exists('calculate_total_price') ? calculate_total_price($value) : 0;
                $insurer_name = $value["Страховщик"] ?? '';
                $insurer_display_name = get_insurer_display_name($insurer_name);
                $cl_w = ($insurer_name === 'Сбербанк страхование') ? ' cl-width' : '';
                ?>
                <div class="block-rezult__item">
                    <div class="rezult-top d-flex d-jm">
                        <h5 class="flex-logotypes d-flex<?php echo esc_attr($cl_w); ?>">
                            <?php get_insurer_logo($insurer_name); ?>
                            <?php echo esc_html($insurer_display_name); ?>
                        </h5>
                        <div class="rezult-top__price">
                            <span class="font-semibold text-right text-base 3xl:text-xl"><?php echo number_format($suma_price, 0, ' ', ' '); ?> ₽</span>
                            <span class="desc-r">в год за человека</span>
                        </div>
                    </div>  
                    <span class="program-composition">Состав программы:</span>
                    <div class="rezult-data">
                        <ul>
                            <?php 
                            foreach ($services as $service) {
                                $price_html = format_service_price($value, $service['field']);
                                render_service_item($service['label'], $price_html);
                            }
                            ?>
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
        <?php foreach ($fallback_data as $value) { 
            $suma_price = function_exists('calculate_total_price') ? calculate_total_price($value) : 0;
            $insurer_name = $value["Страховщик"] ?? '';
            $insurer_display_name = get_insurer_display_name($insurer_name);
            $cl_w = ($insurer_name === 'Сбербанк страхование') ? ' cl-width' : '';
            ?>
            <div class="block-rezult__item">
                <div class="rezult-top d-flex d-jm">
                    <h5 class="flex-logotypes d-flex<?php echo esc_attr($cl_w); ?>">
                        <?php get_insurer_logo($insurer_name); ?>
                        <?php echo esc_html($insurer_display_name); ?>
                    </h5>
                    <div class="rezult-top__price">
                        <div class="price-r"><?php echo number_format($suma_price, 0, ' ', ' '); ?> ₽</div>
                        <div class="desc-r">в год за человека</div>
                    </div>
                </div>
                <span class="program-composition">Состав программы:</span>
                <div class="rezult-data">
                    <ul>
                        <?php 
                        foreach ($services as $service) {
                            $price_html = format_service_price($value, $service['field']);
                            render_service_item($service['label'], $price_html);
                        }
                        ?>
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

// Если нет результатов вообще
if (empty($results) && empty($fallback_data)) {
    echo '<!-- Нет результатов по заданным критериям -->';
}

