// Типы для API ответа /api/filter

export interface InsurancePrices {
  polyclinic: string;
  dentistry: string;
  ambulance: string;
  hospitalization: string;
  doctor_home: string;
}

export interface InsuranceRecord {
  level: string;
  employees: string;
  prices: InsurancePrices;
  total_price: number;
}

export interface Insurer {
  name: string;
  count: number;
  records: InsuranceRecord[];
}

export interface CityResult {
  city: string;
  count: number;
  insurers: Insurer[];
}

export interface FilterFilters {
  cities: string[];
  levels: string[];
  count: number | null;
}

export interface FallbackData {
  title: string;
  description: string;
  not_found_cities: string[];
  count: number;
  insurers: Insurer[];
}

export interface FilterResponse {
  success: boolean;
  count: number;
  cities_count: number;
  not_found_cities: string[];
  filters: FilterFilters;
  results: CityResult[];
  fallback: FallbackData | null;
}

// Типы для отправки форм в Contact Form 7

export interface SubmitOrderRequest {
  formData: {
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
      organizationName: string;
      inn: string;
      responsiblePerson: string;
      workEmail: string;
      workPhone: string;
      serviceRegion: string;
    };
  };
  additionalData: {
    coverageLevel: string;
    selectedCities: string[];
    numberOfEmployees: string;
    selectedOffer: {
      insurerName: string;
      city: string;
      record: {
        total_price: number;
      };
    } | null;
    subId?: string | null;
    clickId?: string | null;
  };
}

export interface SubmitCallbackRequest {
  formData: {
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
      callbackName: string;
      callbackPhone: string;
      isAgreed: boolean;
    };
  };
  additionalData?: {
    subId?: string | null;
    clickId?: string | null;
  };
}

export interface SubmitResponse {
  success: boolean;
  data?: {
    message: string;
    status: string;
  };
  message?: string;
  errors?: string[];
  status?: string;
}

export interface SubmitError {
  message: string;
  errors?: string[];
  status?: string;
}

