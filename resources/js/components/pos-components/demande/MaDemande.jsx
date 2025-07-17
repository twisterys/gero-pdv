import {useState} from "react";
import axios from "axios";

export const MaDemande = ({demande,setDemandeShow}) => {

    const [fetching, setFetching] = useState(false);

    const demandeLignes = () => {
        if (demande.lignes !== undefined){
            return demande.lignes.map((ligne, index) => {
                return (<tr key={ligne.id + '-' + demande.id + '+' + index}>
                    <td>{ligne.article} ({ligne.article_reference})</td>
                    <td>{+ligne.quantite_demande}</td>
                    <td>
                        {+ligne.quantite_livre}
                    </td>
                    <td>{+ligne.quantite_stock}</td>
                </tr>)
            })
        }

    }

    const annulerDemande = (id) => {
        axios
            .post("demande-transfert/" + id + "/annuler")
            .then((response) => {
                toastr.success("Demande annulée");
                setDemandeShow(0);
            })
            .catch((response) => {
                setDemandeShow(0);
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
    const accepterDemande = (id) => {
        axios
            .post("demande-transfert/" + id + "/accepter")
            .then((response) => {
                toastr.success("Demande acceptée");
                setDemandeShow(0);
            })
            .catch((response) => {
                setDemandeShow(0);
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
    return (
        <>
            {fetching ? (<tr>
                    <td colSpan={100} className="text-center">
                        <div className="spinner-border spinner-border-sm me-2" role="status"></div>
                    </td>
                </tr>)
                :
                <>
                    <div className="d-flex align-center">
                        <i className="fa fa-arrow-left" onClick={()=> setDemandeShow(0)}></i>
                        <h5 className="ms-2 mb-0">{demande.reference}</h5>
                    </div>
                    <hr/>
                   <div className="p-3 bg-body rounded shadow-sm">
                       <div className="row mb-4">
                           <div className=" col-sm-6   ">
                               <div className="rounded p-3 bg-white w-100 d-flex align-items-center">
                                   <div className="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                        style={{width: '48px'}}>
                                       <i className="fa fa-cash-register fa-2x"></i>
                                   </div>
                                   <div className="ms-3 ">
                                       <span className="font-weight-bolder font-size-sm">Demandé à</span>
                                       <p className="mb-0 h5 text-black text-capitalize">{demande.magasin_sortie}</p>
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
                       <table className="table table-striped table-bordered">
                           <thead>
                           <tr className="bg-primary" >
                               <td className="text-white" >Produit</td>
                               <td className="text-white">Quantité demandée</td>
                               <td className="text-white">Quantité livré</td>
                               <td className="text-white">Quantité actuelle de stock</td>
                           </tr>
                           </thead>
                           <tbody>
                           {demandeLignes()}
                           </tbody>
                       </table>
                       {demande.statut === 'Livrée'? <div className="d-flex align-items-center gap-2">
                           <button onClick={()=>annulerDemande(demande.id)} className="btn btn-soft-danger w-100  font-size-18" > <i className="fa fa-times me-2"></i> Annuler</button>
                           <button onClick={()=>accepterDemande(demande.id)} className="btn btn-soft-success  w-100 font-size-18" > <i className="fa fa-check me-2"></i> Accepter</button>
                       </div> : null}
                   </div>
                </>
            }
        </>
    )
}
