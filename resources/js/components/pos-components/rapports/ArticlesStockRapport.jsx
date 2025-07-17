import React from "react";

const ArticlesStockRapport = ({
    data,
    isLoading,
    onPrint,
    onRefresh,
    onBack
}) => {
    return (
        <div className="card">
            <div className="card-header px-0 d-flex align-items-center">
                <button
                    className="btn btn-sm btn-outline-secondary me-2"
                    onClick={onBack}
                >
                    <i className="fas fa-chevron-left me-1"></i> Retour
                </button>
                <h5 className="mb-0">Rapport Stock</h5>
            </div>
            <div className="card-body">
                {isLoading ? (
                    <div className="text-center my-5">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                    </div>
                ) : (
                    <div className="table-responsive" id="rapport-table-container-as">
                        <table className="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th className="bg-light">Reference</th>
                                <th className="bg-light">Designation</th>
                                <th className="bg-light">Stock</th>
                            </tr>
                            </thead>
                            <tbody>
                            {data.map((item, index) => (
                                <tr key={index}>
                                    <td>{item.reference}</td>
                                    <td>{item.designation}</td>
                                    <td>{item.stock}</td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default React.memo(ArticlesStockRapport);
