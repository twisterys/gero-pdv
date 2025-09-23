
const DepenseTable = ({data,formatCurrency}) => {
    // Calculate total amount
    const totalMontant = Array.isArray(data)
        ? data.reduce((sum, item) => sum + (parseFloat(item?.montant) || 0), 0)
        : 0;

    return (
        <div className="table-responsive" id="rapport-table-container-dp">
            <table className="table  table-bordered ">
                <thead>
                <tr>
                    <th className="bg-light">Catégorie</th>
                    <th className="bg-light text-end">Montant</th>
                </tr>
                </thead>
                <tbody>
                {data.length > 0 ? (
                        data.map((item, index) => {
                            return (
                                <tr key={index} >
                                    <td>{item.categorie || "-"}</td>
                                    <td className="text-end">{item.montant ? formatCurrency(item.montant) : "-"}</td>
                                </tr>
                            );
                        })
                    )
                    : (
                        <tr>
                            <td colSpan="2" className="text-center">
                                Aucune dépense trouvée
                            </td>
                        </tr>
                    )}
                </tbody>
                {data.length > 0 && (
                    <tfoot>
                    <tr className="bg-soft-light">
                        <th className="text-end">Total</th>
                        <td className="text-end">{formatCurrency(totalMontant)}</td>
                    </tr>
                    </tfoot>
                )}
            </table>
        </div>
    );
};

export default DepenseTable;
