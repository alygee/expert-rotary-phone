/**
 * Форматирует номер телефона в формат +7 ___ ___ ____
 * @param value - исходное значение
 * @returns отформатированная строка
 */
export function formatPhoneInput(value: string): string {
  // Удаляем все нецифровые символы, кроме + в начале
  const digits = value.replace(/\D/g, '');
  
  // Если строка пустая, возвращаем +7
  if (digits.length === 0) {
    return '+7';
  }
  
  // Если первая цифра не 7, добавляем 7 в начало
  const phoneDigits = digits.startsWith('7') ? digits : `7${digits}`;
  
  // Ограничиваем до 11 цифр (7 + 10 цифр номера)
  const limitedDigits = phoneDigits.slice(0, 11);
  
  // Форматируем: +7 XXX XXX XXXX
  if (limitedDigits.length <= 1) {
    return '+7';
  }
  
  const code = limitedDigits.slice(1, 4);
  const firstPart = limitedDigits.slice(4, 7);
  const secondPart = limitedDigits.slice(7, 9);
  const thirdPart = limitedDigits.slice(9, 11);
  
  let formatted = '+7';
  
  if (code.length > 0) {
    formatted += ` ${code}`;
  }
  if (firstPart.length > 0) {
    formatted += ` ${firstPart}`;
  }
  if (secondPart.length > 0) {
    formatted += ` ${secondPart}`;
  }
  if (thirdPart.length > 0) {
    formatted += ` ${thirdPart}`;
  }
  
  return formatted;
}

/**
 * Обрабатывает изменение значения в поле телефона
 * @param value - новое значение
 * @param onChange - функция обратного вызова для обновления значения
 */
export function handlePhoneChange(
  value: string,
  onChange: (value: string) => void
): void {
  const formatted = formatPhoneInput(value);
  onChange(formatted);
}

/**
 * Получает только цифры из отформатированного номера телефона
 * @param formattedPhone - отформатированный номер (например, "+7 999 123 4567")
 * @returns строка только с цифрами (например, "79991234567")
 */
export function getPhoneDigits(formattedPhone: string): string {
  return formattedPhone.replace(/\D/g, '');
}

