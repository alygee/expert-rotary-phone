import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(__dirname, '..');
const distDir = path.join(rootDir, 'dist');

const typesContent = `/**
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
`;

// Создаем директорию dist, если её нет
if (!fs.existsSync(distDir)) {
  fs.mkdirSync(distDir, { recursive: true });
}

// Записываем файл типов
const typesPath = path.join(distDir, 'index.d.ts');
fs.writeFileSync(typesPath, typesContent, 'utf-8');

console.log('✓ Файл типов создан:', typesPath);




