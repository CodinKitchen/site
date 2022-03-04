module.exports = {
  content: ["./templates/**/*.twig"],
  theme: {
    extend: {},
    fontFamily: {
      'regular': ["SpaceMono"],
      'bold': ['SpaceMono-Bold'],
      'italic': ['SpaceMono-Italic'],
      'bold-italic': ['SpaceMono-BoldItalic'],
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
