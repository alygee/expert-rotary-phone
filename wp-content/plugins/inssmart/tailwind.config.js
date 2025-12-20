/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ["class"],
  content: [
    './pages/**/*.{ts,tsx}',
    './components/**/*.{ts,tsx}',
    './app/**/*.{ts,tsx}',
    './src/**/*.{ts,tsx}',
  ],
  safelist: [
    'text-h1',
    'text-h2',
    'text-h3',
    'text-h4',
    'text-h5',
    'text-h6',
    'text-subtitle1',
    'text-subtitle2',
    'text-body1',
    'text-body2',
    'text-input',
    'text-caption',
    'text-overline',
  ],
  prefix: "",
  theme: {
    screens: {
      'sm': '481px', // Кастомный breakpoint для >480px
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '1536px',
    },
    container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
    extend: {
      fontFamily: {
        sans: ['Jost', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        // Базовые стили для ≤480px (мобильные)
        // H1. Heading - мобильная версия
        'h1': ['76px', {
          lineHeight: '80px',
          letterSpacing: '-1.0px',
          fontWeight: '500',
        }],
        // H2. Heading - мобильная версия
        'h2': ['48px', {
          lineHeight: '56px',
          letterSpacing: '-0.6px',
          fontWeight: '450',
        }],
        // H3. Heading - мобильная версия
        'h3': ['38px', {
          lineHeight: '48px',
          letterSpacing: '-0.2px',
          fontWeight: '500',
        }],
        // H4. Heading - мобильная версия
        'h4': ['28px', {
          lineHeight: '40px',
          letterSpacing: '0.0px',
          fontWeight: '450',
        }],
        // H5. Heading - мобильная версия
        'h5': ['20px', {
          lineHeight: '24px',
          letterSpacing: '0.4px',
          fontWeight: '450',
        }],
        // H6. Heading - мобильная версия
        'h6': ['18px', {
          lineHeight: '24px',
          letterSpacing: '0.6px',
          fontWeight: '500',
        }],
        // Subtitle 1 - мобильная версия
        'subtitle1': ['16px', {
          lineHeight: '24px',
          letterSpacing: '0.4px',
          fontWeight: '450',
        }],
        // Subtitle 2 - мобильная версия
        'subtitle2': ['14px', {
          lineHeight: '24px',
          letterSpacing: '0.4px',
          fontWeight: '450',
        }],
        // Body 1 - мобильная версия
        'body1': ['16px', {
          lineHeight: '24px',
          letterSpacing: '0.0px',
          fontWeight: '400',
        }],
        // Body 2 - мобильная версия
        'body2': ['14px', {
          lineHeight: '16px',
          letterSpacing: '0.0px',
          fontWeight: '400',
        }],
        // Input - одинаковый для всех экранов
        'input': ['16px', {
          lineHeight: '16px',
          letterSpacing: '0.0px',
          fontWeight: '400',
        }],
        // Caption - одинаковый для всех экранов
        'caption': ['12px', {
          lineHeight: '12px',
          letterSpacing: '0.4px',
          fontWeight: '400',
        }],
        // OVERLINE - одинаковый для всех экранов
        'overline': ['13px', {
          lineHeight: '16px',
          letterSpacing: '1.0px',
          fontWeight: '400',
        }],
      },
      fontWeight: {
        normal: '400',
        medium: '450',
        semibold: '500',
      },
      colors: {
        // Primary colors
        primary: {
          dark: "hsl(var(--primary-dark))",
          DEFAULT: "hsl(var(--primary))",
          light: "hsl(var(--primary-light))",
          "4p": "hsl(var(--primary-4p) / 0.04)",
          "8p": "hsl(var(--primary-8p) / 0.08)",
          "12p": "hsl(var(--primary-12p) / 0.12)",
          "20p": "hsl(var(--primary-20p) / 0.20)",
          "30p": "hsl(var(--primary-30p) / 0.30)",
          "50p": "hsl(var(--primary-50p) / 0.50)",
          "80p": "hsl(var(--primary-80p) / 0.80)",
          foreground: "hsl(var(--primary-foreground))",
        },
        // Secondary colors
        secondary: {
          dark: "hsl(var(--secondary-dark))",
          DEFAULT: "hsl(var(--secondary))",
          light: "hsl(var(--secondary-light))",
          "4p": "hsl(var(--secondary-4p) / 0.04)",
          "8p": "hsl(var(--secondary-8p) / 0.08)",
          "12p": "hsl(var(--secondary-12p) / 0.12)",
          "20p": "hsl(var(--secondary-20p) / 0.20)",
          "30p": "hsl(var(--secondary-30p) / 0.30)",
          "50p": "hsl(var(--secondary-50p) / 0.50)",
          "80p": "hsl(var(--secondary-80p) / 0.80)",
          foreground: "hsl(var(--secondary-foreground))",
        },
        // Grey colors
        grey: {
          900: "hsl(var(--grey-900))",
          800: "hsl(var(--grey-800))",
          700: "hsl(var(--grey-700))",
          600: "hsl(var(--grey-600))",
          500: "hsl(var(--grey-500))",
          400: "hsl(var(--grey-400))",
          300: "hsl(var(--grey-300))",
          200: "hsl(var(--grey-200))",
          100: "hsl(var(--grey-100))",
          50: "hsl(var(--grey-50))",
        },
        // Error colors
        error: {
          dark: "hsl(var(--error-dark))",
          DEFAULT: "hsl(var(--error))",
          light: "hsl(var(--error-light))",
          "4p": "hsl(var(--error-4p) / 0.04)",
          "8p": "hsl(var(--error-8p) / 0.08)",
          "12p": "hsl(var(--error-12p) / 0.12)",
          "20p": "hsl(var(--error-20p) / 0.20)",
          "30p": "hsl(var(--error-30p) / 0.30)",
          "50p": "hsl(var(--error-50p) / 0.50)",
          "80p": "hsl(var(--error-80p) / 0.80)",
        },
        // Warning colors
        warning: {
          dark: "hsl(var(--warning-dark))",
          DEFAULT: "hsl(var(--warning))",
          light: "hsl(var(--warning-light))",
          "4p": "hsl(var(--warning-4p) / 0.04)",
          "8p": "hsl(var(--warning-8p) / 0.08)",
          "12p": "hsl(var(--warning-12p) / 0.12)",
          "20p": "hsl(var(--warning-20p) / 0.20)",
          "30p": "hsl(var(--warning-30p) / 0.30)",
          "50p": "hsl(var(--warning-50p) / 0.50)",
          "80p": "hsl(var(--warning-80p) / 0.80)",
        },
        // Success colors
        success: {
          dark: "hsl(var(--success-dark))",
          DEFAULT: "hsl(var(--success))",
          light: "hsl(var(--success-light))",
          "4p": "hsl(var(--success-4p) / 0.04)",
          "8p": "hsl(var(--success-8p) / 0.08)",
          "12p": "hsl(var(--success-12p) / 0.12)",
          "20p": "hsl(var(--success-20p) / 0.20)",
          "30p": "hsl(var(--success-30p) / 0.30)",
          "50p": "hsl(var(--success-50p) / 0.50)",
          "80p": "hsl(var(--success-80p) / 0.80)",
        },
        // Background
        background: {
          paper: "hsl(var(--background-paper))",
          DEFAULT: "hsl(var(--background-default))",
        },
        // Actions
        action: {
          primary: "hsl(var(--action-primary) / 0.54)",
          hover: "hsl(var(--action-hover) / 0.04)",
          selected: "hsl(var(--action-selected) / 0.08)",
          disabled: "hsl(var(--action-disabled) / 0.26)",
          "disabled-bg": "hsl(var(--action-disabled-bg) / 0.12)",
          focus: "hsl(var(--action-focus) / 0.12)",
        },
        // Text
        text: {
          primary: "hsl(var(--text-primary) / 0.87)",
          secondary: "hsl(var(--text-secondary) / 0.60)",
          disabled: "hsl(var(--text-disabled) / 0.38)",
        },
        // Shadcn/ui compatibility
        border: "hsl(var(--border))",
        input: "hsl(var(--input) / 0.87)",
        ring: "hsl(var(--ring))",
        foreground: "hsl(var(--foreground))",
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
      },
      backgroundImage: {
        "morning-light": "radial-gradient(157.77% 168.58% at 0% 2.5%, #EBE7F8 0%, #FDDAD8 100%)",
      },
      boxShadow: {
        "elevation-1": "0px 2px 8px 0px rgba(0, 0, 0, 0.05), 0px 0px 2px 0px rgba(0, 0, 0, 0.03)",
        "elevation-2": "0px 4px 16px 0px rgba(0, 0, 0, 0.05), 0px 0px 3px 0px rgba(0, 0, 0, 0.03)",
        "elevation-3": "0px 6px 20px 0px rgba(0, 0, 0, 0.06), 0px 0px 6px -1px rgba(0, 0, 0, 0.05)",
        "elevation-4": "0px 6px 32px 0px rgba(0, 0, 0, 0.08), 0px 0px 8px -2px rgba(0, 0, 0, 0.05)",
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
      keyframes: {
        "accordion-down": {
          from: { height: "0" },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: "0" },
        },
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
      },
    },
  },
  plugins: [require("tailwindcss-animate")],
}

