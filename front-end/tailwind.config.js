import PrimeUI from 'tailwindcss-primeui';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  important: '.dock-funnels-root',
  theme: {
    extend: {
      colors: {
        primary: {
          50: 'var(--dock-funnels-color-primary-50)',
          100: 'var(--dock-funnels-color-primary-100)',
          200: 'var(--dock-funnels-color-primary-200)',
          300: 'var(--dock-funnels-color-primary-300)',
          400: 'var(--dock-funnels-color-primary-400)',
          500: 'var(--dock-funnels-color-primary-500)',
          600: 'var(--dock-funnels-color-primary-600)',
          700: 'var(--dock-funnels-color-primary-700)',
          800: 'var(--dock-funnels-color-primary-800)',
          900: 'var(--dock-funnels-color-primary-900)',
          950: 'var(--dock-funnels-color-primary-950)',
        },
        surface: {
          50: 'var(--dock-funnels-color-surface-50)',
          100: 'var(--dock-funnels-color-surface-100)',
          200: 'var(--dock-funnels-color-surface-200)',
          300: 'var(--dock-funnels-color-surface-300)',
          400: 'var(--dock-funnels-color-surface-400)',
          500: 'var(--dock-funnels-color-surface-500)',
          600: 'var(--dock-funnels-color-surface-600)',
          700: 'var(--dock-funnels-color-surface-700)',
          800: 'var(--dock-funnels-color-surface-800)',
          900: 'var(--dock-funnels-color-surface-900)',
          950: 'var(--dock-funnels-color-surface-950)',
        },
      }
    },
  },
}

