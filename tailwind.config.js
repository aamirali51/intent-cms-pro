/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.php",
    "./public/*.php"
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        primary: "#7c3aed", // Violet-600
        "background-light": "#f8fafc", // Slate-50
        "background-dark": "#0f172a", // Slate-900
        "surface-light": "#ffffff",
        "surface-dark": "#1e293b", // Slate-800
      },
      fontFamily: {
        sans: ["Inter", "sans-serif"],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
