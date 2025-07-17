import ReactSelect from "react-select";
import { useState } from "react";
import axios from "axios";
export const ClientModal = ({ setClient, setIsLoading }) => {
    const [newClient, setNewClient] = useState({
        city: null,
        nom: null,
        telephone: null,
        adresse: null,
    });

    const saveClient = () => {
        setIsLoading(true);
        axios
            .post("clients", {
                ...newClient,
            })
            .then((res) => {
                setClient(res.data.client);
                setIsLoading(false);
                toastr.success(res.data.message);
                $("#client-modal").modal("hide");

                setNewClient({
                    city: null,
                    nom: null,
                    telephone: null,
                    adresse: null,
                });
            })
            .catch((err) => {
                setIsLoading(false);
                if (err.response.status == 422) {
                    let errors = err.response.data.errors;
                    let errorsHtml = "";
                    for (let key in errors) {
                        errorsHtml += `<p class="text-white">${errors[key][0]}</p>`;
                    }
                    toastr.warning(errorsHtml);
                } else {
                    toastr.error("Vuillez ressayer plus tard");
                }
            });
    };

    return (
        <div
            class="modal fade "
            id="client-modal"
            tabindex="-1"
            aria-hidden="true"
            style={{ display: "none" }}
        >
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5
                            class="modal-title align-self-center"
                            id="edit-cat-modal-title"
                        >
                            Ajout rapide de client
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="row px-3 align-items-start pt-4">
                        <div class="col-12  mb-3">
                            <label
                                for="nom"
                                class="form-label required"
                                id="dynamic_label"
                            >
                                Dénomination
                            </label>
                            <input
                                type="text"
                                class="form-control "
                                id="nom"
                                name="nom"
                                required
                                value={newClient.nom}
                                onChange={(e) =>
                                    setNewClient({
                                        ...newClient,
                                        nom: e.target.value,
                                    })
                                }
                                placeholder="Nom du client"
                            />
                        </div>
                        <div class="col-12 mb-3">
                            <label for="telephone" class="form-label">
                                Téléphone
                            </label>
                            <input
                                type="tel"
                                class="form-control"
                                id="telephone"
                                name="telephone"
                                value={newClient.telephone}
                                onChange={(e) =>
                                    setNewClient({
                                        ...newClient,
                                        telephone: e.target.value,
                                    })
                                }
                                placeholder="Numéro de téléphone"
                            />
                        </div>
                        <div class="col-12  mb-3">
                            <label for="city" class="form-label">
                                Ville
                            </label>
                            <input
                                type="text"
                                className="form-control"
                                value={newClient.city}
                                id="city"
                                onChange={(e) =>
                                    setNewClient({
                                        ...newClient,
                                        city: e.target.value,
                                    })
                                }
                                placeholder="Ville du client"
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Fermer
                        </button>
                        <button
                            onClick={() => saveClient()}
                            type="button"
                            id="add-btn-client"
                            class="btn btn-primary"
                        >
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
