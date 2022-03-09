module.exports = {
  content: ["./templates/**/*.twig", "./vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig"],
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
