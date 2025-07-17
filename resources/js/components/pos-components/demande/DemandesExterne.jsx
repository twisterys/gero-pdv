import axios from "axios";
import {useEffect, useState} from "react";
import {DemandeExterne} from "./DemandeExterne.jsx";

export const DemandesExterne = ({refresh}) => {
    const [demandes, setDemandes] = useState([]);
    const [fetching, setFetching] = useState(false);

    const [selectedDemande, setSelectedDemande] = useState(0);

    const [showDemande, setShowDemande] = useState(false);

    const fetchData = () => {
        setFetching(true)
        axios.get("demandes-externe").then((response) => {
            setDemandes(response.data);
            setFetching(false);
        });
    }
    const printElm = (id) =>{
        axios.get("demandes-externe-print/"+id).then((response)=>{
            if (document.getElementById("iframe")) {
                document.getElementById("iframe").remove();
            }
            let iframe = document.createElement("iframe");
            iframe.style.display = "none";
            iframe.id = "iframe";
            iframe.srcdoc = response.data;
            document.body.append(iframe);
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        })
    }

    useEffect(() => {
        if (!showDemande) fetchData();
    }, [showDemande,refresh])

    useEffect(() => {
        if (selectedDemande) {
            setShowDemande(true)
        } else {
            setShowDemande(false)
        }
    }, [selectedDemande])

    const demandesList = () => {
        if (demandes.length > 0)
            return demandes.map((demande, index) => {
                return (
                    <tr key={'mes-demandes-' + index}>
                        <td>{demande.reference}</td>
                        <td>{demande.magasin_entree}</td>
                        <td>{demande.statut}</td>
                        <td>
                            <button className="btn btn-sm btn-primary mx-1" onClick={() => setSelectedDemande(demande)}>
                                <i className="fa fa-eye"></i>
                            </button>
                            {demande.statut === 'Livrée' ? <button onClick={()=>printElm(demande.id)} className="btn btn-sm btn-success mx-1" >
                                <i className="fa fa-print"></i>
                            </button> : null    }
                        </td>
                    </tr>
                )
            })
        else
            return (<tr>
                <td colSpan="100" className="text-center">Aucune demande</td>
            </tr>)
    }
    return (
        <>
            {
                showDemande ? <DemandeExterne demande={selectedDemande} setDemandeShow={setSelectedDemande}/> :
                    (<table className="table table-bordered table-striped">
                        <thead>
                        <tr className="bg-primary">
                            <th className="text-white">Référence</th>
                            <th className="text-white">Demandé par</th>
                            <th className="text-white">Status</th>
                            <th className="text-white">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        {fetching ? (<tr>
                                <td colSpan={100} className="text-center">
                                    <div className="spinner-border spinner-border-sm me-2" role="status"></div>
                                </td>
                            </tr>)
                            :
                            demandesList()
                        }
                        </tbody>
                    </table>)
            }

        </>
    );
}
