import * as React from "react"
import { cn } from "@/lib/utils"

export interface LoaderProps extends React.SVGProps<SVGSVGElement> {
  size?: number
  className?: string
}

const Loader = React.forwardRef<SVGSVGElement, LoaderProps>(
  ({ size = 34, className, ...props }, ref) => {
    return (
      <svg
        ref={ref}
        width={size}
        height={size}
        viewBox="0 0 34 34"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        className={cn("animate-spin", className)}
        {...props}
      >
        <defs>
          <linearGradient
            id="paint0_linear_48416_7628"
            x1="16.6665"
            y1="33.2402"
            x2="16.6665"
            y2="1.57357"
            gradientUnits="userSpaceOnUse"
          >
            <stop stopColor="#4FA5E3" stopOpacity="0.55" />
            <stop offset="0.5" stopColor="#4FA5E3" stopOpacity="0.460492" />
            <stop offset="1" stopColor="#4FA5E3" stopOpacity="0.05" />
          </linearGradient>
        </defs>
        <path
          d="M14.81 0.010119C15.2494 -0.038504 15.6901 0.0894057 16.0351 0.365709C16.3802 0.642013 16.6014 1.04408 16.65 1.48345C16.6986 1.92283 16.5707 2.36352 16.2944 2.70859C16.0181 3.05366 15.616 3.27483 15.1767 3.32345C11.7961 3.7107 8.69139 5.37563 6.49833 7.97734C4.30527 10.579 3.18975 13.9207 3.38017 17.3181C3.57059 20.7154 5.05256 23.9115 7.52262 26.2518C9.99268 28.5922 13.264 29.8997 16.6667 29.9068V33.2401C7.46167 33.2401 0 25.7801 0 16.5735C0 8.06345 6.41333 0.943452 14.81 0.0117857V0.010119Z"
          fill="#4DA4E3"
        />
        <path
          d="M23.8698 3.30856C24.1227 2.94617 24.5092 2.69906 24.9443 2.62155C25.3793 2.54404 25.8273 2.64248 26.1898 2.89523C28.3964 4.42948 30.1986 6.47515 31.4426 8.85742C32.6865 11.2397 33.3352 13.8877 33.3332 16.5752C33.3332 25.7802 25.8715 33.2419 16.6665 33.2419V29.9086C19.5057 29.909 22.2709 29.003 24.5595 27.3227C26.848 25.6424 28.5405 23.2754 29.3904 20.5664C30.2402 17.8574 30.2032 14.9478 29.2846 12.2613C28.366 9.57483 26.6138 7.25172 24.2832 5.63023C24.1035 5.50499 23.9503 5.34558 23.8323 5.1611C23.7142 4.97661 23.6337 4.77067 23.5953 4.55506C23.557 4.33944 23.5614 4.11837 23.6085 3.90449C23.6556 3.69061 23.7444 3.48811 23.8698 3.30856Z"
          fill="url(#paint0_linear_48416_7628)"
        />
      </svg>
    )
  }
)

Loader.displayName = "Loader"

export { Loader }

