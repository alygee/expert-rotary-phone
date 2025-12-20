// Типы для формы

export interface FormData {
  step1: {
    numberOfEmployees: string;
    email: string;
    phone: string;
  };
  step2: {
    company: string;
    position: string;
  };
  step3: {
    // Поля формы оформления
    organizationName: string;
    inn: string;
    responsiblePerson: string;
    workEmail: string;
    workPhone: string;
    serviceRegion: string;
    // Поля формы обратного звонка
    callbackName: string;
    callbackPhone: string;
    isAgreed: boolean;
  };
}

export interface TouchedFields {
  step1: {
    numberOfEmployees: boolean;
    coverageLevel: boolean;
    selectedCities: boolean;
  };
  step3?: {
    organizationName?: boolean;
    inn?: boolean;
    responsiblePerson?: boolean;
    workEmail?: boolean;
    workPhone?: boolean;
    coverageLevel?: boolean;
    serviceRegion?: boolean;
    callbackName?: boolean;
    callbackPhone?: boolean;
    isAgreed?: boolean;
  };
}

export interface FormErrors {
  step1: {
    numberOfEmployees?: string;
    coverageLevel?: string;
    selectedCities?: string;
  };
  step3?: {
    organizationName?: string;
    inn?: string;
    responsiblePerson?: string;
    workEmail?: string;
    workPhone?: string;
    coverageLevel?: string;
    serviceRegion?: string;
    callbackName?: string;
    callbackPhone?: string;
    isAgreed?: string;
  };
}

export type Step3Mode = 'order' | 'callback' | null;

