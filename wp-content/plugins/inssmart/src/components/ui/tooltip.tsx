import * as React from 'react';
import { createPortal } from 'react-dom';
import { cn } from '@/lib/utils';

interface TooltipContextValue {
  open: boolean;
  setOpen: (open: boolean) => void;
  triggerRef: React.MutableRefObject<HTMLElement | null>;
  contentId: string;
  isHovered: boolean;
  setIsHovered: (hovered: boolean) => void;
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
  const [isHovered, setIsHovered] = React.useState(false);
  const triggerRef = React.useRef<HTMLElement | null>(null);
  const contentId = React.useId();

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

  const contextValue = React.useMemo<TooltipContextValue>(
    () => ({
      open,
      setOpen,
      triggerRef,
      contentId,
      isHovered,
      setIsHovered,
    }),
    [open, setOpen, contentId, isHovered]
  );

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

  // Сброс состояния наведения при закрытии tooltip
  React.useEffect(() => {
    if (!open) {
      setIsHovered(false);
    }
  }, [open]);

  React.useEffect(() => {
    if (!open || isHovered) return;

    const timeoutId = setTimeout(() => {
      setOpen(false);
    }, 2000);

    return () => {
      clearTimeout(timeoutId);
    };
  }, [open, isHovered, setOpen]);

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
    const { open, setOpen, triggerRef } = useTooltipContext();

    const handleClick = (e: React.MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      // Переключаем состояние tooltip
      const newOpenState = !open;
      setOpen(newOpenState);
      onClick?.(e);
    };

    if (asChild && React.isValidElement(children)) {
      const childElement = children as React.ReactElement & {
        ref?: React.Ref<HTMLElement>;
      };
      const childRef = childElement.ref;
      return React.cloneElement(children, {
        ref: (node: HTMLElement | null) => {
          triggerRef.current = node;
          if (typeof ref === 'function') {
            ref(node as HTMLButtonElement);
          } else if (ref) {
            (ref as React.MutableRefObject<HTMLButtonElement | null>).current =
              node as HTMLButtonElement;
          }
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
        ref={(node) => {
          triggerRef.current = node;
          if (typeof ref === 'function') {
            ref(node);
          } else if (ref) {
            (ref as React.MutableRefObject<HTMLButtonElement | null>).current =
              node;
          }
        }}
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
  side?: 'top' | 'bottom' | 'left' | 'right';
  sideOffset?: number;
  align?: 'start' | 'center' | 'end';
  alignOffset?: number;
}

const TooltipContent = React.forwardRef<HTMLDivElement, TooltipContentProps>(
  (
    {
      className,
      side = 'top',
      sideOffset = 4,
      align = 'center',
      alignOffset = 0,
      children,
      ...props
    },
    ref
  ) => {
    const { open, triggerRef, contentId, setIsHovered } = useTooltipContext();
    const contentRef = React.useRef<HTMLDivElement | null>(null);
    const [position, setPosition] = React.useState<{
      top: number;
      left: number;
    } | null>(null);
    // Вычисляем позицию стрелки относительно триггера
    const [arrowPosition, setArrowPosition] = React.useState<{
      left?: number;
      top?: number;
    }>({});

    // Позиционирование tooltip
    React.useEffect(() => {
      if (!open || !triggerRef.current) {
        setPosition(null);
        return;
      }

      const updatePosition = () => {
        const trigger = triggerRef.current;
        const content = contentRef.current;
        if (!trigger) return;

        // Если content еще не в DOM, устанавливаем временную позицию
        if (!content) {
          const triggerRect = trigger.getBoundingClientRect();
          const scrollX = window.scrollX || window.pageXOffset;
          const scrollY = window.scrollY || window.pageYOffset;

          let top = 0;
          let left = 0;

          switch (side) {
            case 'top':
              top = triggerRect.top + scrollY - 100 - sideOffset; // Временная высота
              break;
            case 'bottom':
              top = triggerRect.bottom + scrollY + sideOffset;
              break;
            case 'left':
              top = triggerRect.top + scrollY;
              left = triggerRect.left + scrollX - 200 - sideOffset; // Временная ширина
              break;
            case 'right':
              top = triggerRect.top + scrollY;
              left = triggerRect.right + scrollX + sideOffset;
              break;
          }

          switch (align) {
            case 'start':
              if (side === 'top' || side === 'bottom') {
                left = triggerRect.left + scrollX + alignOffset;
              }
              break;
            case 'center':
              if (side === 'top' || side === 'bottom') {
                left =
                  triggerRect.left +
                  scrollX +
                  triggerRect.width / 2 -
                  100 +
                  alignOffset; // Временная ширина / 2
              }
              break;
            case 'end':
              if (side === 'top' || side === 'bottom') {
                left = triggerRect.right + scrollX - 200 + alignOffset; // Временная ширина
              }
              break;
          }

          setPosition({ top, left });
          return;
        }

        const triggerRect = trigger.getBoundingClientRect();
        const contentRect = content.getBoundingClientRect();
        const scrollX = window.scrollX || window.pageXOffset;
        const scrollY = window.scrollY || window.pageYOffset;

        let top = 0;
        let left = 0;

        switch (side) {
          case 'top':
            top = triggerRect.top + scrollY - contentRect.height - sideOffset;
            break;
          case 'bottom':
            top = triggerRect.bottom + scrollY + sideOffset;
            break;
          case 'left':
            top =
              triggerRect.top +
              scrollY +
              triggerRect.height / 2 -
              contentRect.height / 2;
            left = triggerRect.left + scrollX - contentRect.width - sideOffset;
            break;
          case 'right':
            top =
              triggerRect.top +
              scrollY +
              triggerRect.height / 2 -
              contentRect.height / 2;
            left = triggerRect.right + scrollX + sideOffset;
            break;
        }

        switch (align) {
          case 'start':
            if (side === 'top' || side === 'bottom') {
              left = triggerRect.left + scrollX + alignOffset;
            } else {
              left = triggerRect.left + scrollX + alignOffset;
            }
            break;
          case 'center':
            if (side === 'top' || side === 'bottom') {
              left =
                triggerRect.left +
                scrollX +
                triggerRect.width / 2 -
                contentRect.width / 2 +
                alignOffset;
            }
            break;
          case 'end':
            if (side === 'top' || side === 'bottom') {
              left =
                triggerRect.right + scrollX - contentRect.width + alignOffset;
            } else {
              left = triggerRect.right + scrollX + alignOffset;
            }
            break;
        }

        // Проверка границ экрана
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        if (left < 0) {
          left = 8;
        } else if (left + contentRect.width > viewportWidth) {
          left = viewportWidth - contentRect.width - 8;
        }

        if (top < scrollY) {
          top = scrollY + 8;
        } else if (top + contentRect.height > scrollY + viewportHeight) {
          top = scrollY + viewportHeight - contentRect.height - 8;
        }

        setPosition({ top, left });
      };

      // Устанавливаем начальную позицию сразу
      updatePosition();

      // Обновляем позицию после того, как элемент будет в DOM
      const rafId = requestAnimationFrame(() => {
        updatePosition();
      });

      const handleResize = () => updatePosition();
      const handleScroll = () => updatePosition();

      window.addEventListener('resize', handleResize);
      window.addEventListener('scroll', handleScroll, true);

      return () => {
        cancelAnimationFrame(rafId);
        window.removeEventListener('resize', handleResize);
        window.removeEventListener('scroll', handleScroll, true);
      };
    }, [open, side, sideOffset, align, alignOffset, triggerRef]);

    // Вычисляем позицию стрелки относительно триггера
    React.useEffect(() => {
      if (!open || !position || !triggerRef.current || !contentRef.current) {
        setArrowPosition({});
        return;
      }

      const trigger = triggerRef.current;
      const content = contentRef.current;
      const triggerRect = trigger.getBoundingClientRect();
      const contentRect = content.getBoundingClientRect();

      let arrowLeft: number | null = null;
      let arrowTop: number | null = null;

      switch (side) {
        case 'top':
          // Стрелка внизу tooltip, указывает вниз на триггер
          arrowTop = contentRect.height;
          // Позиция стрелки относительно левого края content
          arrowLeft =
            triggerRect.left + triggerRect.width / 2 - contentRect.left;
          break;
        case 'bottom':
          // Стрелка вверху tooltip, указывает вверх на триггер
          arrowTop = -8;
          arrowLeft =
            triggerRect.left + triggerRect.width / 2 - contentRect.left;
          break;
        case 'left':
          // Стрелка справа tooltip, указывает вправо на триггер
          arrowLeft = contentRect.width;
          arrowTop =
            triggerRect.top + triggerRect.height / 2 - contentRect.top - 8;
          break;
        case 'right':
          // Стрелка слева tooltip, указывает влево на триггер
          arrowLeft = -8;
          arrowTop =
            triggerRect.top + triggerRect.height / 2 - contentRect.top - 8;
          break;
      }

      setArrowPosition({
        left: arrowLeft !== null ? arrowLeft : undefined,
        top: arrowTop !== null ? arrowTop : undefined,
      });
    }, [open, position, side, triggerRef]);

    // Если позиция еще не установлена, используем начальную позицию
    const initialPosition = position || { top: 0, left: 0 };

    if (!open) {
      return null;
    }

    const content = (
      <div
        ref={(node) => {
          contentRef.current = node;
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
          top: `${initialPosition.top}px`,
          left: `${initialPosition.left}px`,
          visibility: position ? 'visible' : 'hidden',
        }}
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
        {...props}
      >
        {children}
        {/* Треугольник-стрелка */}
        {position && arrowPosition.left !== undefined && (
          <div
            className={cn(
              'absolute w-0 h-0 border-solid',
              side === 'top' &&
                'border-t-[8px] border-t-popover border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent',
              side === 'bottom' &&
                'border-b-[8px] border-b-popover border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent',
              side === 'left' &&
                'border-l-[8px] border-l-popover border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent',
              side === 'right' &&
                'border-r-[8px] border-r-popover border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent'
            )}
            style={{
              left: `${arrowPosition.left}px`,
              ...(arrowPosition.top !== undefined && {
                top: `${arrowPosition.top}px`,
              }),
              ...(side === 'top' || side === 'bottom'
                ? { transform: 'translateX(-50%)' }
                : { transform: 'translateY(-50%)' }),
            }}
          />
        )}
      </div>
    );

    return typeof document !== 'undefined'
      ? createPortal(content, document.body)
      : null;
  }
);
TooltipContent.displayName = 'TooltipContent';

export { Tooltip, TooltipTrigger, TooltipContent, TooltipProvider };
