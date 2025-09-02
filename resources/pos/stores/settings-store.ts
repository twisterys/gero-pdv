import {create} from 'zustand';
import {endpoints} from "../services/api";

export interface SettingsApiResponse {
    features: {
        ticketPrinting: boolean;
        autoTicketPrinting: boolean;
        priceEditing: boolean;
        reductionEnabled: boolean;
        globalReductionEnabled: boolean;
        demandes: boolean;
        history:boolean;
        depense:boolean;
        cloture:boolean;
        rebut:boolean;
    };
    url: string;
    apiUrl: string;
    defaultClient: {value:number,label:string} | null;
    rapports: {
        stock:boolean;
        saleByProductAndCLient:boolean;
        productBySupplier:boolean;
        paymentsAndCredit:boolean;
        treasury:boolean;
        daily:boolean;
    },
    buttons :{
        credit:boolean;
        other:boolean;
        cash:boolean;
    }
    posType: "parfums" | "classic"|"caisse";
}

interface SettingsState {
    features: {
        ticketPrinting: boolean;
        autoTicketPrinting: boolean;
        priceEditing: boolean;
        reductionEnabled: boolean;
        globalReductionEnabled: boolean;
        demandes: boolean;
        history:boolean;
        depense:boolean;
        cloture:boolean;
        rebut:boolean;
    };

    posType: "parfums" | "classic"|"caisse";

    rapports: {
        stock:boolean;
        saleByProductAndCLient:boolean;
        productBySupplier:boolean;
        paymentsAndCredit:boolean;
        treasury:boolean;
        daily:boolean;
    }
    buttons :{
        credit:boolean;
        other:boolean;
        cash:boolean;
    }

    defaultClient: {value:number,label:string} | null;
    url:string;
    apiUrl:string;

    // Actions
    toggleFeature: (featureName: keyof SettingsState['features']) => void;
    setFeature: (featureName: keyof SettingsState['features'], value: boolean) => void;
    fetchSettings: () => Promise<void>;
}

/**
 * `useSettingsStore` is a Zustand store used to manage application-wide settings and features.
 *
 * The store maintains the state for various configuration options, feature toggles, and reporting settings.
 * It also provides a set of utility functions for toggling features, customizing feature flags, and
 * fetching settings from an external source.
 *
 * State properties:
 * - `features`: An object containing feature flags for different functionalities such as ticket printing, price editing, etc.
 * - `posType`: A string representing the type of POS (Point of Sale) system (e.g., "caisse").
 * - `rapports`: An object containing options for enabling or disabling different report types (e.g., stock, daily reports).
 * - `buttons`: Tracks the visibility or states of specific button functionalities.
 * - `defaultClient`: Stores the default client information, or `null` if unset.
 * - `apiUrl`: A string representing the base API URL for external requests.
 * - `url`: An additional URL string for configuration purposes.
 *
 * Methods:
 * - `toggleFeature(featureName)`: Toggles the state of a specified feature flag.
 * - `setFeature(featureName, value)`: Manually sets the state of a specified feature flag to a specific value.
 * - `fetchSettings()`: Asynchronously fetches the settings from an external data source and updates the store with the received data.
 *
 * The `fetchSettings` method is particularly useful for initializing the state when the application starts,
 * ensuring that the latest settings are applied.
 */
export const useSettingsStore = create<SettingsState>()(
    (set) => ({
        // Initial state
        features: {
            ticketPrinting: false,
            autoTicketPrinting: false,
            priceEditing: false,
            reductionEnabled: false,
            globalReductionEnabled: false,
            demandes: false,
            history:false,
            depense:false,
            cloture:false,
            rebut:false,
        },
        posType: "caisse",
        rapports: {
            stock:false,
            saleByProductAndCLient:false,
            productBySupplier:false,
            paymentsAndCredit:false,
            treasury:false,
            daily:false,
        },
        buttons :{
            credit:false,
            other:false,
            cash:false,
        },
        defaultClient: null,
        apiUrl:"",
        url:"",

        // Toggle a feature flag
        toggleFeature: (featureName) =>
            set((state) => ({
                features: {
                    ...state.features,
                    [featureName]: !state.features[featureName],
                }
            })),

        // Set a feature flag to a specific value
        setFeature: (featureName, value) =>
            set((state) => ({
                features: {
                    ...state.features,
                    [featureName]: value,
                }
            })),

        fetchSettings: async () => {
            try {
                const resp = await endpoints.system.getSettings();
                const data = (resp as any)?.data ?? resp;
                if (!data) return;
                set((state) => ({
                    features: {
                        ...state.features,
                        ...(data.features ?? {}),
                    },
                    defaultClient: data.defaultClient ?? state.defaultClient,
                    rapports: {
                        ...(state.rapports ?? {}),
                        ...(data.rapports ?? {}),
                    },
                    url: data.url ?? state.url,
                    apiUrl: data.apiUrl ?? state.apiUrl,
                    posType: data.posType ?? state.posType,
                    buttons :{
                        ...(state.buttons ?? {}),
                        ...(data.buttons ?? {}),
                    },
                }));
            } catch (e) {
                console.warn('Failed to fetch settings:', e);
            }
        }
    })
);
