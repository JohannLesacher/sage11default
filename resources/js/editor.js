import '@scripts/admin/animation';
import '@scripts/admin/responsive-panel';
import '@scripts/admin/responsive-options';

// Éditeurs des blocs natifs : resources/blocks/{name}/editor.jsx, importés automatiquement
import.meta.glob('../blocks/*/editor.jsx', { eager: true });
