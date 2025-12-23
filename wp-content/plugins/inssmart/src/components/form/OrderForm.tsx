import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '../ui/card';
import type { FormData, FormErrors, TouchedFields } from '@/types/form';
import type { MultiSelectOption } from '@/components/ui/multi-select';
import { SelectedInsurerCard } from './SelectedInsurerCard';
import { getInsurerLogo } from '@/utils/insurerLogos';
import { handlePhoneChange } from '@/utils/phoneMask';
import { coverageLevels } from '@/constants/form';
import { validateStep3Order } from '@/utils/validation';
import {
  ContactIcon,
  InsPolicyIcon,
  CrowdIcon,
  Profile2Icon,
  MailOutlineIcon,
  CallIcon,
  ShieldIcon,
  LocationIcon,
} from '@/components/icons';

interface OrderFormProps {
  formData: FormData;
  insurerName: string;
  totalPrice: number;
  coverageLevel: string;
  numberOfEmployees: string;
  selectedCities: string[];
  cities: MultiSelectOption[];
  onCreateCity?: (label: string) => string | null;
  errors?: FormErrors['step3'];
  touched?: TouchedFields['step3'];
  onInputChange: (field: string, value: string) => void;
  onCoverageLevelChange?: (value: string) => void;
  onCitiesChange?: (value: string[]) => void;
  onBlur?: (field: keyof NonNullable<TouchedFields['step3']>) => void;
  onSubmit: () => void;
  isSubmitting?: boolean;
  submitError?: string | null;
}

export function OrderForm({
  formData,
  insurerName,
  totalPrice,
  coverageLevel,
  numberOfEmployees,
  selectedCities,
  cities,
  onCreateCity,
  errors,
  touched,
  onInputChange,
  onCoverageLevelChange,
  onCitiesChange,
  onBlur,
  onSubmit,
  isSubmitting = false,
  submitError = null,
}: OrderFormProps) {
  const logoPath = getInsurerLogo(insurerName);

  const handleCoverageLevelChange = (value: string) => {
    if (onCoverageLevelChange) {
      onCoverageLevelChange(value);
    }
    if (onBlur && touched && !touched.coverageLevel) {
      onBlur('coverageLevel');
    }
  };

  const handleCitiesChange = (value: string[]) => {
    if (onCitiesChange) {
      onCitiesChange(value);
    }
    if (onBlur && touched && !touched.serviceRegion) {
      onBlur('serviceRegion');
    }
  };

  const handleSubmit = () => {
    // Выполняем валидацию
    const validationErrors = validateStep3Order(
      formData,
      coverageLevel,
      selectedCities
    );

    // Помечаем все поля как touched, чтобы показать ошибки
    const allFields: Array<keyof NonNullable<TouchedFields['step3']>> = [
      'organizationName',
      'inn',
      'responsiblePerson',
      'workEmail',
      'workPhone',
      'coverageLevel',
      'serviceRegion',
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
    <div className="space-y-6">
      {/* Карточка страховщика */}
      <SelectedInsurerCard
        insurerName={insurerName}
        totalPrice={totalPrice}
        logoPath={logoPath}
      />

      {/* Форма */}
      <Card className="w-full ">
        <CardContent>
          <div className="space-y-4">
            <div className="flex flex-col md:flex-row gap-4">
              <div className="w-full md:w-1/2">
                <Input
                  id="organizationName"
                  type="text"
                  placeholder="Как называется?"
                  value={formData.step3.organizationName}
                  onChange={(e) =>
                    onInputChange('organizationName', e.target.value)
                  }
                  onBlur={() => onBlur?.('organizationName')}
                  label="Название организации"
                  icon={<ContactIcon />}
                  error={errors?.organizationName}
                  touched={touched?.organizationName}
                />
              </div>

              <div className="w-full md:w-1/2">
                <Input
                  id="inn"
                  type="text"
                  placeholder="Введите ИНН"
                  value={formData.step3.inn}
                  onChange={(e) => onInputChange('inn', e.target.value)}
                  onBlur={() => onBlur?.('inn')}
                  label="ИНН"
                  icon={<InsPolicyIcon />}
                  error={errors?.inn}
                  touched={touched?.inn}
                />
              </div>
            </div>

            <div className="flex flex-col md:flex-row gap-4">
              <div className="w-full md:w-1/2">
                <Input
                  id="numberOfEmployees"
                  type="text"
                  placeholder="Сколько сотрудников в компании"
                  value={numberOfEmployees}
                  label="Количество сотрудников"
                  icon={<CrowdIcon />}
                />
              </div>

              <div className="w-full md:w-1/2">
                <Input
                  id="responsiblePerson"
                  type="text"
                  placeholder="Введите ФИО"
                  value={formData.step3.responsiblePerson}
                  onChange={(e) =>
                    onInputChange('responsiblePerson', e.target.value)
                  }
                  onBlur={() => onBlur?.('responsiblePerson')}
                  label="ФИО ответственного"
                  icon={<Profile2Icon />}
                  error={errors?.responsiblePerson}
                  touched={touched?.responsiblePerson}
                />
              </div>
            </div>

            <div className="flex flex-col md:flex-row gap-4">
              <div className="w-full md:w-1/2">
                <Input
                  id="workEmail"
                  type="email"
                  placeholder="Какой рабочий email?"
                  value={formData.step3.workEmail}
                  onChange={(e) => onInputChange('workEmail', e.target.value)}
                  onBlur={() => onBlur?.('workEmail')}
                  label="Рабочая почта"
                  icon={<MailOutlineIcon />}
                  error={errors?.workEmail}
                  touched={touched?.workEmail}
                />
              </div>

              <div className="w-full md:w-1/2">
                <Input
                  id="workPhone"
                  type="tel"
                  placeholder="+7 ___ ___ ____"
                  value={formData.step3.workPhone}
                  onChange={(e) =>
                    handlePhoneChange(e.target.value, (value) =>
                      onInputChange('workPhone', value)
                    )
                  }
                  onBlur={() => onBlur?.('workPhone')}
                  label="Телефон"
                  icon={<CallIcon />}
                  error={errors?.workPhone}
                  touched={touched?.workPhone}
                />
              </div>
            </div>

            <Select
              options={coverageLevels}
              value={coverageLevel}
              onChange={handleCoverageLevelChange}
              placeholder="Выберите уровень покрытия"
              label="Уровень покрытия"
              icon={<ShieldIcon />}
              error={errors?.coverageLevel}
              touched={touched?.coverageLevel}
            />

            <MultiSelect
              options={cities}
              value={selectedCities}
              onChange={handleCitiesChange}
              placeholder="Выберите регионы обслуживания"
              creatable={true}
              onCreateOption={onCreateCity}
              closeOnSelect={false}
              label="Регион обслуживания"
              icon={<LocationIcon />}
              error={errors?.serviceRegion}
              touched={touched?.serviceRegion}
            />

            <div className="space-y-2 pt-4">
              {submitError && (
                <div className="p-3 bg-error/10 border border-error rounded-md">
                  <p className="text-error text-sm">{submitError}</p>
                </div>
              )}
              <div className="flex justify-end">
                <Button
                  variant="solid"
                  size="large"
                  onClick={handleSubmit}
                  className="text-white"
                  disabled={isSubmitting}
                >
                  {isSubmitting ? 'Отправка...' : 'Отправить заявку'}
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
