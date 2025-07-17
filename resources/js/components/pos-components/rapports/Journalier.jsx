import React, { useState, useEffect, useMemo } from "react";
import axios from "axios";
import ArticlesClientsTable from "./tables/ArticlesClientsTable.jsx";
import ArticlesFournisseursTable from "./tables/ArticlesFournisseursTable.jsx";
import CreanceTable from "./tables/CreanceTable.jsx";
import TresorieTable from "./tables/TresorieTable.jsx";

const RapportJournalier = ({
                               onBack,
    onPrint
                           }) => {
    const [isLoading, setIsLoading] = useState(true);
    const [data, setData] = useState({
        articlesClients: { clients: [], articles: [], data: {} },
        articlesFournisseurs: { fournisseurs: [], articles: [], data: {} },
        creance: [],
        tresorie: {}
    });

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        setIsLoading(true);
        try {
            const [acResponse, afResponse, crResponse, trResponse] = await Promise.all([
                axios.post('articles-clients-rapport', { session_id: __session_id }),
                axios.post('articles-fournisseurs-rapport', { session_id: __session_id }),
                axios.post('creance-rapport', { session_id: __session_id }),
                axios.post('tresorie-rapport', { session_id: __session_id })
            ]);

            setData({
                articlesClients: acResponse.data,
                articlesFournisseurs: afResponse.data,
                creance: crResponse.data,
                tresorie: trResponse.data
            });
        } catch (error) {
            console.error("Error fetching rapport data:", error);
        } finally {
            setIsLoading(false);
        }
    };

    // Function to format date strings
    const formatDate = (dateStr) => {
        if (!dateStr) return "-";
        return dateStr;
    };

    // Function to format currency values
    const formatCurrency = (value) => {
        if (value === null || value === undefined) return "-";
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    };

    return (
        <div className="card ">
            <div className="card-header px-0 d-flex align-items-center">
                <button
                    className="btn btn-sm btn-outline-secondary me-2"
                    onClick={onBack}
                >
                    <i style={{fontFamily: 'Font Awesome 5 Free !important', fontWeight: 900, marginRight: '0.25rem'}}
                       className="fa-chevron-left"></i> Retour
                </button>
                <h5 className="mb-0">Rapport Journalier</h5>
            </div>
            <div className="card-body">
                {isLoading ? (
                    <div className="text-center my-5">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                    <div className="row" id="journlaier-tables">
                        {
                            __rapport_ac_enabled == 1 && (
                                <div className="col-12 mb-4">
                                    <h4>Rapport de vente par article et client</h4>
                                    <ArticlesClientsTable data={data.articlesClients}/>
                                </div>
                            )
                        }
                        {
                            __rapport_af_enabled == 1 && (
                                <div className="col-12 mb-4">
                                    <h4>Rapport Articles-Fournisseurs</h4>
                                    <ArticlesFournisseursTable data={data.articlesFournisseurs}/>
                                </div>
                            )
                        }
                        {
                            __rapport_cr_enabled == 1 && (
                                <div className="col-12 mb-4">
                                    <h4>Rapport des Paiements et Créances</h4>
                                    <CreanceTable
                                        data={data.creance}
                                        formatDate={formatDate}
                                        formatCurrency={formatCurrency}
                                    />
                                </div>
                            )
                        }
                        {
                            __rapport_tr_enabled == 1 && (
                                <div className="col-12 mb-4">
                                    <h4>Rapport de Trésorerie</h4>
                                    <TresorieTable
                                        data={data.tresorie}
                                        formatCurrency={formatCurrency}
                                    />
                                </div>
                            )
                        }





                    </div>
                )}
            </div>
        </div>
    );
};

export default React.memo(RapportJournalier);
