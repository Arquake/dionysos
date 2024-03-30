/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      colors : {
        'winter-hazel': {
          '50': '#faf9f0',
          '100': '#f1efd4',
          '200': '#e1dda6',
          '300': '#d9d18e',
          '400': '#c7b658',
          '500': '#bc9e44',
          '600': '#a68139',
          '700': '#8a6433',
          '800': '#72512e',
          '900': '#5e4329',
          '950': '#352313',
      },
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
