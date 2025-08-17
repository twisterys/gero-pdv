import {create} from "zustand";

interface VenteLigne {
    produit_id:number,
    quantite:number,
    prix_unitaire:number,
    reduction:number,
    reduction_type:"pourcentage" | "fixe",
    total:number,
    tva:number,
}

interface VentePaiement {
    montant:number,
    methode:string,
    compte:number,
    date_prevu:string|null,
    check_reference:string|null,
}

interface VenteStore {
    client_id:number|null,
    lignes:VenteLigne[],
    paiement:VentePaiement|null,
    isLoading:boolean,
    isError:boolean,
    error:string|null,
    setClient: (client_id:number) => void,
    setLignes: (lignes:VenteLigne[]) => void,
    setPaiement: (paiement:VentePaiement) => void,
    setIsLoading: (isLoading:boolean) => void,
    setIsError: (isError:boolean) => void,
    setError: (error:string|null) => void,
}

/**
 * A state management store for managing sales-related data in the application.
 * Provides state variables to track client information, sale lines, payment status,
 * as well as states for errors and loading activities.
 *
 * State Variables:
 * - client_id: Stores the identifier of the active client.
 * - lignes: Stores an array of sales line items.
 * - paiement: Stores the current payment information or status.
 * - isLoading: Indicates whether a loading operation is in progress.
 * - isError: Indicates whether an error is currently present.
 * - error: Stores the details of the current error.
 *
 * Methods:
 * - setClient(client_id): Updates the client identifier in the store.
 * - setLignes(lignes): Updates the array of sale line items in the store.
 * - setPaiement(paiement): Updates the current payment information in the store.
 * - setIsLoading(isLoading): Toggles the loading state in the store.
 * - setIsError(isError): Toggles the error presence state in the store.
 * - setError(error): Updates the error details in the store.
 */
export const UseVenteStore = create<VenteStore>((set)=>({
    client_id:null,
    lignes:[],
    paiement:null,
    isLoading:false,
    isError:false,
    error:null,

    setClient: (client_id) => set({client_id}),
    setLignes: (lignes) => set({lignes}),
    setPaiement: (paiement) => set({paiement}),
    setIsLoading: (isLoading) => set({isLoading}),
    setIsError: (isError) => set({isError}),
    setError: (error) => set({error}),
}))
