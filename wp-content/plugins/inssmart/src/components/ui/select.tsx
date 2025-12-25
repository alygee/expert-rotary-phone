import * as React from 'react';
import { cn } from '@/lib/utils';
import { ArrowDownIcon, ArrowUpIcon } from '@/components/icons';
import { Label } from '@/components/ui/label';
import { FormError } from '@/components/ui/form-error';

export interface SelectOption {
  value: string;
  label: string;
}

export interface SelectProps {
  options: SelectOption[];
  value?: string;
  onChange?: (value: string) => void;
  placeholder?: string;
  className?: string;
  disabled?: boolean;
  searchable?: boolean;
  label?: React.ReactNode;
  icon?: React.ReactNode;
  error?: string | null;
  touched?: boolean;
}

export const Select = React.forwardRef<HTMLDivElement, SelectProps>(
  (
    {
      options,
      value,
      onChange,
      placeholder = 'Выберите...',
      className,
      disabled = false,
      searchable = true,
      label,
      icon,
      error,
      touched,
      ...props
    },
    ref
  ) => {
    const [isOpen, setIsOpen] = React.useState(false);
    const [isFocused, setIsFocused] = React.useState(false);
    const [searchQuery, setSearchQuery] = React.useState('');
    const inputRef = React.useRef<HTMLInputElement>(null);
    const dropdownRef = React.useRef<HTMLDivElement>(null);

    const selectedOption = options.find((opt) => opt.value === value);

    const filteredOptions = React.useMemo(() => {
      if (!searchable || !searchQuery) return options;
      return options.filter((opt) =>
        opt.label.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }, [options, searchQuery, searchable]);

    React.useEffect(() => {
      const handleClickOutside = (event: MouseEvent) => {
        if (
          dropdownRef.current &&
          !dropdownRef.current.contains(event.target as Node) &&
          inputRef.current &&
          !inputRef.current.contains(event.target as Node)
        ) {
          setIsOpen(false);
          setSearchQuery('');
        }
      };

      if (isOpen) {
        document.addEventListener('mousedown', handleClickOutside);
      }

      return () => {
        document.removeEventListener('mousedown', handleClickOutside);
      };
    }, [isOpen]);

    React.useEffect(() => {
      if (isOpen && inputRef.current) {
        inputRef.current.focus();
      }
    }, [isOpen]);

    const handleSelect = (optionValue: string) => {
      onChange?.(optionValue);
      setIsOpen(false);
      setSearchQuery('');
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      setSearchQuery(e.target.value);
      if (!isOpen) {
        setIsOpen(true);
      }
    };

    const handleInputFocus = () => {
      setIsFocused(true);
      setIsOpen(true);
    };

    const handleInputBlur = () => {
      setIsFocused(false);
    };

    const handleToggle = () => {
      if (!disabled) {
        setIsOpen(!isOpen);
        if (!isOpen) {
          setIsFocused(true);
          setSearchQuery('');
        } else {
          setIsFocused(false);
        }
      }
    };

    const hasError = touched && error;
    const showError = hasError && error;

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

    const selectElement = (
      <div ref={ref} className={cn('relative w-full', className)} {...props}>
        {/* Input field */}
        <div className="relative">
          <input
            ref={inputRef}
            type="text"
            value={isOpen ? searchQuery : selectedOption?.label || ''}
            onChange={handleInputChange}
            onFocus={handleInputFocus}
            onBlur={handleInputBlur}
            placeholder={placeholder}
            disabled={disabled}
            readOnly={!searchable}
            className={cn(
              'flex w-full rounded-2xl border border-gray-300 bg-background-paper px-4 py-4 text-input',
              'ring-offset-background placeholder:text-muted-foreground',
              'focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary focus-visible:border-primary',
              'hover:outline-none hover:ring-1 hover:ring-primary hover:border-primary',
              'disabled:cursor-not-allowed disabled:opacity-50',
              'pr-10',
              hasError &&
                'border-error placeholder:text-error hover:border-error hover:ring-1 hover:ring-error focus-visible:border-error focus-visible:ring-1 focus-visible:ring-error'
            )}
          />
          <button
            type="button"
            onClick={handleToggle}
            disabled={disabled}
            className={cn(
              'absolute right-4 top-1/2 -translate-y-1/2',
              'flex items-center justify-center',
              'text-foreground',
              'transition-colors',
              'disabled:opacity-50 disabled:cursor-not-allowed'
            )}
          >
            {isOpen ? (
              <ArrowUpIcon className="h-2.5 w-2.5" />
            ) : (
              <ArrowDownIcon className="h-2.5 w-2.5" />
            )}
          </button>
        </div>

        {/* Dropdown */}
        {isOpen && !disabled && (
          <div
            ref={dropdownRef}
            className={cn(
              'absolute z-50 mt-3 w-full',
              'bg-background-paper rounded-2xl',
              'shadow-elevation-3',
              'max-h-60 overflow-auto'
            )}
          >
            {filteredOptions.length > 0 ? (
              <ul className="py-1">
                {filteredOptions.map((option) => (
                  <li
                    key={option.value}
                    onClick={() => handleSelect(option.value)}
                    className={cn(
                      'px-4 py-3 cursor-pointer',
                      'text-body1 text-text-primary',
                      'hover:bg-action-hover',
                      'transition-colors',
                      value === option.value && 'bg-action-selected'
                    )}
                  >
                    {option.label}
                  </li>
                ))}
              </ul>
            ) : (
              <div className="px-4 py-3 text-body2 text-text-secondary text-center">
                Ничего не найдено
              </div>
            )}
          </div>
        )}
      </div>
    );

    if (label) {
      return (
        <div className="space-y-2">
          <Label>
            <div
              onClick={(e) => {
                e.stopPropagation();
                e.preventDefault();
                // // Блокируем клик на label, но пропускаем клики на tooltip кнопку
                // const target = e.target as HTMLElement;
                // if (!target.closest('button[type="button"]')) {
                //   e.stopPropagation();
                // }
              }}
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
          {selectElement}
          {showError && <FormError>{error}</FormError>}
        </div>
      );
    }

    return selectElement;
  }
);

Select.displayName = 'Select';

Select.displayName = 'Select';
