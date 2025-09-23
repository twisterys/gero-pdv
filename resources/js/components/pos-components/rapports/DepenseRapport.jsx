import React, { useMemo } from "react";
import CreanceTable from "./tables/CreanceTable.jsx";
import DepenseTable from "./tables/depenseTable.jsx";

const DepenseRapport = ({
                            data,
                            isLoading,
                            onPrint,
                            onRefresh,
                            onBack
                        }) => {
    const depenseData = useMemo(() => data, [data]);

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
        <div className="card">
            <div className="card-header px-0 d-flex align-items-center">
                <button
                    className="btn btn-sm btn-outline-secondary me-2"
                    onClick={onBack}
                >
                    <i className="fas fa-chevron-left me-1"></i> Retour
                </button>
                <h5 className="mb-0">Rapport des dépenses</h5>
            </div>
            <div className="card-body">
                {isLoading ? (
                    <div className="text-center my-5">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                   <DepenseTable data={depenseData}  formatCurrency={formatCurrency}  />
                )}
            </div>
        </div>
    );
};

export default React.memo(DepenseRapport);
