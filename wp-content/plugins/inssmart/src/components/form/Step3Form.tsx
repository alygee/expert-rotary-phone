import type { FormData, Step3Mode, FormErrors, TouchedFields } from "@/types/form";
import type { InsuranceRecord } from "@/types/api";
import type { MultiSelectOption } from "@/components/ui/multi-select";
import { OrderForm } from "./OrderForm";
import { CallbackForm } from "./CallbackForm";
import { SuccessCard } from "./SuccessCard";

interface Step3FormProps {
  formData: FormData;
  step3Mode: Step3Mode;
  selectedOffer: {
    insurerName: string;
    city: string;
    record: InsuranceRecord;
  } | null;
  coverageLevel: string;
  selectedCities: string[];
  numberOfEmployees: string;
  cities: MultiSelectOption[];
  onCreateCity?: (label: string) => string | null;
  errors?: FormErrors['step3'] | null;
  touched?: TouchedFields['step3'];
  onInputChange: (field: string, value: string | boolean) => void;
  onCoverageLevelChange?: (value: string) => void;
  onCitiesChange?: (value: string[]) => void;
  onBlur?: (field: keyof NonNullable<TouchedFields['step3']>) => void;
  onSubmit: () => void;
  onBackToOffers?: () => void;
  isSubmitted?: boolean;
  isSubmitting?: boolean;
  submitError?: string | null;
  onCloseSuccess?: () => void;
  onResetForm?: () => void;
}

export function Step3Form({
  formData,
  step3Mode,
  selectedOffer,
  coverageLevel,
  selectedCities,
  numberOfEmployees,
  cities,
  onCreateCity,
  errors,
  touched,
  onInputChange,
  onCoverageLevelChange,
  onCitiesChange,
  onBlur,
  onSubmit,
  onBackToOffers,
  isSubmitted,
  isSubmitting,
  submitError,
  onCloseSuccess,
  onResetForm,
}: Step3FormProps) {
  // Форма оформления
  if (step3Mode === "order" && selectedOffer) {
    // Показываем карточку успеха после отправки формы
    if (isSubmitted) {
      return (
        <SuccessCard
          onClose={onCloseSuccess}
          onReset={onResetForm}
        />
      );
    }
    
    return (
      <OrderForm
        formData={formData}
        insurerName={selectedOffer.insurerName}
        totalPrice={selectedOffer.record.total_price}
        coverageLevel={coverageLevel}
        numberOfEmployees={numberOfEmployees}
        selectedCities={selectedCities}
        cities={cities}
        onCreateCity={onCreateCity}
        errors={errors || undefined}
        touched={touched}
        onInputChange={onInputChange}
        onCoverageLevelChange={onCoverageLevelChange}
        onCitiesChange={onCitiesChange}
        onBlur={onBlur}
        onSubmit={onSubmit}
        isSubmitting={isSubmitting}
        submitError={submitError}
      />
    );
  }

  // Форма обратного звонка
  if (step3Mode === "callback") {
    // Показываем карточку успеха после отправки формы
    if (isSubmitted) {
      return (
        <SuccessCard
          onClose={onCloseSuccess}
          onReset={onResetForm}
        />
      );
    }
    
    return (
      <CallbackForm
        formData={formData}
        onInputChange={onInputChange}
        onSubmit={onSubmit}
        onBackToOffers={onBackToOffers}
        errors={errors || undefined}
        touched={touched}
        onBlur={onBlur}
        isSubmitting={isSubmitting}
        submitError={submitError}
      />
    );
  }

  return null;
}
