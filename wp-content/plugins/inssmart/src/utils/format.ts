/**
 * Форматирует число как валюту в формате RUB
 */
export function formatCurrency(value: number): string {
  return new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
}

/**
 * Преобразует строку с запятой в число
 */
export function parsePriceString(priceString: string): number {
  return parseFloat(priceString.replace(",", "."));
}


