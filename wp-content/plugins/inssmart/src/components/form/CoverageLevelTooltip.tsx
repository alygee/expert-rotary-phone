import { useState } from 'react';
import { InfoIcon } from '@/components/icons';
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip';

export function CoverageLevelTooltip() {
  const [isTooltipOpen, setIsTooltipOpen] = useState(false);

  return (
    <TooltipProvider>
      <Tooltip open={isTooltipOpen} onOpenChange={setIsTooltipOpen}>
        <TooltipTrigger asChild>
          <button
            type="button"
            className="group transition-colors inline-flex shrink-0"
          >
            <InfoIcon
              size={20}
              className="text-grey-300 group-hover:text-gray-600 transition-colors"
            />
          </button>
        </TooltipTrigger>
        <TooltipContent
          className="max-w-xs p-4"
          sideOffset={16}
          alignOffset={-20}
          align="start"
        >
          <p className="text-subtitle2 mb-2">Уровни покрытия</p>
          <ul className="list-disc flex flex-col gap-1 px-4">
            <li className="text-caption">
              Базовый: основное покрытие, включая обязательные медицинские
              услуги
            </li>
            <li className="text-caption">
              Комфорт: Расширенная помощь, включая амбулаторное лечение и доп.
              услуги
            </li>
            <li className="text-caption">
              Премиум: Полный пакет с приоритетным обслуживанием и доп. опциями
            </li>
          </ul>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  );
}

