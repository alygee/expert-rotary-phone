import { type ClassValue, clsx } from "clsx"
import { extendTailwindMerge } from "tailwind-merge"

// Кастомные классы размера текста (fontSize) - они не должны конфликтовать с классами цвета
const customTextSizeClasses = [
  "h1",
  "h2",
  "h3",
  "h4",
  "h5",
  "h6",
  "subtitle1",
  "subtitle2",
  "body1",
  "body2",
  "input",
  "caption",
  "overline",
];

// Создаем кастомный twMerge
// Настраиваем так, чтобы кастомные классы размера текста не конфликтовали с классами цвета
const twMerge = extendTailwindMerge({
  extend: {
    classGroups: {
      // Добавляем кастомные классы размера текста в группу font-size
      // Они будут конфликтовать только друг с другом, но не с text-color
      "font-size": [
        {
          text: customTextSizeClasses,
        },
      ],
    },
  },
});

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}


