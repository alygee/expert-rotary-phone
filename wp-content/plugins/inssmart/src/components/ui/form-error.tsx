import * as React from "react"
import { cn } from "@/lib/utils"

export interface FormErrorProps extends React.HTMLAttributes<HTMLParagraphElement> {}

const FormError = React.forwardRef<HTMLParagraphElement, FormErrorProps>(
  ({ className, ...props }, ref) => {
    return (
      <p
        ref={ref}
        className={cn("text-caption text-error ml-5 -mt-2", className)}
        {...props}
      />
    )
  }
)
FormError.displayName = "FormError"

export { FormError }

