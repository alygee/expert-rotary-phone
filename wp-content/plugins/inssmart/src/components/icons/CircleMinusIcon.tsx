import * as React from "react";

interface CircleMinusIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number;
  color?: string;
}

export const CircleMinusIcon = React.forwardRef<
  SVGSVGElement,
  CircleMinusIconProps
>(({ size = 17, color = "#E95D62", ...props }, ref) => {
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
        d="M4.16667 8.33333C4.16667 8.79167 4.54167 9.16667 5 9.16667H11.6667C12.125 9.16667 12.5 8.79167 12.5 8.33333C12.5 7.875 12.125 7.5 11.6667 7.5H5C4.54167 7.5 4.16667 7.875 4.16667 8.33333ZM8.33333 0C3.73333 0 0 3.73333 0 8.33333C0 12.9333 3.73333 16.6667 8.33333 16.6667C12.9333 16.6667 16.6667 12.9333 16.6667 8.33333C16.6667 3.73333 12.9333 0 8.33333 0ZM8.33333 15C4.65833 15 1.66667 12.0083 1.66667 8.33333C1.66667 4.65833 4.65833 1.66667 8.33333 1.66667C12.0083 1.66667 15 4.65833 15 8.33333C15 12.0083 12.0083 15 8.33333 15Z"
        fill={color}
      />
    </svg>
  );
});

CircleMinusIcon.displayName = "CircleMinusIcon";


