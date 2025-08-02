/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './app/**/*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
 plugins: [
    require('@tailwindcss/typography'),
],
}
