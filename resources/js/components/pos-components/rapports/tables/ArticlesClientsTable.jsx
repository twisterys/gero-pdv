import React from 'react';

const ArticlesClientsTable = ({data}) => {
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
                    </tr>
                ))}
                {
                    data.clients.length === 0 && <tr>
                        <td colSpan={200} className="text-center">Aucune donnée trouvée</td>
                    </tr>
                }
                </tbody>
            </table>
        </div>
    );
};

export default ArticlesClientsTable;
