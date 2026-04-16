// @ts-check
import { defineConfig, devices } from '@playwright/test';

/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */

/**
 * @see https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
  testDir: './e2e',
  timeout: 60000,
  /* Run tests in files in parallel */
  fullyParallel: false,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry failed tests */
  retries: process.env.CI ? 1 : 0,
  /* Opt out of parallel tests on CI. */
  workers: process.env.CI ? 1 : undefined,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: [
    ['list', { printSteps: true }],
    ['json', { outputFile: 'artifacts/results.json' }],
    ['junit', { outputFile: 'artifacts/junit.xml' }],
    ['html', { outputFolder: 'artifacts/html-report', open: 'always' }] // Auto-open report after tests complete
  ],
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    baseURL: 'https://poolplat.agilecollab.com/',
    headless: false,
    viewport: { width: 1366, height: 768 },
    trace: 'off',
    screenshot: { mode: 'on', fullPage: true },
    video: 'off',
    navigationTimeout: 45000,
    actionTimeout: 45000,
    ignoreHTTPSErrors: true,
    maxRetries: 2,
    testIsolation: true
  },

  /* Configure projects for major browsers */
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        viewport: { width: 1366, height: 768 },
        launchOptions: {
          args: [
            '--no-sandbox',
            '--disable-web-security',
            '--disable-features=VizDisplayCompositor'
          ]
        }
      },
    },
    {
      name: 'firefox',
      use: {
        ...devices['Desktop Firefox'],
        viewport: { width: 1366, height: 768 },
      },
    },
    {
      name: 'edge',
      use: {
        ...devices['Desktop Edge'],
        viewport: { width: 1366, height: 768 },
        channel: 'msedge',
      },
    }
  ],

  /* Test output directory */
  outputDir: 'artifacts/',

  /* Expect timeout */
  expect: {
    timeout: 45000
  },

  /* Handle test failures */
  maxFailures: 0,
  preserveOutput: 'always',
  quiet: false,
});
