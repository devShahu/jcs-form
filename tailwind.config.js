/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
    "./src/index.html",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          500: '#EF4444',
          600: '#DC2626',
          700: '#B91C1C',
        },
        gray: {
          50: '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#D1D5DB',
          600: '#4B5563',
          900: '#111827',
        },
        success: {
          500: '#10B981',
        },
        error: {
          500: '#EF4444',
        },
        warning: {
          500: '#F59E0B',
        },
      },
      fontFamily: {
        sans: ['Inter', 'Segoe UI', 'system-ui', 'sans-serif'],
        bengali: ['Noto Sans Bengali', 'Nikosh', 'SolaimanLipi', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
