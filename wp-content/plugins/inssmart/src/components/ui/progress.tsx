import * as React from "react"
import { cn } from "@/lib/utils"

export interface ProgressProps {
  value?: number
  totalSteps?: number
  currentStep?: number
  className?: string
}

const Progress = React.forwardRef<HTMLDivElement, ProgressProps>(
  ({ value, totalSteps = 3, currentStep, className, ...props }, ref) => {
    // Если передан currentStep, используем его для определения состояния шагов
    const steps = currentStep !== undefined 
      ? Array.from({ length: totalSteps }, (_, i) => i + 1)
      : []
    
    // Если передан только value, вычисляем currentStep из него
    const computedCurrentStep = currentStep !== undefined 
      ? currentStep 
      : value !== undefined 
        ? Math.ceil((value / 100) * totalSteps)
        : 1

    return (
      <div
        ref={ref}
        className={cn("flex w-full gap-0.5", className)}
        {...props}
      >
        {steps.map((step, index) => {
          const isCompleted = step < computedCurrentStep
          const isCurrent = step === computedCurrentStep
          
          return (
            <React.Fragment key={step}>
              <div
                className={cn(
                  "h-2 flex-1 transition-colors rounded",
                  (isCurrent || isCompleted) && "bg-primary",
                  (!isCurrent && !isCompleted) && "bg-primary-12p"
                )}
              />
              {index < steps.length - 1 && (
                <div className="w-0.5 bg-white" />
              )}
            </React.Fragment>
          )
        })}
      </div>
    )
  }
)
Progress.displayName = "Progress"

export { Progress }


