// Le JSX (runtime automatique) importe react/jsx-runtime : alias Vite vers ce module, qui renvoie
// au runtime fourni par WordPress (handle react-jsx-runtime, global ReactJSXRuntime).
import { jsx, jsxs, Fragment } from 'wp-react-jsx-runtime';

export { jsx, jsxs, Fragment };
