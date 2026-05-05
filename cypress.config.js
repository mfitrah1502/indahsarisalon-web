import { defineConfig } from "cypress";

export default defineConfig({
  e2e: {
    baseUrl: 'http://127.0.0.1:8000',
    // Mengarahkan Cypress untuk mencari file tes di folder User Anda
    specPattern: 'C:/Users/Hype/cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
    excludeSpecPattern: ['**/node_modules/**', '**/package-lock.json'],
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
    supportFile: false,
  },
  numTestsKeptInMemory: 0,
  video: false,
  screenshotOnRunFailure: false,
});
