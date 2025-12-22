import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { Check } from "lucide-react";

import { cn } from "@/lib/utils";

const checkboxVariants = cva(
  "shrink-0 rounded-sm border bg-background-paper transition-all duration-200 flex items-center justify-center relative overflow-hidden",
  {
    variants: {
      variant: {
        default: "border-grey-400",
        secondary: "border-grey-400",
        error: "border-grey-400",
        success: "border-grey-400",
        warning: "border-grey-400",
      },
      size: {
        small: "h-4 w-4",
        medium: "h-[18px] w-[18px]",
        large: "h-6 w-6",
      },
      checked: {
        true: "",
        false: "",
      },
    },
    compoundVariants: [
      {
        variant: "default",
        checked: true,
        class: "bg-primary border-primary",
      },
      {
        variant: "secondary",
        checked: true,
        class: "bg-secondary border-secondary",
      },
      {
        variant: "error",
        checked: true,
        class: "bg-error border-error",
      },
      {
        variant: "success",
        checked: true,
        class: "bg-success border-success",
      },
      {
        variant: "warning",
        checked: true,
        class: "bg-warning border-warning",
      },
    ],
    defaultVariants: {
      variant: "default",
      size: "medium",
      checked: false,
    },
  },
);

const checkIconVariants = cva(
  "text-white transition-transform duration-200 origin-center",
  {
    variants: {
      size: {
        small: "h-2.5 w-2.5",
        medium: "h-3 w-3",
        large: "h-3.5 w-3.5",
      },
      checked: {
        true: "scale-100",
        false: "scale-0",
      },
    },
    defaultVariants: {
      size: "medium",
      checked: false,
    },
  },
);

export interface CheckboxProps
  extends Omit<React.InputHTMLAttributes<HTMLInputElement>, "size" | "type">,
    Omit<VariantProps<typeof checkboxVariants>, "checked"> {
  label?: string;
  labelClassName?: string;
  containerClassName?: string;
}

const Checkbox = React.forwardRef<HTMLInputElement, CheckboxProps>(
  (
    {
      className,
      variant,
      size,
      label,
      labelClassName,
      containerClassName,
      checked,
      disabled,
      ...props
    },
    ref,
  ) => {
    const isControlled = checked !== null && checked !== undefined;
    const [internalChecked, setInternalChecked] = React.useState(
      checked ?? false,
    );

    React.useEffect(() => {
      if (isControlled) {
        setInternalChecked(checked);
      }
    }, [checked, isControlled]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
      if (!isControlled) {
        setInternalChecked(e.target.checked);
      }
      if (props.onChange) {
        props.onChange(e);
      }
    };

    const [isFocused, setIsFocused] = React.useState(false);
    const [ripple, setRipple] = React.useState<{
      x: number;
      y: number;
    } | null>(null);
    const checkboxRef = React.useRef<HTMLDivElement>(null);
    const checkboxId =
      props.id || `checkbox-${Math.random().toString(36).substr(2, 9)}`;
    const isChecked = isControlled ? checked : internalChecked;

    const handleFocus = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(true);
      if (props.onFocus) {
        props.onFocus(e);
      }
    };

    const handleBlur = (e: React.FocusEvent<HTMLInputElement>) => {
      setIsFocused(false);
      if (props.onBlur) {
        props.onBlur(e);
      }
    };

    const handleMouseDown = (
      e: React.MouseEvent<HTMLLabelElement | HTMLDivElement>,
    ) => {
      if (disabled) return;

      if (checkboxRef.current) {
        const rect = checkboxRef.current.getBoundingClientRect();
        // Вычисляем координаты относительно чекбокса
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Если клик был вне чекбокса (например, на тексте label), используем центр
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        // Используем координаты клика, если они внутри чекбокса, иначе центр
        const finalX =
          x >= 0 && x <= rect.width && y >= 0 && y <= rect.height ? x : centerX;
        const finalY =
          x >= 0 && x <= rect.width && y >= 0 && y <= rect.height ? y : centerY;

        setRipple({ x: finalX, y: finalY });
        setTimeout(() => setRipple(null), 600);
      }
    };

    return (
      <div
        className={cn(
          "flex items-center gap-2",
          disabled && "opacity-50",
          containerClassName,
        )}
      >
        <label
          htmlFor={checkboxId}
          onMouseDown={handleMouseDown}
          className={cn(
            "flex items-center gap-2 cursor-pointer select-none",
            disabled && "cursor-not-allowed",
          )}
        >
          <input
            type="checkbox"
            className="sr-only peer"
            ref={ref}
            id={checkboxId}
            checked={isChecked}
            disabled={disabled}
            onChange={handleChange}
            onFocus={handleFocus}
            onBlur={handleBlur}
            {...props}
          />
          <div
            ref={checkboxRef}
            className={cn(
              checkboxVariants({ variant, size, checked: isChecked }),
              isFocused && "ring-2 ring-primary ring-opacity-50 ring-offset-0",
              disabled && "cursor-not-allowed opacity-50",
              className,
            )}
          >
            {/* Ripple effect */}
            {ripple && (
              <span
                className="absolute rounded-full pointer-events-none"
                style={{
                  left: `${ripple.x}px`,
                  top: `${ripple.y}px`,
                  width: "1px",
                  height: "1px",
                  transform: "translate(-50%, -50%)",
                  backgroundColor: isChecked
                    ? "rgba(255, 255, 255, 0.5)"
                    : "rgba(0, 0, 0, 0.2)",
                  animation: "ripple 600ms ease-out",
                  willChange: "transform, opacity",
                }}
              />
            )}
            {/* Check icon */}
            <Check
              className={cn(
                "stroke-[2.5]",
                checkIconVariants({ size, checked: isChecked }),
              )}
            />
          </div>
          {label && (
            <span
              className={cn(
                "text-body2 text-text-secondary",
                disabled && "cursor-not-allowed",
                labelClassName,
              )}
            >
              {label}
            </span>
          )}
        </label>
      </div>
    );
  },
);
Checkbox.displayName = "Checkbox";

export { Checkbox, checkboxVariants };
