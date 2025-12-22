import type { FilterResponse } from '@/types/api';

interface FilterParams {
  cities?: string[];
  level?: string;
  count?: string;
}

export async function fetchFilterData(
  params: FilterParams
): Promise<FilterResponse> {
  const urlParams = new URLSearchParams();

  if (params.cities && params.cities.length > 0) {
    urlParams.append('cities', params.cities.join(','));
  }
  if (params.level) {
    urlParams.append('levels', params.level);
  }
  if (params.count) {
    urlParams.append('count', params.count);
  }

  const queryString = urlParams.toString();
  const url = queryString
    ? `/wp-json/dmc/v1/filter?${queryString}`
    : '/wp-json/dmc/v1/filter';

  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error(`Ошибка при загрузке данных: ${response.status}`);
  }

  const data: FilterResponse = await response.json();
  return data;
}
