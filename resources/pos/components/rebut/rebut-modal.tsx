import React, { useEffect, useState } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { useRebutStore } from '../../stores/rebut-store';
import { endpoints } from '../../services/api';
import { Controller } from 'react-hook-form';
import ArticleSelect from './article-select';

interface RebutModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: (text: string) => void;
}

interface ArticleOption { id: number; libelle: string; }
interface FormLine { i_article_id: number | ''; i_article?: string; quantite_rebut: number | ''; }
interface FormData { lignes: FormLine[] }

const RebutModal: React.FC<RebutModalProps> = ({ isOpen, onClose, onSuccess }) => {
    const { createRebut, isLoading, error } = useRebutStore();
    const { control, register, handleSubmit, reset, formState: { errors } } = useForm<FormData>({
        defaultValues: { lignes: [{ i_article_id: '', quantite_rebut: '' }] }
    });
    const { fields, append, remove } = useFieldArray({ control, name: 'lignes' });

    const [articles, setArticles] = useState<ArticleOption[]>([]);
    const [page, setPage] = useState(1);

    useEffect(() => {
        if (!isOpen) return;
        reset({ lignes: [{ i_article_id: '', quantite_rebut: '' }] });
        setArticles([]);
        setPage(1);
        // Chargement simple paginé (MVP)
        endpoints.products.getAll(1).then(res => {
            const items = (res.data?.data ?? res.data ?? []) as any[];
            setArticles(items.map(a => ({ id: a.id, libelle: a.name })));
        }).catch(() => {});
    }, [isOpen, reset]);

    const onSubmit = async (data: FormData) => {
        const lignes = (data.lignes || []).map(l => ({
            i_article_id: Number(l.i_article_id),
            quantite_rebut: Number(l.quantite_rebut),
        })).filter(l => l.i_article_id && l.quantite_rebut > 0);

        if (!lignes.length) return;

        try {
            const res = await createRebut({ lignes });
            onSuccess?.(res.data?.message || 'Rebut ajouté avec succès !');
            reset();
            setTimeout(() => onClose(), 500);
        } catch (e) {
            // handled via error state or toast
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div className="w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-xl font-semibold text-gray-800">Nouveau Rebut</h2>
                    <button onClick={onClose} className="text-gray-500 hover:text-gray-700">✕</button>
                </div>

                {error && <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md">{error}</div>}

                <form onSubmit={handleSubmit(onSubmit)}>
                    <div className="space-y-4">
                        {fields.map((field, index) => (
                            <div key={field.id} className="grid grid-cols-12 gap-3 items-end">
                                <div className="col-span-7">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Article</label>
                                    <Controller
                                        name={`lignes.${index}.i_article_id` as const}
                                        control={control}
                                        rules={{ required: 'Article requis' }}
                                        render={({ field }) => (
                                            <ArticleSelect
                                                value={field.value ?? ''}
                                                onChange={field.onChange}
                                                placeholder="Rechercher un article…"
                                                minChars={1}
                                            />
                                        )}
                                    />
                                    {errors?.lignes?.[index]?.i_article_id && (
                                        <p className="text-red-500 text-xs mt-1">
                                            {String(errors.lignes[index]?.i_article_id?.message)}
                                        </p>
                                    )}
                                </div>                                <div className="col-span-4">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Quantité de rebut</label>
                                    <input
                                        type="number"
                                        step="0.001"
                                        className={`w-full px-3 py-2 border ${errors?.lignes?.[index]?.quantite_rebut ? 'border-red-500' : 'border-gray-300'} rounded-md`}
                                        {...register(`lignes.${index}.quantite_rebut` as const, {
                                            required: 'Quantité requise',
                                            valueAsNumber: true,
                                            validate: v => (Number(v) > 0) || 'Doit être > 0'
                                        })}
                                    />
                                    {errors?.lignes?.[index]?.quantite_rebut && (
                                        <p className="text-red-500 text-xs mt-1">{String(errors.lignes[index]?.quantite_rebut?.message)}</p>
                                    )}
                                </div>
                                <div className="col-span-1 flex justify-end">
                                    <button type="button" onClick={() => remove(index)} className="text-red-600 hover:text-red-800">🗑</button>
                                </div>
                            </div>
                        ))}
                        <div>
                            <button type="button" onClick={() => append({ i_article_id: '', quantite_rebut: '' })} className="px-3 py-2 border rounded-md">
                                + Ajouter une ligne
                            </button>
                        </div>
                    </div>

                    <div className="flex justify-end space-x-3 mt-6">
                        <button type="button" onClick={onClose} className="px-4 py-2 border rounded-md">Annuler</button>
                        <button type="submit" disabled={isLoading} className="px-4 py-2 bg-primary text-white rounded-md disabled:opacity-50">
                            {isLoading ? 'Enregistrement…' : 'Enregistrer'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RebutModal;
