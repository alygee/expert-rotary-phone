# Инструкция по настройке Contact Form 7 для плагина Inssmart

## Шаг 1: Создание форм в Contact Form 7

### Форма для заказа (Order Form)

1. Перейдите в **Contact → Contact Forms** в админ-панели WordPress
2. Нажмите **Add New** для создания новой формы
3. Назовите форму, например: "Форма заказа Inssmart"
4. Добавьте поля в форму, используя теги Contact Form 7:

```
[text* your-organizationName placeholder "Название организации"]
[text* your-inn placeholder "ИНН"]
[text* your-responsiblePerson placeholder "Ответственное лицо"]
[email* your-workEmail placeholder "Рабочий email"]
[tel* your-workPhone placeholder "Рабочий телефон"]
[text your-serviceRegion placeholder "Регион обслуживания"]
[text your-coverageLevel placeholder "Уровень покрытия"]
[text your-numberOfEmployees placeholder "Количество сотрудников"]
[text your-insurerName placeholder "Название страховщика"]
[text your-totalPrice placeholder "Общая стоимость"]
[text your-city placeholder "Город"]
```

**Важно:** Имена полей (например, `your-organizationName`) должны совпадать с теми, что вы укажете в настройках mapping.

### Форма для обратного звонка (Callback Form)

1. Создайте еще одну форму
2. Назовите её, например: "Форма обратного звонка Inssmart"
3. Добавьте поля:

```
[text* your-callbackName placeholder "Имя"]
[tel* your-callbackPhone placeholder "Телефон"]
[checkbox* your-isAgreed "1" "Согласен на обработку персональных данных"]
```

## Шаг 2: Настройка mapping в плагине Inssmart

1. Перейдите в **Настройки → Inssmart Form** в админ-панели WordPress

2. **Выберите формы Contact Form 7:**
   - **Order Form (CF7 ID)**: выберите форму для заказов
   - **Callback Form (CF7 ID)**: выберите форму для обратного звонка

3. **Настройте Field Mapping - Order Form:**
   
   После выбора формы вы увидите список доступных полей в выбранной CF7 форме.
   
   Для каждого поля React формы укажите соответствующее имя поля из Contact Form 7:
   
   | React Field | CF7 Field Name (пример) |
   |-------------|------------------------|
   | organizationName | your-organizationName |
   | inn | your-inn |
   | responsiblePerson | your-responsiblePerson |
   | workEmail | your-workEmail |
   | workPhone | your-workPhone |
   | serviceRegion | your-serviceRegion |
   | coverageLevel | your-coverageLevel |
   | numberOfEmployees | your-numberOfEmployees |
   | insurerName | your-insurerName |
   | totalPrice | your-totalPrice |
   | city | your-city |

4. **Настройте Field Mapping - Callback Form:**
   
   | React Field | CF7 Field Name (пример) |
   |-------------|------------------------|
   | callbackName | your-callbackName |
   | callbackPhone | your-callbackPhone |
   | isAgreed | your-isAgreed |

5. Нажмите **Save Settings**

## Шаг 3: Как узнать имена полей в Contact Form 7

### Способ 1: Через админ-панель плагина
После выбора формы в настройках Inssmart Form, вы увидите список доступных полей в информационном блоке над таблицей mapping.

### Способ 2: Через редактор формы CF7
1. Откройте форму в Contact Form 7
2. В редакторе формы найдите теги полей
3. Имя поля указано в теге после типа поля:
   - `[text* your-fieldname]` → имя поля: `your-fieldname`
   - `[email* your-email]` → имя поля: `your-email`
   - `[checkbox your-agreement "1"]` → имя поля: `your-agreement`

### Способ 3: Через вкладку Mail в CF7
1. Откройте форму в Contact Form 7
2. Перейдите на вкладку **Mail**
3. В поле **Message body** вы увидите mail-tags вида `[your-fieldname]`
4. Имя поля без квадратных скобок — это то, что нужно указать в mapping

## Важные замечания

1. **Имена полей чувствительны к регистру:** `your-fieldName` ≠ `your-fieldname`
2. **Используйте точные имена:** скопируйте имя поля из CF7 и вставьте в поле mapping
3. **Поля не обязательны:** если поле не указано в mapping, будет использовано значение по умолчанию (имя React поля)
4. **Проверка:** после настройки отправьте тестовую форму и проверьте, что данные приходят в CF7

## Пример полной настройки

### Форма заказа в CF7:
```
[text* your-organizationName]
[text* your-inn]
[text* your-responsiblePerson]
[email* your-workEmail]
[tel* your-workPhone]
[text your-serviceRegion]
[text your-coverageLevel]
[text your-numberOfEmployees]
[text your-insurerName]
[text your-totalPrice]
[text your-city]
```

### Mapping в настройках Inssmart:
- organizationName → `your-organizationName`
- inn → `your-inn`
- responsiblePerson → `your-responsiblePerson`
- workEmail → `your-workEmail`
- workPhone → `your-workPhone`
- serviceRegion → `your-serviceRegion`
- coverageLevel → `your-coverageLevel`
- numberOfEmployees → `your-numberOfEmployees`
- insurerName → `your-insurerName`
- totalPrice → `your-totalPrice`
- city → `your-city`

### Форма обратного звонка в CF7:
```
[text* your-callbackName]
[tel* your-callbackPhone]
[checkbox* your-isAgreed "1"]
```

### Mapping в настройках Inssmart:
- callbackName → `your-callbackName`
- callbackPhone → `your-callbackPhone`
- isAgreed → `your-isAgreed`

## Устранение проблем

### Данные не приходят в CF7
1. Проверьте, что форма выбрана в настройках
2. Убедитесь, что имена полей в mapping совпадают с именами в CF7 форме
3. Проверьте права доступа на запись в `wp-content/debug.log` для просмотра логов

### Поля пустые в CF7
1. Проверьте, что React форма отправляет данные с правильными именами полей
2. Убедитесь, что mapping настроен корректно
3. Проверьте логи в `wp-content/debug.log`

