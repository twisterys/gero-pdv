
const DepenseTable = ({data,formatCurrency}) => {
    return (
        <div className="table-responsive" id="rapport-table-container-dp">
            <table className="table  table-bordered ">
                <thead>
                <tr>
                    <th className="bg-light">Catégorie</th>
                    <th className="bg-light">Montant</th>
                </tr>
                </thead>
                <tbody>
                {data.length > 0 ? (
                        data.map((item, index) => {

                            return (
                                <tr key={index} >
                                    <td>{item.categorie || "-"}</td>
                                    <td>{item.montant ? formatCurrency(item.montant) : "-"}</td>
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
            </table>
        </div>
    );
};

export default DepenseTable;
