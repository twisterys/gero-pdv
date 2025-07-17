export const TableDemande = ({
    items,
    setItems,
    sendDemande,
    setAddDemande
}) => {
    const fitContentStyle = {
        width: "12%",
        whiteSpace: "nowrap",
        flexWrap: "nowrap",
        justifyContent: "center",
        verticalAlign: "middle",
    };

    // update row quantity value in items array
    const setQuantity = (id, value) => {
        let allItems = items.map((item) =>
            item.id == id ? { ...item, quantity: value } : item
        );
        setItems(allItems);
    };


    // remove row from table and from items array
    const deleteRow = (event) => {
        let key = event.target.closest("tr").dataset.id;
        setItems(items.filter((item) => item.id != key));
    };

    // Table body rows generation from items array
    const arrayDataItems =
        items.length > 0 ? (
            items.map((item) => (
                <tr
                    key={item.id}
                    data-id={item.id}
                    data-position={item.position}
                >
                    <td style={{ verticalAlign: "middle" }}>{item.name} ({item.reference})</td>
                    <td
                        style={{
                            minWidth: "70px",
                            maxWidth: "100px",
                            whiteSpace: "nowrap",
                            verticalAlign: "middle",
                        }}
                    >
                        <div className="input-group">
                            <button
                                className=" btn btn-danger input-group-text"
                                onClick={() =>
                                    setQuantity(item.id, item.quantity - 1)
                                }
                            >
                                -
                            </button>
                            <input
                                type="number"
                                value={item.quantity}
                                onChange={(e) =>
                                    setQuantity(item.id, e.target.value)
                                }
                                className="form-control"
                            />
                            <button
                                className=" btn btn-success input-group-text"
                                onClick={() =>
                                    setQuantity(item.id, item.quantity + 1)
                                }
                            >
                                +
                            </button>
                        </div>
                    </td>
                    <td style={fitContentStyle}>
                        {item.stock}
                    </td>
                    <td style={fitContentStyle}>
                        <button
                            className="btn btn-soft-danger"
                            onClick={(event) => deleteRow(event)}
                        >
                            <i className="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            ))
        ) : (
            <tr>
                <td colSpan="6" className="text-center">
                    Aucun produit n'est pas ajouté{" "}
                </td>
            </tr>
        );

    return (
        <div className="card position-absolute" style={{ inset: ".3rem" }}>
            <div className="card-body position-relative overflow-y-auto scrollbar">
                <div className="col-12 h-100 ">
                    <table
                        className="table table-fixed table-striped table-hover table-bordered rounded-top overflow-hidden"
                        id="products-table"
                    >
                        <thead>
                            <tr
                                className="bg-primary"
                            >
                                <th className="text-white">Produit</th>
                                <th className="text-white">Quantité</th>
                                <th className="text-white">Quantité actuelle</th>
                                <th className="text-white">Action</th>
                            </tr>
                        </thead>
                        <tbody>{arrayDataItems}</tbody>
                    </table>
                </div>
            </div>
            <div
                className="bg-white rounded-bottom p-3 border-light px-4"
                style={{ borderTop: "2px dashed #eee" }}>
                <div className="row mt-3">
                    <div className="col-6">
                        <button
                            className="btn btn-danger py-3 mx-1 w-100"
                            onClick={()=>{
                                setItems([]);
                                setAddDemande(false);
                            }}
                        >
                            <i className="fa fa-times-circle me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Annuler</span>
                        </button>
                    </div>

                    <div className="col-6">
                        <button
                            onClick={()=>sendDemande()}
                            className="btn btn-success py-3 mx-1 w-100">
                            <i className="fa fa-paper-plane me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Demander</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
