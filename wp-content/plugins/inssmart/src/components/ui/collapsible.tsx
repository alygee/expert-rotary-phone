import * as React from 'react';
import { useState } from 'react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { ChevronDownIcon, FilterIcon } from '@/components/icons';
import { Typography } from './typography';

interface CollapsibleProps extends React.HTMLAttributes<HTMLDivElement> {
  triggerText?: string;
  defaultOpen?: boolean;
  children: React.ReactNode;
}

export const Collapsible = React.forwardRef<HTMLDivElement, CollapsibleProps>(
  (
    {
      className,
      triggerText = 'Фильтры',
      defaultOpen = false,
      children,
      ...props
    },
    ref
  ) => {
    const [isOpen, setIsOpen] = useState(defaultOpen);

    const toggle = () => {
      setIsOpen((prev) => !prev);
    };

    return (
      <div ref={ref} className={cn('w-full', className)} {...props}>
        <div className="flex justify-between items-center" onClick={toggle}>
          <div className="flex gap-3">
            <FilterIcon />
            <Typography variant="subtitle1">{triggerText}</Typography>
          </div>
          <Button variant="text" size="smallSquare">
            <ChevronDownIcon
              className={cn(
                'h-2.5 w-2.5 transition-transform',
                isOpen && 'rotate-180'
              )}
            />
          </Button>
        </div>
        <div
          id="collapsible-content"
          className={cn(
            'overflow-hidden transition-all duration-300 ease-in-out',
            isOpen
              ? 'max-h-[5000px] opacity-100 mt-4'
              : 'max-h-0 opacity-0 mt-0'
          )}
        >
          {children}
        </div>
      </div>
    );
  }
);

Collapsible.displayName = 'Collapsible';






