import type { MultiSelectOption } from "@/components/ui/multi-select";

/**
 * Преобразует массив значений городов в строку с названиями через запятую
 * @param cityValues - массив значений городов (value)
 * @param cities - массив всех доступных городов с value и label
 * @returns строка с названиями городов, разделенными запятой
 */
export function getCityLabels(
  cityValues: string[],
  cities: MultiSelectOption[]
): string {
  return cityValues
    .map((value) => {
      const city = cities.find((c) => c.value === value);
      return city ? city.label : value;
    })
    .join(", ");
}

