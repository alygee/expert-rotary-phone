/**
 * Inssmart Form - TypeScript определения
 */

/**
 * Инициализирует форму в указанном контейнере
 * @param containerId - ID элемента контейнера (по умолчанию: 'inssmart-form-container')
 */
export declare function initInssmartForm(containerId?: string): void;

/**
 * Глобальный объект для доступа к функциям формы
 */
export interface InssmartFormGlobal {
  initInssmartForm: (containerId?: string) => void;
}

declare global {
  interface Window {
    initInssmartForm: (containerId?: string) => void;
    InssmartForm?: InssmartFormGlobal;
  }
}


