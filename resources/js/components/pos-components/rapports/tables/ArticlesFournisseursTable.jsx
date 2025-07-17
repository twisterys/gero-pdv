import React from 'react';

const ArticlesFournisseursTable = ({data}) => {
    return (
        <div className="table-responsive" id="rapport-table-container-af">
            <table className="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th className="bg-light sticky-col">Fournisseurs/Articles</th>
                    {data.articles?.map((article, index) => (
                        <th key={index} className="text-center bg-light">
                            {article}
                        </th>
                    ))}
                </tr>
                </thead>
                <tbody>
                {data.fournisseurs?.map((fournisseur, fournisseurIndex) => (
                    <tr key={fournisseurIndex}>
                        <th className="bg-light sticky-col">{fournisseur}</th>
                        {data.articles?.map((article, articleIndex) => (
                            <td key={articleIndex} className="text-center">
                                {data.data[fournisseur]?.[article] && (
                                    <div className="cell-content">
                                        <div className="quantity">
                                            {data.data[fournisseur][article].quantite > 0 ? data.data[fournisseur][article].quantite : ""}
                                        </div>
                                    </div>
                                )}
                            </td>
                        ))}
                    </tr>
                ))}
                {
                    data.fournisseurs.length === 0 && <tr>
                    <td colSpan={200} className="text-center">Aucune donnée trouvée</td>
                    </tr>
                }
                </tbody>
            </table>
        </div>
    );
};

export default ArticlesFournisseursTable;
