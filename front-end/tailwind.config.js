import PrimeUI from 'tailwindcss-primeui';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{vue,js,ts,jsx,tsx}",
    "./index.html",
    "./form.html",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    PrimeUI
  ],
}

