<?php
/**
 * Блок подбора медицинского страхования
 * 
 * Содержит форму для сбора данных о компании и сотрудниках,
 * блок загрузки результатов и блок отображения предложений
 */
?>

<section id="block-top" class="block-top">
  <div class="max-w-6xl mx-auto">
    <div class="block-top__wrap pt-8 pb-16 px-2.5 lg:pt-8 lg:px-8 2xl:px-12 6xl:px-16 6xl:py-10">
      <div class="block-top__wrp h2-style max-w-2xl mx-auto 2xl:mx-0">

        <?php // Первый экран - приветствие и описание процесса ?>
        <div class="block-v1 mb-7 md:mb-9 text-center xl:text-left">
          <h2 class="max-w-lg tracking-wide">Подберем самые выгодные предложения за <span>3 минуты</span></h2>
          <span class="sub-zag text-base mx-auto xl:mx-0">
            Заполните данные о компании,
            <wbr /> и мы предложим уникальный вариант
            <wbr /> под ваши требования
          </span>
        </div>

        <?php // Второй экран - заголовок формы (показывается после первого шага) ?>
        <div class="block-v2">
          <h2>Оформление заявки <br>на медицинское страхование</h2>
          <span>Заполните все поля, чтобы получить самые выгодные условия <br>для вашей компании</span>
        </div>

        <?php // Форма сбора данных о компании ?>
        <div class="kviz-wrap flex flex-col gap-7 max-w-lg bg-white rounded-[10px] p-5 xl:py-9 xl:px-12">
          <h3 class="font-semibold text-2xl text-black text-center 3xl:text-left">Расскажите о коллективе</h3>
          <div class="kviz-wrp grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-6 place-content-start">

            <?php // Поле: Наименование компании ?>
            <div class="input-wrp relative input-wrp1">
              <div class="label label1">Наименование компании</div>
              <input name="1" class="validate2" type="text" placeholder="Наименование компании">
              <span class="errorMsg errorMsg2"></span>
            </div>

            <?php // Поле: Количество сотрудников ?>
            <div class="input-wrp relative input-wrp2">
              <div class="label label-k1 ml-1">Количество сотрудников</div>
              <input name="2" class="validate1" type="text" placeholder="128">
              <span class="text-xs text-red-1 ml-1 mt-1 errorMsg1 absolute"></span>
            </div>

            <?php // Поле: ИНН компании ?>
            <div class="input-wrp relative input-wrp3">
              <div class="label label-kt1">ИНН</div>
              <input name="3" class="validate3" type="text" placeholder="ИНН">
              <span class="errorMsg errorMsg3"></span>
            </div>

            <?php // Поле: Уровень покрытия с подсказкой ?>
            <div class="input-wrp relative input-wrp4">
              <div class="label label-k2 ml-1">
                Уровень покрытия
                <?php // Всплывающая подсказка с описанием уровней покрытия ?>
                <div class="ml-4">
                  <a class="label-hover-hover" href="#">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                      <path d="M14.4668 8C14.4668 4.42856 11.5714 1.5332 8 1.5332C4.42856 1.5332 1.5332 4.42856 1.5332 8C1.5332 11.5714 4.42856 14.4668 8 14.4668C11.5714 14.4668 14.4668 11.5714 14.4668 8ZM8 7.4668C8.29455 7.4668 8.5332 7.70545 8.5332 8V10.5775H8.778C9.07245 10.5776 9.3112 10.8168 9.3112 11.1113C9.31108 11.4057 9.07237 11.6444 8.778 11.6445H8C7.70552 11.6445 7.46691 11.4058 7.4668 11.1113V8.5332H7.22201C6.92755 8.53309 6.6888 8.29448 6.6888 8C6.6888 7.70552 6.92755 7.46691 7.22201 7.4668H8ZM8.00781 4.35547C8.30228 4.35549 8.5409 4.59423 8.54102 4.88867C8.54102 5.18321 8.30235 5.42251 8.00781 5.42253H8C7.70545 5.42253 7.4668 5.18322 7.4668 4.88867C7.46691 4.59422 7.70552 4.35547 8 4.35547H8.00781ZM15.5332 8C15.5332 12.1605 12.1605 15.5332 8 15.5332C3.83946 15.5332 0.466797 12.1605 0.466797 8C0.466797 3.83946 3.83946 0.466797 8 0.466797C12.1605 0.466797 15.5332 3.83946 15.5332 8Z" fill="#1A1A1A" />
                    </svg>
                  </a>
                  <div class="label-hover-wrap">
                    <b>Уровень покрытия</b>
                    <ul>
                      <li>Базовый: основное покрытие, включая обязательные медицинские услуги</li>
                      <li>Комфорт: Расширенная помощь, включая амбулаторное лечение и доп. услуги</li>
                      <li>Премиум: Полный пакет с приоритетным обслуживанием и доп. опциями</li>
                    </ul>
                  </div>
                </div>
              </div>
              <select class="main-select" name="4">
                <option value="Базовый">Базовый</option>
                <option value="Комфорт">Комфорт</option>
                <option value="Премиум">Премиум</option>
              </select> 
            </div>

            <?php // Поле: Регион обслуживания (множественный выбор) ?>
            <div class="input-wrp relative input-wrp5">
              <div class="label label-k3 ml-1">Регион обслуживания</div>
              <select class="region-select" multiple name="5">
                <?php 
                  // Загружаем список городов из функции сity()
                  if(!empty(сity())){
                    foreach (сity() as $value) {
                      echo '<option value="'.$value.'">'.$value.'</option>';
                    } 
                  } 
                ?>
              </select>
              <span class="text-xs text-red-1 ml-1 mt-1 region-error hidden absolute">
                Выберите хотя бы один регион
              </span>
            </div>

            <?php // Поле: ФИО ответственного лица ?>
            <div class="input-wrp relative input-wrp6">
              <div class="label label-kt2">ФИО ответственного</div>
              <input name="6" class="validate4" type="text" placeholder="Введите ФИО ответственного">
              <span class="text-xs text-red-1 ml-1 mt-1 absolute"></span>
            </div>

            <?php // Поле: Email ответственного ?>
            <div class="input-wrp relative input-wrp7">
              <div class="label label-kt4">Введите рабочую почту</div>
              <input name="7" class="validate5" type="email" placeholder="Рабочая почта">
              <span class="errorMsg errorMsg5"></span>
              <span class="errorMsg errorMsg55"></span>
            </div>

            <?php // Поле: Телефон ответственного ?>
            <div class="input-wrp relative input-wrp8">
              <div class="label label-kt3">Телефон</div>
              <input name="8" class="validate6 phone-mask" type="tel" placeholder="Введите номер телефона">
              <span class="errorMsg errorMsg6"></span>
              <span class="errorMsg errorMsg66"></span>
            </div>

            <?php // Кнопка отправки формы и скрытая форма Contact Form 7 ?>
            <div class="input-wrp relative input-wrp9">
              <div class="btn-submit-wrp">
                <?php // Кнопка "Дальше" - отправляет данные на расчет ?>
                <button class="btn-submit btn-submit1 click-step1" type="button">
                    Дальше

                  <span class="hidden">
                    <svg width="10" height="18" viewBox="0 0 10 18" fill="none">
                        <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
                    </svg>
                    </span>
                </button>

                <?php // Кнопка "Обновить" - для обновления результатов (скрыта по умолчанию) ?>
                <div class="update-rez">
                  <button class="btn-submit click-step2" type="button">
                      Обновить
                    <svg width="10" height="18" viewBox="0 0 10 18" fill="none">
                        <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
                    </svg>
                    </button>
                </div>

                <?php // Скрытая форма Contact Form 7 для отправки данных ?>
                <?php echo do_shortcode( '[contact-form-7 id="363f513" title="Контактная форма 1"]'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php // Блок загрузки - показывается во время расчета предложений ?>
    <div style="display: none;" class="block-process d-flex d-m">
      <div class="block-process__wrp">
        <h3>Подождите, ведем расчет</h3>
        <span>Сравните предложения от страховых компаний, выберите лучшееи оформите полис без визита в офис</span>
        <img class="loader" src="<?php bloginfo('template_url'); ?>/assets/img/loader.svg" alt="">
      </div>
    </div>

    <?php // Блок результатов - отображает предложения страховых компаний ?>
    <div id="block-rezult" style="display: none;" class="block-rezult">
      <h3 class="prdl-dlvs">Предложения для вас</h3>

      <?php // Блок для крупных компаний - индивидуальный расчет через менеджера ?>
      <div class="block-large">
        <h2>Оу, вы крупная рыба! Для вас <br>индивидуальный расчет</h2>
        <span>Оставьте свои контактные данные, и наш менеджер вернется <br>с коммерческим предложением</span>
        <div class="block-large__forma">
          <div class="form-block">
            <?php // Форма для связи с менеджером ?>
            <?php echo do_shortcode('[contact-form-7 id="3dfc896" title="Контактная форма-2"]'); ?>
          </div>
        </div>
      </div>

      <?php // Сетка с предложениями страховых компаний (заполняется через JavaScript) ?>
      <div class="block-rezult__grid grids">
      </div>
    </div>
  </div>
</section>

