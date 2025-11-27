# REST API Endpoints для фильтрации данных

## Обзор

Создан REST API endpoint для фильтрации данных страховщиков через функцию `filterData2`.

## Endpoints

### 1. Фильтрация данных

**URL:** `/wp-json/dmc/v1/filter`

**Метод:** `GET`

**Параметры:**

- `cities` (string, опционально) - Города для фильтрации, через запятую или массив
  - Пример: `cities=Москва,СПБ,Барнаул`
  - Или: `cities[]=Москва&cities[]=СПБ`
  
- `levels` (string, опционально) - Уровни для фильтрации, через запятую или массив
  - Пример: `levels=Комфорт,Стандарт`
  - Или: `levels[]=Комфорт&levels[]=Стандарт`
  
- `count` (integer, опционально) - Количество сотрудников
  - Пример: `count=5`
  
- `format` (string, опционально) - Формат ответа: `json` (по умолчанию) или `html`
  - Пример: `format=html`

**Примеры запросов:**

```bash
# JSON формат (по умолчанию)
GET /wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&count=5

# HTML формат
GET /wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&count=5&format=html

# Несколько городов
GET /wp-json/dmc/v1/filter?cities=Москва,СПБ,Барнаул&levels=Стандарт

# Только по уровню
GET /wp-json/dmc/v1/filter?levels=Комфорт
```

**Ответ (JSON формат):**

```json
{
  "success": true,
  "count": 2,
  "cities_count": 2,
  "filters": {
    "cities": ["Москва"],
    "levels": ["Комфорт"],
    "count": 5
  },
  "results": [
    {
      "city": "Москва",
      "count": 4,
      "insurers": [
        {
          "name": "Зетта",
          "count": 1,
          "records": [
            {
              "level": "Комфорт",
              "employees": "1-10",
              "prices": {
                "polyclinic": "25136.91",
                "dentistry": "24066.33",
                "ambulance": "1868.03",
                "hospitalization": "5021.12",
                "doctor_home": "1856.99"
              },
              "total_price": 55949.38
            }
          ]
        }
      ]
    }
  ]
}
```

**Ответ (HTML формат):**

Возвращает готовый HTML код для вставки на страницу (аналогично AJAX обработчику).

### 2. Информация о доступных фильтрах

**URL:** `/wp-json/dmc/v1/filter-info`

**Метод:** `GET`

**Параметры:** нет

**Ответ:**

```json
{
  "success": true,
  "total_records": 3718,
  "available_filters": {
    "cities": ["Архангельск", "Барнаул", "Москва", ...],
    "levels": ["Базовый", "Бизнес", "Комфорт", ...],
    "insurers": ["Зетта", "Ингос", "ООО «Капитал Лайф Страхование Жизни»", ...],
    "employee_ranges": ["1-10", "11-50", "51-1000000", ...]
  }
}
```

## Использование в JavaScript

### Fetch API

```javascript
// Получение отфильтрованных данных
fetch('/wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&count=5')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Найдено городов:', data.count);
      data.results.forEach(city => {
        console.log(`Город: ${city.city}, записей: ${city.count}`);
        city.insurers.forEach(insurer => {
          console.log(`  Страховщик: ${insurer.name}`);
        });
      });
    }
  });

// Получение HTML
fetch('/wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&format=html')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      document.getElementById('results').innerHTML = data.data;
    }
  });

// Получение списка доступных фильтров
fetch('/wp-json/dmc/v1/filter-info')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Доступные города:', data.available_filters.cities);
      console.log('Доступные уровни:', data.available_filters.levels);
    }
  });
```

### jQuery

```javascript
// JSON формат
$.get('/wp-json/dmc/v1/filter', {
  cities: 'Москва,СПБ',
  levels: 'Комфорт',
  count: 5
}, function(data) {
  if (data.success) {
    // Обработка данных
    console.log(data.results);
  }
});

// HTML формат
$.get('/wp-json/dmc/v1/filter', {
  cities: 'Москва',
  levels: 'Комфорт',
  format: 'html'
}, function(data) {
  if (data.success) {
    $('#results').html(data.data);
  }
});
```

### Axios

```javascript
import axios from 'axios';

// Получение данных
const response = await axios.get('/wp-json/dmc/v1/filter', {
  params: {
    cities: 'Москва,СПБ',
    levels: 'Комфорт',
    count: 5
  }
});

if (response.data.success) {
  console.log(response.data.results);
}
```

## Преимущества REST API перед AJAX

1. **Стандартизация** - использует стандартный REST API WordPress
2. **Кеширование** - можно кешировать ответы
3. **Документация** - автоматическая документация через Swagger/OpenAPI
4. **Гибкость** - можно использовать из любого места (JavaScript, мобильные приложения, внешние сервисы)
5. **Формат ответа** - можно выбрать JSON или HTML
6. **Масштабируемость** - легче добавлять новые endpoints

## Тестирование

### Через браузер

```
http://your-site.com/wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&count=5
http://your-site.com/wp-json/dmc/v1/filter-info
```

### Через curl

```bash
# Фильтрация
curl "http://your-site.com/wp-json/dmc/v1/filter?cities=Москва&levels=Комфорт&count=5"

# Информация о фильтрах
curl "http://your-site.com/wp-json/dmc/v1/filter-info"
```

### Через Postman

1. Создайте GET запрос
2. URL: `http://your-site.com/wp-json/dmc/v1/filter`
3. Параметры:
   - `cities`: `Москва`
   - `levels`: `Комфорт`
   - `count`: `5`

## Обработка ошибок

API возвращает стандартные HTTP коды:

- `200` - Успешный запрос
- `500` - Ошибка сервера (не удалось загрузить данные)

Пример ошибки:

```json
{
  "code": "no_data",
  "message": "Не удалось загрузить данные из CSV",
  "data": {
    "status": 500
  }
}
```

## Безопасность

- Endpoint публичный (не требует авторизации)
- Все входные данные санитизируются
- Используется стандартная система безопасности WordPress REST API

