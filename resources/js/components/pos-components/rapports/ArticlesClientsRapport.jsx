import React, { useMemo } from "react";
import ArticlesClientsTable from "./tables/ArticlesClientsTable.jsx";

const ArticlesClientsRapport = ({
    data,
    isLoading,
    onPrint,
    onRefresh,
    onBack
}) => {
    const acData = useMemo(() => data, [data]);

    return (
        <div className="card">
            <div className="card-header px-0 d-flex align-items-center">
                <button
                    className="btn btn-sm btn-outline-secondary me-2"
                    onClick={onBack}
                >
                    <i className="fas fa-chevron-left me-1"></i> Retour
                </button>
                <h5 className="mb-0">Rapport de vente par article et client</h5>
            </div>
            <div className="card-body">
                {isLoading ? (
                    <div className="text-center my-5">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                   <ArticlesClientsTable data={acData}/>
                )}
            </div>
        </div>
    );
};

export default React.memo(ArticlesClientsRapport);
