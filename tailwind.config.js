/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
       fontFamily: {
        geist: ['Geist', 'sans-serif'],
        wordBreak: {
        'auto-phrase': 'auto-phrase',
      }
      },
      colors: {
        'pool-gray': {
          50: '#FAFBFC',   // Input background
          100: '#EEEEEE', // Light border
          200: '#ECECEC', // Divider
          300: '#DFE1E6', // Input border
          400: '#797979', // Muted text
          500: '#5A5A5A', // Body text
          600: '#4C4C4C', // Button/Dark button
          700: '#353535', // Dark text
        },
        'pool-warning': {
          bg: '#D9D9D9',
          'icon-bg': '#9E9E9E',
          'icon-stroke': '#2C2C2C',
        },
      },
    },
  },
  plugins: [],
} 