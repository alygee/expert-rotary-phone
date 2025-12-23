import * as React from 'react';

import { cn } from '@/lib/utils';
import { Label } from '@/components/ui/label';
import { FormError } from '@/components/ui/form-error';

export interface InputProps
  extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: React.ReactNode;
  labelFocusedText?: string;
  icon?: React.ReactNode;
  error?: string | null;
  touched?: boolean;
  onBlur?: (event: React.FocusEvent<HTMLInputElement>) => void;
}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
  (
    { className, type, label, icon, error, touched, onBlur, onFocus, ...props },
    ref
  ) => {
    const [isFocused, setIsFocused] = React.useState(false);

    const handleFocus = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(true);
      onFocus?.(e);
    };

    const handleBlur = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(false);
      onBlur?.(e);
    };

    const hasError = touched && error;
    const showError = hasError && error;

    const inputElement = (
      <input
        type={type}
        className={cn(
          'flex w-full rounded-2xl border border-grey-300 bg-background-paper px-4 py-4 text-input file:border-0 file:bg-transparent file:text-input file:font-normal placeholder:text-muted-foreground focus-visible:outline-none focus-visible:border-primary focus-visible:ring-1 hover:border-primary hover:ring-1 disabled:cursor-not-allowed disabled:opacity-50',
          hasError &&
            'border-error placeholder:text-error hover:border-error hover:ring-1 hover:ring-error focus-visible:border-error focus-visible:ring-1 focus-visible:ring-error',
          className
        )}
        ref={ref}
        onFocus={handleFocus}
        onBlur={handleBlur}
        {...props}
      />
    );

    if (label) {
      const renderLabel = () => {
        if (typeof label === 'string') {
          return <span>{label}</span>;
        }

        return label;
      };

      const renderIcon = () => {
        if (!icon) return null;

        if (React.isValidElement(icon)) {
          const iconProps = (icon as React.ReactElement).props;

          return (
            <div className="ml-1">
              {React.cloneElement(icon as React.ReactElement, {
                ...(hasError && { color: 'hsl(1, 77%, 55%)' }), // error color
                className: cn(iconProps.className, hasError && 'text-error'),
              })}
            </div>
          );
        }

        return <div className="ml-1">{icon}</div>;
      };

      return (
        <div className="space-y-2">
          <Label>
            <div
              className={cn(
                'flex items-center gap-2.5 tracking-wide',
                isFocused && 'text-primary',
                hasError && 'text-error'
              )}
            >
              {renderIcon()}
              {renderLabel()}
            </div>
          </Label>
          {inputElement}
          {showError && <FormError>{error}</FormError>}
        </div>
      );
    }

    return inputElement;
  }
);
Input.displayName = 'Input';

export { Input };
