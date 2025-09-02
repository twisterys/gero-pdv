import React, {useEffect, useRef, useState} from 'react';
import axios from 'axios';

const emptyLine = () => ({ i_article_id: '', i_article_label: '', quantite_rebut: '' });

export const RebutModal = () => {
    const [open, setOpen] = useState(false);
    const [lines, setLines] = useState([emptyLine()]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const handlerShow = () => { if (__is_rebut == 1) setOpen(true); };
        const handlerHide = () => setOpen(false);
        document.addEventListener('show.bs.modal', (e) => {
            if (e.target && e.target.id === 'rebut-modal') handlerShow();
        });
        document.addEventListener('hide.bs.modal', (e) => {
            if (e.target && e.target.id === 'rebut-modal') handlerHide();
        });
        return () => {};
    }, []);

    const setLine = (idx, patch) => {
        setLines(prev => prev.map((l,i) => i===idx ? ({...l, ...patch}) : l));
    };

    const addLine = () => setLines(prev => [...prev, emptyLine()]);
    const removeLine = (idx) => setLines(prev => prev.filter((_,i) => i!==idx));

    // --- Article search behavior (like sale page input) ---
// javascript
    const searchArticle = (() => {
        const timerRef = { current: null };
        return async (idx, term) => {
            const t = (term || '').trim();
            setLine(idx, { i_article_label: term });

            if (timerRef.current) {
                clearTimeout(timerRef.current);
                timerRef.current = null;
            }

            if (t.length < 1) {
                setLine(idx, { __choices: undefined, i_article_id: '' });
                return;
            }

            timerRef.current = setTimeout(async () => {
                try {
                    if (!axios.defaults.baseURL && typeof __api_url !== 'undefined') {
                        axios.defaults.baseURL = __api_url;
                    }
                    const token = sessionStorage.getItem('access_token');
                    if (token) {
                        axios.defaults.headers.Authorization = 'Bearer ' + token;
                    }

                    // Utiliser l'endpoint de recherche fulltext
                    const res = await axios.get('articles-liste', { params: { search: t } });
                    const arr = res.data || [];

                    if (arr.length === 1) {
                        const a = arr[0];
                        setLine(idx, {
                            i_article_id: a.id,
                            i_article_label: a.designation || a.libelle || a.nom || a.reference || a.ref || ('#'+a.id),
                            __choices: undefined,
                        });
                        console.log('Résultat de la recherche article:', arr);
                    } else if (arr.length > 1) {
                        setLine(idx, { __choices: arr });
                    } else {
                        setLine(idx, { __choices: [], i_article_id: '' });
                        window.toastr && toastr.warning('Aucun article trouvé');
                    }
                } catch (e) {
                    window.toastr && toastr.error('Erreur lors de la recherche');
                } finally {
                    timerRef.current = null;
                }
            }, 300);
        };
    })();
    const chooseArticle = (idx, a) => {
        setLine(idx, {
            i_article_id: a.id,
            i_article_label: a.name
                ? `${a.name}${a.reference ? ' (' + a.reference + ')' : ''}`
                : (a.libelle || a.nom || a.reference || a.ref || ('#'+a.id)),
            __choices: undefined,
        });
    };

    const submit = async () => {
        const payload = {
            lignes: lines
                .map(l => ({ i_article_id: Number(l.i_article_id), quantite_rebut: Number(l.quantite_rebut) }))
                .filter(l => l.i_article_id && l.quantite_rebut > 0),
        };
        if (!payload.lignes.length) {
            window.toastr && toastr.warning('Ajoutez au moins une ligne valide');
            return;
        }
        setLoading(true);
        try {
            const res = await axios.post('rebut', { ...payload, session_id: __session_id })
            window.toastr && toastr.success((res.data && res.data.message) || 'Rebut ajouté avec succès !');
            setLines([emptyLine()]);
            const modal = document.getElementById('rebut-modal');
            if (modal) {
                const instance = window.bootstrap?.Modal?.getInstance(modal);
                instance?.hide();
            }
        } catch (e) {
            window.toastr && toastr.error('Erreur lors de la création du rebut');
        } finally {
            setLoading(false);
        }
    };

    if (!open) return (
        <div className="modal fade" id="rebut-modal" tabIndex="-1" aria-hidden="true">
            <div className="modal-dialog modal-lg"><div className="modal-content"/></div>
        </div>
    );

    return (
        <div className="modal fade show" id="rebut-modal" style={{display:'block'}} aria-modal="true">
            <div className="modal-dialog modal-dialog-centered modal-lg">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">Nouveau Rebut</h5>
                        <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"/>
                    </div>
                    <div className="modal-body">
                        <div className="container-fluid">
                            {lines.map((l, idx) => (
                                <div key={idx} className="row g-2 align-items-end mb-2">
                                    <div className="col-7">
                                        <label className="form-label">Article</label>
                                        <div className="position-relative">
                                            <input
                                                type="text"
                                                className="form-control"
                                                placeholder="Rechercher un article…"
                                                value={l.i_article_label}
                                                onChange={(e) => searchArticle(idx, e.target.value)}
                                            />
                                            {!!(l.__choices && l.__choices.length > 1) && (
                                                <div className="position-absolute bg-white border rounded w-100 mt-1" style={{zIndex: 1056}}>
                                                    <ul className="list-unstyled mb-0" style={{maxHeight: '200px', overflowY: 'auto'}}>
                                                        {l.__choices.map((a) => (
                                                            <li
                                                                key={a.id}
                                                                className="px-2 py-1 hover-bg-light cursor-pointer"
                                                                onClick={() => chooseArticle(idx, a)}
                                                            >
                                                                { (a.designation || a.libelle || a.nom || a.reference || a.ref) ? (
                                                                    a.name
                                                                        ? `${a.name}${a.reference ? ' (' + a.reference + ')' : ''}`
                                                                        : (a.libelle || a.nom || a.reference || a.ref)
                                                                ) : ('#' + a.id)
                                                                }
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    <div className="col-4">
                                        <label className="form-label">Quantité de rebut</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            className="form-control"
                                            value={l.quantite_rebut}
                                            onChange={(e) => setLine(idx, {quantite_rebut: e.target.value})}
                                        />
                                    </div>
                                    <div className="col-1 d-flex justify-content-end">
                                        <button className="btn btn-link text-danger" onClick={() => removeLine(idx)} title="Supprimer">
                                            <i className="fa fa-trash"/>
                                        </button>
                                    </div>
                                </div>
                            ))}
                            <button className="btn btn-light" onClick={addLine}>+ Ajouter une ligne</button>
                        </div>
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" className="btn btn-primary" onClick={submit} disabled={loading}>
                            {loading ? 'Enregistrement…' : 'Enregistrer'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
