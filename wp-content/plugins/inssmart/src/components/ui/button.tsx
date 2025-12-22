import * as React from 'react';
import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';

import { cn } from '@/lib/utils';

const buttonVariants = cva(
  'inline-flex items-center justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:bg-action-disabled-bg disabled:text-text-disabled disabled:cursor-not-allowed',
  {
    variants: {
      variant: {
        // Solid - сплошной фон
        solid: 'bg-primary text-white hover:bg-primary-dark',
        'solid-secondary': 'bg-secondary text-white hover:bg-secondary-dark',
        'solid-error': 'bg-error text-white hover:bg-error-dark',
        'solid-success': 'bg-success text-white hover:bg-success-dark',
        'solid-warning': 'bg-warning text-white hover:bg-warning-dark',

        // Contained - с границей
        contained:
          'border border-primary bg-transparent text-primary hover:bg-primary-4p',
        'contained-secondary':
          'border border-secondary bg-transparent text-secondary hover:bg-secondary-4p',
        'contained-error':
          'border border-error bg-transparent text-error hover:bg-error-4p',
        'contained-success':
          'border border-success bg-transparent text-success hover:bg-success-4p',
        'contained-warning':
          'border border-warning bg-transparent text-warning hover:bg-warning-4p',

        // Text - текстовый вариант
        text: 'bg-transparent text-primary hover:bg-primary-20p',
        'text-secondary': 'bg-transparent text-secondary hover:bg-gray-200',
        'text-error': 'bg-transparent text-error hover:bg-error-4p',
        'text-success': 'bg-transparent text-success hover:bg-success-4p',
        'text-warning': 'bg-transparent text-warning hover:bg-warning-4p',

        // Обратная совместимость со старыми вариантами
        default: 'bg-primary text-white hover:bg-primary-dark',
        destructive: 'bg-error text-white hover:bg-error-dark',
        outline:
          'border border-primary bg-transparent text-primary hover:bg-primary-4p',
        secondary: 'bg-secondary text-white hover:bg-secondary-dark',
        ghost: 'bg-transparent text-primary hover:bg-primary-4p',
        link: 'bg-transparent text-primary underline-offset-4 hover:underline',
      },
      size: {
        small: 'h-10 rounded-[32px] px-6 py-2 text-body2 gap-2',
        medium: 'h-12 rounded-[40px] px-8 py-3 text-body1 gap-2',
        large: 'h-14 rounded-[64px] px-9 py-4 text-body1 gap-2',
        mediumSquare: 'h-12 rounded-xl p-4 text-body1 gap-2',
        smallSquare: 'rounded-lg p-4 text-body2 gap-2',
        // Обратная совместимость
        default: 'h-12 rounded-[40px] px-8 py-3 text-body1 gap-2',
        sm: 'h-10 rounded-[32px] px-6 py-2 text-body2 gap-2',
        lg: 'h-14 rounded-[64px] px-8 py-4 text-body1 gap-2',
        icon: 'h-10 w-10 rounded-full',
      },
    },
    defaultVariants: {
      variant: 'solid',
      size: 'medium',
    },
  }
);

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  asChild?: boolean;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  (
    {
      className,
      variant,
      size,
      asChild = false,
      leftIcon,
      rightIcon,
      children,
      ...props
    },
    ref
  ) => {
    const Comp = asChild ? Slot : 'button';
    return (
      <Comp
        className={cn(buttonVariants({ variant, size }), className)}
        ref={ref}
        {...props}
      >
        {leftIcon && (
          <span className="inline-flex items-center">{leftIcon}</span>
        )}
        {children}
        {rightIcon && (
          <span className="inline-flex items-center">{rightIcon}</span>
        )}
      </Comp>
    );
  }
);
Button.displayName = 'Button';

export { Button, buttonVariants };
