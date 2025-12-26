import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'

// Точка входа для iframe версии
// Автоматически монтирует приложение в элемент #root
if (typeof window !== 'undefined') {
  const rootElement = document.getElementById('root')
  if (rootElement) {
    const root = ReactDOM.createRoot(rootElement)
    root.render(
      <React.StrictMode>
        <App />
      </React.StrictMode>
    )
  } else {
    console.error('Элемент #root не найден')
  }
}

