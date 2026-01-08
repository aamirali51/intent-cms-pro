/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.{php,html,twig}",
    "./public/**/*.php",
    "./public/**/*.html",
    "./app/**/*.php",
  ],
  darkMode: "class",
  // Safelist ensures these classes are ALWAYS included, even if not found in content
  safelist: [
    // Common background colors that might be dynamically generated
    'bg-red-100', 'bg-red-500', 'bg-red-600', 'bg-red-700', 'bg-red-900',
    'bg-green-100', 'bg-green-500', 'bg-green-600', 'bg-green-700', 'bg-green-900',
    'bg-blue-100', 'bg-blue-500', 'bg-blue-600', 'bg-blue-700', 'bg-blue-900',
    'bg-yellow-100', 'bg-yellow-500', 'bg-yellow-600', 'bg-yellow-700', 'bg-yellow-900',
    'bg-purple-100', 'bg-purple-500', 'bg-purple-600', 'bg-purple-700', 'bg-purple-900',
    'bg-orange-100', 'bg-orange-500', 'bg-orange-600', 'bg-orange-900',
    'bg-indigo-100', 'bg-indigo-500', 'bg-indigo-600', 'bg-indigo-900',
    'bg-teal-100', 'bg-teal-500', 'bg-teal-600', 'bg-teal-900',
    'bg-slate-100', 'bg-slate-500', 'bg-slate-600', 'bg-slate-900',
    'bg-gray-100', 'bg-gray-500', 'bg-gray-600', 'bg-gray-700', 'bg-gray-800', 'bg-gray-900',
    // Text colors
    'text-red-100', 'text-red-400', 'text-red-500', 'text-red-600', 'text-red-700', 'text-red-800',
    'text-green-100', 'text-green-400', 'text-green-500', 'text-green-600', 'text-green-700', 'text-green-800',
    'text-blue-100', 'text-blue-400', 'text-blue-500', 'text-blue-600', 'text-blue-700', 'text-blue-800',
    'text-yellow-100', 'text-yellow-400', 'text-yellow-500', 'text-yellow-600', 'text-yellow-700', 'text-yellow-800',
    'text-purple-100', 'text-purple-400', 'text-purple-500', 'text-purple-600', 'text-purple-700', 'text-purple-800',
    'text-orange-100', 'text-orange-400', 'text-orange-500', 'text-orange-600', 'text-orange-700',
    'text-indigo-100', 'text-indigo-400', 'text-indigo-500', 'text-indigo-600',
    'text-teal-100', 'text-teal-400', 'text-teal-500', 'text-teal-600',
    'text-white', 'text-gray-400', 'text-gray-500', 'text-gray-600', 'text-gray-700', 'text-gray-800', 'text-gray-900',
    // Dark mode variants
    'dark:bg-red-900', 'dark:bg-red-900/30', 'dark:text-red-300', 'dark:text-red-400',
    'dark:bg-green-900', 'dark:bg-green-900/30', 'dark:text-green-300', 'dark:text-green-400',
    'dark:bg-blue-900', 'dark:bg-blue-900/30', 'dark:text-blue-300', 'dark:text-blue-400',
    'dark:bg-yellow-900', 'dark:bg-yellow-900/30', 'dark:text-yellow-300', 'dark:text-yellow-400',
    'dark:bg-purple-900', 'dark:bg-purple-900/30', 'dark:text-purple-300', 'dark:text-purple-400',
    'dark:bg-gray-700', 'dark:bg-gray-800', 'dark:bg-gray-900',
    'dark:text-gray-300', 'dark:text-gray-400', 'dark:text-white',
    // Ring colors
    'ring-red-300', 'ring-green-300', 'ring-blue-300', 'ring-gray-300',
    'dark:ring-red-600', 'dark:ring-green-600', 'dark:ring-blue-600', 'dark:ring-gray-600',
    // Hover states
    'hover:bg-red-500', 'hover:bg-red-700', 'hover:text-red-500',
    'hover:bg-green-500', 'hover:bg-green-700',
    'hover:bg-blue-500', 'hover:bg-blue-700',
    'hover:bg-gray-50', 'hover:bg-gray-100', 'dark:hover:bg-gray-600', 'dark:hover:bg-gray-700',
  ],
  theme: {
    extend: {
      colors: {
        primary: "#8b5cf6", // Violet-500 (matches admin panel)
        primaryHover: "#7c3aed", // Violet-600
        "background-light": "#f8fafc", // Slate-50
        "background-dark": "#0f172a", // Slate-900
        "surface-light": "#ffffff",
        "surface-dark": "#1e293b", // Slate-800
        "border-light": "#e2e8f0", // Slate-200
        "border-dark": "#334155", // Slate-700
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
