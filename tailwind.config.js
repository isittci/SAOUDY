/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        colors: {
                primarygreen: "#05fa3a",
                primaryyellow: "#fbe905",
                primaryorange: "#ff8d28",
                
            },
    },
  },
  plugins: [],
}
