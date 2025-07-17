import {useState} from "react";
import axios from "axios";

export const DemandeExterne = ({demande, setDemandeShow}) => {

    const [fetching, setFetching] = useState(false);
    const [demandeData, setDemandeData] = useState(demande);

    const demandeLignes = () => {
        if (demandeData.lignes !== undefined) {
            return demandeData.lignes.map((ligne, index) => {
                return (<tr key={ligne.id + '-' + demande.id + '+' + index}>
                    <td>{ligne.article} ({ligne.article_reference})</td>
                    <td>{+ligne.quantite_demande}</td>
                    <td>
                        {demande.statut === 'Nouvelle' ? <div className="input-group">
                            <button
                                className=" btn btn-danger input-group-text"
                                onClick={() =>
                                    setDemandeData({
                                        ...demandeData,
                                        lignes: demandeData.lignes.map(
                                            (e) => {
                                                if (e.id == ligne.id) {
                                                    return {
                                                        ...e,
                                                        quantite_livre:
                                                            +e.quantite_livre -
                                                            1,
                                                    };
                                                } else {
                                                    return e;
                                                }
                                            }
                                        ),
                                    })
                                }
                            >
                                -
                            </button>
                            <input
                                min={+ligne.quantite_demande}
                                max={+ligne.quantite_stock}
                                type="number"
                                value={+ligne.quantite_livre}
                                onInput={(event) => {
                                    setDemandeData({
                                        ...demandeData,
                                        lignes: demandeData.lignes.map(
                                            (e) => {
                                                if (e.id == ligne.id) {
                                                    return {
                                                        ...e,
                                                        quantite_livre:
                                                            +event.target
                                                                .value,
                                                    };
                                                } else {
                                                    return e;
                                                }
                                            }
                                        ),
                                    });
                                }}
                                className="form-control"
                            />
                            <button
                                className=" btn btn-success input-group-text"
                                onClick={() =>
                                    setDemandeData({
                                        ...demandeData,
                                        lignes: demandeData.lignes.map(
                                            (e) => {
                                                if (e.id == ligne.id) {
                                                    return {
                                                        ...e,
                                                        quantite_livre:
                                                            +e.quantite_livre +
                                                            1,
                                                    };
                                                } else {
                                                    return e;
                                                }
                                            }
                                        ),
                                    })
                                }
                            >
                                +
                            </button>
                        </div> : +ligne.quantite_livre}

                    </td>
                    <td>{+ligne.quantite_stock}</td>
                </tr>)
            })
        }

    }

    const refuseDemande = (id) => {
        axios
            .post("demande-transfert/" + id + "/refuser")
            .then((response) => {
                toastr.success("Demande refusée");
                setDemandeShow(0);
            })
            .catch((response) => {
                setIsLoading(false);
                populateData();
                Swal.fire({
                    icon: "error",
                    title: "Erreur !",
                    text: "Vuillez ressayer plus tard",
                    confirmButtonText: "Ok !",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-lg btn-soft-danger mx-2",
                    },
                });
            });
    };
    const livrerDemande = (id) => {
        axios
            .post("demande-transfert/" + id + "/livrer", demandeData)
            .then((response) => {
                setDemandeShow(0);
                toastr.success(response.data);
            })
            .catch((response) => {
                toastr.error("Vuillez ressayer plus tard");
            });
    };


    return (
        <>
            <div className="d-flex align-center">
                <i className="fa fa-arrow-left" onClick={() => setDemandeShow(0)}></i>
                <h5 className="ms-2 mb-0">{demande.reference}</h5>
            </div>
            <hr/>
            <div className="bg-body p-3 rounded shadow-sm">
                <div className="row mb-4">
                    <div className=" col-sm-6   ">
                        <div className="rounded p-3 bg-white w-100 d-flex align-items-center">
                            <div className="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style={{width: '48px'}}>
                                <i className="fa fa-cash-register fa-2x"></i>
                            </div>
                            <div className="ms-3 ">
                                <span className="font-weight-bolder font-size-sm">Demandé par</span>
                                <p className="mb-0 h5 text-black text-capitalize">{demande.magasin_entree}</p>
                            </div>
                        </div>
                    </div>
                    <div className=" col-sm-6   ">
                        <div className="rounded p-3 bg-white w-100 d-flex align-items-center">
                            <div
                                className="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                style={{width: '48px'}}>
                                <i className="fa fa-star fa-2x"></i>
                            </div>
                            <div className="ms-3 ">
                                <span className="font-weight-bolder font-size-sm">Statut</span>
                                <p className="mb-0 h5 text-black text-capitalize">{demande.statut}</p>
                            </div>
                        </div>
                    </div>

                </div>
                <table className="table table-striped table-bordered border-primary">
                    <thead>
                    <tr className="bg-primary">
                        <td className="text-white">Produit</td>
                        <td className="text-white">Quantité demandée</td>
                        <td className="text-white">Quantité livré</td>
                        <td className="text-white">Quantité actuelle de stock</td>
                    </tr>
                    </thead>
                    <tbody>
                    {demandeLignes()}
                    </tbody>
                </table>
                {demande.statut === 'Nouvelle' ? <div className="d-flex align-items-center gap-2">
                    <button onClick={() => refuseDemande(demandeData.id)} className="btn btn-soft-danger w-100 font-size-18"><i
                        className="fa fa-times me-2"></i> Refuser
                    </button>
                    <button onClick={() => livrerDemande(demandeData.id)} className="btn btn-soft-success w-100  font-size-18"><i
                        className="fa fa-check me-2"></i> Livrer
                    </button>
                </div> : null}

            </div>

        </>
    )
}
