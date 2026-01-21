import type {
  SubmitOrderRequest,
  SubmitCallbackRequest,
  SubmitResponse,
} from '@/types/api';
import { API_CONFIG } from '@/config/api';

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
 * Отправка формы обратного звонка в Contact Form 7 через REST API
 */
export async function submitCallbackForm(
  request: SubmitCallbackRequest
): Promise<SubmitResponse> {
  // Используем новый REST API эндпоинт
  const apiUrl = API_CONFIG.getRestApiUrl('inssmart/v1/callback-form');

  try {
    // Формируем additional_data с subId и clickId, если они присутствуют
    const additionalData: Record<string, string | null> = {};
    if (request.additionalData) {
      if (request.additionalData.subId) {
        additionalData.subId = request.additionalData.subId;
      }
      if (request.additionalData.clickId) {
        additionalData.clickId = request.additionalData.clickId;
      }
    }

    const response = await fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        form_data: request.formData,
        additional_data: Object.keys(additionalData).length > 0 ? additionalData : {},
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
