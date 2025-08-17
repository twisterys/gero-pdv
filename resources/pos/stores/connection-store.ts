import { create } from "zustand";
import { endpoints } from "../services/api";

interface ConnectionState {
  isOnline: boolean;
  isServerConnected: boolean;
  lastChecked: Date | null;
  errorMessage: string | null;
  showToast: boolean;

  // Methods
  setIsOnline: (isOnline: boolean) => void;
  setIsServerConnected: (isServerConnected: boolean) => void;
  setErrorMessage: (errorMessage: string | null) => void;
  showConnectionToast: (message: string) => void;
  hideConnectionToast: () => void;
  checkConnection: () => Promise<void>;
}

/**
 * Store for managing the connection state of the application.
 *
 * This store is responsible for handling both the browser's online status
 * and the application's connection to the server. It includes methods to
 * update the online state, check server connectivity, and display error or
 * status notifications when necessary.
 *
 * State Properties:
 * - `isOnline`: Boolean value reflecting the browser's online/offline status.
 * - `isServerConnected`: Boolean indicating whether the server connection is successful.
 * - `lastChecked`: Timestamp of the last connectivity check.
 * - `errorMessage`: Error message displayed if the connection to the server fails.
 * - `showToast`: Boolean indicating whether a toast message related to connection issues should be displayed.
 *
 * Methods:
 * - `setIsOnline(isOnline)`: Updates the `isOnline` state.
 * - `setIsServerConnected(isServerConnected)`: Updates the `isServerConnected` state.
 * - `setErrorMessage(errorMessage)`: Sets an error message in the store.
 * - `showConnectionToast(message)`: Displays a toast message with the given `message`.
 * - `hideConnectionToast()`: Hides the connection toast notification.
 * - `checkConnection()`: Checks the browser's online status and pings the server to verify connectivity. Updates the state based on connectivity results.
 */
export const useConnectionStore = create<ConnectionState>((set, get) => ({
  isOnline: navigator.onLine, // Initial state based on browser's online status
  isServerConnected: true, // Assume server is connected initially
  lastChecked: null,
  errorMessage: null,
  showToast: false,

  setIsOnline: (isOnline) => set({ isOnline }),

  setIsServerConnected: (isServerConnected) => set({ isServerConnected }),

  setErrorMessage: (errorMessage) => set({ errorMessage }),

  showConnectionToast: (message) => set({
    showToast: true,
    errorMessage: message
  }),

  hideConnectionToast: () => set({
    showToast: false
  }),

  checkConnection: async () => {
    const state = get();

    // First check if browser reports as online
    const isOnline = navigator.onLine;
    set({ isOnline });

    if (!isOnline) {
      set({
        isServerConnected: false,
        errorMessage: "Pas de connexion Internet",
        showToast: true,
        lastChecked: new Date()
      });
      return;
    }

    // Then try to ping the server using our API service
    try {
      const isServerConnected = await endpoints.system.healthCheck().then(() => true).catch(() => false);

      if (isServerConnected) {
        set({
          isServerConnected: true,
          errorMessage: null,
          showToast: false,
          lastChecked: new Date()
        });
      } else {
        set({
          isServerConnected: false,
          errorMessage: "Problème de connexion au serveur",
          showToast: true,
          lastChecked: new Date()
        });
      }
    } catch (error) {
      set({
        isServerConnected: false,
        errorMessage: "Impossible de se connecter au serveur",
        showToast: true,
        lastChecked: new Date()
      });
    }
  }
}));

// Set up event listeners for online/offline events
if (typeof window !== 'undefined') {
  window.addEventListener('online', () => {
    const store = useConnectionStore.getState();
    store.setIsOnline(true);
    store.checkConnection();
  });

  window.addEventListener('offline', () => {
    const store = useConnectionStore.getState();
    store.setIsOnline(false);
    store.showConnectionToast("Pas de connexion Internet");
  });
}
