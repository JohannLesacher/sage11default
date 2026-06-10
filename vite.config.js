import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'
import {wordpressPlugin} from '@roots/vite-plugin';
import {globSync} from 'glob';

// Set APP_URL if it doesn't exist for Laravel Vite plugin
if (!process.env.APP_URL) {
  process.env.APP_URL = 'https://sane.test';
}

export default defineConfig({
  base: '/app/themes/taste/public/build/',
  plugins: [
    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js',
        'resources/css/editor.scss',
        'resources/js/editor.js',
        // Scan dynamique des blocs
        ...globSync('resources/{css,js}/blocks/*.{scss,js}'),
      ],
      refresh: true,
      assets: ['resources/images/**', 'resources/fonts/**'],
    }),
    wordpressPlugin(),
  ],
  esbuild: {
    jsx: 'transform',
    jsxFactory: 'wp.element.createElement',
    jsxFragment: 'wp.element.Fragment',
  },
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
})
