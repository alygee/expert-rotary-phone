/**
 * Обработчик событий виджета Jivo для отправки уведомлений на email
 * Использует Widget API Jivo для отслеживания событий на клиентской стороне
 */

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
            
            // Обработчик: чат начат
            if (typeof jivo_onClientStartChat === 'function') {
                jivo_api.onChatStarted(function() {
                    console.log('Jivo Widget Handler: Чат начат');
                    sendEventToServer('chat_started', {
                        client: getClientData()
                    });
                });
            }
        }
        
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
        
    // Глобальная функция для ручного тестирования из консоли
    window.testJivoWidgetHandler = function() {
        console.log('=== Тест Jivo Widget Handler ===');
        console.log('jivo_api доступен:', typeof jivo_api !== 'undefined');
        if (typeof jivo_api !== 'undefined') {
            console.log('jivo_api методы:', Object.keys(jivo_api));
            console.log('jivo_api объект:', jivo_api);
        }
        
        // Тест отправки события
        const testData = {
            client: {
                name: 'Тестовый клиент',
                phone: '+7 (999) 123-45-67',
                email: 'test@example.com'
            },
            message: {
                text: 'Тестовое сообщение из консоли'
            }
        };
        
        sendEventToServer('client_message', testData);
        console.log('Тестовое событие отправлено');
    };
