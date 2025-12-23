import * as React from 'react';
import { X } from 'lucide-react';
import { cn } from '@/lib/utils';
import { ArrowDownIcon } from '../icons';
import { Label } from '@/components/ui/label';
import { FormError } from '@/components/ui/form-error';

export interface MultiSelectOption {
  value: string;
  label: string;
}

export interface MultiSelectProps {
  options: MultiSelectOption[];
  value?: string[];
  onChange?: (value: string[]) => void;
  onBlur?: (e: React.FocusEvent<HTMLDivElement>) => void;
  placeholder?: string;
  className?: string;
  disabled?: boolean;
  searchable?: boolean;
  creatable?: boolean;
  onCreateOption?: (label: string) => string | null;
  closeOnSelect?: boolean; // Если false, dropdown останется открытым после выбора опции
  label?: React.ReactNode;
  icon?: React.ReactNode;
  error?: string | null;
  touched?: boolean;
}

export const MultiSelect = React.forwardRef<HTMLDivElement, MultiSelectProps>(
  (
    {
      options = [],
      value = [],
      onChange,
      onBlur,
      placeholder = 'Выберите...',
      className,
      disabled = false,
      searchable = true,
      creatable = false,
      onCreateOption,
      closeOnSelect = false,
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
    const containerRef = React.useRef<HTMLDivElement>(null);

    const selectedOptions = React.useMemo(() => {
      return options.filter((opt) => value.includes(opt.value));
    }, [options, value]);

    const filteredOptions = React.useMemo(() => {
      const unselected = options.filter((opt) => !value.includes(opt.value));

      if (!searchable || !searchQuery.trim()) {
        return unselected;
      }

      const queryLower = searchQuery.toLowerCase().trim();
      const filtered = unselected.filter((opt) =>
        opt.label.toLowerCase().includes(queryLower)
      );

      // Если включен creatable и есть поисковый запрос, которого нет в опциях
      if (creatable && queryLower.length > 0) {
        const exists = options.some(
          (opt) => opt.label.toLowerCase() === queryLower
        );

        if (!exists) {
          // Добавляем виртуальную опцию для создания новой
          return [
            ...filtered,
            {
              value: `__create__${searchQuery.trim()}`,
              label: `Создать: "${searchQuery.trim()}"`,
            },
          ];
        }
      }

      return filtered;
    }, [options, searchQuery, searchable, value, creatable]);

    React.useEffect(() => {
      const handleClickOutside = (event: MouseEvent) => {
        if (
          dropdownRef.current &&
          !dropdownRef.current.contains(event.target as Node) &&
          containerRef.current &&
          !containerRef.current.contains(event.target as Node)
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

    const handleInputBlur = (e: React.FocusEvent<HTMLInputElement>) => {
      // Используем setTimeout, чтобы дать время onClick на опциях сработать первым
      setTimeout(() => {
        const relatedTarget = document.activeElement;

        // Проверяем, что фокус не перешел на элементы внутри компонента
        if (
          relatedTarget &&
          (containerRef.current?.contains(relatedTarget) ||
            dropdownRef.current?.contains(relatedTarget))
        ) {
          return; // Фокус остался внутри компонента
        }

        setIsFocused(false);
        setSearchQuery('');
        // Создаем синтетическое событие для onBlur
        if (onBlur && containerRef.current) {
          const syntheticEvent = {
            ...e,
            currentTarget: containerRef.current,
            target: containerRef.current,
          } as React.FocusEvent<HTMLDivElement>;
          onBlur(syntheticEvent);
        }
      }, 200);
    };

    React.useEffect(() => {
      if (isOpen && inputRef.current) {
        inputRef.current.focus();
      }
    }, [isOpen]);

    const handleSelect = (optionValue: string) => {
      // Проверяем, является ли это опцией создания
      if (optionValue.startsWith('__create__')) {
        // Извлекаем label из value
        const newLabel = optionValue.replace('__create__', '').trim();

        if (newLabel && onCreateOption) {
          // Вызываем callback для создания новой опции
          const newValue = onCreateOption(newLabel);

          if (newValue !== null) {
            // Автоматически выбираем новую опцию
            onChange?.([...value, newValue]);
          }
        }

        // Если closeOnSelect = true, закрываем dropdown после создания опции
        if (closeOnSelect) {
          setIsOpen(false);
        } else {
          // Фокусируемся обратно на input, чтобы dropdown оставался открытым
          setTimeout(() => {
            setIsOpen(true);
            inputRef.current?.focus();
          }, 0);
        }
        setSearchQuery('');
        return;
      }

      // Обычная логика выбора
      if (value.includes(optionValue)) {
        onChange?.(value.filter((v) => v !== optionValue));
      } else {
        onChange?.([...value, optionValue]);
      }
      setSearchQuery('');

      // Если closeOnSelect = false, оставляем dropdown открытым
      if (closeOnSelect) {
        setIsOpen(false);
      } else {
        // Фокусируемся обратно на input, чтобы dropdown оставался открытым
        setTimeout(() => {
          setIsOpen(true);
          inputRef.current?.focus();
        }, 0);
      }
    };

    const handleRemove = (optionValue: string, e: React.MouseEvent) => {
      e.stopPropagation();
      onChange?.(value.filter((v) => v !== optionValue));
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

    const handleKeyDown = (e: React.KeyboardEvent) => {
      if (e.key === 'Backspace' && searchQuery === '' && value.length > 0) {
        onChange?.(value.slice(0, -1));
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

    const multiSelectElement = (
      <div ref={ref} className={cn('relative w-full', className)} {...props}>
        {/* Input container */}
        <div
          ref={containerRef}
          className={cn(
            'flex w-full min-h-[56px] rounded-2xl border border-gray-300 bg-background-paper p-4',
            'focus-visible:outline-none',
            'focus-visible:ring-1 focus-visible:ring-primary focus-visible:border-primary',
            'hover:ring-1 hover:ring-primary hover:border-primary',
            'disabled:cursor-not-allowed disabled:opacity-50',
            'cursor-text',
            showError &&
              'border-error hover:border-error hover:ring-error focus-visible:ring-error focus-visible:border-error'
          )}
          onClick={() => !disabled && inputRef.current?.focus()}
        >
          <div className="flex flex-wrap gap-2 flex-1 items-center">
            {/* Selected tags */}
            {selectedOptions.map((option) => (
              <div
                key={option.value}
                className={cn(
                  'inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl',
                  'bg-primary-12p border border-primary text-text-primary',
                  'text-body2'
                )}
              >
                <span>{option.label}</span>
                <button
                  type="button"
                  onClick={(e) => handleRemove(option.value, e)}
                  className={cn(
                    'flex items-center justify-center rounded-full bg-text-secondary text-paper p-0.5',
                    'hover:bg-grey-900 transition-colors',
                    'focus:outline-none focus:ring-2 focus:ring-ring'
                  )}
                >
                  <X className="h-3.5 w-3.5 text-background-paper" />
                </button>
              </div>
            ))}

            {/* Input field */}
            <input
              ref={inputRef}
              type="text"
              value={searchQuery}
              onChange={handleInputChange}
              onFocus={handleInputFocus}
              onBlur={handleInputBlur}
              onKeyDown={handleKeyDown}
              placeholder={selectedOptions.length === 0 ? placeholder : ''}
              disabled={disabled}
              className={cn(
                'flex-1 min-w-[120px] outline-none',
                'text-input bg-transparent',
                'placeholder:text-muted-foreground',
                hasError && 'placeholder:text-error'
              )}
            />
          </div>

          {/* Dropdown button */}
          <button
            type="button"
            onClick={handleToggle}
            disabled={disabled}
            className={cn(
              'flex items-center justify-center ml-2',
              'text-muted-foreground hover:text-foreground',
              'transition-colors',
              'disabled:opacity-50 disabled:cursor-not-allowed'
            )}
          >
            <ArrowDownIcon
              className={cn(
                'h-2.5 w-2.5 transition-transform',
                isOpen && 'rotate-180'
              )}
            />
          </button>
        </div>

        {/* Dropdown */}
        {isOpen && !disabled && (
          <div
            ref={dropdownRef}
            className={cn(
              'absolute z-50 mt-3 w-full',
              'bg-background-paper rounded-2xl shadow-elevation-3',
              'shadow-elevation-2',
              'max-h-60 overflow-auto'
            )}
          >
            {filteredOptions.length > 0 ? (
              <ul className="py-1">
                {filteredOptions.map((option) => {
                  const isCreateOption = option.value.startsWith('__create__');
                  return (
                    <li
                      key={option.value}
                      onMouseDown={(e) => {
                        e.preventDefault(); // Предотвращаем blur на input
                        handleSelect(option.value);
                      }}
                      className={cn(
                        'px-4 py-3 cursor-pointer',
                        'text-body1',
                        'hover:bg-action-hover',
                        'transition-colors',
                        isCreateOption
                          ? 'text-primary font-medium'
                          : 'text-text-primary'
                      )}
                    >
                      {option.label}
                    </li>
                  );
                })}
              </ul>
            ) : (
              <div className="px-4 py-3 text-body2 text-text-secondary text-center">
                {searchQuery ? 'Ничего не найдено' : 'Все опции выбраны'}
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
          {multiSelectElement}
          {showError && <FormError>{error}</FormError>}
        </div>
      );
    }

    return multiSelectElement;
  }
);

MultiSelect.displayName = 'MultiSelect';
