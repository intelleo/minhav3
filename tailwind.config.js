// tailwind.config.js
module.exports = {
  content: [
    "./app/Views/**/*.{php,html}",
    "./public/css/**/*.{css}", // ← penting: scan file CSS kamu
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
