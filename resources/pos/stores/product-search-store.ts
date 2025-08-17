import { create } from 'zustand';
// @ts-ignore
import { debounce } from 'lodash';
import { usePOSStore, type Product } from "~/pos/pos-store";
import {toast} from "react-toastify";

interface ProductSearchState {
  // state
  searchTerm: string;
  searchResults: Product[];
  loading: boolean;
  isOpen: boolean;
  isFocused: boolean;

  // actions
  setSearchTerm: (val: string | ((prev: string) => string),search?:boolean) => void;
  setSearchResults: (val: Product[]) => void;
  setLoading: (val: boolean) => void;
  setIsOpen: (val: boolean) => void;
  setIsFocused: (val: boolean) => void;
  clearSearch: () => void;
  selectProduct: (product: Product) => void;
}

/**
 * A Zustand store for managing product search state and actions in an application.
 *
 * This store includes the state variables for handling product search functionality, such as the search term,
 * search results, loading status, and UI-related properties like open and focus states. It also provides
 * various actions for updating the state and handling operations like searching, clearing search results,
 * and selecting a product.
 *
 * State:
 * - `searchTerm` (string): The current search input value.
 * - `searchResults` (Product[]): The list of products matching the search term.
 * - `loading` (boolean): Indicates whether a search operation is in progress.
 * - `isOpen` (boolean): Determines if the search UI is open.
 * - `isFocused` (boolean): Tracks the focus state of the search input field.
 *
 * Actions:
 * - `setSearchTerm(val, search?)`: Updates the search input value and optionally triggers a debounced search.
 * - `setSearchResults(val)`: Sets the current search results to the given list of products.
 * - `setLoading(val)`: Updates the loading status.
 * - `setIsOpen(val)`: Updates the open state of the search UI.
 * - `setIsFocused(val)`: Updates the focus state of the search input.
 * - `clearSearch()`: Clears the search term, results, and resets the isOpen state to false.
 * - `selectProduct(product)`: Handles logic when a product is selected, adds the product to the cart, and resets the search state.
 */
export const useProductSearchStore = create<ProductSearchState>()((set, get) => ({
  // initial state
  searchTerm: '',
  searchResults: [],
  loading: false,
  isOpen: false,
  isFocused: false,

  // actions
  setSearchTerm: (val: string | ((prev: string) => string),search=true) => {
    const prev = get().searchTerm;
    const next = typeof val === 'function' ? (val as (p: string) => string)(prev) : val;
    set({ searchTerm: next });
    if(search)
    debouncedSearch(next);
  },
  setSearchResults: (val: Product[]) => set({ searchResults: val }),
  setLoading: (val: boolean) => set({ loading: val }),
  setIsOpen: (val: boolean) => set({ isOpen: val }),
  setIsFocused: (val: boolean) => set({ isFocused: val }),
  clearSearch: () => set({ searchTerm: '', searchResults: [], isOpen: false }),
  selectProduct: (product: Product) => {
    const { addToCart } = usePOSStore.getState();
    addToCart(product);
    // Reset search
    // Using setState directly to avoid triggering another debounced search
    useProductSearchStore.setState({ searchTerm: '', isOpen: false });
  },
}));

// Lightweight error tone (no external file) for "not found" cases
const playNotFoundSound = () => {
  try {
    if (typeof window === 'undefined') return;
    const AudioCtx = (window as any).AudioContext || (window as any).webkitAudioContext;
    if (!AudioCtx) return;

    const ctx = new AudioCtx();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.type = 'square';
    osc.frequency.setValueAtTime(440, ctx.currentTime); // start A4

    gain.gain.setValueAtTime(0.0001, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.01);

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.start();
    // quick descending blip
    osc.frequency.exponentialRampToValueAtTime(220, ctx.currentTime + 0.15);
    gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
    osc.stop(ctx.currentTime + 0.2);

    // Clean up context shortly after
    setTimeout(() => {
      try { ctx.close(); } catch {}
    }, 300);
  } catch (e) {
    // no-op: sound is non-critical
  }
};

// Debounced search runner living at module scope so it persists between component mounts
const debouncedSearch = debounce((search: string) => {
  const {
    setLoading,
    setIsOpen,
    setSearchResults,
  } = useProductSearchStore.getState();

  if (!search.trim()) {
    setSearchResults([]);
    setIsOpen(false);
    return;
  }

  setLoading(true);

  const { products, addToCart } = usePOSStore.getState();
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
    addToCart(results[0]);
    useProductSearchStore.setState({ searchTerm: '', isOpen: false });
  }
  if (results.length === 0) {
      playNotFoundSound();
      toast.warning("Aucun produit trouvé. Veuillez réessayer avec une autre référence ou désignation.")
  }
}, 300);
