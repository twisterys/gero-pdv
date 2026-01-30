import {useEffect, useState} from "react";

export const TableCommercial = ({
                                    items,
                                    setItems,
                                    type,
                                    cashOut,
                                    resetAll,
                                    paiement,
                                    is_prix_editable,
                                    is_reduction_on,
                                    setIsLoading
                                }) => {
    const fitContentStyle = {
        width: "12%",
        whiteSpace: "nowrap",
        flexWrap: "nowrap",
        justifyContent: "center",
        verticalAlign: "middle",
    };

    const [deleting, setDeleting] = useState(false);
    // update row quantity value in items array
    const setQuantity = (id, value) => {
        let allItems = items.map((item) =>
            item.id == id ? {...item, quantity: value} : item
        );
        setItems(allItems);
    };
    const setReduction = (id, value) => {
        let allItems = items.map((item) =>
            item.id == id ? {...item, reduction: value} : item
        );
        setItems(allItems);
    };

    const setPrice = (id, value) => {
        let allItems = items.map((item) =>
            item.id == id ? {...item, prix: value} : item
        );
        setItems(allItems);
    };

    // remove row from table and from items array
    const deleteRow = (event) => {
        if (!deleting) {
            setIsLoading(true);
            setDeleting(true);
            let key = event.target.closest("tr").dataset.id;
            setItems(items.filter((item) => item.id != key));
            setDeleting(false)
            setIsLoading(false);
        }
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
                    <td className="align-middle">
                        {item.name} ({item.reference} - <span className="text-muted fw-bold">Stock: {item.stock}</span>)
                    </td>

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

                    {!is_prix_editable ? (<td style={fitContentStyle}>{item.prix} MAD</td>) : (
                        <td
                            style={{
                                minWidth: "70px",
                                maxWidth: "100px",
                                whiteSpace: "nowrap",
                                verticalAlign: "middle",
                            }}
                        >
                            <div className="input-group"
                                 style={{display: 'flex', alignItems: 'center', minWidth: '150px'}}>
                                <input
                                    type="number"
                                    value={item.prix}
                                    onChange={(e) =>
                                        setPrice(item.id, e.target.value)
                                    }
                                    className="form-control"
                                    style={{flex: 1}}
                                />
                                <span className="input-group-text" style={{marginLeft: '5px'}}>MAD</span>
                            </div>
                        </td>
                    )}
                    {
                        !is_reduction_on ? null : (
                            <td
                                style={{
                                    minWidth: "70px",
                                    maxWidth: "100px",
                                    whiteSpace: "nowrap",
                                    verticalAlign: "middle",
                                }}
                            >
                                <div className="input-group">
                                    {/* <button
                                    className=" btn btn-danger input-group-text"
                                    onClick={() =>
                                        setReduction(
                                            item.id,
                                            item.reduction - 1
                                        )
                                    }
                                >
                                    -
                                </button> */}
                                    <input
                                        type="number"
                                        value={item.reduction}
                                        onChange={(e) =>
                                            setReduction(item.id, e.target.value)
                                        }
                                        className="form-control"
                                    />
                                    <span className="input-group-text">%</span>
                                    {/* <button
                                    className=" btn btn-success input-group-text"
                                    onClick={() =>
                                        setReduction(
                                            item.id,
                                            +item.reduction + 1
                                        )
                                    }
                                >
                                    +
                                </button> */}
                                </div>
                            </td>
                        )}
                    <td style={fitContentStyle}>
                        {(
                            item.quantity *
                            item.prix *
                            (1 - item.reduction / 100)
                        ).toFixed(3)}
                        MAD
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
        <div className="card position-absolute" style={{inset: ".3rem"}}>
            <div className="card-body position-relative overflow-y-auto scrollbar">
                <div className="col-12 h-100 ">
                    <table
                        className="table table-fixed table-striped table-hover table-bordered rounded-top overflow-hidden"
                        id="products-table"
                    >
                        <thead>
                        <tr
                            className={
                                type === "bc" ? "bg-primary" : "bg-success"
                            }
                        >
                            <th className="text-white">Produit</th>
                            <th className="text-white">Quantite</th>
                            <th className="text-white">Prix</th>
                            {!is_reduction_on ? null : (
                                <th className="text-white">Reduction</th>
                            )}
                            <th className="text-white">Total</th>
                            <th className="text-white">Action</th>
                        </tr>
                        </thead>
                        <tbody>{arrayDataItems}</tbody>
                    </table>
                </div>
            </div>
            <div
                className="bg-white rounded-bottom p-3 border-light px-4"
                style={{borderTop: "2px dashed #eee"}}>
                <div className="row">
                    <div className="col-12 my-1 d-flex justify-content-between align-items-center">
                        <h4 className="m-0">Montant total :</h4>
                        <h4 className="m-0">
                            {paiement.i_montant.toLocaleString(undefined, {minimumFractionDigits: 2})}
                            <span className="ms-2">MAD</span>
                        </h4>
                    </div>
                    <div className="col-12 my-1 d-flex justify-content-between align-items-center">
                        <h4 className="m-0">Nombre d'articles :</h4>
                        <h4 className="m-0">
                            {items.reduce((a, b) => a + b.quantity, 0)}
                        </h4>
                    </div>
                </div>
                <div className="row mt-3 g-3 justify-content-end">
                    <div className="col-6">
                        <button
                            className="btn btn-danger py-3 mx-1 w-100"
                            onClick={() => resetAll()}>
                            <i className="fa fa-times-circle me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Annuler</span>
                        </button>
                    </div>

                    <div className="col-6">
                        <button
                            onClick={() => cashOut()}
                            className="btn btn-success py-3 mx-1 w-100">
                            <i className="fa fa-coins me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Espèces</span>
                        </button>
                    </div>
                    <div className="col-3">
                        <button
                            className="btn btn-warning py-3 mx-1 w-100"
                            onClick={() => cashOut(true)}
                        >
                            <i className="fa fa-wallet me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Credit</span>
                        </button>
                    </div>
                    <div className="col-3">
                        <button
                            className="btn btn-info py-3 mx-1 w-100"
                            data-bs-target="#paiement-modal"
                            data-bs-toggle="modal"
                        >
                            <i className="fa fa-cash-register me-2 h3 mb-0"></i>
                            <span className="h4 mb-0">Autre</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
