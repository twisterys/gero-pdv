import { create } from 'zustand';
// @ts-ignore
import { debounce } from 'lodash';
import { useDemandesStore, type Product } from './demandes-store';
import { toast } from 'react-toastify';

interface DemandeProductSearchState {
  // state
  searchTerm: string;
  searchResults: Product[];
  loading: boolean;
  isOpen: boolean;
  isFocused: boolean;

  // actions
  setSearchTerm: (val: string | ((prev: string) => string), search?: boolean) => void;
  setSearchResults: (val: Product[]) => void;
  setLoading: (val: boolean) => void;
  setIsOpen: (val: boolean) => void;
  setIsFocused: (val: boolean) => void;
  clearSearch: () => void;
  selectProduct: (product: Product) => void;
}

/**
 * A Zustand store that manages the state and actions for searching products in the product demande flow.
 *
 * @constant {object} useDemandeProductSearchStore
 *
 * State:
 * - `searchTerm` {string}: The current search term entered by the user.
 * - `searchResults` {Product[]}: The list of products resulting from the search.
 * - `loading` {boolean}: Indicates whether the search operation is in progress.
 * - `isOpen` {boolean}: Determines if the search dropdown or modal is open.
 * - `isFocused` {boolean}: Indicates whether the search input is currently focused.
 *
 * Actions:
 * - `setSearchTerm(val: string | ((prev: string) => string), search?: boolean): void`
 *   Updates the search term and optionally triggers a debounced search operation.
 * - `setSearchResults(val: Product[]): void`
 *   Sets the list of search results.
 * - `setLoading(val: boolean): void`
 *   Sets the loading status for the search operation.
 * - `setIsOpen(val: boolean): void`
 *   Toggles the open state of the search dropdown or modal.
 * - `setIsFocused(val: boolean): void`
 *   Updates the input focus status.
 * - `clearSearch(): void`
 *   Clears the search term, search results, and closes the dropdown or modal.
 * - `selectProduct(product: Product): void`
 *   Selects a product and adds it to the demande cart. Resets the search term and closes the dropdown or modal.
 */
export const useDemandeProductSearchStore = create<DemandeProductSearchState>()((set, get) => ({
  // initial state
  searchTerm: '',
  searchResults: [],
  loading: false,
  isOpen: false,
  isFocused: false,

  // actions
  setSearchTerm: (val: string | ((prev: string) => string), search = true) => {
    const prev = get().searchTerm;
    const next = typeof val === 'function' ? (val as (p: string) => string)(prev) : val;
    set({ searchTerm: next });
    if (search) demandDebouncedSearch(next);
  },
  setSearchResults: (val: Product[]) => set({ searchResults: val }),
  setLoading: (val: boolean) => set({ loading: val }),
  setIsOpen: (val: boolean) => set({ isOpen: val }),
  setIsFocused: (val: boolean) => set({ isFocused: val }),
  clearSearch: () => set({ searchTerm: '', searchResults: [], isOpen: false }),
  selectProduct: (product: Product) => {
    const { addToDemandeCart } = useDemandesStore.getState();
    addToDemandeCart(product);
    // Reset search without triggering another search
    useDemandeProductSearchStore.setState({ searchTerm: '', isOpen: false });
  },
}));

// Feedback tone for not-found cases (optional)
const playNotFoundSound = () => {
  try {
    if (typeof window === 'undefined') return;
    const AudioCtx = (window as any).AudioContext || (window as any).webkitAudioContext;
    if (!AudioCtx) return;

    const ctx = new AudioCtx();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.type = 'square';
    osc.frequency.setValueAtTime(440, ctx.currentTime);

    gain.gain.setValueAtTime(0.0001, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.01);

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.start();
    osc.frequency.exponentialRampToValueAtTime(220, ctx.currentTime + 0.15);
    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
    osc.stop(ctx.currentTime + 0.2);

    setTimeout(() => {
      try { ctx.close(); } catch {}
    }, 300);
  } catch (e) {
    // no-op
  }
};

// Debounced search using demandes store data
const demandDebouncedSearch = debounce((search: string) => {
  const { setLoading, setIsOpen, setSearchResults } = useDemandeProductSearchStore.getState();

  if (!search.trim()) {
    setSearchResults([]);
    setIsOpen(false);
    return;
  }

  setLoading(true);

  const { products, addToDemandeCart } = useDemandesStore.getState();
  const searchLower = search.toLowerCase();
  const results = products.filter((product) =>
    product.reference.toLowerCase().includes(searchLower) ||
    product.designation.toLowerCase().includes(searchLower)
  );

  setSearchResults(results);
  setIsOpen(results.length > 0);
  setLoading(false);

  if (results.length === 1) {
    // Auto add single result
    addToDemandeCart(results[0]);
    useDemandeProductSearchStore.setState({ searchTerm: '', isOpen: false });
  }
  if (results.length === 0) {
    playNotFoundSound();
    toast.warning('Aucun produit trouvé. Veuillez réessayer avec une autre référence ou désignation.');
  }
}, 300);
