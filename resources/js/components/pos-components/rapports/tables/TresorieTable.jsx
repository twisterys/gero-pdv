import React from 'react';

const TresorieTable = ({data,formatCurrency}) => {
    return (
        <div className="table-responsive" id="rapport-table-container-tr">
            <table className="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th className="bg-light">Description</th>
                    <th className="bg-light text-end">Montant</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><strong>Total de Vente</strong></td>
                    <td className="text-end">{formatCurrency(data.total_vente)}</td>
                </tr>
                <tr>
                    <td><strong>Total Espèce</strong></td>
                    <td className="text-end">{formatCurrency(data.total_espece)}</td>
                </tr>
                <tr>
                    <td><strong>Total Chèque</strong></td>
                    <td className="text-end">{formatCurrency(data.total_cheque)}</td>
                </tr>
                <tr>
                    <td><strong>Total LCN</strong></td>
                    <td className="text-end">{formatCurrency(data.total_lcn)}</td>
                </tr>
                <tr>
                    <td><strong>Total Dépenses</strong></td>
                    <td className="text-end">{formatCurrency(data.total_depenses)}</td>
                </tr>
                <tr className="table-info">
                    <td><strong>Reste en Caisse</strong></td>
                    <td className="text-end"><strong>{formatCurrency(data.reste_en_caisse)}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    );
};

export default TresorieTable;
