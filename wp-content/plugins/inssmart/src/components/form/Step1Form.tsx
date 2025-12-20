import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { MultiSelect } from "@/components/ui/multi-select";
import { FormError } from "@/components/ui/form-error";
import {
  CrowdIcon,
  InfoIcon,
  LocationIcon,
  ShieldIcon,
} from "@/components/icons";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import { cn } from "@/lib/utils";
import { coverageLevels } from "@/constants/form";
import type { FormData, TouchedFields, FormErrors } from "@/types/form";
import type { MultiSelectOption } from "@/components/ui/multi-select";

interface Step1FormProps {
  formData: FormData;
  touched: TouchedFields;
  errors: FormErrors["step1"];
  selectedCities: string[];
  coverageLevel: string;
  cities: MultiSelectOption[];
  onCreateCity?: (label: string) => string | null;
  onInputChange: (field: string, value: string) => void;
  onBlur: (field: keyof TouchedFields["step1"]) => void;
  onCoverageLevelChange: (value: string) => void;
  onCitiesChange: (value: string[]) => void;
}

export function Step1Form({
  formData,
  touched,
  errors,
  selectedCities,
  coverageLevel,
  cities,
  onCreateCity,
  onInputChange,
  onBlur,
  onCoverageLevelChange,
  onCitiesChange,
}: Step1FormProps) {
  return (
    <div className="space-y-6">
      <div className="space-y-2">
        <Label htmlFor="numberOfEmployees">
          <div className="flex items-center gap-2.5 tracking-wide">
            <CrowdIcon className="ml-1" />
            <span>Количество сотрудников</span>
          </div>
        </Label>
        <Input
          id="numberOfEmployees"
          type="number"
          placeholder="Какое количество сотрудников в компании?"
          value={formData.step1.numberOfEmployees}
          onChange={(e) => onInputChange("numberOfEmployees", e.target.value)}
          onBlur={() => onBlur("numberOfEmployees")}
          className={cn(
            touched.step1.numberOfEmployees &&
              errors.numberOfEmployees &&
              "border-error focus-visible:ring-error placeholder:text-error",
          )}
        />
        {touched.step1.numberOfEmployees && errors.numberOfEmployees && (
          <FormError>{errors.numberOfEmployees}</FormError>
        )}
      </div>

      <div className="space-y-2">
        <Label htmlFor="coverageLevel">
          <div className="flex items-center gap-2.5 tracking-wide">
            <ShieldIcon className="ml-1" />
            <span>Уровень покрытия </span>
            <TooltipProvider>
              <Tooltip>
                <TooltipTrigger asChild>
                  <button>
                    <InfoIcon size={20} />
                  </button>
                </TooltipTrigger>
                <TooltipContent className="max-w-xl">
                  <p className="text-subtitle1">Уровень покрытия</p>
                  <br />
                  <ul>
                    <li>
                      - Базовый: основное покрытие, включая обязательные
                      медицинские услуги
                    </li>
                    <li>
                      - Комфорт: Расширенная помощь, включая амбулаторное
                      лечение и доп. услуги
                    </li>
                    <li>
                      - Премиум: Полный пакет с приоритетным обслуживанием и
                      доп. опциями
                    </li>
                  </ul>
                </TooltipContent>
              </Tooltip>
            </TooltipProvider>
          </div>
        </Label>
        <div
          className={cn(
            touched.step1.coverageLevel &&
              errors.coverageLevel &&
              "[&_input]:border-error [&_input]:focus-visible:ring-error [&_input]:placeholder:text-error",
          )}
        >
          <Select
            options={coverageLevels}
            value={coverageLevel}
            onChange={onCoverageLevelChange}
            placeholder="Выберите уровень покрытия"
          />
        </div>
        {touched.step1.coverageLevel && errors.coverageLevel && (
          <FormError>{errors.coverageLevel}</FormError>
        )}
      </div>

      <div className="space-y-2">
        <Label htmlFor="selectedCities">
          <div className="flex items-center gap-2.5 tracking-wide">
            <LocationIcon className="ml-1" />
            <span>Регион обслуживания</span>
          </div>
        </Label>
        <div
          className={cn(
            touched.step1.selectedCities &&
              errors.selectedCities &&
              "[&_div]:border-error [&_div]:focus-within:ring-error [&_input]:placeholder:text-error",
          )}
        >
          <MultiSelect
            closeOnSelect
            options={cities}
            value={selectedCities}
            onChange={onCitiesChange}
            placeholder="Выберите город или регион"
            onBlur={() => onBlur("selectedCities")}
            creatable={true}
            onCreateOption={onCreateCity}
          />
        </div>
        {touched.step1.selectedCities && errors.selectedCities && (
          <FormError>{errors.selectedCities}</FormError>
        )}
      </div>
    </div>
  );
}
