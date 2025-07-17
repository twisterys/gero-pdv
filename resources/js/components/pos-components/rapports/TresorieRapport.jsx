import React, { useMemo } from "react";
import TresorieTable from "./tables/TresorieTable.jsx";

const TresorieRapport = ({
    data,
    isLoading,
    onPrint,
    onRefresh,
    onBack
}) => {
    const tresorieData = useMemo(() => data, [data]);

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
                    <i style={{ fontFamily: 'Font Awesome 5 Free !important', fontWeight: 900, marginRight: '0.25rem' }} className="fa-chevron-left"></i> Retour
                </button>
                <h5 className="mb-0">Rapport de Trésorerie</h5>
            </div>
            <div className="card-body">
                {isLoading ? (
                    <div className="text-center my-5">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                   <TresorieTable data={tresorieData} formatCurrency={formatCurrency} />
                )}
            </div>
        </div>
    );
};

export default React.memo(TresorieRapport);
