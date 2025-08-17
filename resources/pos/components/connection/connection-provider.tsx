import React from 'react';
import { ConnectionToast } from './connection-toast';

interface ConnectionProviderProps {
  children: React.ReactNode;
}

/**
 * ConnectionProvider is a React functional component that provides a context
 * for managing connection-related behaviors and displaying connection status
 * notifications.
 *
 * This component wraps its children with additional functionality, ensuring
 * that a ConnectionToast component is always rendered alongside its children.
 *
 * The component is intended to be used as a provider for connection-related logic
 * and UI elements throughout the application.
 * @typedef {Object} ConnectionProviderProps
 * @property {React.ReactNode} children - The content to be rendered within the provider.
 * @type {React.FC<ConnectionProviderProps>}
 *
 */
export const ConnectionProvider: React.FC<ConnectionProviderProps> = ({ children }) => {
  return (
    <>
      {children}
      <ConnectionToast />
    </>
  );
};
