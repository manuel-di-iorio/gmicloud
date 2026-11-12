/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './includes/**/*.php',
    './pages/**/*.php',
    './assets/ui-kit/**/*.php',
    './assets/js/**/*.js',
    './*.php',
    './api/**/*.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        page: 'var(--ds-color-app-bg)',
        'page-light': 'var(--ds-color-primary-light)',
        surface: {
          DEFAULT: 'var(--ds-color-surface)',
          solid: 'var(--ds-color-surface-solid)',
          muted: 'var(--ds-color-surface-muted)',
          elevated: 'var(--ds-color-surface-elevated)',
        },
        content: {
          DEFAULT: 'var(--ds-color-content)',
          muted: 'var(--ds-color-content-muted)',
        },
        secondary: 'var(--ds-color-secondary)',
        accent: {
          DEFAULT: 'var(--ds-color-accent)',
          warm: 'var(--ds-color-accent-warm)',
          cool: 'var(--ds-color-accent-cool)',
        },
        success: 'var(--ds-color-success)',
        'success-light': 'var(--ds-color-success-light)',
        error: 'var(--ds-color-error)',
        warning: 'var(--ds-color-warning)',
        info: 'var(--ds-color-secondary)',
        gold: 'var(--ds-color-gold)',
        'gold-light': 'var(--ds-color-gold-light)',
        silver: 'var(--ds-color-silver)',
        bronze: 'var(--ds-color-bronze)',
        discord: 'var(--ds-color-discord)',
        night: {
          600: 'var(--ds-color-night-600)',
          700: 'var(--ds-color-night-700)',
          800: 'var(--ds-color-night-800)',
          900: 'var(--ds-color-night-900)',
        },
        DEFAULT: 'var(--ds-color-border)',
        default: 'var(--ds-color-border)',

      },
      borderRadius: {
        card: 'var(--ds-radius-card)',
        'lg-card': 'var(--ds-radius-lg)',
        'xl-card': 'var(--ds-radius-xl)',
        cta: 'var(--ds-radius-cta)',
        sm: 'var(--ds-radius-sm)',
      },
      maxWidth: {
        '7xl': '70rem',
      },
      fontFamily: {
        sans: ['ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', '"Noto Sans"', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"'],
      },
      boxShadow: {
        card: 'none',
        'card-lg': 'none',
        navbar: 'none',
        'card-prominent': 'none',
        'card-subtle': 'none',
        footer: 'none',
      },
      keyframes: {
        skeletonShimmer: {
          from: { backgroundPosition: '-200% 0' },
          to: { backgroundPosition: '200% 0' },
        },
        modalIn: {
          from: { opacity: '0', transform: 'translateY(8px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        confirmPulse: {
          '0%, 100%': { transform: 'scale(1)' },
          '50%': { transform: 'scale(1.03)' },
        },
        tabsIn: {
          from: { opacity: '0', transform: 'translateY(6px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        'skeleton-shimmer': 'skeletonShimmer 1.8s ease-in-out infinite',
        'modal-in': 'modalIn .25s cubic-bezier(.16,1,.3,1)',
        'confirm-pulse': 'confirmPulse 1s ease-in-out infinite',
        'tabs-in': 'tabsIn 0.25s ease',
      },
    },
  },
  plugins: [require('@tailwindcss/typography')],
};
