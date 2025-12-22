import * as React from "react";

interface ContactIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number;
  color?: string;
}

export const ContactIcon = React.forwardRef<SVGSVGElement, ContactIconProps>(
  ({ size = 22, color = "#4DA4E3", ...props }, ref) => {
    return (
      <svg
        ref={ref}
        width={size}
        height={Math.round(size * (15 / 22))}
        viewBox="0 0 22 15"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
      >
        <path
          d="M8 7.5C9.26071 7.5 10.2857 6.49102 10.2857 5.25C10.2857 4.00898 9.26071 3 8 3C6.73929 3 5.71429 4.00898 5.71429 5.25C5.71429 6.49102 6.73929 7.5 8 7.5ZM4.8 12L11.2 12C11.6429 12 12 11.6977 12 11.325V10.65C12 9.53203 10.925 8.625 9.6 8.625C9.21429 8.625 8.93214 8.90625 8 8.90625C7.03929 8.90625 6.80714 8.625 6.4 8.625C5.075 8.625 4 9.53203 4 10.65V11.325C4 11.6977 4.35714 12 4.8 12Z"
          fill={color}
        />
        <path
          fillRule="evenodd"
          clipRule="evenodd"
          d="M20 1.5L2 1.5C1.72386 1.5 1.5 1.72386 1.5 2L1.5 13C1.5 13.2761 1.72386 13.5 2 13.5L20 13.5C20.2761 13.5 20.5 13.2761 20.5 13V2C20.5 1.72386 20.2761 1.5 20 1.5ZM2 0C0.895431 0 0 0.89543 0 2L0 13C0 14.1046 0.89543 15 2 15L20 15C21.1046 15 22 14.1046 22 13V2C22 0.895431 21.1046 0 20 0L2 0Z"
          fill={color}
        />
        <path
          d="M13.0142 5.5C13.0142 5.22386 13.238 5 13.5142 5L18.5 5C18.7761 5 19 5.22386 19 5.5C19 5.77614 18.7761 6 18.5 6L13.5142 6C13.238 6 13.0142 5.77614 13.0142 5.5Z"
          fill={color}
        />
        <path
          d="M13.0142 7.5C13.0142 7.22386 13.238 7 13.5142 7L18.5 7C18.7761 7 19 7.22386 19 7.5C19 7.77614 18.7761 8 18.5 8H13.5142C13.238 8 13.0142 7.77614 13.0142 7.5Z"
          fill={color}
        />
        <path
          d="M13.0142 9.5C13.0142 9.22386 13.238 9 13.5142 9H18.5C18.7761 9 19 9.22386 19 9.5C19 9.77614 18.7761 10 18.5 10H13.5142C13.238 10 13.0142 9.77614 13.0142 9.5Z"
          fill={color}
        />
      </svg>
    );
  },
);

ContactIcon.displayName = "ContactIcon";
