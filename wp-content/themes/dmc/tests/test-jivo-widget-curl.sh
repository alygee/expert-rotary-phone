#!/bin/bash
# Примеры curl команд для тестирования Jivo Widget Event endpoint
# Использование: ./test-jivo-widget-curl.sh

# Базовый URL (замените на ваш домен)
BASE_URL="https://kubiki.ai"
# Или для локального тестирования:
# BASE_URL="http://localhost"

ENDPOINT="${BASE_URL}/wp-json/dmc/v1/jivo-widget-event"

echo "=== Тестирование Jivo Widget Event Endpoint ==="
echo "URL: ${ENDPOINT}"
echo ""

# 1. Тест события: client_message (новое сообщение от клиента)
echo "1. Тест: client_message (новое сообщение от клиента)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "event_type": "client_message",
    "data": {
      "client": {
        "name": "Тестовый клиент",
        "phone": "+7 (999) 123-45-67",
        "email": "test@example.com"
      },
      "message": {
        "text": "Это тестовое сообщение для проверки работы endpoint. Время: '"$(date '+%Y-%m-%d %H:%M:%S')"'"
      }
    },
    "timestamp": "'"$(date -u +"%Y-%m-%dT%H:%M:%SZ")"'",
    "page_url": "https://kubiki.ai/test-page",
    "user_agent": "Mozilla/5.0 (Test Browser)"
  }'
echo -e "\n\n"

# 2. Тест события: chat_started (чат начат)
echo "2. Тест: chat_started (чат начат)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "event_type": "chat_started",
    "data": {
      "client": {
        "name": "Иван Иванов",
        "phone": "+7 (999) 555-12-34",
        "email": "ivan@example.com"
      }
    },
    "timestamp": "'"$(date -u +"%Y-%m-%dT%H:%M:%SZ")"'",
    "page_url": "https://kubiki.ai/",
    "user_agent": "Mozilla/5.0 (Test Browser)"
  }'
echo -e "\n\n"

# 3. Тест события: chat_accepted (оператор принял чат)
echo "3. Тест: chat_accepted (оператор принял чат)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "event_type": "chat_accepted",
    "data": {
      "client": {
        "name": "Петр Петров",
        "phone": "+7 (999) 777-88-99",
        "email": "petr@example.com"
      },
      "agent": {
        "name": "Мария Операторова",
        "id": "agent_123"
      }
    },
    "timestamp": "'"$(date -u +"%Y-%m-%dT%H:%M:%SZ")"'",
    "page_url": "https://kubiki.ai/contact"
  }'
echo -e "\n\n"

# 4. Тест события: chat_finished (чат завершен)
echo "4. Тест: chat_finished (чат завершен)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "event_type": "chat_finished",
    "data": {
      "client": {
        "name": "Анна Смирнова",
        "phone": "+7 (999) 111-22-33",
        "email": "anna@example.com"
      }
    },
    "timestamp": "'"$(date -u +"%Y-%m-%dT%H:%M:%SZ")"'"
  }'
echo -e "\n\n"

# 5. Тест события: widget_ready (виджет загружен)
echo "5. Тест: widget_ready (виджет загружен)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "event_type": "widget_ready",
    "data": {
      "client": {}
    },
    "timestamp": "'"$(date -u +"%Y-%m-%dT%H:%M:%SZ")"'"
  }'
echo -e "\n\n"

# 6. Тест с ошибкой (без event_type)
echo "6. Тест с ошибкой (без event_type - должен вернуть 400)"
curl -X POST "${ENDPOINT}" \
  -H "Content-Type: application/json" \
  -d '{
    "data": {
      "client": {
        "name": "Тест"
      }
    }
  }'
echo -e "\n\n"

echo "=== Тестирование завершено ==="

