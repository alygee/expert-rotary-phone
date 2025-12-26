import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'

// Функция для инициализации формы в WordPress
function initInssmartForm(containerId: string = 'inssmart-form-container') {
  const container = document.getElementById(containerId)
  if (container) {
    const root = ReactDOM.createRoot(container)
    root.render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    )
  } else {
    console.error(`Container with id "${containerId}" not found`)
  }
}

// Экспортируем функцию для использования в WordPress
declare global {
  interface Window {
    initInssmartForm: (containerId?: string) => void
    InssmartForm?: {
      initInssmartForm: (containerId?: string) => void
    }
  }
}

if (typeof window !== 'undefined') {
  window.initInssmartForm = initInssmartForm
}

// Режим разработки: автоматический запуск если есть элемент #root
if (typeof window !== 'undefined') {
  const rootElement = document.getElementById('root')
  if (rootElement && !document.getElementById('inssmart-form-container')) {
    // Автоматический запуск только если нет WordPress контейнера
    const root = ReactDOM.createRoot(rootElement)
    root.render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    )
  }
}

// Для IIFE формата нужно экспортировать функцию
export { initInssmartForm }

// Убеждаемся, что функция доступна глобально для WordPress
if (typeof window !== 'undefined') {
  // Дополнительная проверка для случаев, когда скрипт загружается асинхронно
  window.InssmartForm = { initInssmartForm }
}

