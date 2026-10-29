import { defineConfig, loadEnv, transformWithOxc } from 'vite';
import laravel from 'laravel-vite-plugin';
import { wordpressPlugin } from '@roots/vite-plugin';
import { globSync } from 'glob';
import { readFileSync, writeFileSync } from 'node:fs';
import { basename, resolve } from 'node:path';

// Dossier du thème (web/app/themes/<theme>) et racine Bedrock, déduits de l'emplacement du thème
const themeName = basename(import.meta.dirname);
const bedrockRoot = resolve(import.meta.dirname, '../../../..');

// APP_URL pour le HMR : variable d'environnement, sinon WP_HOME du .env Bedrock
if (!process.env.APP_URL) {
  process.env.APP_URL = loadEnv('', bedrockRoot, '').WP_HOME ?? 'http://localhost';
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
      // Breakpoints partagés avec la visibilité par viewport de WordPress (défauts core si absents)
      `$viewport: (\n${Object.entries({ mobile: '480px', tablet: '782px', ...settings.viewport }).map(([slug, size]) => `  "${slug}": ${size},`).join('\n')}\n);\n`,
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

// Compile le JSX (runtime automatique) avant @roots/vite-plugin, en dev comme en build.
// react/jsx-runtime est ensuite redirigé vers le runtime fourni par WordPress (resolve.alias).
function wordpressJsx() {
  return {
    name: 'wordpress-jsx',
    enforce: 'pre',
    async transform(code, id) {
      if (!id.endsWith('.jsx')) return null;

      const { code: js, map } = await transformWithOxc(code, id, {
        lang: 'jsx',
        jsx: { runtime: 'automatic', development: false },
      });

      return { code: js, map };
    },
  };
}

export default defineConfig({
  base: `/app/themes/${themeName}/public/build/`,
  plugins: [
    wordpressJsx(),
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

    wordpressPlugin({
      externalMappings: {
        'wp-react-jsx-runtime': { global: ['ReactJSXRuntime'], handle: 'react-jsx-runtime' },
        react: { global: ['React'], handle: 'react' },
      },
    }),
  ],
  // Même réglage JSX pour le pré-scan des dépendances (sinon il cherche react/jsx-dev-runtime)
  optimizeDeps: {
    rolldownOptions: {
      transform: { jsx: { runtime: 'automatic', development: false } },
    },
  },
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
      'react/jsx-runtime': '/resources/js/lib/react-jsx-runtime.js',
    },
  },
});
