import type {
  SubmitOrderRequest,
  SubmitCallbackRequest,
  SubmitResponse,
} from '@/types/api';
import { API_CONFIG } from '@/config/api';

// Получаем AJAX URL и nonce из WordPress (если доступно)
// Безопасная проверка для iframe версии, где эта переменная может отсутствовать
function getInssmartAjax(): { ajaxurl: string; nonce: string } | null {
  if (typeof window !== 'undefined') {
    try {
      // Проверяем, определена ли переменная inssmartAjax
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const ajax = (window as any).inssmartAjax;
      if (ajax && typeof ajax === 'object' && ajax.ajaxurl && ajax.nonce) {
        return ajax;
      }
    } catch {
      // Игнорируем ошибки доступа к переменной
    }
  }
  return null;
}

/**
 * Отправка формы заказа в Contact Form 7 через REST API
 */
export async function submitOrderForm(
  request: SubmitOrderRequest
): Promise<SubmitResponse> {
  // Используем новый REST API эндпоинт
  const apiUrl = API_CONFIG.getRestApiUrl('dmc/v1/order-form');

  try {
    const response = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        form_data: request.formData,
        additional_data: request.additionalData,
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      // Обрабатываем ошибки HTTP
      return {
        success: false,
        message: data.message || data.data?.message || 'Ошибка при отправке формы',
        errors: data.errors || data.data?.errors || [],
        status: data.status || data.data?.status || null,
      };
    }

    if (data.success) {
      return {
        success: true,
        data: {
          message: data.message || 'Форма успешно отправлена',
          status: data.status || 'mail_sent',
        },
      };
    } else {
      return {
        success: false,
        message: data.message || 'Ошибка при отправке формы',
        errors: data.errors || [],
        status: data.status || null,
      };
    }
  } catch (error) {
    return {
      success: false,
      message:
        error instanceof Error
          ? error.message
          : 'Ошибка сети при отправке формы',
      errors: [],
    };
  }
}

/**
 * Отправка формы обратного звонка в Contact Form 7
 */
export async function submitCallbackForm(
  request: SubmitCallbackRequest
): Promise<SubmitResponse> {
  // Используем AJAX URL из WordPress, если доступен, иначе используем базовый URL
  const inssmartAjax = getInssmartAjax();
  const ajaxUrl = inssmartAjax?.ajaxurl || API_CONFIG.getAjaxUrl();
  const nonce = inssmartAjax?.nonce || '';

  try {
    const formData = new FormData();
    formData.append('action', 'inssmart_submit_callback');
    if (nonce) {
      formData.append('nonce', nonce);
    }
    formData.append('form_data', JSON.stringify(request.formData));
    // Включаем subId и clickId в additional_data, если они присутствуют
    if (request.additionalData) {
      const additionalData: Record<string, string | null> = {};
      if (request.additionalData.subId) {
        additionalData.subId = request.additionalData.subId;
      }
      if (request.additionalData.clickId) {
        additionalData.clickId = request.additionalData.clickId;
      }
      if (Object.keys(additionalData).length > 0) {
        formData.append('additional_data', JSON.stringify(additionalData));
      }
    }

    const response = await fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      return {
        success: true,
        data: {
          message: data.data?.message || 'Форма успешно отправлена',
          status: data.data?.status || 'mail_sent',
        },
      };
    } else {
      return {
        success: false,
        message: data.data?.message || 'Ошибка при отправке формы',
        errors: data.data?.errors || [],
        status: data.data?.status || null,
      };
    }
  } catch (error) {
    return {
      success: false,
      message:
        error instanceof Error
          ? error.message
          : 'Ошибка сети при отправке формы',
      errors: [],
    };
  }
}
