import React from 'react';

const ArticlesClientsTable = ({data = {}}) => {
    const formater = new Intl.NumberFormat('fr-FR', {currency:"MAD",style:"currency"})

    // Calcul de secours des totaux si non fournis par l'API
    const totals = React.useMemo(() => {
        const base = { total_ttc: 0, total_paye: 0, total_creance: 0 };
        if (!data) return base;
        if (data.totals) return {
            total_ttc: data.totals.total_ttc || 0,
            total_paye: data.totals.total_paye || 0,
            total_creance: data.totals.total_creance || 0,
        };
        if (data.client_totals) {
            return Object.values(data.client_totals).reduce((acc, t) => {
                const tt = (t && t.total_ttc) || 0;
                const tp = (t && t.total_paye) || 0;
                const tc = (t && t.total_creance) != null ? t.total_creance : (tt - tp);
                acc.total_ttc += tt;
                acc.total_paye += tp;
                acc.total_creance += tc;
                return acc;
            }, { ...base });
        }
        return base;
    }, [data]);

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
                    <th className="text-center bg-light">Montant Créance</th>
                </tr>
                </thead>
                <tbody>
                {data.clients?.map((client, clientIndex) => (
                    <tr key={clientIndex}>
                        <th className="bg-light sticky-col">{client}</th>
                        {data.articles?.map((article, articleIndex) => (
                            <td key={articleIndex} className="text-center">
                                {data.data?.[client]?.[article] && (
                                    <div className="cell-content">
                                        <div className="quantity">
                                            {data.data[client][article].quantite > 0 ? data.data[client][article].quantite : ""}
                                        </div>
                                    </div>
                                )}
                            </td>
                        ))}
                        <td >
                            {formater.format(data.client_totals?.[client]?.total_ttc || 0)}
                        </td>
                        <td >
                            {formater.format(data.client_totals?.[client]?.total_paye || 0)}
                        </td>
                        <td >
                            {formater.format(data.client_totals?.[client]?.total_creance || 0)}
                        </td>
                    </tr>
                ))}
                {
                    !(data.clients?.length > 0) && <tr>
                        <td colSpan={200} className="text-center">Aucune donnée trouvée</td>
                    </tr>
                }
                </tbody>
                {data.clients?.length > 0 && (
                    <tfoot>
                        <tr className="bg-light">
                            {/* colSpan doit couvrir la colonne "Clients/Articles" + toutes les colonnes d'articles */}
                            <th colSpan={(data.articles ? data.articles.length : 0) + 1} className="text-end">Total</th>
                            <th className="text-end">{formater.format(totals.total_ttc || 0)}</th>
                            <th className="text-end">{formater.format(totals.total_paye || 0)}</th>
                            <th className="text-end">{formater.format(totals.total_creance || 0)}</th>
                        </tr>
                    </tfoot>
                )}
            </table>
        </div>
    );
};

export default ArticlesClientsTable;
