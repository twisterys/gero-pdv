import React from 'react';

const ArticlesClientsTable = ({data}) => {
    const formater = new Intl.NumberFormat('fr-FR', {currency:"MAD",style:"currency"})
    return (
        <div className="table-responsive" id="rapport-table-container-ac">
            <table className="table table-bordered  table-bordered ">
                <thead>
                <tr>
                    <th className="bg-light sticky-col">Clients/Articles</th>
                    {data.articles?.map((article, index) => (
                        <th key={index} className="text-center bg-light">
                            {article}
                        </th>
                    ))}
                    <th className="text-center bg-light">Chiffre d'affaire</th>
                    <th className="text-center bg-light">Montant Payé</th>
                </tr>
                </thead>
                <tbody>
                {data.clients?.map((client, clientIndex) => (
                    <tr key={clientIndex}>
                        <th className="bg-light sticky-col">{client}</th>
                        {data.articles?.map((article, articleIndex) => (
                            <td key={articleIndex} className="text-center">
                                {data.data[client]?.[article] && (
                                    <div className="cell-content">
                                        <div className="quantity">
                                            {data.data[client][article].quantite > 0 ? data.data[client][article].quantite : ""}
                                        </div>
                                    </div>
                                )}
                            </td>
                        ))}
                        <td className="text-center">
                            {formater.format(data.client_totals?.[client]?.total_ttc || 0)}
                        </td>
                        <td className="text-center">
                            {formater.format(data.client_totals?.[client]?.total_paye || 0)}
                        </td>
                    </tr>
                ))}
                {
                    !(data.clients?.length > 0) && <tr>
                        <td colSpan={200} className="text-center">Aucune donnée trouvée</td>
                    </tr>
                }
                </tbody>
            </table>
        </div>
    );
};

export default ArticlesClientsTable;
