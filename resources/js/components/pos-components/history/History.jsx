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
                <h5 className="m-0">Total Ventes: {total_ventes.toFixed(2)+" MAD"}</h5>
                <h5 className="m-0">Total Retours: {total_retours.toFixed(2)+" MAD"}</h5>
                <h5 className="m-0">Total Dépenses: {total_depenses.toFixed(2)+" MAD"}</h5>
                <h5 className="m-0">Total : {(total_ventes - total_retours - total_depenses).toFixed(2)+" MAD"}</h5>
            </>
        )

    };
    const fetchHistory = () => {
        if (isFetching) return;
        setIsFetching(true);
        let data = {
            session_id: __session_id,
        };
        axios.get("history", {params: data}).then((response) => {
            setVentes(response.data['ventes']);
            setRetours(response.data['retours']);
            setDepense(response.data['depenses']);
            console.log(response.data['depenses']) ;
            setIsFetching(false);
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
                    </div>
                    <div className="card shadow-none flex-grow-1 d-flex flex-column">
                        <div className="card-body flex-grow-1">
                            {isFetching ? (
                                <div className="d-flex align-items-center justify-content-center">
                                    <div class="spinner-border" role="status"></div>
                                </div>
                            ) : tab === 'ventes' ? (
                                <div className="row">{populateVentes()}</div>
                            ) : tab === 'depenses' ? (
                                <div className="row">{populateDepense()}</div>
                            ) : tab==='retours' ? (
                                <div className="row">{populateRetours()}</div>
                            ):''}
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
            <HistoryModal reduction={reduction} item={selectedHistory}/>
        </>
    );
};
