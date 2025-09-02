import { create } from 'zustand';
import { endpoints } from '../services/api';

export interface RebutLine {
    i_article_id: number;
    i_article?: string; // optionnel, pour l’affichage
    quantite_rebut: number;
}

export interface RebutPayload {
    lignes: RebutLine[];
}

interface RebutState {
    isLoading: boolean;
    error: string | null;
    createRebut: (payload: RebutPayload) => Promise<any>;
    listRebuts: () => Promise<any>;
}

const handleAsync = async <T>(op: () => Promise<T>, set: (s: Partial<RebutState>) => void, msg: string): Promise<T> => {
    set({ isLoading: true, error: null });
    try {
        return await op();
    } catch (e) {
        set({ isLoading: false, error: msg });
        throw e;
    }
};

export const useRebutStore = create<RebutState>((set) => ({
    isLoading: false,
    error: null,
    createRebut: async (payload) => {
        return await handleAsync(async () => {
            const res = await endpoints.rebuts.create(payload);
            set({ isLoading: false });
            return res;
        }, set, 'Échec de création du rebut');
    },
    listRebuts: async () => {
        return await handleAsync(async () => {
            const res = await endpoints.rebuts.list();
            set({ isLoading: false });
            return res;
        }, set, 'Échec du chargement des rebuts');
    },
}));
