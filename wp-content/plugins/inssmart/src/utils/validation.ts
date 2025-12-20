import type { FormData, FormErrors } from "@/types/form";
import { getPhoneDigits } from "@/utils/phoneMask";

export function validateStep1(
  formData: FormData,
  coverageLevel: string,
  selectedCities: string[]
): FormErrors['step1'] {
  const errors: FormErrors['step1'] = {};
  
  if (!formData.step1.numberOfEmployees.trim()) {
    errors.numberOfEmployees = "Обязательное поле";
  }
  
  if (!coverageLevel) {
    errors.coverageLevel = "Выберите уровень покрытия";
  }
  
  if (selectedCities.length === 0) {
    errors.selectedCities = "Выберите хотя бы один регион";
  }
  
  return errors;
}

export function validateStep3Order(
  formData: FormData,
  coverageLevel: string,
  selectedCities: string[]
): FormErrors['step3'] {
  const errors: FormErrors['step3'] = {};
  
  if (!formData.step3.organizationName.trim()) {
    errors.organizationName = "Обязательное поле";
  }
  
  if (!formData.step3.inn.trim()) {
    errors.inn = "Обязательное поле";
  } else if (!/^\d{10,12}$/.test(formData.step3.inn.trim())) {
    errors.inn = "ИНН должен содержать 10 или 12 цифр";
  }
  
  if (!formData.step3.responsiblePerson.trim()) {
    errors.responsiblePerson = "Обязательное поле";
  }
  
  if (!formData.step3.workEmail.trim()) {
    errors.workEmail = "Обязательное поле";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.step3.workEmail.trim())) {
    errors.workEmail = "Некорректный email";
  }
  
  if (!formData.step3.workPhone.trim()) {
    errors.workPhone = "Обязательное поле";
  } else {
    const phoneDigits = getPhoneDigits(formData.step3.workPhone.trim());
    if (phoneDigits.length !== 11 || !phoneDigits.startsWith('7')) {
      errors.workPhone = "Некорректный номер телефона";
    }
  }
  
  if (!coverageLevel) {
    errors.coverageLevel = "Выберите уровень покрытия";
  }
  
  if (selectedCities.length === 0) {
    errors.serviceRegion = "Выберите хотя бы один регион обслуживания";
  }
  
  return errors;
}

export function validateStep3Callback(
  formData: FormData
): FormErrors['step3'] {
  const errors: FormErrors['step3'] = {};
  
  if (!formData.step3.callbackName.trim()) {
    errors.callbackName = "Обязательное поле";
  }
  
  if (!formData.step3.callbackPhone.trim()) {
    errors.callbackPhone = "Обязательное поле";
  } else {
    const phoneDigits = getPhoneDigits(formData.step3.callbackPhone.trim());
    if (phoneDigits.length !== 11 || !phoneDigits.startsWith('7')) {
      errors.callbackPhone = "Некорректный номер телефона";
    }
  }
  
  if (!formData.step3.isAgreed) {
    errors.isAgreed = "Необходимо согласие на обработку персональных данных";
  }
  
  return errors;
}

export function isStepValid(
  step: number,
  formData: FormData,
  errors: FormErrors['step1'],
  coverageLevel: string,
  selectedCities: string[]
): boolean {
  switch (step) {
    case 1:
      return (
        formData.step1.numberOfEmployees.trim() !== "" &&
        coverageLevel !== "" &&
        selectedCities.length > 0 &&
        !errors.numberOfEmployees &&
        !errors.coverageLevel &&
        !errors.selectedCities
      );
    case 2:
      return (
        formData.step2.company.trim() !== "" &&
        formData.step2.position.trim() !== ""
      );
    case 3:
      // Валидация для step3 зависит от режима (order/callback)
      // Проверка выполняется через validateStep3Order или validateStep3Callback
      return true;
    default:
      return false;
  }
}

