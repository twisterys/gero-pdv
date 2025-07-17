import ReactSelectAsync from "react-select/async";
import axios from "axios";
import {Searchbar} from "../Searchbar.jsx";
import React, {useState} from "react";
import ReactSelect from "react-select";
import {Table} from "../Table.jsx";
import {Numpad} from "../Numpad.jsx";
import {TableDemande} from "../TableDemande.jsx";


export const AddDemande = ({setAddDemande}) => {

    const [searchValue, setSearchValue] = useState("");
    const [magasinSortie, setMagasinSortie] = useState("");
    const [items,setItems] = useState([]);

    const clearSearch = () => {
        setSearchValue("");
    };
    const validateSearch = (event) => {
        event.target.setAttribute("disabled", "true");
        axios
            .get("articles", {
                params: {
                    reference: searchValue,
                },
            })
            .then((response) => {
                populateData(response);
                event.target.removeAttribute("disabled");
            })
            .catch((error) => {
                toastr.error("Une erreur est survenue");
                event.target.removeAttribute("disabled");
            });
    };
    const populateData = (response) => {
        if (response.data.length === 1) {
            let newItem = response.data[0];
            newItem.quantity = 1;
            newItem.position = items.length + 1;
            newItem.reduction = 0;
            if (items.some((item) => item.id == newItem.id)) {
                let allItems = items.map((item) =>
                    item.id == newItem.id
                        ? {...item, quantity: item.quantity + 1}
                        : item
                );
                setItems(allItems);
            } else {
                setItems([...items, newItem]);
            }
            toastr.success("Article ajouté");
        } else if (response.data.length > 1) {
            toastr.warning("Plusieurs articles trouvés");
        } else {
            toastr.warning("Aucun article trouvé");
        }
        clearSearch();
    };

    const sendDemande = () =>{
        if (items.length <= 0) {
            toastr.warning("Veuillez d'abord ajouter au moins une ligne");
            return null;
        }
        if (magasinSortie == null || magasinSortie == "") {
            toastr.warning("Veuillez sélectionner un magasin de sortie");
            return null;
        }
        axios
            .post("demande-transfert", {
                lignes: items,
                magasin_sortie: magasinSortie,
            })
            .then((response) => {
                toastr.success(response.data);
                setAddDemande(false);
            })
            .catch((error) => {
                if (error.response.status == 422) {
                    let errors = error.response.data.errors;
                    let errorsHtml = "";
                    for (let key in errors) {
                        errorsHtml += `<p class="text-danger">${errors[key][0]}</p>`;
                    }

                    Swal.fire({
                        icon: "warning",
                        title: "Attention !",
                        html: errorsHtml,
                        confirmButtonText: "Ok !",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-lg btn-soft-danger mx-2",
                        },
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Erreur !",
                        text: "Vuillez ressayer plus tard",
                        confirmButtonText: "Ok !",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-lg btn-soft-danger mx-2",
                        },
                    });
                }
            });
    }
    return (
        <>
            <div className="col-7">
                <div className="card">
                    <div className="card-body">
                        <div className="row">
                            <div className="col-5">
                                <div className="input-group flex-nowrap">
                                    <div className="w-100">
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
                                                    state.isFocused ? "bg-primary" : "bg-light";
                                                },
                                            }}
                                            placeholder="Magasin de sortie"
                                            options={__magasins}
                                            value={magasinSortie}
                                            onChange={(e) => setMagasinSortie(e)}
                                        />
                                    </div>
                                    <button
                                        data-bs-toggle="modal"
                                        data-bs-target="#client-modal"
                                        className="input-group-text btn btn-light"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                            <div className="col-7">
                                <Searchbar
                                    searchValue={searchValue}
                                    setSearchValue={setSearchValue}
                                    validateSearch={validateSearch}
                                    populateData={populateData}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                className="col-7 d-flex flex-column"
                style={{ height: "calc(100vh - 16rem)" }}
            >
                <TableDemande
                    items={items}
                    setItems={setItems}
                    sendDemande={sendDemande}
                    setAddDemande={setAddDemande}
                />
            </div>
            <div className="col-5">
                <Numpad setSearchValue={setSearchValue} validateSearch={validateSearch} clearSearch={clearSearch} searchValue={searchValue} />
            </div>


        </>
    )
}
