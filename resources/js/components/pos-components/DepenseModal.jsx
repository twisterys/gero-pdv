import ReactSelect from "react-select";
import axios from "axios";
import {useState} from "react";

export const DepenseModal = ({depense, setDepense}) => {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const saveDepense = () => {
        setIsSubmitting(true);
        let data = {
            ...depense,
            category_id: depense.category_id ? depense.category_id.value : null
        }
        axios.post("depense", data)
            .then((response) => {
                toastr.success(response.data);
                $("#depense-modal").modal("hide");
                setDepense({
                    nom: "",
                    category_id: "",
                    benificiaire: "",
                    montant: 0,
                    description: "",
                    session_id: __session_id
                });
                setIsSubmitting(false);
            })
            .catch((error) => {
                setIsSubmitting(false);
                if (error.response.status == 422) {
                    let errors = error.response.data.errors;
                    let errorsHtml = "";
                    for (let key in errors) {
                        errorsHtml += `<p class="text-white">${errors[key][0]}</p>`;
                    }
                    toastr.warning(errorsHtml);
                } else {
                    toastr.danger("Vuillez ressayer plus tard");
                }
            });
    }

    return (<div
            className={"modal fade"}
            data-bs-backdrop="static"
            tabIndex="-1"
            id="depense-modal"
            style={{display: "none"}}
        >
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title align-self-center">Ajouter une dépense</h5>
                        <button
                            type="button"
                            className="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div className="modal-body">
                        <div className="col-12 mt-3">
                            <label
                                htmlFor="depense_nom"
                                className="form-label required"
                            >
                                Nom de depense
                            </label>
                            <div className="input-group">
                                <input id="depense_nom" className="form-control" type="text" required
                                       value={depense.nom} onChange={e => {
                                    setDepense({
                                        ...depense,
                                        nom: e.target.value,
                                    })
                                }}/>
                            </div>
                        </div>
                        <div className="col-12 mt-3">
                            <label
                                htmlFor="depense-category"
                                className="form-label required"
                            >
                                Catégorie
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
                                value={depense.category_id}
                                onChange={(choice) =>
                                    setDepense({
                                        ...depense,
                                        category_id: choice,
                                    })
                                }
                                options={__depenses}
                            />
                        </div>
                        <div className="col-12 mt-3">
                            <label
                                htmlFor="depense_benificiaire"
                                className="form-label required"
                            >
                                Bénéficiaire
                            </label>
                            <div className="input-group">
                                <input id="depense_benificiaire" className="form-control" type="text" required
                                       value={depense.benificiaire} onChange={e => {
                                    setDepense({
                                        ...depense,
                                        benificiaire: e.target.value,
                                    })
                                }}/>
                            </div>
                        </div>
                        <div className="col-12 mt-3">
                            <label htmlFor="montant" className="form-label required">
                                Montant
                            </label>
                            <div className="input-group">
                                <input
                                    required
                                    className="form-control"
                                    step="0.001"
                                    min="1"
                                    max={depense.montant}
                                    type="number"
                                    value={depense.montant}
                                    onChange={(e) =>
                                        setDepense({
                                            ...depense,
                                            montant: e.target.value,
                                        })
                                    }
                                    id="montant"
                                />
                                <span className="input-group-text">MAD</span>
                            </div>
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
                        <button
                            className="btn btn-success"
                            onClick={() => saveDepense()}
                            disabled={isSubmitting} // Désactive le bouton pendant l'envoi
                        >
                            {isSubmitting ? "En cours..." : "Ajouter"}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}
