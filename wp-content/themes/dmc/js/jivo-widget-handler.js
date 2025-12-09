/**
 * Обработчик событий виджета Jivo для отправки уведомлений на email
 * Использует Widget API Jivo для отслеживания событий на клиентской стороне
 */

(function() {
    'use strict';
    
    // Ждем загрузки виджета Jivo
    function initJivoWidgetHandler() {
        // Проверяем наличие jivo_api
        if (typeof jivo_api === 'undefined') {
            // Если jivo_api еще не загружен, ждем
            setTimeout(initJivoWidgetHandler, 100);
            return;
        }
        
        console.log('Jivo Widget Handler: Виджет загружен, инициализация обработчиков...');
        
        // URL для отправки данных на сервер
        const ajaxUrl = '/wp-json/dmc/v1/jivo-widget-event';
        
        /**
         * Отправляет данные события на сервер
         */
        function sendEventToServer(eventType, eventData) {
            const data = {
                event_type: eventType,
                data: eventData,
                timestamp: new Date().toISOString(),
                page_url: window.location.href,
                user_agent: navigator.userAgent
            };
            
            // Отправляем через fetch API
            fetch(ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log('Jivo Widget Handler: Событие успешно отправлено на сервер', eventType);
                } else {
                    console.error('Jivo Widget Handler: Ошибка отправки события', result);
                }
            })
            .catch(error => {
                console.error('Jivo Widget Handler: Ошибка при отправке события', error);
            });
        }
        
        /**
         * Извлекает данные клиента из виджета
         */
        function getClientData() {
            const clientData = {
                name: '',
                phone: '',
                email: ''
            };
            
            try {
                // Пытаемся получить данные через API виджета
                if (typeof jivo_api.getContactInfo === 'function') {
                    const contactInfo = jivo_api.getContactInfo();
                    if (contactInfo) {
                        clientData.name = contactInfo.name || '';
                        clientData.phone = contactInfo.phone || '';
                        clientData.email = contactInfo.email || '';
                    }
                }
            } catch (e) {
                console.warn('Jivo Widget Handler: Не удалось получить данные клиента', e);
            }
            
            return clientData;
        }
        
        // Обработчик: виджет готов
        // Jivo API может использовать разные варианты
        if (typeof jivo_api !== 'undefined') {
            // Вариант 1: jivo_api.onReady
            if (typeof jivo_api.onReady === 'function') {
                jivo_api.onReady(function() {
                    console.log('Jivo Widget Handler: Виджет готов (onReady)');
                    sendEventToServer('widget_ready', {
                        client: getClientData()
                    });
                });
            }
            
            // Вариант 2: jivo_api.ready (альтернативный способ)
            if (typeof jivo_api.ready === 'function') {
                jivo_api.ready(function() {
                    console.log('Jivo Widget Handler: Виджет готов (ready)');
                });
            }
            
            // Обработчик: новое сообщение от клиента
            // Jivo может использовать разные методы для сообщений
            if (typeof jivo_api.onMessage === 'function') {
                jivo_api.onMessage(function(message) {
                    console.log('Jivo Widget Handler: Получено сообщение (onMessage)', message);
                    handleClientMessage(message);
                });
            }
            
            // Альтернативный способ отслеживания сообщений
            if (typeof jivo_api.onMessageReceived === 'function') {
                jivo_api.onMessageReceived(function(message) {
                    console.log('Jivo Widget Handler: Получено сообщение (onMessageReceived)', message);
                    handleClientMessage(message);
                });
            }
            
            // Обработчик: чат начат
            if (typeof jivo_api.onChatStarted === 'function') {
                jivo_api.onChatStarted(function() {
                    console.log('Jivo Widget Handler: Чат начат');
                    sendEventToServer('chat_started', {
                        client: getClientData()
                    });
                });
            }
            
            // Обработчик: чат завершен
            if (typeof jivo_api.onChatFinished === 'function') {
                jivo_api.onChatFinished(function() {
                    console.log('Jivo Widget Handler: Чат завершен');
                    sendEventToServer('chat_finished', {
                        client: getClientData()
                    });
                });
            }
            
            // Обработчик: оператор принял чат
            if (typeof jivo_api.onChatAccepted === 'function') {
                jivo_api.onChatAccepted(function(agent) {
                    console.log('Jivo Widget Handler: Оператор принял чат', agent);
                    sendEventToServer('chat_accepted', {
                        client: getClientData(),
                        agent: agent || {}
                    });
                });
            }
            
            // Обработчик: оператор подключился
            if (typeof jivo_api.onOperatorConnected === 'function') {
                jivo_api.onOperatorConnected(function(agent) {
                    console.log('Jivo Widget Handler: Оператор подключился', agent);
                    sendEventToServer('chat_accepted', {
                        client: getClientData(),
                        agent: agent || {}
                    });
                });
            }
        }
        
        /**
         * Обрабатывает сообщение от клиента
         */
        function handleClientMessage(message) {
            // Проверяем, что это сообщение от клиента (не от оператора)
            const isFromClient = !message.agent_id && 
                                !message.operator_id &&
                                (message.from === 'visitor' || 
                                 message.type === 'visitor' || 
                                 message.sender === 'visitor' ||
                                 !message.sender); // Если нет sender, скорее всего от клиента
            
            if (isFromClient && message.text) {
                const clientData = getClientData();
                
                sendEventToServer('client_message', {
                    client: clientData,
                    message: {
                        text: message.text,
                        id: message.id || '',
                        timestamp: message.timestamp || new Date().toISOString()
                    }
                });
            }
        }
        
        // Альтернативный способ: отслеживание через DOM события
        // Jivo виджет может генерировать кастомные события
        document.addEventListener('jivo_message', function(event) {
            if (event.detail && event.detail.text) {
                const clientData = getClientData();
                sendEventToServer('client_message', {
                    client: clientData,
                    message: {
                        text: event.detail.text,
                        timestamp: new Date().toISOString()
                    }
                });
            }
        });
        
        // Отслеживание отправки сообщений через перехват формы чата
        // Это fallback метод, если API события не работают
        setTimeout(function() {
            const chatContainer = document.querySelector('.jivo-container, #jivo-container, [id^="jivo"]');
            if (chatContainer) {
                // Наблюдаем за изменениями в контейнере чата
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length) {
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) { // Element node
                                    // Ищем сообщения от клиента
                                    const clientMessages = node.querySelectorAll && node.querySelectorAll('.jivo-message-visitor, .message-visitor, [class*="visitor"]');
                                    if (clientMessages && clientMessages.length > 0) {
                                        clientMessages.forEach(function(msgEl) {
                                            const text = msgEl.textContent || msgEl.innerText;
                                            if (text && text.trim()) {
                                                const clientData = getClientData();
                                                sendEventToServer('client_message', {
                                                    client: clientData,
                                                    message: {
                                                        text: text.trim(),
                                                        timestamp: new Date().toISOString()
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                });
                
                observer.observe(chatContainer, {
                    childList: true,
                    subtree: true
                });
            }
        }, 2000); // Даем время виджету загрузиться
    }
    
    // Запускаем инициализацию после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initJivoWidgetHandler);
    } else {
        initJivoWidgetHandler();
    }
    
    // Также запускаем после полной загрузки страницы
    window.addEventListener('load', function() {
        setTimeout(initJivoWidgetHandler, 500);
    });
    
})();

