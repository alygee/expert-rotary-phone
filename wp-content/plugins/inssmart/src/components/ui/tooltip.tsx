import * as React from 'react';
import { createPortal } from 'react-dom';
import {
  useFloating,
  autoUpdate,
  offset,
  flip,
  shift,
  Placement,
  arrow,
} from '@floating-ui/react-dom';
import { cn } from '@/lib/utils';

interface TooltipContextValue {
  open: boolean;
  setOpen: (open: boolean) => void;
  triggerRef: React.MutableRefObject<HTMLElement | null>;
  contentId: string;
  floating: ReturnType<typeof useFloating>;
  arrowRef: React.MutableRefObject<HTMLDivElement | null>;
  setPlacement: (placement: Placement) => void;
  setSideOffset: (offset: number) => void;
  setAlignOffset: (offset: number) => void;
}

const TooltipContext = React.createContext<TooltipContextValue | null>(null);

const useTooltipContext = () => {
  const context = React.useContext(TooltipContext);
  if (!context) {
    throw new Error('Tooltip components must be used within Tooltip');
  }
  return context;
};

interface TooltipProviderProps {
  children: React.ReactNode;
  delayDuration?: number;
}

const TooltipProvider = ({ children }: TooltipProviderProps) => {
  return <>{children}</>;
};

interface TooltipProps {
  children: React.ReactNode;
  open?: boolean;
  onOpenChange?: (open: boolean) => void;
}

const Tooltip = ({
  children,
  open: controlledOpen,
  onOpenChange,
}: TooltipProps) => {
  const [internalOpen, setInternalOpen] = React.useState(false);
  const triggerRef = React.useRef<HTMLElement | null>(null);
  const arrowRef = React.useRef<HTMLDivElement | null>(null);
  const contentId = React.useId();
  const [placement, setPlacement] = React.useState<Placement>('top');
  const [sideOffset, setSideOffset] = React.useState(8);
  const [alignOffset, setAlignOffset] = React.useState(0);

  const isControlled = controlledOpen !== undefined;
  const open = isControlled ? controlledOpen : internalOpen;
  const setOpen = React.useCallback(
    (newOpen: boolean) => {
      if (isControlled) {
        onOpenChange?.(newOpen);
      } else {
        setInternalOpen(newOpen);
      }
    },
    [isControlled, onOpenChange]
  );

  // Настройка floating-ui для позиционирования
  const middleware = React.useMemo(
    () => [
      offset({ mainAxis: sideOffset, crossAxis: alignOffset }),
      flip(),
      shift({ padding: 8 }),
      arrow({ element: arrowRef }),
    ],
    [sideOffset, alignOffset]
  );

  const floating = useFloating({
    placement,
    middleware,
    whileElementsMounted: autoUpdate,
  });

  // Обновляем позицию при изменении placement, sideOffset или alignOffset
  React.useEffect(() => {
    floating.update();
  }, [placement, sideOffset, alignOffset, floating]);

  // Закрытие при клике вне tooltip
  React.useEffect(() => {
    if (!open) return;

    const handleClickOutside = (event: MouseEvent) => {
      const target = event.target as Node;
      const trigger = triggerRef.current;
      const content = document.getElementById(contentId);

      if (
        trigger &&
        content &&
        !trigger.contains(target) &&
        !content.contains(target)
      ) {
        setOpen(false);
      }
    };

    // Небольшая задержка, чтобы не закрыть сразу после открытия
    const timeoutId = setTimeout(() => {
      document.addEventListener('mousedown', handleClickOutside);
    }, 0);

    return () => {
      clearTimeout(timeoutId);
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [open, setOpen, contentId]);

  // Закрытие при нажатии Escape
  React.useEffect(() => {
    if (!open) return;

    const handleEscape = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        setOpen(false);
      }
    };

    document.addEventListener('keydown', handleEscape);
    return () => {
      document.removeEventListener('keydown', handleEscape);
    };
  }, [open, setOpen]);

  const contextValue = React.useMemo<TooltipContextValue>(
    () => ({
      open,
      setOpen,
      triggerRef,
      contentId,
      floating,
      arrowRef,
      setPlacement,
      setSideOffset,
      setAlignOffset,
    }),
    [open, setOpen, contentId, floating]
  );

  return (
    <TooltipContext.Provider value={contextValue}>
      {children}
    </TooltipContext.Provider>
  );
};

interface TooltipTriggerProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  asChild?: boolean;
  children: React.ReactNode;
}

const TooltipTrigger = React.forwardRef<HTMLButtonElement, TooltipTriggerProps>(
  ({ asChild, children, onClick, ...props }, ref) => {
    const { open, setOpen, triggerRef, floating } = useTooltipContext();

    const handleClick = (e: React.MouseEvent<HTMLButtonElement>) => {
      // Проверяем, что клик произошел именно на trigger элементе или его дочерних элементах
      const target = e.target as HTMLElement;
      const trigger = triggerRef.current;
      
      // Если trigger установлен, проверяем, что клик был на нем или его дочерних элементах
      if (trigger) {
        const isClickOnTrigger = trigger === target || trigger.contains(target);
        if (!isClickOnTrigger) {
          // Клик произошел вне trigger элемента, игнорируем
          return;
        }
      }
      
      e.preventDefault();
      e.stopPropagation();
      // Переключаем состояние tooltip
      const newOpenState = !open;
      setOpen(newOpenState);
      onClick?.(e);
    };

    const mergedRef = React.useCallback(
      (node: HTMLElement | null) => {
        triggerRef.current = node;
        floating.refs.setReference(node);
        if (typeof ref === 'function') {
          ref(node as HTMLButtonElement);
        } else if (ref) {
          (ref as React.MutableRefObject<HTMLButtonElement | null>).current =
            node as HTMLButtonElement;
        }
      },
      [ref, triggerRef, floating.refs]
    );

    if (asChild && React.isValidElement(children)) {
      const childElement = children as React.ReactElement & {
        ref?: React.Ref<HTMLElement>;
      };
      const childRef = childElement.ref;
      return React.cloneElement(children, {
        ref: (node: HTMLElement | null) => {
          mergedRef(node);
          if (typeof childRef === 'function') {
            childRef(node);
          } else if (childRef && 'current' in childRef) {
            (childRef as React.MutableRefObject<HTMLElement | null>).current =
              node;
          }
        },
        onClick: handleClick,
        ...props,
      } as React.HTMLAttributes<HTMLElement>);
    }

    return (
      <button
        ref={mergedRef}
        type="button"
        onClick={handleClick}
        aria-expanded={open}
        {...props}
      >
        {children}
      </button>
    );
  }
);
TooltipTrigger.displayName = 'TooltipTrigger';

interface TooltipContentProps extends React.HTMLAttributes<HTMLDivElement> {
  side?: Placement;
  sideOffset?: number;
  align?: 'start' | 'center' | 'end';
  alignOffset?: number;
}

const TooltipContent = React.forwardRef<HTMLDivElement, TooltipContentProps>(
  (
    {
      className,
      side = 'top',
      sideOffset = 8,
      align = 'center',
      alignOffset = 0,
      children,
      ...props
    },
    ref
  ) => {
    const { open, contentId, floating, arrowRef, setPlacement, setSideOffset, setAlignOffset } =
      useTooltipContext();

    // Определяем placement для floating-ui
    const placement = React.useMemo<Placement>(() => {
      if (align === 'start') {
        return `${side}-start` as Placement;
      }
      if (align === 'end') {
        return `${side}-end` as Placement;
      }
      return side;
    }, [side, align]);

    // Обновляем placement в floating
    React.useEffect(() => {
      setPlacement(placement);
    }, [placement, setPlacement]);

    // Обновляем sideOffset
    React.useEffect(() => {
      setSideOffset(sideOffset);
    }, [sideOffset, setSideOffset]);

    // Обновляем alignOffset
    React.useEffect(() => {
      setAlignOffset(alignOffset);
    }, [alignOffset, setAlignOffset]);

    // Обновляем позицию стрелки
    React.useEffect(() => {
      if (floating.middlewareData.arrow && arrowRef.current) {
        const { x, y } = floating.middlewareData.arrow;
        Object.assign(arrowRef.current.style, {
          left: x != null ? `${x}px` : '',
          top: y != null ? `${y}px` : '',
        });
      }
    }, [floating.middlewareData.arrow, arrowRef]);

    if (!open) {
      return null;
    }

    const { x, y, strategy, placement: actualPlacement } = floating;

    // Определяем направление стрелки на основе фактического placement
    const arrowSide = actualPlacement?.split('-')[0] || side;

    const content = (
      <div
        ref={(node) => {
          floating.refs.setFloating(node);
          if (typeof ref === 'function') {
            ref(node);
          } else if (ref) {
            (ref as React.MutableRefObject<HTMLDivElement | null>).current =
              node;
          }
        }}
        id={contentId}
        role="tooltip"
        className={cn(
          'fixed z-50 overflow-hidden rounded-xl bg-popover p-4 text-sm text-popover-foreground shadow-elevation-4',
          'animate-in fade-in-0 zoom-in-95',
          className
        )}
        style={{
          position: strategy,
          top: y != null ? `${y}px` : '',
          left: x != null ? `${x}px` : '',
        }}
        {...props}
      >
        {children}
        {/* Треугольник-стрелка */}
        <div
          ref={arrowRef}
          className={cn(
            'absolute w-0 h-0 border-solid pointer-events-none',
            arrowSide === 'top' &&
              'border-t-[8px] border-t-popover border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent bottom-[-8px]',
            arrowSide === 'bottom' &&
              'border-b-[8px] border-b-popover border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent top-[-8px]',
            arrowSide === 'left' &&
              'border-l-[8px] border-l-popover border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent right-[-8px]',
            arrowSide === 'right' &&
              'border-r-[8px] border-r-popover border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent left-[-8px]'
          )}
          style={{
            ...(arrowSide === 'top' || arrowSide === 'bottom'
              ? { transform: 'translateX(-50%)' }
              : { transform: 'translateY(-50%)' }),
          }}
        />
      </div>
    );

    return typeof document !== 'undefined'
      ? createPortal(content, document.body)
      : null;
  }
);
TooltipContent.displayName = 'TooltipContent';

export { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider };
