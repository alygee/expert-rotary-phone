/**
 * Утилита для получения параметров из URL
 */

/**
 * Получает параметры subId и clickId из URL
 * @returns Объект с subId и clickId или null, если параметры отсутствуют
 */
export function getUrlParams(): {
  subId: string | null;
  clickId: string | null;
} {
  if (typeof window === 'undefined') {
    return { subId: null, clickId: null };
  }

  const urlParams = new URLSearchParams(window.location.search);
  const subId = urlParams.get('subId');
  const clickId = urlParams.get('clickId');

  return {
    subId: subId || null,
    clickId: clickId || null,
  };
}

