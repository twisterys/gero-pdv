import React from 'react';

const TresorieTable = ({data,formatCurrency}) => {
    return (
        <div className="table-responsive" id="rapport-table-container-tr">
            <table className="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th className="bg-light">Description</th>
                    <th className="bg-light text-end">Jour</th>
                    <th className="bg-light text-end">Créance</th>
                    <th className="bg-light text-end">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><strong>Total Vente</strong></td>
                    <td className="text-end"></td>
                    <td className="text-end"></td>
                    <td className="text-end">{formatCurrency(data.total_vente_jour)}</td>
                </tr>
                <tr>
                    <td><strong>Espèce</strong></td>
                    <td className="text-end">{formatCurrency(data.total_espece_jour)}</td>
                    <td className="text-end">{formatCurrency(data.total_espece_creance)}</td>
                    <td className="text-end">{formatCurrency(data.total_espece)}</td>
                </tr>
                <tr>
                    <td><strong>Chèque</strong></td>
                    <td className="text-end">{formatCurrency(data.total_cheque_jour)}</td>
                    <td className="text-end">{formatCurrency(data.total_cheque_creance)}</td>
                    <td className="text-end">{formatCurrency(data.total_cheque)}</td>
                </tr>
                <tr>
                    <td><strong>LCN</strong></td>
                    <td className="text-end">{formatCurrency(data.total_lcn_jour)}</td>
                    <td className="text-end">{formatCurrency(data.total_lcn_creance)}</td>
                    <td className="text-end">{formatCurrency(data.total_lcn)}</td>
                </tr>
                <tr>
                    <td><strong>Dépenses</strong></td>
                    <td className="text-end"></td>
                    <td className="text-end"></td>
                    <td className="text-end">{formatCurrency(data.total_depenses)}</td>
                </tr>
                <tr className="table-info">
                    <td><strong>Reste en Caisse</strong></td>
                    <td className="text-end"></td>
                    <td className="text-end"></td>
                    <td className="text-end"><strong>{formatCurrency(data.reste_en_caisse)}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    );
};

export default TresorieTable;
