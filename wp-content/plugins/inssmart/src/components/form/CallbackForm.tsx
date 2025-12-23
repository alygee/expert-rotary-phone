import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { FormError } from '@/components/ui/form-error';
import { Card, CardContent } from '../ui/card';
import { Typography } from '../ui/typography';
import { Checkbox } from '@/components/ui/checkbox';
import type { FormData, FormErrors, TouchedFields } from '@/types/form';
import { handlePhoneChange } from '@/utils/phoneMask';
import { validateStep3Callback } from '@/utils/validation';
import { CallIcon, ContactIcon } from '../icons';

type Step3CallbackField = 'callbackName' | 'callbackPhone' | 'isAgreed';

interface CallbackFormProps {
  formData: FormData;
  onInputChange: (field: Step3CallbackField, value: string | boolean) => void;
  onSubmit: () => void;
  onBackToOffers?: () => void;
  errors?: FormErrors['step3'];
  touched?: TouchedFields['step3'];
  onBlur?: (field: Step3CallbackField) => void;
  isSubmitting?: boolean;
  submitError?: string | null;
}

export function CallbackForm({
  formData,
  onInputChange,
  onSubmit,
  onBackToOffers,
  errors,
  touched,
  onBlur,
  isSubmitting = false,
  submitError = null,
}: CallbackFormProps) {
  const handleSubmit = () => {
    // Выполняем валидацию
    const validationErrors = validateStep3Callback(formData);

    // Помечаем все поля как touched, чтобы показать ошибки
    const allFields: Step3CallbackField[] = [
      'callbackName',
      'callbackPhone',
      'isAgreed',
    ];

    allFields.forEach((field) => {
      if (onBlur && (!touched || !touched[field])) {
        onBlur(field);
      }
    });

    // Проверяем, есть ли ошибки валидации
    const hasErrors =
      validationErrors && Object.keys(validationErrors).length > 0;

    // Если есть ошибки, не вызываем onSubmit
    if (hasErrors) {
      return;
    }

    // Если ошибок нет, вызываем onSubmit
    onSubmit();
  };

  return (
    <Card>
      <CardContent>
        <div className="space-y-4">
          <div className="space-y-1">
            <Typography variant="h4">
              Получите индивидуальное предложение по ДМС от топовых СК
            </Typography>
            <Typography className="text-lg text-text-secondary">
              Наш менеджер свяжется с вами в течение 30 минут
            </Typography>
          </div>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="w-full md:w-1/2">
              <Input
                id="callbackName"
                type="text"
                placeholder="Как вас зовут?"
                value={formData.step3.callbackName}
                onChange={(e) => onInputChange('callbackName', e.target.value)}
                onBlur={() => onBlur?.('callbackName')}
                label="Ваше имя"
                icon={<ContactIcon />}
                error={errors?.callbackName}
                touched={touched?.callbackName}
              />
            </div>

            <div className="w-full md:w-1/2">
              <Input
                id="callbackPhone"
                type="tel"
                placeholder="+7 ___ ___ ____"
                value={formData.step3.callbackPhone}
                onChange={(e) =>
                  handlePhoneChange(e.target.value, (value) =>
                    onInputChange('callbackPhone', value)
                  )
                }
                onBlur={() => onBlur?.('callbackPhone')}
                label="Телефон"
                icon={<CallIcon />}
                error={errors?.callbackPhone}
                touched={touched?.callbackPhone}
              />
            </div>
          </div>

          <div className="space-y-2">
            <Checkbox
              label="Я согласен(а) на обработку персональных данных согласно политике конфиденциальности"
              checked={formData.step3.isAgreed}
              onChange={(e) => onInputChange('isAgreed', e.target.checked)}
              onBlur={() => onBlur?.('isAgreed')}
              variant={
                touched?.isAgreed && errors?.isAgreed ? 'error' : 'default'
              }
            />
            {touched?.isAgreed && errors?.isAgreed && (
              <FormError>{errors.isAgreed}</FormError>
            )}
          </div>
          <div className="space-y-2 pt-4">
            {submitError && (
              <div className="p-3 bg-error/10 border border-error rounded-md">
                <p className="text-error text-sm">{submitError}</p>
              </div>
            )}
            <div className="flex justify-end gap-2 md:gap-4 flex-col md:flex-row">
              <Button
                className="text-primary"
                variant="outline"
                size="large"
                onClick={onBackToOffers}
                disabled={isSubmitting}
              >
                Вернуться к выбору офферов
              </Button>
              <Button
                variant="solid"
                size="large"
                onClick={handleSubmit}
                className="text-white"
                disabled={isSubmitting}
              >
                {isSubmitting ? 'Отправка...' : 'Отправить'}
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
