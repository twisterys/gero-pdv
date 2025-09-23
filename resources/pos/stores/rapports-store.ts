import {create} from "zustand";
import { endpoints } from "../services/api";


export interface StockRapport {
    id:number,
    designation:string,
    reference:string,
    stock:number,
}

export interface SaleByProductAndClientRapport{
    clients: string[];
    articles: string[];
    data: {
        [clientName: string]: {
            [productName: string]: {
                quantite: number;
                total_ttc: number;
            };
        };
    };
    client_totals: {
        [clientName: string]: {
            total_ttc: number;
            total_paye: number;
        };
    };
}

export interface ProductBySupplierRapport {
    fournisseurs: string[];
    articles: string[];
    data: {
        [fournisseurName: string]: {
            [productName: string]: {
                quantite: number;
                total_ttc: number;
            };
        };
    };
}

export interface PaymentsAndCreditRapport {
    reference: string;
    client_name: string;
    last_payment_method:  string;
    last_payment_date: string;
    cheque_lcn_reference: string | null;
    sale_date: string;
    is_controled: 0 | 1;
    total_ttc: string;
    statut_paiement: string;
    creance_amount: string;
}

export interface TreasuryRapport {
    reste_en_caisse:number,
    total_cheque:number,
    total_depenses:number,
    total_espece:number,
    total_lcn:number,
    total_vente:number,
}

export interface DepenseItem {
    categorie: string;
    montant: number;
}

interface RapportStore {
    stock:StockRapport[];
    saleByProductAndCLient:SaleByProductAndClientRapport,
    productBySupplier:ProductBySupplierRapport,
    paymentsAndCredit:PaymentsAndCreditRapport[],
    treasury:TreasuryRapport,
    depenses: DepenseItem[];
    isLoading:boolean;
    isError:boolean;
    error:string|null;

    setIsLoading: (isLoading:boolean) => void;
    setIsError: (isError:boolean) => void;
    setError: (error:string|null) => void;

    getStock: () => Promise<void>;
    getSaleByProductAndClient: () => Promise<void>;
    getProductBySupplier: () => Promise<void>;
    getPaymentsAndCredit: () => Promise<void>;
    getTreasury: () => Promise<void>;
    getDepenses: () => Promise<void>;
}

/**
 * A store module for managing rapport-related data and state.
 *
 * This store includes functionalities for fetching and managing various
 * reports related to stock, sales, products by supplier, payments and credits,
 * and treasury details. Includes loading and error states for better API request management.
 *
 * Properties:
 * - `stock`: Array of stock-related data.
 * - `saleByProductAndCLient`: Object containing data on sales by product and client.
 * - `productBySupplier`: Object containing data on products categorized by supplier.
 * - `paymentsAndCredit`: Array of data on payments and credits.
 * - `treasury`: Object containing various metrics related to treasury status.
 * - `isLoading`: Boolean indicating if an operation is in progress.
 * - `isError`: Boolean indicating if any error has occurred.
 * - `error`: String representing the current error message, if applicable.
 *
 * Methods:
 * - `setIsLoading(isLoading)`: Updates the loading state.
 * - `setIsError(isError)`: Sets the error state.
 * - `setError(error)`: Assigns the error message.
 * - `getStock()`: Fetches stock data from an external API and updates the store.
 * - `getSaleByProductAndClient()`: Fetches sales data categorized by product and client.
 * - `getProductBySupplier()`: Fetches product data categorized by supplier.
 * - `getPaymentsAndCredit()`: Fetches information on payments and credits.
 * - `getTreasury()`: Fetches treasury metrics and updates the treasury state.
 */
export const useRapportsStore = create<RapportStore>((set) => ({
    stock: [],
    saleByProductAndCLient: {
        clients: [],
        articles: [],
        data: {},
        client_totals: {}
    },
    productBySupplier: {
        fournisseurs: [],
        articles: [],
        data: {},
    },
    paymentsAndCredit:[],
    treasury: {
        reste_en_caisse:0,
        total_cheque:0,
        total_depenses:0,
        total_espece:0,
        total_lcn:0,
        total_vente:0,
    },
    depenses: [],
    isLoading: false,
    isError: false,
    error: null,

    setIsLoading: (isLoading: boolean) => set({ isLoading }),
    setIsError: (isError: boolean) => set({ isError }),
    setError: (error: string | null) => set({ error }),

    // The following getters are scaffolded with proper loading/error handling.
    // Replace the TODOs with real API calls when endpoints are available.
    getStock: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.articlesStockRapport();
            const rows = Array.isArray(data) ? (data as StockRapport[]) : ([data] as StockRapport[]);
            set({ stock: rows });
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch stock report' });
        } finally {
            set({ isLoading: false });
        }
    },

    getSaleByProductAndClient: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.articlesClientsRapport();
            set({ saleByProductAndCLient: data });
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch sale by product and client report' });
        } finally {
            set({ isLoading: false });
        }
    },

    getProductBySupplier: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.articlesFournisseursRapport();
            set({ productBySupplier:data });
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch product by supplier report' });
        } finally {
            set({ isLoading: false });
        }
    },

    getPaymentsAndCredit: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.creanceRapport();
            set({ paymentsAndCredit: Array.isArray(data) ? (data as any) : ([data] as any) });
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch payments and credit report' });
        } finally {
            set({ isLoading: false });
        }
    },

    getTreasury: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.tresorieRapport();
            set({ treasury: data});
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch treasury report' });
        } finally {
            set({ isLoading: false });
        }
    },

    getDepenses: async () => {
        set({ isLoading: true, isError: false, error: null });
        try {
            const { data } = await endpoints.rapports.depensesRapport();
            const rows = Array.isArray(data) ? (data as any) : ([data] as any);
            set({ depenses: rows });
        } catch (e: any) {
            set({ isError: true, error: e?.message || 'Failed to fetch depenses report' });
        } finally {
            set({ isLoading: false });
        }
    },
}));
