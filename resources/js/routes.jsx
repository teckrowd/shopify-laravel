import { Routes as ReactRoutes, Route} from "react-router-dom";
import NotFound from "./pages/notFound";

/**
 * File-based routing.
 * @desc File-based routing that uses React Router under the hood.
 * To create a new route create a new .jsx file in `/pages` with a default export.
 *
 * Some examples:
 * * `/pages/index.jsx` matches `/`
 * * `/pages/blog/[id].jsx` matches `/blog/123`
 * * `/pages/[...catchAll].jsx` matches any URL not explicitly matched
 *
 * @param {object} pages value of import.meta.globEager(). See https://vitejs.dev/guide/features.html#glob-import
 *
 * @return {Routes} `<Routes/>` from React Router, with a `<Route/>` for each file in `pages`
 */
const Routes = ({
  pages
}) => {
  const routes = useRoutes(pages);
  const routeComponents = routes.map(({ path, component: Component }) => (
    <Route key={path}
      path={path}
      element={<Component />} />
  ));

  return (
    <ReactRoutes>
      {routeComponents}
      <Route path="*"
        element={<NotFound />} />
    </ReactRoutes>
  );
}

const useRoutes = (pages) => {
  const routes = Object.keys(pages)
    .map((key) => {
      let path = key
        .replace("./pages", "")
        .replace(/\.(t|j)sx?$/, "")
        /**
         * Replace /index with /
         */
        .replace(/\/index$/i, "/")
        /**
         * Only lowercase the first letter. This allows the developer to use camelCase
         * dynamic paths while ensuring their standard routes are normalized to lowercase.
         */
        .replace(/\b[A-Z]/, (firstLetter) => firstLetter.toLowerCase())
        /**
         * Convert /[handle].jsx and /[...handle].jsx to /:handle.jsx for react-router-dom
         */
        .replace(/\[(?:[.]{3})?(\w+?)\]/g, (_match, param) => `:${param}`);

      if (path.endsWith("/") && path !== "/") {
        path = path.substring(0, path.length - 1);
      }

      if (!pages[key].default) {
        console.warn(`${key} doesn't export a default React component`);
      }
      if(path !== '/notFound' && import.meta.env.VITE_SHOPIFY_PATH_PREFIX) {
        const prefix = import.meta.env.VITE_SHOPIFY_PATH_PREFIX;
        if(prefix !== "/") {
          path = prefix.startsWith("/") ? `/${prefix.substr(1)}${path}` : `/${prefix}${path}`;
        }
      }

      return {
        path,
        component: pages[key].default,
      };
    })
      .filter((route) => route.component);

  return routes;
}

export default Routes;