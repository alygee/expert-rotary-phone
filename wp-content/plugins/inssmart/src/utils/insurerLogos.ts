import logotypes1 from "@/assets/logotypes/logotypes1.svg";
import logotypes2 from "@/assets/logotypes/logotypes2.svg";
import logotypes3 from "@/assets/logotypes/logotypes3.svg";
import logotypes4 from "@/assets/logotypes/logotypes4.svg";
import logotypes5 from "@/assets/logotypes/logotypes5.svg";
import logotypes7 from "@/assets/logotypes/logotypes7.svg";

/**
 * Маппинг имен страховщиков на их логотипы
 */
const insurerLogoMap: Record<string, string> = {
  Зетта: logotypes1,
  Ингос: logotypes2,
  РГС: logotypes3,
  Сбер: logotypes4,
  Пари: logotypes5,
  "Капитал Лайф": logotypes7,
};

/**
 * Получает путь к логотипу страховщика по его имени
 */
export function getInsurerLogo(insurerName: string): string | null {
  return insurerLogoMap[insurerName] || null;
}

