import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { MultiSelect } from '@/components/ui/multi-select';
import {
  CrowdIcon,
  LocationIcon,
  ShieldIcon,
} from '@/components/icons';
import { CoverageLevelTooltip } from './CoverageLevelTooltip';
import { coverageLevels } from '@/constants/form';
import type { FormData, TouchedFields, FormErrors } from '@/types/form';
import type { MultiSelectOption } from '@/components/ui/multi-select';

interface Step1FormProps {
  formData: FormData;
  touched: TouchedFields;
  errors: FormErrors['step1'];
  selectedCities: string[];
  coverageLevel: string;
  cities: MultiSelectOption[];
  onCreateCity?: (label: string) => string | null;
  onInputChange: (field: string, value: string) => void;
  onBlur: (field: keyof TouchedFields['step1']) => void;
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
      <Input
        id="numberOfEmployees"
        type="number"
        placeholder="Какое количество сотрудников в компании?"
        value={formData.step1.numberOfEmployees}
        onChange={(e) => onInputChange('numberOfEmployees', e.target.value)}
        onBlur={() => onBlur('numberOfEmployees')}
        label="Количество сотрудников"
        icon={<CrowdIcon />}
        error={errors.numberOfEmployees}
        touched={touched.step1.numberOfEmployees}
      />

      <Select
        options={coverageLevels}
        value={coverageLevel}
        onChange={onCoverageLevelChange}
        placeholder="Выберите уровень покрытия"
        label={
          <>
            <span>Уровень покрытия</span>
            <CoverageLevelTooltip />
          </>
        }
        icon={<ShieldIcon />}
        error={errors.coverageLevel}
        touched={touched.step1.coverageLevel}
      />

      <MultiSelect
        closeOnSelect
        options={cities}
        value={selectedCities}
        onChange={onCitiesChange}
        placeholder="Выберите город или регион"
        onBlur={() => onBlur('selectedCities')}
        creatable={true}
        onCreateOption={onCreateCity}
        label="Регион обслуживания"
        icon={<LocationIcon />}
        error={errors.selectedCities}
        touched={touched.step1.selectedCities}
      />
    </div>
  );
}
