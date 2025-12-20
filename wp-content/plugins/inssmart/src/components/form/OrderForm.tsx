import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import { Button } from '@/components/ui/button';
import { FormError } from '@/components/ui/form-error';
import { Card, CardContent } from '../ui/card';
import { cn } from '@/lib/utils';
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
              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="organizationName">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <ContactIcon className="ml-1" />
                    <span>Название организации</span>
                  </div>
                </Label>
                <Input
                  id="organizationName"
                  type="text"
                  placeholder="Как называется?"
                  value={formData.step3.organizationName}
                  onChange={(e) =>
                    onInputChange('organizationName', e.target.value)
                  }
                  onBlur={() => onBlur?.('organizationName')}
                  className={cn(
                    touched?.organizationName &&
                      errors?.organizationName &&
                      'border-error focus-visible:ring-error placeholder:text-error'
                  )}
                />
                {touched?.organizationName && errors?.organizationName && (
                  <FormError>{errors.organizationName}</FormError>
                )}
              </div>

              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="inn">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <InsPolicyIcon className="ml-1" />
                    <span>ИНН</span>
                  </div>
                </Label>
                <Input
                  id="inn"
                  type="text"
                  placeholder="Введите ИНН"
                  value={formData.step3.inn}
                  onChange={(e) => onInputChange('inn', e.target.value)}
                  onBlur={() => onBlur?.('inn')}
                  className={cn(
                    touched?.inn &&
                      errors?.inn &&
                      'border-error focus-visible:ring-error placeholder:text-error'
                  )}
                />
                {touched?.inn && errors?.inn && (
                  <FormError>{errors.inn}</FormError>
                )}
              </div>
            </div>

            <div className="flex flex-col md:flex-row gap-4">
              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="numberOfEmployees">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <CrowdIcon className="ml-1" />
                    <span>Количество сотрудников</span>
                  </div>
                </Label>
                <Input
                  id="numberOfEmployees"
                  type="text"
                  placeholder="Сколько сотрудников в компании"
                  value={numberOfEmployees}
                />
              </div>

              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="responsiblePerson">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <Profile2Icon className="ml-1" />
                    <span>ФИО ответственного</span>
                  </div>
                </Label>
                <Input
                  id="responsiblePerson"
                  type="text"
                  placeholder="Введите ФИО"
                  value={formData.step3.responsiblePerson}
                  onChange={(e) =>
                    onInputChange('responsiblePerson', e.target.value)
                  }
                  onBlur={() => onBlur?.('responsiblePerson')}
                  className={cn(
                    touched?.responsiblePerson &&
                      errors?.responsiblePerson &&
                      'border-error focus-visible:ring-error placeholder:text-error'
                  )}
                />
                {touched?.responsiblePerson && errors?.responsiblePerson && (
                  <FormError>{errors.responsiblePerson}</FormError>
                )}
              </div>
            </div>

            <div className="flex flex-col md:flex-row gap-4">
              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="workEmail">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <MailOutlineIcon className="ml-1" />
                    <span>Рабочая почта</span>
                  </div>
                </Label>
                <Input
                  id="workEmail"
                  type="email"
                  placeholder="Какой рабочий email?"
                  value={formData.step3.workEmail}
                  onChange={(e) => onInputChange('workEmail', e.target.value)}
                  onBlur={() => onBlur?.('workEmail')}
                  className={cn(
                    touched?.workEmail &&
                      errors?.workEmail &&
                      'border-error focus-visible:ring-error placeholder:text-error'
                  )}
                />
                {touched?.workEmail && errors?.workEmail && (
                  <FormError>{errors.workEmail}</FormError>
                )}
              </div>

              <div className="space-y-2 w-full md:w-1/2">
                <Label htmlFor="workPhone">
                  <div className="flex items-center gap-2.5 tracking-wide">
                    <CallIcon className="ml-1" />
                    <span>Телефон</span>
                  </div>
                </Label>
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
                  className={cn(
                    touched?.workPhone &&
                      errors?.workPhone &&
                      'border-error focus-visible:ring-error placeholder:text-error'
                  )}
                />
                {touched?.workPhone && errors?.workPhone && (
                  <FormError>{errors.workPhone}</FormError>
                )}
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="coverageLevel">
                <div className="flex items-center gap-2.5 tracking-wide">
                  <ShieldIcon className="ml-1" />
                  <span>Уровень покрытия</span>
                </div>
              </Label>
              <Select
                options={coverageLevels}
                value={coverageLevel}
                onChange={handleCoverageLevelChange}
                placeholder="Выберите уровень покрытия"
                className={cn(
                  touched?.coverageLevel &&
                    errors?.coverageLevel &&
                    'border-error'
                )}
              />
              {touched?.coverageLevel && errors?.coverageLevel && (
                <FormError>{errors.coverageLevel}</FormError>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="serviceRegion">
                <div className="flex items-center gap-2.5 tracking-wide">
                  <LocationIcon className="ml-1" />
                  <span>Регион обслуживания</span>
                </div>
              </Label>
              <MultiSelect
                options={cities}
                value={selectedCities}
                onChange={handleCitiesChange}
                placeholder="Выберите регионы обслуживания"
                creatable={true}
                onCreateOption={onCreateCity}
                closeOnSelect={false}
                className={cn(
                  touched?.serviceRegion &&
                    errors?.serviceRegion &&
                    'border-error'
                )}
              />
              {touched?.serviceRegion && errors?.serviceRegion && (
                <FormError>{errors.serviceRegion}</FormError>
              )}
            </div>

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
