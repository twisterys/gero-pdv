import ReactSelect from "react-select";
export const PaiementModal = ({ paiement, setPaiement, payer }) => {
    return (
        <div
            className={"modal fade"}
            data-bs-backdrop="static"
            tabindex="-1"
            id="paiement-modal"
            style={{ display: "none" }}
        >
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title align-self-center">Paiement</h5>
                        <button
                            type="button"
                            className="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div className="modal-body">
                        <div className="col-12 mt-3" style={{ display: "none" }}>
                            <label
                                htmlFor="date_paiement"
                                className="form-label required"
                            >
                                Date de paiement
                            </label>
                            <div className="input-group">
                                <input
                                    required
                                    className="form-control datupickeru"
                                    data-provide="datepicker"
                                    data-date-autoclose="true"
                                    type="text"
                                    id="date_paiement"
                                    value={paiement.i_date_paiement}
                                    onChange={(e) =>
                                        setPaiement({
                                            ...paiement,
                                            i_date_paiement:
                                                document.querySelector(
                                                    "#date_paiement"
                                                ).value,
                                            i_date: document.querySelector(
                                                "#date_prevu"
                                            ).value,
                                        })
                                    }
                                />
                                <span className="input-group-text">
                                    <i className="fa fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div className="col-12 mt-3">
                            <label htmlFor="montant" className="form-label required">
                                Montant de paiement
                            </label>
                            <div className="input-group">
                                <input
                                    required
                                    className="form-control"
                                    step="0.001"
                                    min="1"
                                    max={paiement.i_montant}
                                    type="number"
                                    value={paiement.i_montant}
                                    onChange={(e) =>
                                        setPaiement({
                                            ...paiement,
                                            i_montant: e.target.value,
                                            i_date_paiement:
                                                document.querySelector(
                                                    "#date_paiement"
                                                ).value,
                                            i_date: document.querySelector(
                                                "#date_prevu"
                                            ).value,
                                        })
                                    }
                                    id="montant"
                                />
                                <span className="input-group-text">MAD</span>
                            </div>
                        </div>
                        <div className="col-12 mt-3">
                            <label
                                htmlFor="compte-input"
                                className="form-label required"
                            >
                                Compte
                            </label>
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
                                onChange={(choice) =>
                                    setPaiement({
                                        ...paiement,
                                        i_compte_id: choice,
                                        i_date_paiement:
                                            document.querySelector(
                                                "#date_paiement"
                                            ).value,
                                        i_date: document.querySelector(
                                            "#date_prevu"
                                        ).value,
                                    })
                                }
                                options={__comptes}
                            />
                        </div>
                        <div className="col-12 mt-3">
                            <label
                                htmlFor="method-input"
                                className="form-label required"
                            >
                                Méthode de paiement
                            </label>
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
                                        i_date_paiement:
                                            document.querySelector(
                                                "#date_paiement"
                                            ).value,
                                        i_date: document.querySelector(
                                            "#date_prevu"
                                        ).value,
                                    });
                                }}
                                options={__methodes}
                            />
                        </div>
                        <div
                            className="col-12 mt-3"
                            style={{
                                display:
                                    paiement.i_method_key.value === "cheque" ||
                                    paiement.i_method_key.value === "lcn"
                                        ? ""
                                        : "none",
                            }}
                        >
                            <label htmlFor="date" className="form-label required">
                                Date prévu
                            </label>
                            <div className="input-group">
                                <input
                                    required
                                    className="form-control datupickeru"
                                    data-provide="datepicker"
                                    data-date-autoclose="true"
                                    type="text"
                                    id="date_prevu"
                                    value={paiement.i_date}
                                    onChange={(e) =>
                                        setPaiement({
                                            ...paiement,
                                            i_date_paiement:
                                                document.querySelector(
                                                    "#date_paiement"
                                                ).value,
                                            i_date: document.querySelector(
                                                "#date_prevu"
                                            ).value,
                                        })
                                    }
                                />
                                <span className="input-group-text">
                                    <i className="fa fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div
                            className="col-12 mt-3 "
                            style={{
                                display:
                                    paiement.i_method_key.value === "cheque" ||
                                    paiement.i_method_key.value === "lcn"
                                        ? " "
                                        : "none",
                            }}
                        >
                            <label
                                htmlFor="i_reference"
                                className="form-label required"
                            >
                                Référence de chéque
                            </label>
                            <input
                                required
                                className="form-control"
                                type="text"
                                value={paiement.i_reference}
                                onChange={(e) =>
                                    setPaiement({
                                        ...paiement,
                                        i_reference: e.target.value,
                                        i_date_paiement:
                                            document.querySelector(
                                                "#date_paiement"
                                            ).value,
                                        i_date: document.querySelector(
                                            "#date_prevu"
                                        ).value,
                                    })
                                }
                            />
                        </div>
                        <div className="col-12 mt-3">
                            <label htmlFor="i_note" className="form-label">
                                Note
                            </label>
                            <textarea
                                className="form-control"
                                rows="3"
                                value={paiement.i_note}
                                onChange={(e) =>
                                    setPaiement({
                                        ...paiement,
                                        i_note: e.target.value,
                                        i_date_paiement:
                                            document.querySelector(
                                                "#date_paiement"
                                            ).value,
                                        i_date: document.querySelector(
                                            "#date_prevu"
                                        ).value,
                                    })
                                }
                            ></textarea>
                        </div>
                    </div>
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Fermer
                        </button>
                        <button className="btn btn-info" onClick={() => payer()}>
                            Payer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
