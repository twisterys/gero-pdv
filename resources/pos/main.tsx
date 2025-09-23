import React from 'react';
import ReactDOM from 'react-dom/client';
import { createBrowserRouter, RouterProvider } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';

// Providers from existing codebase
import { AuthProvider } from './components/auth/auth-provider';
import { SettingsProvider } from './components/settings/settings-provider';
import { ConnectionProvider } from './components/connection/connection-provider';

// Global styles (reusing existing app styles)
import './app/app.css';

// Routes mapping (reusing existing route components)
import Home from './app/routes/home';
import InjectAuthRoute from './app/routes/auth/inject';
import POSRoute from './app/routes/pos';
import DemandesRoute from './app/routes/demandes';
import RapportsRoute from './app/routes/rapports';

// Rapports children
import RapportsIndex from './app/rapports/index';
import Stock from './app/rapports/stock';
import SaleByProductClient from './app/rapports/sale-by-product-client';
import ProductBySupplier from './app/rapports/product-by-supplier';
import PaymentsAndCredit from './app/rapports/payments-and-credit';
import Treasury from './app/rapports/treasury';
import Daily from './app/rapports/daily';
import Depenses from './app/rapports/depenses';

const router = createBrowserRouter([
  {
    path: '/point-de-vente',
    element: <Home />,
  },
  {
    path: '/auth/inject',
    element: <InjectAuthRoute />,
  },
  {
    path: '/pos',
    element: <POSRoute />,
  },
  {
    path: '/demandes',
    element: <DemandesRoute />,
  },
  {
    path: '/rapports',
    element: <RapportsRoute />,
    children: [
      { index: true, element: <RapportsIndex /> },
      { path: 'stock', element: <Stock /> },
      { path: 'sale-by-product-client', element: <SaleByProductClient /> },
      { path: 'product-by-supplier', element: <ProductBySupplier /> },
      { path: 'payments-and-credit', element: <PaymentsAndCredit /> },
      { path: 'treasury', element: <Treasury /> },
      { path: 'depenses', element: <Depenses /> },
      { path: 'daily', element: <Daily /> },
    ],
  },
]);

const root = document.getElementById('app');
if (root) {
  ReactDOM.createRoot(root).render(
    <React.StrictMode>
      <AuthProvider>
        <SettingsProvider>
          <ConnectionProvider>
            <RouterProvider router={router} />
            <ToastContainer />
          </ConnectionProvider>
        </SettingsProvider>
      </AuthProvider>
    </React.StrictMode>
  );
}
