<?php
/**
 * Интеграция с Contact Form 7
 * 
 * Обработка отправки данных из React формы через Contact Form 7 API
 */

// Предотвращаем прямой доступ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Вспомогательная функция для логирования в debug.log
 * 
 * @param mixed $message Сообщение для логирования
 * @param string $level Уровень логирования (info, error, warning)
 */
function inssmart_log($message, $level = 'info') {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    $log_file = WP_CONTENT_DIR . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $level_upper = strtoupper($level);
    
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }
    
    $log_message = "[{$timestamp}] [{$level_upper}] {$message}\n";
    
    // Создаем файл, если его нет, и устанавливаем правильные права
    if (!file_exists($log_file)) {
        @touch($log_file);
        @chmod($log_file, 0664);
    }
    
    // Пытаемся записать в debug.log
    @error_log($log_message, 3, $log_file);
}

/**
 * Проверка наличия Contact Form 7
 */
function inssmart_check_cf7_dependency() {
    if (!class_exists('WPCF7_ContactForm')) {
        return false;
    }
    return true;
}

/**
 * Получение настроек формы из опций
 */
function inssmart_get_cf7_form_data($form_type = 'order') {
    $settings = get_option('inssmart_settings', array());
    
    $form_id_key = $form_type === 'order' ? 'order_form_id' : 'callback_form_id';
    $form_id = isset($settings[$form_id_key]) ? $settings[$form_id_key] : '';
    
    $mapping_key = $form_type === 'order' ? 'order' : 'callback';
    $field_mapping = isset($settings['field_mapping'][$mapping_key]) 
        ? $settings['field_mapping'][$mapping_key] 
        : array();
    
    return array(
        'form_id' => $form_id,
        'field_mapping' => $field_mapping,
    );
}

/**
 * Маппинг данных React формы на поля Contact Form 7
 */
function inssmart_map_form_data($form_data, $form_type = 'order', $additional_data = array()) {
    $form_settings = inssmart_get_cf7_form_data($form_type);
    $field_mapping = $form_settings['field_mapping'];
    
    $mapped_data = array();
    
    if ($form_type === 'order') {
        // Маппинг для формы заказа
        $mapping_fields = array(
            'organizationName' => 'organizationName',
            'inn' => 'inn',
            'responsiblePerson' => 'responsiblePerson',
            'workEmail' => 'workEmail',
            'workPhone' => 'workPhone',
            'serviceRegion' => 'serviceRegion',
            'coverageLevel' => 'coverageLevel',
            'numberOfEmployees' => 'numberOfEmployees',
            'insurerName' => 'insurerName',
            'totalPrice' => 'totalPrice',
            'city' => 'city',
        );
        
        // Маппим основные поля из formData.step3
        if (isset($form_data['step3'])) {
            foreach ($mapping_fields as $react_field => $default_cf7_field) {
                if (isset($form_data['step3'][$react_field])) {
                    $cf7_field = isset($field_mapping[$react_field]) 
                        ? $field_mapping[$react_field] 
                        : $default_cf7_field;
                    $mapped_data[$cf7_field] = $form_data['step3'][$react_field];
                }
            }
        }
        
        // Маппим дополнительные данные
        if (isset($additional_data['coverageLevel'])) {
            $cf7_field = isset($field_mapping['coverageLevel']) 
                ? $field_mapping['coverageLevel'] 
                : 'coverageLevel';
            $mapped_data[$cf7_field] = $additional_data['coverageLevel'];
        }
        
        if (isset($additional_data['selectedCities'])) {
            $cf7_field = isset($field_mapping['serviceRegion']) 
                ? $field_mapping['serviceRegion'] 
                : 'serviceRegion';
            $mapped_data[$cf7_field] = is_array($additional_data['selectedCities']) 
                ? implode(', ', $additional_data['selectedCities']) 
                : $additional_data['selectedCities'];
        }
        
        if (isset($additional_data['numberOfEmployees'])) {
            $cf7_field = isset($field_mapping['numberOfEmployees']) 
                ? $field_mapping['numberOfEmployees'] 
                : 'numberOfEmployees';
            $mapped_data[$cf7_field] = $additional_data['numberOfEmployees'];
        }
        
        // Маппим данные выбранного оффера
        if (isset($additional_data['selectedOffer'])) {
            $offer = $additional_data['selectedOffer'];
            if (isset($offer['insurerName'])) {
                $cf7_field = isset($field_mapping['insurerName']) 
                    ? $field_mapping['insurerName'] 
                    : 'insurerName';
                $mapped_data[$cf7_field] = $offer['insurerName'];
            }
            if (isset($offer['city'])) {
                $cf7_field = isset($field_mapping['city']) 
                    ? $field_mapping['city'] 
                    : 'city';
                $mapped_data[$cf7_field] = $offer['city'];
            }
            if (isset($offer['record']['total_price'])) {
                $cf7_field = isset($field_mapping['totalPrice']) 
                    ? $field_mapping['totalPrice'] 
                    : 'totalPrice';
                $mapped_data[$cf7_field] = $offer['record']['total_price'];
            }
        }
        
    } else {
        // Маппинг для формы обратного звонка
        $mapping_fields = array(
            'callbackName' => 'callbackName',
            'callbackPhone' => 'callbackPhone',
            'isAgreed' => 'isAgreed',
        );
        
        if (isset($form_data['step3'])) {
            foreach ($mapping_fields as $react_field => $default_cf7_field) {
                if (isset($form_data['step3'][$react_field])) {
                    $cf7_field = isset($field_mapping[$react_field]) 
                        ? $field_mapping[$react_field] 
                        : $default_cf7_field;
                    $value = $form_data['step3'][$react_field];
                    
                    // Обработка boolean для чекбоксов
                    if ($react_field === 'isAgreed') {
                        $mapped_data[$cf7_field] = $value ? '1' : '';
                    } else {
                        $mapped_data[$cf7_field] = $value;
                    }
                }
            }
        }
    }
    
    // Добавляем subId и clickId из различных источников
    // Проверяем в additional_data
    if (isset($additional_data['subId']) && !empty($additional_data['subId'])) {
        $mapped_data['subId'] = sanitize_text_field($additional_data['subId']);
    } elseif (isset($additional_data['sub_id']) && !empty($additional_data['sub_id'])) {
        $mapped_data['subId'] = sanitize_text_field($additional_data['sub_id']);
    }
    
    if (isset($additional_data['clickId']) && !empty($additional_data['clickId'])) {
        $mapped_data['clickId'] = sanitize_text_field($additional_data['clickId']);
    } elseif (isset($additional_data['click_id']) && !empty($additional_data['click_id'])) {
        $mapped_data['clickId'] = sanitize_text_field($additional_data['click_id']);
    }
    
    // Проверяем в form_data
    if (!isset($mapped_data['subId']) && isset($form_data['subId']) && !empty($form_data['subId'])) {
        $mapped_data['subId'] = sanitize_text_field($form_data['subId']);
    } elseif (!isset($mapped_data['subId']) && isset($form_data['sub_id']) && !empty($form_data['sub_id'])) {
        $mapped_data['subId'] = sanitize_text_field($form_data['sub_id']);
    }
    
    if (!isset($mapped_data['clickId']) && isset($form_data['clickId']) && !empty($form_data['clickId'])) {
        $mapped_data['clickId'] = sanitize_text_field($form_data['clickId']);
    } elseif (!isset($mapped_data['clickId']) && isset($form_data['click_id']) && !empty($form_data['click_id'])) {
        $mapped_data['clickId'] = sanitize_text_field($form_data['click_id']);
    }
    
    // Проверяем в $_POST (на случай, если переданы через скрытые поля)
    if (!isset($mapped_data['subId']) && isset($_POST['subId']) && !empty($_POST['subId'])) {
        $mapped_data['subId'] = sanitize_text_field($_POST['subId']);
    } elseif (!isset($mapped_data['subId']) && isset($_POST['sub_id']) && !empty($_POST['sub_id'])) {
        $mapped_data['subId'] = sanitize_text_field($_POST['sub_id']);
    }
    
    if (!isset($mapped_data['clickId']) && isset($_POST['clickId']) && !empty($_POST['clickId'])) {
        $mapped_data['clickId'] = sanitize_text_field($_POST['clickId']);
    } elseif (!isset($mapped_data['clickId']) && isset($_POST['click_id']) && !empty($_POST['click_id'])) {
        $mapped_data['clickId'] = sanitize_text_field($_POST['click_id']);
    }
    
    // Проверяем в $_GET (на случай, если переданы через URL)
    if (!isset($mapped_data['subId']) && isset($_GET['subId']) && !empty($_GET['subId'])) {
        $mapped_data['subId'] = sanitize_text_field($_GET['subId']);
    } elseif (!isset($mapped_data['subId']) && isset($_GET['sub_id']) && !empty($_GET['sub_id'])) {
        $mapped_data['subId'] = sanitize_text_field($_GET['sub_id']);
    }
    
    if (!isset($mapped_data['clickId']) && isset($_GET['clickId']) && !empty($_GET['clickId'])) {
        $mapped_data['clickId'] = sanitize_text_field($_GET['clickId']);
    } elseif (!isset($mapped_data['clickId']) && isset($_GET['click_id']) && !empty($_GET['click_id'])) {
        $mapped_data['clickId'] = sanitize_text_field($_GET['click_id']);
    }
    
    return $mapped_data;
}

/**
 * Валидация входящих данных
 */
function inssmart_validate_submit_data($form_data, $form_type = 'order') {
    $errors = array();

    if ($form_type === 'order') {
        if (!isset($form_data['step3'])) {
            $errors[] = 'Отсутствуют данные формы';
            return $errors;
        }

        $required_fields = array('organizationName', 'inn', 'responsiblePerson', 'workEmail', 'workPhone');
        foreach ($required_fields as $field) {
            if (empty($form_data['step3'][$field])) {
                $errors[] = sprintf('Поле %s обязательно для заполнения', $field);
            }
        }

        // Валидация email
        if (!empty($form_data['step3']['workEmail']) && !is_email($form_data['step3']['workEmail'])) {
            $errors[] = 'Некорректный email адрес';
        }

    } else {
        if (!isset($form_data['step3'])) {
            $errors[] = 'Отсутствуют данные формы';
            return $errors;
        }

        $required_fields = array('callbackName', 'callbackPhone');
        foreach ($required_fields as $field) {
            if (empty($form_data['step3'][$field])) {
                $errors[] = sprintf('Поле %s обязательно для заполнения', $field);
            }
        }

        if (empty($form_data['step3']['isAgreed'])) {
            $errors[] = 'Необходимо согласие на обработку персональных данных';
        }
    }

    return $errors;
}

/**
 * Основная функция отправки данных в Contact Form 7
 */
function inssmart_submit_to_cf7($form_data, $form_type = 'order', $additional_data = array()) {
    // Проверка наличия Contact Form 7
    if (!inssmart_check_cf7_dependency()) {
        return array(
            'success' => false,
            'message' => 'Contact Form 7 не установлен или не активирован',
        );
    }

    // Валидация данных
    $validation_errors = inssmart_validate_submit_data($form_data, $form_type);
    if (!empty($validation_errors)) {
        return array(
            'success' => false,
            'message' => implode('. ', $validation_errors),
            'errors' => $validation_errors,
        );
    }

    // Получаем настройки формы
    $form_settings = inssmart_get_cf7_form_data($form_type);
    $form_id = $form_settings['form_id'];

    if (empty($form_id)) {
        return array(
            'success' => false,
            'message' => sprintf('Форма Contact Form 7 для типа "%s" не настроена', $form_type),
        );
    }

    // Получаем объект формы
    $contact_form = WPCF7_ContactForm::get_instance($form_id);

    if (!$contact_form) {
        return array(
            'success' => false,
            'message' => 'Форма Contact Form 7 не найдена',
        );
    }

    // Маппим данные
    $mapped_data = inssmart_map_form_data($form_data, $form_type, $additional_data);
    
    // Логирование для отладки
    if (defined('WP_DEBUG') && WP_DEBUG) {
        inssmart_log('inssmart_submit_to_cf7 - mapped_data keys: ' . implode(', ', array_keys($mapped_data)), 'info');
        inssmart_log('inssmart_submit_to_cf7 - subId в mapped_data: ' . (isset($mapped_data['subId']) ? $mapped_data['subId'] : 'не найден'), 'info');
        inssmart_log('inssmart_submit_to_cf7 - clickId в mapped_data: ' . (isset($mapped_data['clickId']) ? $mapped_data['clickId'] : 'не найден'), 'info');
        inssmart_log('inssmart_submit_to_cf7 - additional_data: ' . print_r($additional_data, true), 'info');
    }

    // Подготавливаем данные для отправки
    // Сохраняем оригинальный $_POST
    $original_post = $_POST;

    // Устанавливаем данные формы
    // ВАЖНО: Объединяем с существующим $_POST, чтобы не потерять другие данные
    $_POST = array_merge($_POST, $mapped_data);
    
    // Убеждаемся, что subId и clickId точно попадут в $_POST
    if (isset($mapped_data['subId']) && !empty($mapped_data['subId'])) {
        $_POST['subId'] = $mapped_data['subId'];
    }
    if (isset($mapped_data['clickId']) && !empty($mapped_data['clickId'])) {
        $_POST['clickId'] = $mapped_data['clickId'];
    }

    // Добавляем служебные поля CF7
    $_POST['_wpcf7'] = $form_id;
    $_POST['_wpcf7_version'] = WPCF7_VERSION;
    $_POST['_wpcf7_locale'] = get_locale();
    $_POST['_wpcf7_unit_tag'] = 'wpcf7-f' . $form_id . '-o1';
    $_POST['_wpcf7_container_post'] = 0;

    // Отправляем форму через метод submit() объекта формы
    // Метод submit() создает submission автоматически и возвращает результат
    $submit_result = $contact_form->submit();
    
    // Восстанавливаем оригинальный $_POST
    $_POST = $original_post;
    
    // Получаем статус из результата
    $status = isset($submit_result['status']) ? $submit_result['status'] : 'unknown';
    
    // Получаем submission для дополнительной информации
    $submission = WPCF7_Submission::get_instance();
    
    if ($status === 'mail_sent' || $status === 'mail_failed') {
        // Форма обработана (даже если email не отправился, данные сохранены)
        return array(
            'success' => true,
            'message' => isset($submit_result['message']) ? $submit_result['message'] : 'Форма успешно отправлена',
            'status' => $status,
            'submission' => $submission,
        );
    } else {
        // Ошибка валидации или другая проблема
        $invalid_fields = array();
        if ($submission) {
            $invalid_fields = $submission->get_invalid_fields();
        }
        
        $error_message = isset($submit_result['message']) 
            ? $submit_result['message'] 
            : 'Ошибка при отправке формы';
            
        return array(
            'success' => false,
            'message' => $error_message,
            'status' => $status,
            'invalid_fields' => $invalid_fields,
        );
    }
}

