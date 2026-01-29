<section id="block-top" class="base-container block-top">
  <div class="wrap">
    <!-- block-top__wrp-final -->
    <div class="block-top__wrap">
      <div class="block-top__wrp h2-style">
        <div class="block-v1 text-center mb-8">
          <h2 class="tracking-wide">Калькулятор ДМС</h2>
          <p class="text-white -mt-3 text-lg">Подберем самые выгодные предложения <span>за 3 минуты</span></p>
        </div>
        <div class="block-v2">
          <h2>Оформление заявки <br>на медицинское страхование</h2>
          <span>Заполните все поля, чтобы получить самые выгодные условия <br>для вашей компании</span>
        </div>
        <div class="kviz-wrap max-w-[880px] mx-auto px-5 py-5 md:py-10 md:px-12">
          <h3 class="kviz-title">Расскажите о коллективе</h3>
          <div class="kviz-wrp d-flex-wrap d-j">
            <div class="input-wrp input-wrp1">
              <div class="label label1">Наименование компании</div>
              <input name="1" class="validate2" type="text" placeholder="Наименование компании">
              <span class="errorMsg errorMsg2"></span>
            </div>
            <div class="mb-3 mt-5 relative w-[320px] input-wrp2">
              <div class="label label-k1">Количество сотрудников</div>
              <input name="2" class="validate1" type="text" placeholder="128">
              <span class="errorMsg errorMsg1"></span>
            </div>
            <div class="input-wrp input-wrp3">
              <div class="label label-kt1">ИНН</div>
              <input name="3" class="validate3" type="text" placeholder="ИНН">
              <span class="errorMsg errorMsg3"></span>
            </div>

            <!-- start: регион обслуживания -->
            <div class="mb-3 mt-5 relative w-[444px] input-wrp5">
              <div class="label label-k3">Регион обслуживания</div>
              <select class="region-select" multiple name="5">
                <?php 
                  if(!empty(сity())){
                    foreach (сity() as $value) {
                      echo '<option value="'.$value.'">'.$value.'</option>';
                    } 
                  }  
                ?>
              </select>
              <span class="region-error" style="color:red; display:none;">Выберите хотя бы один регион</span>
            </div>
            <!-- end: регион обслуживания -->

            <!-- start: Выбор страховщика -->
            <div class="w-full input-wrp4">
              <label class="flex gap-2 text-lg mb-1.5 pl-1" for="insurer">
                <img class="" src="<?php bloginfo('template_url'); ?>/img/icons/clinic.svg" alt="clinic icon">
                Выбор страховщика
              </label>
              <select class="insurer-select" name="insurer" multiple>
                <option value="Зетта">Зетта</option>
                <option value="Ингос">Ингос</option>
                <option value="Капитал Лайф">Капитал Лайф</option>
                <option value="Пари">Пари</option>
                <option value="РГС">РГС</option>
                <option value="Сбер">Сбер</option>
              </select>
              <span class="insurer-error" style="color:red; display:none;">Выберите хотя бы одного страховщика</span>
            </div>
            <!-- end: Выбор страховщика -->

            <div class="input-wrp input-wrp6">
              <div class="label label-kt2">ФИО ответственного</div>
              <input name="6" class="validate4" type="text" placeholder="Введите ФИО ответственного">
              <span class="errorMsg errorMsg4"></span>
            </div>

            <div class="input-wrp input-wrp7">
              <div class="label label-kt4">Введите рабочую почту</div>
              <input name="7" class="validate5" type="email" placeholder="Рабочая почта">
              <span class="errorMsg errorMsg5"></span>
              <span class="errorMsg errorMsg55"></span>
            </div>
            <div class="input-wrp input-wrp8">
              <div class="label label-kt3">Телефон</div>
              <input name="8" class="validate6 phone-mask" type="tel" placeholder="Введите номер телефона">
              <span class="errorMsg errorMsg6"></span>
              <span class="errorMsg errorMsg66"></span>
            </div>

            <div class="mt-4 w-full">
              <label class="flex gap-2 text-lg mb-1.5 pl-1">
                <img class="" src="<?php bloginfo('template_url'); ?>/img/icons/service.svg" alt="clinic icon">
                Выбор услуг
              </label>

              <div class="bg-grey-1 px-5 py-7 rounded-xl">

                <div class="flex justify-between items-center py-1.5">
                  <div class="form-group">
                    <div class="checkbox-wrap">
                      <input type="checkbox" name="service-polyclinic" data-service="polyclinic" checked disabled>
                    </div>
                    <label for="service-polyclinic" class="cursor-pointer">Поликлиника</label>
                  </div>

                  <span class="text-xs text-black-secondary">
                    обязательно
                    </span>
                </div>

                <hr class="text-grey-4">

                <div class="flex justify-between items-center py-1.5">
                  <div class="form-group">
                    <div class="checkbox-wrap">
                      <input type="checkbox" name="service-dentistry" data-service="dentistry" checked disabled>
                    </div>
                    <label for="service-dentistry" class="cursor-pointer">Стоматология</label>
                  </div>

                  <span class="text-xs text-black-secondary">
                    обязательно
                    </span>
                </div>

                <hr class="text-grey-4">

                <div class="flex justify-between items-center py-1.5">
                  <div class="form-group">
                    <div class="checkbox-wrap">
                      <input type="checkbox" name="service-hospitalization" data-service="hospitalization" id="service-hospitalization">
                    </div>
                    <label for="service-hospitalization" class="cursor-pointer">Госпитализация</label>
                  </div>
                </div>

                <hr class="text-grey-4">

                <div class="flex justify-between items-center py-1.5">
                  <div class="form-group">
                    <div class="checkbox-wrap">
                      <input type="checkbox" name="service-doctor-home" data-service="doctor-home">
                    </div>
                    <label for="service-doctor-home" class="cursor-pointer">Вызов врача на дом</label>
                  </div>
                </div>

                
              </div>
            </div>

            <div class="w-full mt-4">
              <button class="btn-submit btn-submit1 click-step1" type="button">
                Показать предложения
                <svg width="10" height="18" viewBox="0 0 10 18" fill="none">
                  <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
                </svg>
              </button>

              <div class="update-rez">
                <button class="btn-submit click-step2" type="button">
                    Обновить
                  <svg width="10" height="18" viewBox="0 0 10 18" fill="none">
                      <path d="M0.5 17L8.5 9L0.499999 0.999999" stroke="white"></path>
                  </svg>
                  </button>
              </div>
              <?php echo do_shortcode( '[contact-form-7 id="363f513" title="Контактная форма 1"]'); ?>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div style="display: none;" class="block-process d-flex d-m">
      <div class="block-process__wrp">
        <h3>Подождите, ведем расчет</h3>
        <span>Сравните предложения от страховых компаний, выберите лучшееи оформите полис без визита в офис</span>
        <img class="loader" src="<?php bloginfo('template_url'); ?>/img/loader.svg" alt="">
      </div>
    </div>
    <div id="block-rezult" style="display: none;" class="block-rezult">
      <!-- <div class="block-rezult__top d-flex d-m">
        <h3>Предложение для вас</h3>
        <div>Уникальное предложение, соответствующее вашим параметрам, доступно по спецусловиям в течение <span>2 часов</span></div>
      </div> -->
      <h3 class="prdl-dlvs">Предложения для вас</h3>

      <div class="block-large">
        <h2>Оу, вы крупная рыба! Для вас <br>индивидуальный расчет</h2>
        <span>Оставьте свои контактные данные, и наш менеджер вернется <br>с коммерческим предложением</span>
        <div class="block-large__forma">
          <div class="form-block">
            <?php echo do_shortcode('[contact-form-7 id="3dfc896" title="Контактная форма-2"]'); ?>
          </div>
        </div>
      </div>
      <div class="block-rezult__grid grids">
      </div>
    </div>
  </div>
</section>

