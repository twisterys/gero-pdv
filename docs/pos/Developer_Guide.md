# Gero POS — Developer Guide

This document describes the architecture, libraries, coding conventions and extension points for the POS React app located under `resources/pos`.

Scope
- Frontend POS SPA mounted via Laravel Blade at `resources/views/pos/pos.blade.php`.
- Integrates with the Laravel backend (`routes/api.php`, controllers under `app/Http/Controllers/Api/classic`).

1. Stack and key libraries
- Build tooling: Vite + Laravel Vite plugin (entry: `resources/pos/main.tsx`).
- Language: TypeScript (strict=true). Path alias `~/*` -> `resources/pos/app/*` in `tsconfig.json`.
- UI: React 18, React Router (createBrowserRouter + RouterProvider).
- Styling: Tailwind CSS (via `@tailwindcss/postcss` in `postcss.config.js`) + local CSS `resources/pos/app/app.css`.
- State management: Zustand (`zustand`).
- HTTP client: Axios with interceptors.
- Forms: react-hook-form.
- Notifications: react-toastify.
- Utilities: lodash (debounce), Intl APIs for formatting.

2. Directory layout
- `resources/pos/main.tsx`: App bootstrap (providers, router, toasts).
- `resources/pos/app`
  - `pos/`: POS screen (layout, type toggler) and the main POS store (`pos-store.ts`).
  - `rapports/`: Reports pages (stock, daily, payments & credit, product/client matrix, product by supplier, treasury; plus index and layout).
  - `demandes/`: Entry for demandes.
  - `routes/`: Route components for home, pos, rapports, demandes, and auth.
  - `hooks/`: e.g., cart or POS hooks.
  - `+types/`: route types.
  - `welcome/`: assets for welcome page.
- `resources/pos/components`
  - `cart/`: cart table/layout and `payment-modal.tsx` (react-hook-form).
  - `articles/`, `product-search/`, `client-select/` (with quick-add modal), `depense/`, `history/` (panel/offcanvas), `pos/` (caisse/cloture/shutdown), `rapports/` (report toolbar), `keyboard/` (on-screen numpad and keys), `settings/`, `auth/`, `connection/` (providers and toasts).
- `resources/pos/stores`: Zustand stores for settings, POS/sales, product search, depense, demandes, rapports, connection.
- `resources/pos/services/api.ts`: Axios instance with interceptors and endpoint wrappers.
- `resources/pos/utils`: helpers for printing, formatting numbers/dates, and sounds.

3. Bootstrapping and routing
- Blade: `pos.blade.php` mounts React on `#app` and loads `resources/pos/main.tsx`.
- `main.tsx`
  - Providers: `AuthProvider`, `SettingsProvider`, `ConnectionProvider` wrap the app; `ToastContainer` is mounted globally.
  - Router: paths `/point-de-vente` (Home), `/auth/inject`, `/pos`, `/demandes`, and `/rapports` with children: `stock`, `sale-by-product-client`, `product-by-supplier`, `payments-and-credit`, `treasury`, `daily`.

4. API layer (`services/api.ts`)
- Axios instance with baseURL `http://pdv.gero-pdv.test/api/pos/v1` (adjust per environment).
- Interceptors
  - Request: injects `Authorization: Bearer <token>` using `localStorage.auth_token`, and `session_id` in query/body for GET/POST.
  - Response: logs common status codes and propagates errors.
- Endpoints are grouped by domain: `system`, `payment`, `products`, `clients`, `orders`, `depenses`, `demandes`, `auth`, `history`, `rapports`.
- Convention: endpoint functions return Axios promises; stores/components orchestrate usage and manage UI state.

5. State management with Zustand

5.1 Global POS store (`app/pos/pos-store.ts`)
- Holds product pagination, cart items, reductions, totals, client, order type, and checkout/payment flows.
- Key operations:
  - `fetchProducts(page)`, `fetchNextPage()` using `endpoints.products.getAll` (pagination meta stored separately).
  - Cart: `addToCart`, `removeFromCart`, `updateQuantity`, `updatePrice`, `updateReduction`, `setGlobalReduction`, `clearCart`.
  - Client: `setClient`, `clearClient`.
  - Order: `toggleOrderType`, `setOrderType`, `checkout(paymentData)`, `addPaymentToOrder(orderId, paymentData)`.
  - Helpers: `isPaymentComplete`, `clearLastOrderInfo`.
- Calculations: internal helpers compute line totals with reduction types (percentage/fixed), tax, and rounding to 0.001.
- Side effects: HTTP calls via `endpoints.orders.*`; auditory feedback via `playSound` if configured.

5.2 Settings store (`stores/settings-store.ts`)
- Feature flags: `ticketPrinting`, `autoTicketPrinting`, `priceEditing`, `reductionEnabled`, `globalReductionEnabled`, `demandes`, `history`, `depense`.
- POS type: `posType` is one of `classic | parfums | caisse` and drives the UI (type toggler).
- Report availability flags: `rapports.{stock, saleByProductAndCLient, productBySupplier, paymentsAndCredit, treasury, daily}`.
- Actions: `toggleFeature`, `setFeature`, `fetchSettings` (populates defaults and URLs from backend).

5.3 Other domain stores
- `stores/product-search-store.ts`
  - Debounced in-memory search across `usePOSStore.getState().products`, matching `reference` or `designation`.
  - Auto-add when a single match is found; shows toast warning and plays a short sound when none.
  - UI state: `searchTerm`, `searchResults`, `isOpen`, `isFocused`, `loading`.
- `stores/depense-store.ts`
  - `fetchCategories`, `createDepense` via `endpoints.depenses`.
  - Loading/error flags centralized via a small `handleAsyncOperation` helper.
- `stores/demandes-store.ts`
  - Lifecycle: `fetchDemandesIntern`, `fetchDemandesExtern`, `createDemande`, `cancelDemande`, `acceptDemande`, `printDemande`, `livrerDemande`.
  - Demande cart: `addToDemandeCart`, `removeFromDemandeCart`, `updateQuantity`, `clearDemandeCart`.
  - Product pagination for demande: `fetchProducts`, `fetchNextPage`.
- `stores/rapports-store.ts`
  - Fetchers: `getStock`, `getSaleByProductAndClient`, `getProductBySupplier`, `getPaymentsAndCredit`, `getTreasury` mapped to backend endpoints.
  - Normalized state shapes for each report; consistent loading/error flags.
- `stores/connection-store.ts`
  - Tracks `isOnline`, `isServerConnected`, `lastChecked`, `errorMessage` with `checkConnection()` and window online/offline listeners.

6. Components and providers
- `components/cart/payment-modal.tsx`
  - Uses `react-hook-form` with `PaymentData` type; supports additional payments by passing `isAdditionalPayment` and `remainingAmount`.
  - Fetches accounts and payment methods from `endpoints.payment` on open.
  - Server error mapping example: maps backend fields (e.g., `paiement.i_montant`) to form fields and applies `setError`.
- `components/pos/*`
  - `cloture-button.tsx`, `shutdown-button.tsx`: call `endpoints.system.cloture()` and `endpoints.system.shutdown()`.
  - `caisse-panel.tsx`: simplified cashier UI when `posType === 'caisse'`.
- `components/history/*`
  - History panel/offcanvas for session review and operations like reprint/add payment.
- `components/product-search/*` and `components/keyboard/*`
  - `useProductSearch` hook bridges UI input with `product-search-store`.
- Providers
  - `components/settings/settings-provider.tsx`: loads settings on mount and exposes context as needed alongside Zustand store.
  - `components/connection/connection-provider.tsx`: triggers periodic `checkConnection()` and renders toast/banner via `connection-toast.tsx`.
  - `components/auth/auth-provider.tsx`: injects/refreshes auth context and persists tokens in `localStorage`.

7. Utilities
- `utils/formats.ts`: `formatNumber(number, currency?)` and `formatDate(date, withTime?)` — both use Intl with `fr-FR` and MAD currency; always 2 decimals.
- `utils/helpers.ts`: `printReport(ref, title)` clones a table into a print window; `printHtml(html, title)` for HTML-based printouts.
- `utils/sound.ts`: `playSound(file, volume?)` plays from `/sounds/<file>`; non-blocking.

8. Coding guidelines and conventions
- TypeScript
  - Keep types/interfaces colocated with modules when small; extract to `+types` for shared route types.
  - Prefer explicit types for store state; surface only what UI needs.
- Zustand stores
  - State shape convention: `isLoading`, `isError`, `error` keys for async flows; synchronous setters named `setX`.
  - Use functional `set((state)=>({...}))` for state updates; avoid mutating nested arrays/objects in-place.
  - For cross-store access, prefer `Store.getState()` to avoid hook dependency cycles.
  - Debounce heavy operations at module scope (see `product-search-store.ts`) to avoid re-instantiation per render.
- API calls
  - Keep calls centralized in `services/api.ts`. If adding endpoints, extend the `endpoints` map; reuse axios instance.
  - Rely on request interceptor to inject `Authorization` and `session_id`.
- UI/UX
  - Use `react-toastify` for transient feedback (success/warning/error); avoid blocking alerts.
  - Respect feature flags from `settings-store` to conditionally render modules (Demandes, History, Depense, Rapports).
  - Guard privileged actions with backend permissions and fail gracefully.
- Forms
  - Use `react-hook-form` with zod/yup (if introduced) for validation; map server errors to fields as shown in `payment-modal.tsx`.
- Styling
  - Tailwind utility classes for layout and theming; prefer semantic, consistent spacing and colors.
- Files and naming
  - Components: kebab or hyphen separation (e.g., `payment-modal.tsx`), PascalCase exports.
  - Stores: `*-store.ts` exporting `useXStore` hooks.

9. Adding features
- New API resource
  1) Add endpoint to `services/api.ts`.
  2) Create a dedicated store for state and effects, expose actions and flags.
  3) Build UI components and wire them to the store.
  4) Add routes in `main.tsx` (and under `app/routes`/`app/<feature>` as needed).
  5) Gate with feature flags if applicable (extend `settings-store` and backend settings endpoint).
- New report
  1) Add fetcher in `rapports-store.ts` wired to backend endpoint.
  2) Create a page under `app/rapports/` and link via `app/routes/rapports.tsx`.
  3) Update `main.tsx` children under `/rapports` if adding a new route.
  4) Add print/export actions using `utils/helpers.ts`.

10. Build & run
- Development
  - Laravel serves Blade (`php artisan serve`); Vite handles asset building.
  - For HMR in POS, standard Vite dev flow applies when using `@viteReactRefresh` and `@vite` directives in Blade.
- Production
  - Build assets: `npm run build` (see README). Ensure `vite.config.js` includes POS entry (it references `resources/pos/main.tsx` via Blade).
  - Configure backend API base URL via backend settings consumed by `settings-store` where applicable.

11. Troubleshooting
- 401/403: Token missing/expired; `auth-provider` should refresh or redirect; ensure `auth_token` in localStorage.
- No results in product search: verify `usePOSStore` products are loaded and debounce logic is not suppressed.
- Printing issues: use `printReport` or `printHtml`; ensure popups are allowed and styles injected.
- Session issues: `system.cloture()` and `system.shutdown()` endpoints return server-side status; handle errors with toasts.

12. Barecode scanner
- 

References
- Key files: `resources/pos/main.tsx`, `resources/pos/services/api.ts`, `resources/pos/app/pos/pos-store.ts`, `resources/pos/stores/*`, `resources/pos/components/cart/payment-modal.tsx`, `resources/pos/utils/*`.
