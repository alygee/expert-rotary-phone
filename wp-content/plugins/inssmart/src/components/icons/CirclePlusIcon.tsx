import * as React from "react";

interface CirclePlusIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number;
  color?: string;
}

export const CirclePlusIcon = React.forwardRef<
  SVGSVGElement,
  CirclePlusIconProps
>(({ size = 17, color = "#00C853", ...props }, ref) => {
  return (
    <svg
      ref={ref}
      width={size}
      height={size}
      viewBox="0 0 17 17"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      {...props}
    >
      <path
        d="M8.33333 0C3.73333 0 0 3.73333 0 8.33333C0 12.9333 3.73333 16.6667 8.33333 16.6667C12.9333 16.6667 16.6667 12.9333 16.6667 8.33333C16.6667 3.73333 12.9333 0 8.33333 0ZM8.33333 15C4.65833 15 1.66667 12.0083 1.66667 8.33333C1.66667 4.65833 4.65833 1.66667 8.33333 1.66667C12.0083 1.66667 15 4.65833 15 8.33333C15 12.0083 12.0083 15 8.33333 15ZM11.5667 5.24167L6.66667 10.1417L5.1 8.575C4.775 8.25 4.25 8.25 3.925 8.575C3.6 8.9 3.6 9.425 3.925 9.75L6.08333 11.9083C6.40833 12.2333 6.93333 12.2333 7.25833 11.9083L12.75 6.41667C13.075 6.09167 13.075 5.56667 12.75 5.24167C12.425 4.91667 11.8917 4.91667 11.5667 5.24167Z"
        fill={color}
      />
    </svg>
  );
});

CirclePlusIcon.displayName = "CirclePlusIcon";


