import {useState,useEffect} from "react";
import ReactSelect from "react-select";
import {reference} from "@popperjs/core";

export const TableCaisse = ({
                                items,
                                total,
                                setItems,
                                type,
                                payer,
                                resetAll,
                                paiement,
                                is_prix_editable,
                                is_reduction_on,
                                setIsLoading,
                                setPaiement,
                            }) => {
    const fitContentStyle = {
        width: "12%",
        whiteSpace: "nowrap",
        flexWrap: "nowrap",
        justifyContent: "center",
        verticalAlign: "middle",
    };
    // Calculate the actual total to pay from the items
    const totalAPayer = parseFloat(total);

    const [deleting, setDeleting] = useState(false);
    const [refundAmount, setRefundAmount] = useState(0);
    const [paymentRemaining, setPaymentRemaining] = useState(totalAPayer);
    const [paiementRecu, setPaiementRecu] = useState(total);
    // Optimized functions to update item properties
    const updateItemProperty = (id, property, value) => {
        setItems(prevItems =>
            prevItems.map(item =>
                item.id == id ? {...item, [property]: parseFloat(value) || 0} : item
            )
        );
    };

    // Update row quantity value in items array
    const setQuantity = (id, value) => {
        // Ensure quantity is at least 1
        const quantity = Math.max(1, parseFloat(value) || 1);
        updateItemProperty(id, 'quantity', quantity);
    };

    // Update reduction percentage for an item
    const setReduction = (id, value) => {
        // Ensure reduction is between 0 and 100
        const reduction = Math.min(100, Math.max(0, parseFloat(value) || 0));
        updateItemProperty(id, 'reduction', reduction);
    };

    // Update price for an item
    const setPrice = (id, value) => {
        // Ensure price is not negative
        const price = Math.max(0, parseFloat(value) || 0);
        // If tax is applied, we need to divide by tax to get the base price
        const item = items.find(item => item.id == id);
        if (item && item.tax) {
            updateItemProperty(id, 'prix', price / item.tax);
        } else {
            updateItemProperty(id, 'prix', price);
        }
    };

    // Remove row from table and from items array
    const deleteRow = (event) => {
        if (!deleting) {
            try {
                setIsLoading(true);
                setDeleting(true);
                const key = event.target.closest("tr").dataset.id;

                // Use functional update to avoid stale state
                setItems(prevItems => prevItems.filter(item => item.id != key));
            } catch (error) {
                console.error("Error deleting row:", error);
            } finally {
                setDeleting(false);
                setIsLoading(false);
            }
        }
    };
    // Calculate payment remaining and refund amount when total or payment received changes
    useEffect(() => {
        const remaining = Math.max(parseFloat(total) - parseFloat(paiementRecu), 0);
        const refund = parseFloat(paiementRecu) > totalAPayer ? parseFloat(paiementRecu) - totalAPayer : 0;

        setPaymentRemaining(parseFloat(remaining.toFixed(2)));
        setRefundAmount(parseFloat(refund.toFixed(2)));
    }, [total, paiementRecu, totalAPayer]);

    // Update paiement object when payment received changes
    useEffect(() => {
        setPaiement(prev => ({...prev, i_montant: parseFloat(paiementRecu)}));
    }, [paiementRecu, setPaiement]);

    // Initialize payment received when total changes
    useEffect(() => {
        setPaiementRecu(parseFloat(total));
    }, [total])



    // Table body rows generation from items array
    const arrayDataItems =
        items.length > 0 ? (
            items.map((item) => (
                <tr
                    key={item.id}
                    data-id={item.id}
                    data-position={item.position}
                >
                    <td style={{verticalAlign: "middle"}}>{item.name} ({item.reference})</td>
                    <td
                        style={{
                            minWidth: "70px",
                            whiteSpace: "nowrap",
                            verticalAlign: "middle",
                        }}
                    >
                        <div className="input-group flex-nowrap">
                            <input
                                type="number"
                                min={1}
                                value={item.quantity}
                                onChange={(e) =>
                                    setQuantity(item.id, e.target.value)
                                }
                                className="form-control"
                                style={{minWidth: "90px"}}
                            />
                        </div>
                    </td>

                    {is_prix_editable ? (
                        <td
                            style={{
                                minWidth: "100px",
                                whiteSpace: "nowrap",
                                verticalAlign: "middle",
                            }}
                        >
                            <div className="input-group"
                                 style={{display: 'flex', alignItems: 'center', minWidth: '150px'}}>
                                <input
                                    type="number"
                                    min={0}
                                    // Display price with tax for editing
                                    value={parseFloat((item.prix * (item.tax || 1)).toFixed(2))}
                                    onChange={(e) =>
                                        setPrice(item.id, e.target.value)
                                    }
                                    className="form-control"
                                    style={{flex: 1}}
                                />
                                <span className="input-group-text">MAD</span>
                            </div>
                        </td>
                    ) : (
                        <td style={fitContentStyle}>{item.prix} MAD</td>
                    )}
                    {is_reduction_on ? (
                        <td
                            style={{
                                minWidth: "150px",
                                whiteSpace: "nowrap",
                                verticalAlign: "middle",
                            }}
                        >
                            <div className="input-group">
                                <input
                                    type="number"
                                    value={item.reduction}
                                    onChange={(e) =>
                                        setReduction(item.id, e.target.value)
                                    }
                                    className="form-control"
                                />
                                <span className="input-group-text">%</span>
                            </div>
                        </td>
                    ) : null}
                    <td style={fitContentStyle}>
                        {(() => {
                            // Calculate item total with tax and reduction
                            const quantity = parseFloat(item.quantity) || 0;
                            const price = parseFloat(item.prix) || 0;
                            const tax = parseFloat(item.tax) || 1;
                            const reduction = parseFloat(item.reduction) || 0;

                            // Apply tax, then reduction
                            const totalWithTax = quantity * price * tax;
                            const totalAfterReduction = totalWithTax * (1 - reduction / 100);

                            return totalAfterReduction.toFixed(2);
                        })()}
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
        <>
            <div
                className="col-7 d-flex flex-column"
                style={{height: "calc(100vh - 16rem)"}}
            >
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
                                    {is_reduction_on ? (
                                        <th className="text-white">Reduction</th>
                                    ) : null}
                                    <th className="text-white">Total</th>
                                    <th className="text-white">Action</th>
                                </tr>
                                </thead>
                                <tbody>{arrayDataItems}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div className="col-5" style={{height: "calc(100vh - 16rem)"}}>
                <div className="card card-body position-absolute" style={{inset: ".3rem"}}>
                    <div className="d-flex flex-column gap-2 overflow-y-scroll w-100 h-100">
                        <div className="row g-3 w-100">
                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Paiement reçu</h4>
                                    <input
                                        className="form-control"
                                        type="number"
                                        value={paiementRecu}
                                        onChange={e => setPaiementRecu(e.target.value)}
                                    />
                                </div>
                            </div>

                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Référence</h4>
                                    <input
                                        className="form-control"
                                        type="text"
                                        value={paiement.i_reference}
                                        onChange={e => setPaiement({...paiement, i_reference: e.target.value})}
                                    />
                                </div>
                            </div>

                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Montant restant</h4>
                                    <input
                                        className="form-control"
                                        type="text"
                                        value={paymentRemaining}
                                        onChange={e => null}
                                    />
                                </div>
                            </div>

                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Montant du retour</h4>
                                    <input
                                        className="form-control"
                                        type="text"
                                        value={refundAmount}
                                        onChange={e => null}
                                    />
                                </div>
                            </div>

                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Méthode de paiement</h4>
                                    <ReactSelect
                                        classNames={{
                                            control: () => "border-light shadow-none",
                                            option: (state) => {
                                                return state.isFocused
                                                    ? "bg-primary text-white"
                                                    : state.isSelected
                                                        ? "bg-soft-primary text-white"
                                                        : state.isDisabled
                                                            ? "bg-soft-light text-muted"
                                                            : "";
                                            },
                                            singleValue: (state) => {
                                                state.isFocused
                                                    ? "bg-primary"
                                                    : "bg-light";
                                            },
                                        }}
                                        cache={false}
                                        value={paiement.i_method_key}
                                        onChange={(choice) => {
                                            setPaiement({
                                                ...paiement,
                                                i_method_key: choice,
                                            });
                                        }}
                                        options={__methodes}
                                    />
                                </div>
                            </div>

                            <div className="col-md-6">
                                <div className="p-2 bg-light rounded">
                                    <h4>Compte de paiement</h4>
                                    <ReactSelect
                                        classNames={{
                                            control: () => "border-light shadow-none",
                                            option: (state) => {
                                                return state.isFocused
                                                    ? "bg-primary text-white"
                                                    : state.isSelected
                                                        ? "bg-soft-primary text-white"
                                                        : state.isDisabled
                                                            ? "bg-soft-light text-muted"
                                                            : "";
                                            },
                                            singleValue: (state) => {
                                                state.isFocused
                                                    ? "bg-primary"
                                                    : "bg-light";
                                            },
                                        }}
                                        cache={false}
                                        value={paiement.i_compte_id}
                                        onChange={(choice) => {
                                            setPaiement({
                                                ...paiement,
                                                i_compte_id: choice,
                                            });
                                            console.log(choice);
                                        }}
                                        options={__comptes}
                                    />
                                </div>
                            </div>
                            <div className="col-12">
                                <div className="p-2 bg-light rounded">
                                    <h4>
                                        Note
                                    </h4>
                                    <textarea
                                        className="form-control"
                                        rows="3"
                                        value={paiement.i_note}
                                        onChange={(e) =>
                                            setPaiement({
                                                ...paiement,
                                                i_note: e.target.value,
                                            })
                                        }
                                    ></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div
                        className="bg-white rounded-bottom p-3 border-light px-4"
                        style={{borderTop: "2px dashed #eee", marginTop: 'auto'}}>
                        <div className="row">
                            <div className="col-12 my-1 d-flex justify-content-between align-items-center">
                                <h4 className="m-0">Montant total TTC :</h4>
                                <h4 className="m-0">
                                    {parseFloat(total).toFixed(2)}
                                    <span className="ms-2">MAD</span>
                                </h4>
                            </div>
                            <div className="col-12 my-1 d-flex justify-content-between align-items-center">
                                <h4 className="m-0">Nombre d'articles :</h4>
                                <h4 className="m-0">
                                    {items.reduce((total, item) => total + (parseFloat(item.quantity) || 0), 0)}
                                </h4>
                            </div>
                        </div>
                        <div className="row mt-3">
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
                                    onClick={() => payer()}
                                    className="btn btn-success py-3 mx-1 w-100">
                                    <i className="fa fa-coins me-2 h3 mb-0"></i>
                                    <span className="h4 mb-0">Payer</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
        ;
};
