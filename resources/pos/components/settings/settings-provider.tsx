import React, { createContext, useContext, useEffect, useMemo } from 'react';
import { useSettingsStore } from '../../stores/settings-store';

// Context type mirrors the full Zustand store state returned by useSettingsStore()
type SettingsContextType = ReturnType<typeof useSettingsStore>;

const SettingsContext = createContext<SettingsContextType | undefined>(undefined);

/**
 * A React functional component that provides a context for managing application settings.
 * This component subscribes to the settings store and ensures that the settings data
 * is available to all its consumers via the `SettingsContext`. It also fetches remote settings
 * when the component is mounted.
 *
 * @component
 * @param {object} props - The component props.
 * @param {React.ReactNode} props.children - The child components that will have access to the settings context.
 * @returns {JSX.Element} The context provider wrapping the children components.
 */
export const SettingsProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  // Subscribe to the whole store for context consumers
  const fullState = useSettingsStore();
  const fetchSettings = useSettingsStore((s) => s.fetchSettings);

  // Fetch remote settings once on mount
  useEffect(() => {
    void fetchSettings();
  }, [fetchSettings]);

  // Memoize to avoid unnecessary renders of context consumers
  const value = useMemo<SettingsContextType>(() => fullState, [fullState]);

  return <SettingsContext.Provider value={value}>{children}</SettingsContext.Provider>;
};

export function useSettings(): SettingsContextType {
  const ctx = useContext(SettingsContext);
  if (!ctx) throw new Error('useSettings must be used within a SettingsProvider');
  return ctx;
}
