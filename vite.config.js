import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { wordpressPlugin } from '@roots/vite-plugin';
import { globSync } from 'glob';
import { readFileSync, writeFileSync } from 'node:fs';

// Set APP_URL if it doesn't exist for Laravel Vite plugin
if (!process.env.APP_URL) {
  process.env.APP_URL = 'https://sage11default.test';
}

// Génère resources/css/config/_theme.scss depuis theme.json (couleurs, polices, tailles, espacements)
function themeJsonToScss() {
  const src = 'theme.json';
  const out = 'resources/css/config/_theme.scss';

  const map = (name, items, prefix) =>
    `$${name}: (\n${items.map((item) => `  "${item.slug}": var(--wp--preset--${prefix}--${item.slug}),`).join('\n')}\n);\n`;

  const generate = () => {
    const { settings } = JSON.parse(readFileSync(src, 'utf8'));

    writeFileSync(out, [
      '// Généré depuis theme.json par vite.config.js — ne pas éditer\n',
      map('colors', settings.color.palette, 'color'),
      `$colors-hex: (\n${settings.color.palette.map((color) => `  "${color.slug}": ${color.color},`).join('\n')}\n);\n`,
      map('fonts', settings.typography.fontFamilies, 'font-family'),
      map('font-sizes', settings.typography.fontSizes, 'font-size'),
      map('spacing', settings.spacing.spacingSizes, 'spacing'),
    ].join('\n'));
  };

  return {
    name: 'theme-json-to-scss',
    buildStart: generate,
    configureServer(server) {
      server.watcher.add(src);
      server.watcher.on('change', (file) => file.endsWith(src) && generate());
    },
  };
}

export default defineConfig({
  base: '/app/themes/default/public/build/',
  plugins: [
    themeJsonToScss(),

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
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
});
