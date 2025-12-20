import * as React from "react";

interface MailOutlineIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number;
  color?: string;
}

export const MailOutlineIcon = React.forwardRef<SVGSVGElement, MailOutlineIconProps>(
  ({ size = 20, color = "#4DA4E3", ...props }, ref) => {
    return (
      <svg
        ref={ref}
        width={size}
        height={Math.round(size * (16 / 20))}
        viewBox="0 0 20 16"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
      >
        <path
          d="M18 0L2 0C0.9 0 0.00999999 0.9 0.00999999 2L0 14C0 15.1 0.9 16 2 16L18 16C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM17 14L3 14C2.45 14 2 13.55 2 13L2 4L8.94 8.34C9.59 8.75 10.41 8.75 11.06 8.34L18 4V13C18 13.55 17.55 14 17 14ZM10 7L2 2L18 2L10 7Z"
          fill={color}
        />
      </svg>
    );
  },
);

MailOutlineIcon.displayName = "MailOutlineIcon";

