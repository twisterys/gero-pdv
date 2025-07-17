import axios from "axios";
import {useEffect, useState} from "react";
import {MaDemande} from "./MaDemande.jsx";

export const MesDemandes = ({refresh}) => {
    const [demandes, setDemandes] = useState([]);
    const [fetching, setFetching] = useState(false);

    const [selectedDemande, setSelectedDemande] = useState(0);

    const [showDemande, setShowDemande] = useState(false);

    const fetchData = () => {
        setFetching(true)
        axios.get("mes-demandes").then((response) => {
            setDemandes(response.data);
            setFetching(false);
        });
    }

    useEffect(() => {
        if (!selectedDemande) fetchData()
    }, [selectedDemande,refresh])

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
                        <td>{demande.magasin_sortie}</td>
                        <td>{demande.statut}</td>
                        <td>
                            <button className="btn btn-sm btn-primary mx-1" onClick={() => setSelectedDemande(demande)}>
                                <i className="fa fa-eye"></i>
                            </button>
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
                showDemande ? <MaDemande demande={selectedDemande} setDemandeShow={setSelectedDemande}/> :
                    (<table className="table table-bordered table-striped">
                        <thead>
                        <tr className="bg-primary">
                            <th className="text-white">Référence</th>
                            <th className="text-white">Demandé à</th>
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
