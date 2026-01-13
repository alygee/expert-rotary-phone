import { useState, useEffect } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Typography } from './ui/typography';
import type { FilterResponse, InsuranceRecord } from '@/types/api';
import type { FormData, TouchedFields, Step3Mode } from '@/types/form';
import { TOTAL_STEPS, cities } from '@/constants/form';
import type { MultiSelectOption } from '@/components/ui/multi-select';
import {
  Step1Form,
  Step2Form,
  Step3Form,
  LoadingScreen,
  OfferCard,
  StepNavigation,
} from './form';
import {
  validateStep1,
  validateStep3Order,
  validateStep3Callback,
  isStepValid,
} from '@/utils/validation';
import { fetchFilterData } from '@/api/filter';
import { submitOrderForm, submitCallbackForm } from '@/api/submit';
import { Button } from './ui/button';
import { getUrlParams } from '@/utils/urlParams';

export function FormStepper() {
  const [currentStep, setCurrentStep] = useState(1);
  const [selectedCities, setSelectedCities] = useState<string[]>([]);
  const [coverageLevel, setCoverageLevel] = useState<string>('Базовый');
  const [isLoading, setIsLoading] = useState(false);
  const [isLoadingRecalculate, setIsLoadingRecalculate] = useState(false);
  const [apiData, setApiData] = useState<FilterResponse | null>(null);
  const [selectedOffer, setSelectedOffer] = useState<{
    insurerName: string;
    city: string;
    record: InsuranceRecord;
  } | null>(null);
  const [step3Mode, setStep3Mode] = useState<Step3Mode>(null);
  const [citiesList, setCitiesList] = useState<MultiSelectOption[]>(cities);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);
  const [formData, setFormData] = useState<FormData>({
    step1: {
      numberOfEmployees: '',
      email: '',
      phone: '',
    },
    step2: {
      company: '',
      position: '',
    },
    step3: {
      organizationName: '',
      inn: '',
      responsiblePerson: '',
      workEmail: '',
      workPhone: '',
      serviceRegion: '',
      callbackName: '',
      callbackPhone: '',
      isAgreed: true,
    },
  });
  const [touched, setTouched] = useState<TouchedFields>({
    step1: {
      numberOfEmployees: false,
      coverageLevel: false,
      selectedCities: false,
    },
  });
  const [urlParams, setUrlParams] = useState<{
    subId: string | null;
    clickId: string | null;
  }>({ subId: null, clickId: null });

  // Получаем параметры из URL при монтировании компонента
  useEffect(() => {
    const params = getUrlParams();
    setUrlParams(params);
    console.log('url params: ', params);
  }, []);

  const errors = validateStep1(formData, coverageLevel, selectedCities);
  const step3Errors =
    step3Mode === 'order'
      ? validateStep3Order(formData, coverageLevel, selectedCities)
      : step3Mode === 'callback'
        ? validateStep3Callback(formData)
        : null;

  const handleInputChange = (
    step: keyof FormData,
    field: string,
    value: string | boolean
  ) => {
    setFormData((prev) => ({
      ...prev,
      [step]: {
        ...prev[step],
        [field]: value,
      },
    }));
  };

  const handleStep1InputChange = (field: string, value: string) => {
    handleInputChange('step1', field, value);
  };

  const handleStep3InputChange = (field: string, value: string | boolean) => {
    handleInputChange('step3', field, value);
  };

  const handleBlur = (field: keyof TouchedFields['step1']) => {
    setTouched((prev) => ({
      ...prev,
      step1: {
        ...prev.step1,
        [field]: true,
      },
    }));
  };

  const handleStep3Blur = (
    field: keyof NonNullable<TouchedFields['step3']>
  ) => {
    setTouched((prev) => ({
      ...prev,
      step3: {
        ...prev.step3,
        [field]: true,
      },
    }));
  };

  const handleStep3CoverageLevelChange = (value: string) => {
    setCoverageLevel(value);
    if (!touched.step3?.coverageLevel) {
      handleStep3Blur('coverageLevel');
    }
  };

  const handleStep3CitiesChange = (value: string[]) => {
    setSelectedCities(value);
    if (!touched.step3?.serviceRegion) {
      handleStep3Blur('serviceRegion');
    }
  };

  const handleCoverageLevelChange = (value: string) => {
    setCoverageLevel(value);
    if (!touched.step1.coverageLevel) {
      setTouched((prev) => ({
        ...prev,
        step1: {
          ...prev.step1,
          coverageLevel: true,
        },
      }));
    }
  };

  const handleCitiesChange = (value: string[]) => {
    setSelectedCities(value);
    if (!touched.step1.selectedCities) {
      setTouched((prev) => ({
        ...prev,
        step1: {
          ...prev.step1,
          selectedCities: true,
        },
      }));
    }
  };

  const handleNext = async () => {
    if (currentStep === 1) {
      // Помечаем все поля как touched при попытке перейти на следующий шаг
      setTouched({
        step1: {
          numberOfEmployees: true,
          coverageLevel: true,
          selectedCities: true,
        },
      });

      // Проверяем валидность перед переходом
      if (!isStepValid(1, formData, errors, coverageLevel, selectedCities)) {
        return;
      }

      // Отправляем запрос на API
      setIsLoading(true);
      try {
        const data = await fetchFilterData({
          cities: selectedCities,
          level: coverageLevel,
          count: formData.step1.numberOfEmployees,
        });
        setApiData(data);
        console.log('API Response:', data);
      } catch (error) {
        console.error('Ошибка при отправке запроса:', error);
        // Можно добавить обработку ошибок, например, показать уведомление
      } finally {
        setIsLoading(false);
      }
    }

    if (currentStep < TOTAL_STEPS) {
      setCurrentStep(currentStep + 1);
    }
  };

  const handleBack = () => {
    // Если текущий шаг - второй, то при клике Назад нужно перейти на шаг 1
    if (currentStep === 2) {
      setCurrentStep(1);
      return;
    }

    // Если открыта форма OrderForm или CallbackForm, то при клике Назад нужно перейти на шаг 2
    if (
      currentStep === 3 &&
      (step3Mode === 'order' || step3Mode === 'callback')
    ) {
      setCurrentStep(2);
      return;
    }
  };

  const handleRecalculate = async () => {
    // Проверяем валидность данных перед запросом
    if (!isStepValid(1, formData, errors, coverageLevel, selectedCities)) {
      return;
    }

    setIsLoadingRecalculate(true);
    try {
      const data = await fetchFilterData({
        cities: selectedCities,
        level: coverageLevel,
        count: formData.step1.numberOfEmployees,
      });
      setApiData(data);
      console.log('API Response (Recalculate):', data);
    } catch (error) {
      console.error('Ошибка при пересчете:', error);
      // Можно добавить обработку ошибок, например, показать уведомление
    } finally {
      setIsLoadingRecalculate(false);
    }
  };

  const handleSelectOffer = (
    insurerName: string,
    city: string,
    record: InsuranceRecord
  ) => {
    setSelectedOffer({
      insurerName,
      city,
      record,
    });
    setStep3Mode('order');
    setCurrentStep(3);
  };

  const handleCallback = () => {
    setStep3Mode('callback');
    setCurrentStep(3);
  };

  const handleBackToOffers = () => {
    setCurrentStep(2);
    setStep3Mode(null);
  };

  const handleCreateCity = (label: string): string => {
    // Генерировать value из label (lowercase с заменой пробелов на дефисы)
    const value = label.toLowerCase().replace(/\s+/g, '-');

    // Проверить, что такого value еще нет
    if (!citiesList.some((c) => c.value === value)) {
      const newCity = { value, label };
      setCitiesList([...citiesList, newCity]);
      // Автоматически выбрать новый город
      setSelectedCities([...selectedCities, value]);
      return value;
    }

    // Если уже существует, вернуть существующий value
    return value;
  };

  const handleSubmit = async () => {
    setIsSubmitting(true);
    setSubmitError(null);

    try {
      if (step3Mode === 'order') {
        // Отправка формы заказа
        const response = await submitOrderForm({
          formData: formData,
          additionalData: {
            coverageLevel: coverageLevel,
            selectedCities: selectedCities,
            numberOfEmployees: formData.step1.numberOfEmployees,
            selectedOffer: selectedOffer,
            subId: urlParams.subId,
            clickId: urlParams.clickId,
          },
        });

        if (response.success) {
          setIsSubmitted(true);
        } else {
          setSubmitError(response.message || 'Ошибка при отправке формы');
          console.error('Submit error:', response.errors);
        }
      } else if (step3Mode === 'callback') {
        // Отправка формы обратного звонка
        const response = await submitCallbackForm({
          formData: formData,
          additionalData: {
            subId: urlParams.subId,
            clickId: urlParams.clickId,
          },
        });

        if (response.success) {
          setIsSubmitted(true);
        } else {
          setSubmitError(response.message || 'Ошибка при отправке формы');
          console.error('Submit error:', response.errors);
        }
      }
    } catch (error) {
      setSubmitError(
        error instanceof Error
          ? error.message
          : 'Произошла ошибка при отправке формы'
      );
      console.error('Submit exception:', error);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleResetForm = () => {
    setCurrentStep(1);
    setSelectedCities([]);
    setCoverageLevel('Базовый');
    setApiData(null);
    setSelectedOffer(null);
    setStep3Mode(null);
    setFormData({
      step1: {
        numberOfEmployees: '',
        email: '',
        phone: '',
      },
      step2: {
        company: '',
        position: '',
      },
      step3: {
        organizationName: '',
        inn: '',
        responsiblePerson: '',
        workEmail: '',
        workPhone: '',
        serviceRegion: '',
        callbackName: '',
        callbackPhone: '',
        isAgreed: true,
      },
    });
    setTouched({
      step1: {
        numberOfEmployees: false,
        coverageLevel: false,
        selectedCities: false,
      },
    });
    setIsSubmitted(false);
  };

  const handleCloseSuccess = () => {
    setIsSubmitted(false);
  };

  const renderStepContent = () => {
    switch (currentStep) {
      case 1:
        return (
          <Step1Form
            formData={formData}
            touched={touched}
            errors={errors}
            selectedCities={selectedCities}
            coverageLevel={coverageLevel}
            cities={citiesList}
            onCreateCity={handleCreateCity}
            onInputChange={handleStep1InputChange}
            onBlur={handleBlur}
            onCoverageLevelChange={handleCoverageLevelChange}
            onCitiesChange={handleCitiesChange}
          />
        );
      case 2:
        return (
          <Step2Form
            formData={formData}
            touched={touched}
            errors={errors}
            selectedCities={selectedCities}
            coverageLevel={coverageLevel}
            cities={citiesList}
            onCreateCity={handleCreateCity}
            apiData={apiData}
            isLoadingRecalculate={isLoadingRecalculate}
            onInputChange={handleStep1InputChange}
            onBlur={handleBlur}
            onCoverageLevelChange={handleCoverageLevelChange}
            onCitiesChange={handleCitiesChange}
            onRecalculate={handleRecalculate}
            onSelectOffer={handleSelectOffer}
            onCallback={handleCallback}
          />
        );
      case 3:
        return (
          <Step3Form
            formData={formData}
            step3Mode={step3Mode}
            selectedOffer={selectedOffer}
            coverageLevel={coverageLevel}
            selectedCities={selectedCities}
            numberOfEmployees={formData.step1.numberOfEmployees}
            cities={citiesList}
            onCreateCity={handleCreateCity}
            errors={step3Errors}
            touched={touched.step3}
            onInputChange={handleStep3InputChange}
            onCoverageLevelChange={handleStep3CoverageLevelChange}
            onCitiesChange={handleStep3CitiesChange}
            onBlur={handleStep3Blur}
            onSubmit={handleSubmit}
            onBackToOffers={handleBackToOffers}
            isSubmitted={isSubmitted}
            isSubmitting={isSubmitting}
            submitError={submitError}
            onCloseSuccess={handleCloseSuccess}
            onResetForm={handleResetForm}
          />
        );
      default:
        return null;
    }
  };

  const getStepTitle = () => {
    if (currentStep === 1) {
      return '1. Информация о компании';
    }
    if (currentStep === 2) {
      return '2. Выберите подходящее предложение';
    }
    return '3. Оформление заявки';
  };

  return (
    <div className="w-full max-w-6xl mx-auto flex flex-col gap-1.5 md:gap-5 md:mb-20">
      <StepNavigation
        currentStep={currentStep}
        step3Mode={step3Mode}
        isSubmitted={isSubmitted}
        onBack={handleBack}
      />
      <div className="w-full max-w-5xl mx-auto">
        <div className="space-y-2 mb-6">
          <div>
            <Typography variant="h4" className="text-primary">
              Получите выгодные предложения по ДМС
            </Typography>
            <Typography variant="body2" className="text-text-secondary">
              Заполните данные о компании, и мы предложим уникальный вариант под
              ваши требования
            </Typography>
          </div>
          <Typography className="text-subtitle1 font-semibold md:text-h6">
            {getStepTitle()}
          </Typography>
          <Progress totalSteps={TOTAL_STEPS} currentStep={currentStep} />
        </div>

        {(isLoading && currentStep === 1) || isLoadingRecalculate ? (
          <LoadingScreen />
        ) : currentStep === 1 ? (
          <div className="flex flex-col md:flex-row gap-4 mt-6">
            <Card className="w-full md:w-1/2">
              <CardContent>
                <div className="space-y-8">
                  {renderStepContent()}
                  <Button
                    className="w-full text-white"
                    variant="solid"
                    size="large"
                    onClick={handleNext}
                    disabled={isLoading}
                  >
                    Далее
                  </Button>
                </div>
              </CardContent>
            </Card>
            <OfferCard />
          </div>
        ) : currentStep === 2 ? (
          <div className="space-y-2">{renderStepContent()}</div>
        ) : (
          renderStepContent()
        )}
      </div>
    </div>
  );
}
