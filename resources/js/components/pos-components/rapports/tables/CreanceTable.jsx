const CreanceTable = ({data, formatDate, formatCurrency}) => {
    return (
        <div className="table-responsive" id="rapport-table-container-cr">
            <table className="table  table-bordered ">
                <thead>
                <tr>
                    <th className="bg-light">Référence</th>
                    <th className="bg-light">Client</th>
                    <th className="bg-light">Méthode de Paiement</th>
                    <th className="bg-light">Date de Paiement</th>
                    <th className="bg-light">N° Chèque/LCN</th>
                    <th className="bg-light">Date de Vente</th>
                    <th className="bg-light">Contrôlé</th>
                    <th className="bg-light">Montant Total</th>
                    <th className="bg-light">Montant Créance</th>
                </tr>
                </thead>
                <tbody>
                {data.length > 0 ? (
                        data.map((item, index) => {
                            // Define background color based on payment status
                            let rowClass = "";
                            if (item.statut_paiement === "non_paye") {
                                rowClass = "bg-danger bg-opacity-25";
                            } else if (item.statut_paiement === "partiellement_paye") {
                                rowClass = "bg-primary bg-opacity-25";
                            } else if (item.statut_paiement === "paye") {
                                rowClass = "bg-success bg-opacity-25";
                            }

                            return (
                                <tr key={index} className={rowClass}>
                                    <td>{item.reference || "-"}</td>
                                    <td>{item.client_name || "-"}</td>
                                    <td>{item.last_payment_method || "-"}</td>
                                    <td>{formatDate(item.last_payment_date)}</td>
                                    <td>{item.cheque_lcn_reference || "-"}</td>
                                    <td>{formatDate(item.sale_date)}</td>
                                    <td>
                                        {item.is_controled ? (
                                            <span className="badge bg-success">Oui</span>
                                        ) : (
                                            <span className="badge bg-danger">Non</span>
                                        )}
                                    </td>
                                    <td className="text-end">{formatCurrency(item.total_ttc)}</td>
                                    <td className="text-end">{formatCurrency(item.creance_amount)}</td>
                                </tr>
                            );
                        })
                    )
                    : (
                        <tr>
                            <td colSpan="9" className="text-center">
                                Aucune créance trouvée
                            </td>
                        </tr>
                    )}
                </tbody>
                {data.length > 0 && (
                    <tfoot>
                    <tr className="bg-light">
                        <th colSpan="8" className="text-end">Total des Créances:</th>
                        <th className="text-end">
                            {formatCurrency(
                                data.reduce((sum, item) => sum + parseFloat(item.creance_amount || 0), 0)
                            )}
                        </th>
                    </tr>
                    </tfoot>
                )}

                {/*{*/}
                {/*    data.length === 0 && <tr>*/}

                {/*        <td colSpan={200} className="text-center">Aucune donnée trouvée</td>*/}

                {/*    </tr>*/}
                {/*}*/}
            </table>
        </div>
    );
};

export default CreanceTable;
