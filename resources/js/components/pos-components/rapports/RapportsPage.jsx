import React, { useState, useCallback, useMemo } from "react";
import axios from "axios";
import toast from "bootstrap/js/src/toast.js";
import ArticlesClientsRapport from "./ArticlesClientsRapport";
import ArticlesStockRapport from "./ArticlesStockRapport";
import ArticlesFournisseursRapport from "./ArticlesFournisseursRapport";
import CreanceRapport from "./CreanceRapport";
import TresorieRapport from "./TresorieRapport";
import RapportJournalier from "./Journalier.jsx";
import DepenseRapport from "./DepenseRapport.jsx";

const RapportsPage = () => {
    const [activeRapport, setActiveRapport] = useState(null);
    const [rapportsData, setRapportsData] = useState({
        ac: { clients: [], articles: [], data: {}, isLoading: false },
        as: { data: [], isLoading: false },
        af: { fournisseurs: [], articles: [], data: {}, isLoading: false },
        cr: { data: [], isLoading: false },
        tr: { data: {}, isLoading: false },
        dp:{data:[],isLoading:false},
    });

    // Memoized data access helpers
    const acData = useMemo(() => rapportsData.ac, [rapportsData.ac]);
    const asData = useMemo(() => rapportsData.as.data, [rapportsData.as.data]);
    const afData = useMemo(() => rapportsData.af, [rapportsData.af]);
    const crData = useMemo(() => rapportsData.cr.data, [rapportsData.cr.data]);
    const trData = useMemo(() => rapportsData.tr.data, [rapportsData.tr.data]);
    const dpData = useMemo(() => rapportsData.dp.data, [rapportsData.dp.data]);
    const isLoadingAc = useMemo(() => rapportsData.ac.isLoading, [rapportsData.ac.isLoading]);
    const isLoadingAs = useMemo(() => rapportsData.as.isLoading, [rapportsData.as.isLoading]);
    const isLoadingAf = useMemo(() => rapportsData.af.isLoading, [rapportsData.af.isLoading]);
    const isLoadingCr = useMemo(() => rapportsData.cr.isLoading, [rapportsData.cr.isLoading]);
    const isLoadingTr = useMemo(() => rapportsData.tr.isLoading, [rapportsData.tr.isLoading]);
    const isLoadingDp = useMemo(() => rapportsData.dp.isLoading, [rapportsData.dp.isLoading]);

    // Generic fetch function to reduce duplication
    const fetchData = useCallback((type) => {
        let endpoint;
        let isLoading;

        if (type === 'ac') {
            endpoint = 'articles-clients-rapport';
            isLoading = isLoadingAc;
        } else if (type === 'af') {
            endpoint = 'articles-fournisseurs-rapport';
            isLoading = isLoadingAf;
        } else if (type === 'cr') {
            endpoint = 'creance-rapport';
            isLoading = isLoadingCr;
        } else if (type === 'tr') {
            endpoint = 'tresorie-rapport';
            isLoading = isLoadingTr;
        } else if(type === 'dp'){
            endpoint = 'depenses-rapport';
            isLoading = isLoadingDp;
        }else {
            endpoint = 'articles-stock-rapport';
            isLoading = isLoadingAs;
        }

        if (isLoading) return;

        setRapportsData(prev => ({
            ...prev,
            [type]: { ...prev[type], isLoading: true }
        }));

        axios.post(endpoint, { session_id: __session_id })
            .then((res) => {
                setRapportsData(prev => ({
                    ...prev,
                    [type]: {
                        ...(type === 'ac' ? res.data : { data: res.data }),
                        isLoading: false
                    }
                }));
            })
            .catch((err) => {
                setRapportsData(prev => ({
                    ...prev,
                    [type]: { ...prev[type], isLoading: false }
                }));
                toast.show(err.response?.data?.message || 'Error loading data');
            });
    }, [isLoadingAc, isLoadingAs, isLoadingAf, isLoadingCr]);

    // Specific fetch functions
    const fetchAcData = useCallback(() => fetchData('ac'), [fetchData]);
    const fetchAsData = useCallback(() => fetchData('as'), [fetchData]);
    const fetchAfData = useCallback(() => fetchData('af'), [fetchData]);
    const fetchCrData = useCallback(() => fetchData('cr'), [fetchData]);
    const fetchTrData = useCallback(() => fetchData('tr'), [fetchData]);
    const fetchDataJr = useCallback(() => fetchData('jr'), [fetchData]);
    const fetchDpData = useCallback(() => fetchData('dp'), [fetchData]);

    // Generic print handler
    const handlePrint = useCallback((containerId, title) => {
        const printContents = document.getElementById(containerId).innerHTML;
        const printWindow = window.open('', '_blank');

        if (!printWindow) {
            toast.show('Please allow pop-ups for printing');
            return;
        }

        printWindow.document.write(`
            <html>
                <head>
                    <title>${title}</title>
                    <style>
                        @page { size: A4; margin: 1cm; }
                        @media print {
                            body { margin: 0; padding: 0; font-size: 12px; }
                            .table { width: 100%; border-collapse: collapse; font-size: 10px; }
                            .table th, .table td { border: 1px solid #dee2e6; padding: 4px; text-align: center; }
                            .cell-content { page-break-inside: avoid; }
                            .print-header { position: relative; text-align: center; margin-bottom: 20px; padding: 10px; }
                            .print-header h1 { font-size: 16px; margin: 0; }
                            .print-header .print-date { font-size: 12px; color: #666; }
                            .print-meta { position: absolute; right: 10px; top: 10px; text-align: right; font-size: 12px; color: #333; }
                            .print-meta .line { white-space: nowrap; }
                            .page-break { page-break-after: always; }
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <div class="print-meta">
                            <div class="line"><strong>Magasin:</strong> ${__magasin_ref}</div>
                            <div class="line"><strong>Vendeur:</strong> ${__vendeur_nom}</div>
                        </div>
                        <h1>${title}</h1>
                        <div class="print-date">
                            Date d'impression: ${new Date().toLocaleString('fr-FR')}
                        </div>
                    </div>
                    ${printContents}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();

        // Wait for images to load before printing
        printWindow.onload = function () {
            printWindow.print();
            printWindow.close();
        };
    }, []);

    // Specific print handlers
    const handlePrintAc = useCallback(() => {
        handlePrint('rapport-table-container-ac', 'Rapport de vente par article et client');
    }, [handlePrint]);

    const handlePrintAs = useCallback(() => {
        handlePrint('rapport-table-container-as', 'Rapport Articles Stock');
    }, [handlePrint]);

    const handlePrintAf = useCallback(() => {
        handlePrint('rapport-table-container-af', 'Rapport Articles-Fournisseurs');
    }, [handlePrint]);

    const handlePrintCr = useCallback(() => {
        handlePrint('rapport-table-container-cr', 'Rapport des Créances');
    }, [handlePrint]);

    const handlePrintTr = useCallback(() => {
        handlePrint('rapport-table-container-tr', 'Rapport de Trésorerie');
    }, [handlePrint]);

    const handlePrintDp = useCallback(()=>{
        handlePrint('rapport-table-container-dp','Rapport de Dépenses')
    })

    // Open a specific rapport and load data if needed
    const openRapport = useCallback((rapportType) => {
        setActiveRapport(rapportType);
        if (rapportType === 'ac') {
            fetchAcData();
        } else if (rapportType === 'as') {
            fetchAsData();
        } else if (rapportType === 'af') {
            fetchAfData();
        } else if (rapportType === 'cr') {
            fetchCrData();
        } else if (rapportType === 'tr') {
            fetchTrData();
        }else if(rapportType ==='dp'){
            fetchDpData();
        }
        else if (rapportType === 'jr') {
            fetchAsData();
            fetchAfData();
            fetchCrData();
            fetchTrData();
            fetchDpData();
        }
    }, [fetchAcData, fetchAsData, fetchAfData, fetchCrData, fetchTrData,fetchDpData]);

    // Memoized number formatter
    const formatNumber = useMemo(() => (
        (num) => new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num)
    ), []);

    return (
        <>
            <button
                className="btn btn-soft-primary mx-1"
                data-bs-target="#rapports-modal"
                data-bs-toggle="modal">
                <i className="fa fa-file-alt"> Rapports</i>
            </button>
            <div
                className="modal fade"
                id="rapports-modal"
                tabIndex="-1"
                aria-hidden="true"
            >
                <div className="modal-dialog modal-fullscreen">
                    <div className="modal-content bg-body">
                        <div className="modal-header border-0 bg-body">
                            <div className="d-flex align-items-center">
                                <button type="button"
                                        data-bs-dismiss="modal"
                                        className="text-primary h3 me-3 mb-0 border-0 bg-transparent">
                                    <i className="fa fa-arrow-left"></i>
                                </button>
                                <h3 className="modal-title mb-0">Rapports</h3>
                            </div>
                            <div className="ms-auto">
                                {activeRapport === 'ac' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintAc}
                                            disabled={isLoadingAc}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchAcData}
                                            disabled={isLoadingAc}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'as' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintAs}
                                            disabled={isLoadingAs}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchAsData}
                                            disabled={isLoadingAs}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'af' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintAf}
                                            disabled={isLoadingAf}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchAfData}
                                            disabled={isLoadingAf}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'cr' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintCr}
                                            disabled={isLoadingCr}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchCrData}
                                            disabled={isLoadingCr}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'tr' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintTr}
                                            disabled={isLoadingTr}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchTrData}
                                            disabled={isLoadingTr}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'jr' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={()=>handlePrint('journlaier-tables','Rapport Journalier')}
                                            disabled={isLoadingTr}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchDataJr}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                                {activeRapport === 'dp' && (
                                    <>
                                        <button
                                            className="btn btn-success me-2"
                                            onClick={handlePrintDp}
                                            disabled={isLoadingDp}
                                        >
                                            <i className="fas fa-print me-1"></i>
                                            Imprimer
                                        </button>
                                        <button
                                            className="btn btn-primary me-2"
                                            onClick={fetchDpData}
                                            disabled={isLoadingDp}
                                        >
                                            <i className="fas fa-sync-alt me-1"></i>
                                            Actualiser
                                        </button>
                                    </>
                                )}
                            </div>
                        </div>
                        <div className="modal-body p-4">
                            <div className="container-fluid px-0">
                                {!activeRapport && (
                                    <div className="row">
                                        {__rapport_as_enabled == 1 && (
                                            <div className="col-md-6 mb-4">
                                                <div className="card card-rapport h-100"
                                                     onClick={() => openRapport('as')}>
                                                    <div className="card-body d-flex flex-column">
                                                        <h5 className="card-title">Rapport Stock</h5>
                                                        <p className="card-text flex-grow-1">
                                                            Affiche un rapport détaillé du stock disponible pour chaque
                                                            article.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                        <div className="col-12">
                                            <h4>Rapports journalier</h4>
                                            <hr className="border border-primary"/>
                                        </div>
                                        {__rapport_ac_enabled == 1 && (
                                            <div className="col-md-6 mb-4">
                                                <div className="card card-rapport h-100" onClick={() => openRapport('ac')}>
                                                    <div className="card-body d-flex flex-column">
                                                        <h5 className="card-title">Rapport de vente par article et client</h5>
                                                        <p className="card-text flex-grow-1">
                                                            Affiche un rapport détaillé des articles achetés par chaque
                                                            client.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                        {__rapport_af_enabled == 1 && (
                                            <div className="col-md-6 mb-4">
                                                <div className="card card-rapport h-100" onClick={() => openRapport('af')}>
                                                    <div className="card-body d-flex flex-column">
                                                        <h5 className="card-title">Rapport Articles-Fournisseurs</h5>
                                                        <p className="card-text flex-grow-1">
                                                            Affiche un rapport détaillé des articles achetés auprès de chaque
                                                            fournisseur.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                        {
                                            __rapport_cr_enabled == 1 && (
                                                <div className="col-md-6 mb-4">
                                                    <div className="card card-rapport h-100" onClick={() => openRapport('cr')}>
                                                        <div className="card-body d-flex flex-column">
                                                            <h5 className="card-title">Rapport des Paiements et Créances</h5>
                                                            <p className="card-text flex-grow-1">
                                                                Affiche un rapport détaillé des ventes non payées ou partiellement payées.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        }

                                        {
                                            __rapport_tr_enabled == 1 && (
                                                <div className="col-md-6 mb-4">
                                                    <div className="card card-rapport h-100" onClick={() => openRapport('tr')}>
                                                        <div className="card-body d-flex flex-column">
                                                            <h5 className="card-title">Rapport de Trésorerie</h5>
                                                            <p className="card-text flex-grow-1">
                                                                Affiche un résumé de la trésorerie de la session: ventes, espèces, chèques, LCN, dépenses et reste en caisse.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        }
                                        {
                                            __rapport_depense_enabled && (
                                                <div className="col-md-6 mb-4">
                                                    <div className="card card-rapport h-100" onClick={() => openRapport('dp')}>
                                                        <div className="card-body d-flex flex-column">
                                                            <h5 className="card-title">Rapport de Dépenses</h5>
                                                            <p className="card-text flex-grow-1">
                                                                Afficher total des Dépenses par catégorie.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        }
                                        {
                                            (__rapport_tr_enabled ||__rapport_cr_enabled || __rapport_af_enabled || __rapport_depense_enabled || __rapport_ac_enabled) == 1 && (
                                                <div className="col-md-6 mb-4">
                                                    <div className="card card-rapport h-100" onClick={() => openRapport('jr')}>
                                                        <div className="card-body d-flex flex-column">
                                                            <h5 className="card-title">Rapport Journalier</h5>
                                                            <p className="card-text flex-grow-1">
                                                                Afficher tous les rapports journalier.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        }

                                    </div>
                                )}

                                {activeRapport === 'ac' && (
                                    <ArticlesClientsRapport
                                        data={acData}
                                        isLoading={isLoadingAc}
                                        onPrint={handlePrintAc}
                                        onRefresh={fetchAcData}
                                        onBack={() => setActiveRapport(null)}
                                    />
                                )}

                                {activeRapport === 'as' && (
                                    <ArticlesStockRapport
                                        data={asData}
                                        isLoading={isLoadingAs}
                                        onPrint={handlePrintAs}
                                        onRefresh={fetchAsData}
                                        onBack={() => setActiveRapport(null)}
                                    />
                                )}

                                {activeRapport === 'af' && (
                                    <ArticlesFournisseursRapport
                                        data={afData}
                                        isLoading={isLoadingAf}
                                        onPrint={handlePrintAf}
                                        onRefresh={fetchAfData}
                                        onBack={() => setActiveRapport(null)}
                                    />
                                )}

                                {activeRapport === 'cr' && (
                                    <CreanceRapport
                                        data={crData}
                                        isLoading={isLoadingCr}
                                        onPrint={handlePrintCr}
                                        onRefresh={fetchCrData}
                                        onBack={() => setActiveRapport(null)}
                                    />
                                )}

                                {activeRapport === 'tr' && (
                                    <TresorieRapport
                                        data={trData}
                                        isLoading={isLoadingTr}
                                        onPrint={handlePrintTr}
                                        onRefresh={fetchTrData}
                                        onBack={() => setActiveRapport(null)}
                                    />
                                )}
                                {
                                    activeRapport === 'dp' && (
                                        <DepenseRapport
                                            data={dpData}
                                            isLoading={isLoadingDp}
                                            onPrint={handlePrintDp}
                                            onRefresh={fetchDpData}
                                            onBack={() => setActiveRapport(null)}
                                        />
                                    )
                                }
                                {
                                    activeRapport === 'jr' && (
                                        <RapportJournalier
                                            onBack={() => setActiveRapport(null)}
                                            onPrint={handlePrint}
                                        />
                                    )
                                }

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                {`
                    .card-rapport{
                        cursor: pointer;
                        transition: background-color 0.2s ease;
                    }
                    .card-rapport:hover{
                        background-color: #f8f9fa;
                    }
                    #rapports-modal .modal-fullscreen {
                        padding: 0 !important;
                    }

                    #rapports-modal .modal-fullscreen .modal-content {
                        min-height: 100vh;
                    }

                    #rapports-modal .sticky-col {
                        position: sticky;
                        left: 0;
                        z-index: 1;
                    }

                    #rapports-modal .cell-content {
                        padding: 5px;
                    }

                    #rapports-modal .cell-content .quantity {
                        font-weight: 500;
                        color: #495057;
                    }

                    #rapports-modal .cell-content .amount {
                        color: #6c757d;
                        font-size: 0.9em;
                    }

                    #rapport-table-container-ac .table th,
                    #rapport-table-container-as .table th,
                    #rapport-table-container-af .table th,
                    #rapport-table-container-cr .table th,
                    #rapport-table-container-tr .table th {
                        background-color: #f8f9fa;
                        white-space: nowrap;
                    }

                    #rapport-table-container-ac .table td,
                    #rapport-table-container-as .table td,
                    #rapport-table-container-af .table td,
                    #rapport-table-container-cr .table td,
                    #rapport-table-container-tr .table td {
                        vertical-align: middle;
                    }

                    #rapports-modal .modal-header {
                        background-color: #fff;
                        position: sticky;
                        top: 0;
                        z-index: 1050;
                    }
                `}
            </style>
        </>
    );
};

export default React.memo(RapportsPage);
