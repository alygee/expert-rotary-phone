import * as React from "react"

interface ArrowDownIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number
  color?: string
}

export const ArrowDownIcon = React.forwardRef<SVGSVGElement, ArrowDownIconProps>(
  ({ size = 8, color = "rgba(0, 0, 0, 0.6)", ...props }, ref) => {
    return (
      <svg
        ref={ref}
        width={size}
        height={Math.round(size * (5 / 8))}
        viewBox="0 0 8 5"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
      >
        <path
          d="M0.296477 1.71L2.88648 4.3C3.27648 4.69 3.90648 4.69 4.29648 4.3L6.88648 1.71C7.51648 1.08 7.06648 0 6.17648 0H0.996477C0.106477 0 -0.333523 1.08 0.296477 1.71Z"
          fill={color}
        />
      </svg>
    )
  }
)

ArrowDownIcon.displayName = "ArrowDownIcon"

