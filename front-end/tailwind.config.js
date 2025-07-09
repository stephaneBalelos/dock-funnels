import PrimeUI from 'tailwindcss-primeui';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  important: '.dock-funnels-root',
  theme: {
    extend: {},
  },
  plugins: [
    PrimeUI
  ],
}

