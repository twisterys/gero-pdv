import axios from "axios";
import {useEffect, useState} from "react";
import {HistoryCard} from "./HistoryCard.jsx";
import {HistoryModal} from "./HistoryModal.jsx";
import {DepenseCard} from "../DepenseCard.jsx";

export const History = ({reduction}) => {
    const [ventes, setVentes] = useState([]);
    const [retours, setRetours] = useState([]);
    const [selectedHistory, setSelectedHistory] = useState(null);
    const [open, setOpen] = useState(false);
    const [depense, setDepense] = useState([]);
    const [tab, setTab] = useState('ventes');
    const [rebuts, setRebuts] = useState([]);

    const rollbackRebut = (id) => {
        openConfirm({
            title: 'Annulation du rebut',
            message: "Voulez-vous vraiment annuler ce rebut ? Cette action rétablira le stock.",
            confirmText: 'Oui, annuler',
            cancelText: 'Non',
            onConfirm: () => {
                axios
                    .post(`rebuts/${id}/rollback`, { session_id: __session_id })
                    .then(() => {
                        toastr.success('Rebut annulé avec succès');
                        fetchHistory();
                    })
                    .catch(err => {
                        const msg = err?.response?.data?.message || "Erreur lors de l'annulation";
                        toastr.error(msg);
                    })
                    .finally(() => {
                        closeConfirm();
                    });
            },
        });
    };

    const [confirmState, setConfirmState] = useState({
        title: 'Confirmation',
        message: '',
        confirmText: 'Confirmer',
        cancelText: 'Annuler',
        onConfirm: null,
    });

    const openConfirm = (opts = {}) => {
        setConfirmState(prev => ({ ...prev, ...opts }));
        setTimeout(() => { $("#pos-confirm-modal").modal("show"); }, 0);
    };

    const closeConfirm = () => {
        $("#pos-confirm-modal").modal("hide");
    };

    const populateVentes = () => {
        if (ventes.length > 0){
            return ventes.map((item) => {
                return <HistoryCard setHistory={setSelectedHistory} item={item}/>;
            });
        }else{
            return <h5 className="text-center">Aucun vente</h5>

        }
    };
    const populateRetours = () => {
       if (retours.length > 0 ){
           return retours.map((item) => {
               return <HistoryCard setHistory={setSelectedHistory} item={item}/>;
           });
       }else {
           return <h5 className="text-center">Aucun retour</h5>
       }
    };
    const populateDepense = () => {
        if (depense.length > 0){
            return depense.map((item) => {
                return <DepenseCard item={item}/>
            })
        }else {
            return <h5 className="text-center">Aucune dépense</h5>
        }
    }
    const [isFetching, setIsFetching] = useState(false);
    const total = () => {
        let total_ventes =  ventes.reduce(
            (sum, item) => +sum + parseInt(item.total_ttc, 10),
            0
        )
        let total_depenses = depense.reduce(
            (sum, item) => +sum + parseInt(item.total, 10),
            0
        );
        let total_retours = retours.reduce(
            (sum, item) => +sum + parseInt(item.total, 10),
            0
        )

        return (
            <>
                <h5 className="m-0">Total Ventes: {total_ventes.toFixed(3)+" MAD"}</h5>
                <h5 className="m-0">Total Retours: {total_retours.toFixed(3)+" MAD"}</h5>
                <h5 className="m-0">Total Dépenses: {total_depenses.toFixed(3)+" MAD"}</h5>
                <h5 className="m-0">Total : {(total_ventes - total_retours - total_depenses).toFixed(3)+" MAD"}</h5>
            </>
        )

    };
    const fetchHistory = () => {
        if (isFetching) return;
        setIsFetching(true);
        let data = {
            session_id: __session_id,
        };

        axios.get('history', { params: data }).then((response) => {
            setVentes(response.data['ventes']);
            setRetours(response.data['retours']);
            setDepense(response.data['depenses']);
            setIsFetching(false);
            if (__is_rebut == 1) {
                axios.get('rebuts', { params: { session_id: __session_id }})
                    .then(rr => setRebuts(rr.data || []))
                    .catch(() => setRebuts([]));
            }
        });
    };
    $(document).on('show.bs.offcanvas', '#offcanvasExample', function () {
            setOpen(true)
        }
    )
    $(document).on('hide.bs.offcanvas', '#offcanvasExample', function () {
        setOpen(false)

    })
    useEffect(() => {
        if (open) {
            fetchHistory();
        }
    }, [open])

    return (
        <>
            <div
                className="offcanvas offcanvas-end"
                tabindex="-1"
                id="offcanvasExample"
                aria-labelledby="offcanvasExampleLabel"
                style={{width: "800px"}}
            >
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasExampleLabel">
                        Historique
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="offcanvas-body d-flex flex-column">
                    <div className="d-flex">
                        <button
                            className={tab === 'ventes' ? 'btn btn-light me-2 mb-2 history-tab active' : 'btn btn-light me-2 mb-2  history-tab'}
                            onClick={() => setTab('ventes')}>Vente
                        </button>
                        <button
                            className={tab === 'retours' ? 'btn btn-light me-2 mb-2 history-tab active' : 'btn btn-light me-2 mb-2  history-tab'}
                            onClick={() => setTab('retours')}>Retours
                        </button>
                        <button
                            className={tab === 'depenses' ? 'btn btn-light me-2 mb-2 history-tab active' : 'btn btn-light me-2 mb-2  history-tab'}
                            onClick={() => setTab('depenses')}>Dépenses
                        </button>
                        {__is_rebut == 1 && (
                            <button
                                className={tab === 'rebuts' ? 'btn btn-light me-2 mb-2 history-tab active' : 'btn btn-light me-2 mb-2 history-tab'}
                                onClick={() => setTab('rebuts')}
                            >
                                Rebut
                            </button>
                        )}
                    </div>
                    <div className="card shadow-none flex-grow-1 d-flex flex-column">
                        <div className="card-body flex-grow-1">
                            {isFetching ? (
                                <div className="d-flex align-items-center justify-content-center">
                                    <div className="spinner-border" role="status"></div>
                                </div>
                            ) : tab === 'ventes' ? (
                                <div className="row">{populateVentes()}</div>
                            ) : tab === 'depenses' ? (
                                <div className="row">{populateDepense()}</div>
                            ) : tab === 'retours' ? (
                                <div className="row">{populateRetours()}</div>
                            ) : tab === 'rebuts' ? (
                                rebuts.length === 0 ? (
                                    <div className="row"><h5 className="text-center">Aucun rebut</h5></div>
                                ): (
                                    <div className="row">
                                        {rebuts.map((r) => (
                                            <div key={r.id} className="col-12 mb-2">
                                                <div className="card shadow-sm">
                                                    <div className="card-body">
                                                        <div className="d-flex align-items-center justify-content-between mb-2">
                                                            <div>
                                                                <div className="fw-semibold">{r.reference}</div>
                                                                {r.statut === 'Rebut annulé' && (
                                                                    <span className="badge bg-secondary">Annulé</span>
                                                                )}
                                                            </div>
                                                            <div>
                                                                {r.statut !== 'Rebut annulé' && (
                                                                    <button
                                                                        className="btn btn-sm btn-outline-danger"
                                                                        onClick={() => rollbackRebut(r.id)}
                                                                        title="Annuler le rebut"
                                                                    >
                                                                        <i className="mdi mdi-undo"></i> Annuler
                                                                    </button>
                                                                )}
                                                            </div>
                                                        </div>
                                                        <ul className="list-unstyled mb-0">
                                                            {(r.lignes || []).map((l, idx) => (
                                                                <li key={idx} className="d-flex justify-content-between border-top py-1">
                                                                    <span>{l.article}</span>
                                                                    <span className="fw-semibold">{l.quantity}</span>
                                                                </li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )
                            ) : null}
                        </div>
                        <div className="card-body border-top border-light flex-grow-0 d-flex align-baseline justify-content-between">
                             <div>
                                 {total()}
                             </div>
                            <p>Ouverture de session: {__ouverture}</p>

                        </div>
                    </div>
                </div>
            </div>
            <div className="modal fade" id="pos-confirm-modal" tabIndex="-1" aria-hidden="true">
                <div className="modal-dialog modal-dialog-centered">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">{confirmState.title}</h5>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <p className="mb-0">{confirmState.message}</p>
                        </div>
                        <div className="modal-footer d-flex justify-content-end">
                            <button type="button" className="btn btn-light" data-bs-dismiss="modal">
                                {confirmState.cancelText}
                            </button>
                            <button
                                type="button"
                                className="btn btn-danger"
                                onClick={() => {
                                    if (typeof confirmState.onConfirm === 'function') {
                                        confirmState.onConfirm();
                                    } else {
                                        closeConfirm();
                                    }
                                }}
                            >
                                <i className="mdi mdi-undo me-1"></i>
                                {confirmState.confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <HistoryModal reduction={reduction} item={selectedHistory}/>
        </>
    );
};
