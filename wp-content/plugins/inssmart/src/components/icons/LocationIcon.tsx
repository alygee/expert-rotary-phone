import * as React from "react";

interface LocationIconProps extends React.SVGProps<SVGSVGElement> {
  size?: number;
  color?: string;
}

export const LocationIcon = React.forwardRef<SVGSVGElement, LocationIconProps>(
  ({ size = 18, color = "#4DA4E3", ...props }, ref) => {
    return (
      <svg
        ref={ref}
        width={size}
        height={Math.round(size * (20 / 16))}
        viewBox="0 0 16 20"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
      >
        <path
          d="M8 6C9.1 6 10 6.9 10 8C10 9.1 9.1 10 8 10C6.9 10 6 9.1 6 8C6 6.9 6.9 6 8 6Z"
          fill={color}
        />
        <path
          fillRule="evenodd"
          clipRule="evenodd"
          d="M8 0C12.2 0 16 3.2202 16 8.2002L15.9932 8.5C15.8447 11.6145 13.3972 15.2545 8.66992 19.4297C8.28992 19.7597 7.71984 19.7597 7.33984 19.4297C2.60293 15.2546 0.155267 11.6144 0.00683594 8.5L0 8.2002C0 3.2202 3.8 0 8 0ZM8 1.5C4.58177 1.5 1.5 4.09453 1.5 8.2002C1.50005 9.40216 1.96521 10.8476 3.0918 12.5703C4.16383 14.2095 5.78787 16.0212 8.00391 18.0088C10.2154 16.0213 11.8381 14.2106 12.9092 12.5713C14.0348 10.8483 14.4999 9.40229 14.5 8.2002C14.5 4.09453 11.4182 1.5 8 1.5Z"
          fill={color}
        />
      </svg>
    );
  },
);

LocationIcon.displayName = "LocationIcon";
