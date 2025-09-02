import React, { useEffect, useRef, useState, useMemo } from 'react';
import { api } from '../../services/api';

interface Article { id: number; libelle?: string; nom?: string; designation?: string; ref?: string; name?: string; reference?: string; }
interface Option { id: number; label: string; }

interface ArticleSelectProps {
    value: number | '';
    onChange: (id: number | '') => void;
    placeholder?: string;
    minChars?: number; // nombre de caractères avant de lancer la recherche
}

function useDebounce<T>(value: T, delay = 300): T {
    const [debounced, setDebounced] = useState(value);
    useEffect(() => {
        const h = setTimeout(() => setDebounced(value), delay);
        return () => clearTimeout(h);
    }, [value, delay]);
    return debounced;
}

const ArticleSelect: React.FC<ArticleSelectProps> = ({
                                                         value,
                                                         onChange,
                                                         placeholder = 'Rechercher un produit…',
                                                         minChars = 1
                                                     }) => {
    const [search, setSearch] = useState<string>('');
    const [options, setOptions] = useState<Option[]>([]);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [isTyping, setIsTyping] = useState(false);
    const [selected, setSelected] = useState<Option | null>(null);

    const containerRef = useRef<HTMLDivElement | null>(null);
    const inputRef = useRef<HTMLInputElement | null>(null);
    const debounced = useDebounce(search, 300);

    // Fermer au clic extérieur
    useEffect(() => {
        const handler = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    // Fetch uniquement quand on tape assez de caractères
    useEffect(() => {
        const fetch = async () => {
            const term = (debounced || '').trim();
            if (term.length < minChars) {
                setOptions([]);
                setOpen(false);
                return;
            }
            setLoading(true);
            try {
                // POS v1: GET /articles-liste
                const resp = await api.get('/articles-liste', { params: { search: term } });
                const items: Article[] = resp.data ?? [];
                const mapped: Option[] = items.map((a) => ({
                    id: a.id,
                    label: a.libelle || a.nom || a.designation || a.name || a.ref || a.reference || `#${a.id}`,
                }));
                setOptions(mapped);

                // n'afficher que si on tape, et s'il y a plus d'un résultat
                if (mapped.length > 1) {
                    setOpen(true);
                } else {
                    setOpen(false);
                }

                // Auto-sélection si une seule correspondance
                if (mapped.length === 1) {
                    handleSelect(mapped[0]);
                }
            } catch (e) {
                setOptions([]);
                setOpen(false);
            } finally {
                setLoading(false);
            }
        };
        fetch();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [debounced, minChars]);

    // Synchroniser la sélection quand parent passe une value.
    // Si la valeur n'est pas dans `options`, fetcher l'article par id pour afficher son label.
    useEffect(() => {
        let cancelled = false;
        const sync = async () => {
            if (value === '' || value == null) {
                setSelected(null);
                return;
            }
            const match = options.find(o => o.id === value);
            if (match) {
                setSelected(match);
                return;
            }
            // récupérer l'article par id
            setLoading(true);
            try {
                const resp = await api.get(`/articles/${value}`);
                const a: Article = resp.data;
                const label = a.libelle || a.nom || a.designation || a.name || a.ref || a.reference || `#${a.id}`;
                const opt: Option = { id: a.id, label };
                if (!cancelled) setSelected(opt);
            } catch (e) {
                // ignore
            } finally {
                if (!cancelled) setLoading(false);
            }
        };
        sync();
        return () => { cancelled = true; };
    }, [value, options]);

    const handleSelect = (opt: Option) => {
        onChange(opt.id);
        setSelected(opt);
        setOpen(false);
        setSearch('');
        setIsTyping(false);
    };

    const handleClear = () => {
        setSelected(null);
        setSearch('');
        onChange('');
        requestAnimationFrame(() => inputRef.current?.focus());
    };

    const displayValue = useMemo(() => {
        return isTyping ? search : (selected?.label ?? '');
    }, [isTyping, search, selected]);

    const onInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setIsTyping(true);
        setSearch(e.target.value);
    };

    const onInputFocus = () => {
        // Ne pas ouvrir la liste au focus; elle s'ouvrira seulement après recherche (si >1)
        if (isTyping && options.length > 1) {
            setOpen(true);
        }
    };

    const onKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        // Entrée => sélectionner le 1er résultat si la liste est présente
        if (e.key === 'Enter') {
            if (options.length >= 1) {
                handleSelect(options[0]);
            }
        }
        // Echap => fermer
        if (e.key === 'Escape') {
            setOpen(false);
        }
    };

    return (
        <div className="relative" ref={containerRef}>
            <div className="relative">
                <input
                    ref={inputRef}
                    type="text"
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary pr-8"
                    placeholder={placeholder}
                    value={displayValue}
                    onFocus={onInputFocus}
                    onChange={onInputChange}
                    onKeyDown={onKeyDown}
                />
                {selected && (
                    <button
                        type="button"
                        onClick={handleClear}
                        className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        title="Effacer la sélection"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                )}
            </div>

            {open && (
                <div className="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                    {loading && (
                        <div className="p-3 text-sm text-gray-500">Chargement…</div>
                    )}
                    {!loading && options.length === 0 && isTyping && search.trim().length >= minChars && (
                        <div className="p-3 text-sm text-gray-500">Aucun article trouvé</div>
                    )}
                    {options.length > 1 && (
                        <ul className="max-h-60 overflow-y-auto py-1">
                            {options.map((opt) => (
                                <li
                                    key={opt.id}
                                    className="px-3 py-2 cursor-pointer hover:bg-blue-50"
                                    onClick={() => handleSelect(opt)}
                                >
                                    {opt.label}
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            )}
        </div>
    );
};

export default ArticleSelect;
