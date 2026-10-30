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

// Génère depuis theme.json :
// - resources/css/config/_theme.scss : couleurs, polices, tailles, espacements, points de rupture
// - resources/js/config/breakpoints.js : points de rupture (en px)
function themeJsonTokens() {
  const src = 'theme.json';

  const map = (name, items, prefix) =>
    `$${name}: (\n${items.map((item) => `  "${item.slug}": var(--wp--preset--${prefix}--${item.slug}),`).join('\n')}\n);\n`;

  const generate = () => {
    const { settings } = JSON.parse(readFileSync(src, 'utf8'));

    // Points de rupture : viewport de WordPress (mobile, tablet ; défauts core si absents) + custom.breakpoints
    const breakpoints = {
      mobile: '480px',
      tablet: '782px',
      ...settings.viewport,
      ...settings.custom?.breakpoints,
    };

    writeFileSync(
      'resources/css/config/_theme.scss',
      [
        '// Généré depuis theme.json par vite.config.js — ne pas éditer\n',
        map('colors', settings.color.palette, 'color'),
        `$colors-hex: (\n${settings.color.palette.map((color) => `  "${color.slug}": ${color.color},`).join('\n')}\n);\n`,
        map('fonts', settings.typography.fontFamilies, 'font-family'),
        map('font-sizes', settings.typography.fontSizes, 'font-size'),
        map('spacing', settings.spacing.spacingSizes, 'spacing'),
        `$breakpoints: (\n${Object.entries(breakpoints)
          .map(([name, size]) => `  "${name}": ${size},`)
          .join('\n')}\n);\n`,
      ].join('\n'),
    );

    writeFileSync(
      'resources/js/config/breakpoints.js',
      [
        '// Généré depuis theme.json par vite.config.js — ne pas éditer',
        "// Points de rupture en px : « jusqu'à » = width <= valeur, « au-delà » = width > valeur",
        `export const breakpoints = ${JSON.stringify(Object.fromEntries(Object.entries(breakpoints).map(([name, size]) => [name, parseInt(size, 10)])))};`,
        '',
      ].join('\n'),
    );
  };

  return {
    name: 'theme-json-tokens',
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

function blockPrefix(path) {
  const match = path?.match(/resources\/blocks\/([^/]+)\//);

  return match ? `${match[1]}-` : '';
}

export default defineConfig({
  base: `/app/themes/${themeName}/public/build/`,
  plugins: [
    wordpressJsx(),
    themeJsonTokens(),

    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js',
        'resources/css/editor.scss',
        'resources/js/editor.js',
        // Widget du tableau de bord (DashboardServiceProvider)
        'resources/css/admin/dashboard-video.scss',
        'resources/js/admin/dashboard-video.js',
        // Scan dynamique des blocs
        ...globSync('resources/blocks/*/{style.scss,script.js}'),
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
  // Fichiers des blocs préfixés par leur dossier (carte-style-….css plutôt que style-….css)
  build: {
    rolldownOptions: {
      output: {
        entryFileNames: (chunk) => `assets/${blockPrefix(chunk.facadeModuleId)}[name]-[hash].js`,
        assetFileNames: (asset) =>
          `assets/${blockPrefix(asset.originalFileNames?.[0])}[name]-[hash][extname]`,
      },
    },
  },
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
