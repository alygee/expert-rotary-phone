import type {
  SubmitOrderRequest,
  SubmitCallbackRequest,
  SubmitResponse,
} from '@/types/api';

// Получаем AJAX URL и nonce из WordPress
declare const inssmartAjax:
  | {
      ajaxurl: string;
      nonce: string;
    }
  | undefined;

/**
 * Отправка формы заказа в Contact Form 7
 */
export async function submitOrderForm(
  request: SubmitOrderRequest
): Promise<SubmitResponse> {
  if (!inssmartAjax) {
    return {
      success: false,
      message:
        'AJAX конфигурация не найдена. Убедитесь, что плагин правильно загружен.',
      errors: [],
    };
  }

  try {
    const formData = new FormData();
    formData.append('action', 'inssmart_submit_order');
    formData.append('nonce', inssmartAjax.nonce);
    formData.append('form_data', JSON.stringify(request.formData));
    formData.append('additional_data', JSON.stringify(request.additionalData));

    const response = await fetch(inssmartAjax.ajaxurl, {
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

/**
 * Отправка формы обратного звонка в Contact Form 7
 */
export async function submitCallbackForm(
  request: SubmitCallbackRequest
): Promise<SubmitResponse> {
  if (!inssmartAjax) {
    return {
      success: false,
      message:
        'AJAX конфигурация не найдена. Убедитесь, что плагин правильно загружен.',
      errors: [],
    };
  }

  try {
    const formData = new FormData();
    formData.append('action', 'inssmart_submit_callback');
    formData.append('nonce', inssmartAjax.nonce);
    formData.append('form_data', JSON.stringify(request.formData));

    const response = await fetch(inssmartAjax.ajaxurl, {
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
