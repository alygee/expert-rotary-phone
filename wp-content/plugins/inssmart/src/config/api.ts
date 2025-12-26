/**
 * Конфигурация API
 */
export const API_CONFIG = {
  /**
   * Базовый URL для API запросов
   */
  BASE_URL: 'https://kubiki.ai',
  
  /**
   * Получить полный URL для API endpoint
   */
  getApiUrl(endpoint: string): string {
    // Убираем начальный слэш, если он есть
    const cleanEndpoint = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
    return `${this.BASE_URL}/${cleanEndpoint}`;
  },
  
  /**
   * Получить URL для AJAX запросов WordPress
   */
  getAjaxUrl(): string {
    return `${this.BASE_URL}/wp-admin/admin-ajax.php`;
  },
  
  /**
   * Получить URL для REST API запросов WordPress
   */
  getRestApiUrl(endpoint: string): string {
    // Убираем начальный слэш, если он есть
    const cleanEndpoint = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
    return `${this.BASE_URL}/wp-json/${cleanEndpoint}`;
  },
};

