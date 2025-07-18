import axios from "axios";
import React, {useState, useEffect, useMemo, useRef} from "react";
import ReactDOM from "react-dom";
import {FullScreenButton} from "../pos-components/FullScreenButton";
import {ShutdownButton} from "../pos-components/ShutdownButton";
import {ZoomButtons} from "../pos-components/ZoomButtons";
import {ClientSelect} from "../pos-components/ClientSelect";
import {DepenseModal} from "../pos-components/DepenseModal.jsx";
import {History} from "../pos-components/history/History.jsx";
import {ClientModal} from "../pos-components/ClientModal";
import {TableCaisse} from "../pos-components/TableCaisse.jsx";
import {SearchbarWithType} from "@/components/pos-components/Searchbar-with-type.jsx";
import OrderSound from "../../../../public/sounds/addArticle.mp3"
import CashSound from "../../../../public/sounds/cash-paid.mp3"
import {PaiementModal} from "../pos-components/PaiementModal.jsx";
import RapportsPage from "../pos-components/rapports/RapportsPage.jsx";

function PosClassic() {
    // set all variable needed
    const [searchValue, setSearchValue] = useState("");
    const [client, setClient] = useState(__default_client);
    const [type, setType] = useState("bc");
    const [items, setItems] = useState([]);
    const [depense, setDepense] = useState({
        nom: "",
        category_id: "",
        benificiaire: "",
        montant: 0,
        description: "",
        session_id: __session_id
    });
    const [isLoading, setIsLoading] = useState(false);
    const [prixModification, setPrixModification] = useState(__prixModification);
    const [paiement, setPaiement] = useState({
        i_date_paiement: "",
        i_montant: 0,
        i_note: "",
        i_method_key:__methodes[0],
        i_reference: "",
        i_date: "",
        i_compte_id:__comptes[0],
        vente_id: null
    });

    const clearSearch = () => {
        setSearchValue("");
    };

    // search by reference
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

    //for sounds
    const playSound = () => {
        const audio = new Audio(OrderSound);
        audio.play();
    };

    const cashSound=()=>{
        const audio=new Audio(CashSound);
        audio.play();
    }



        const populateData = (response) => {
        if (!response || !response.data || response.data.length === 0 || response.data===[]) {
            toastr.warning("Aucun article trouvé");
            return;
        }
        if (response.data.length === 1 ) {
            let newItem = response.data[0];
            newItem.quantity = 1;
            newItem.reduction = 0;
            newItem.position = items.length + 1;
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
            playSound();
        } else if (response.data.length > 1) {
            toastr.warning("Plusieurs articles trouvés");
        } else {
            toastr.warning("Aucun article trouvé");
        }
        clearSearch();
    };

    const total_ttc = useMemo(()=>{
        return Math.round(
            items.reduce(
                (a, b) =>
                    a + b.prix * b.quantity * (1 - b.reduction / 100) * b.tax,
                0
            ) * 100
        ) / 100
    },[items])
    useEffect(()=>{
        console.log('total ttc',total_ttc);
    })

    function PrintElem(html) {
        if (document.getElementById("iframe")) {
            document.getElementById("iframe").remove();
        }
        let iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.id = "iframe";
        iframe.srcdoc = html;
        document.body.append(iframe);
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }

    const printCloture = () => {
        setIsLoading(true);
        axios.get('pos-session/cloture').then(response => {
            setIsLoading(false)
            Swal.fire({
                title: "Clôture de session",
                html: "<div class='bg-body p-3' >" + response.data + "</div>",
                showCancelButton: true,
                confirmButtonText: "Imprimer",
                cancelButtonText: "Annuler",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-soft-success mx-2",
                    cancelButton: "btn btn-soft-secondary mx-2",
                },
                didOpen: () => {
                    $(".btn").blur();
                },

            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        PrintElem(response.data)
                    } else {
                        Swal.fire({
                            title: "Erreur",
                            text: "Une erreur s'est produite lors de la demande.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-soft-danger mx-2",
                            },
                        });
                    }
                }
            });
        })
    }

    // submit sale with paiement cash
    const cashOut = (credit=false) => {
        if (client == null) {
            toastr.warning("Vuillez inserer un client !");
            return;
        }
        let data = {
            lignes: items,
            client: client.value,
            type: type,
            exercice: __exercice,
            session_id: __session_id,
        };
        if (credit) {
            data.credit = true;
        }
        setIsLoading(true);
        axios
            .post("ventes", data)
            .then((response) => {
                setIsLoading(false);
                Swal.fire({
                    icon: "success",
                    title: "Action réussie !",
                    html: response.data.message,
                    confirmButtonText: "Ok !",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-lg btn-soft-success mx-2",
                    },
                });
                resetAll();
                if (response.data.template != null) {
                    PrintElem(response.data.template);
                }
            })
            .catch((error) => {
                setIsLoading(false);
                if (error.response && error.response.status == 422) {
                    if (error.response.data && error.response.data.error) {
                        Swal.fire({
                            icon: "warning",
                            title: "Attention !",
                            text: error.response.data.error,
                            confirmButtonText: "Ok !",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-lg btn-soft-danger mx-2",
                            },
                        });
                    } else if (error.response.data && error.response.data.errors) {
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
    };

    const payer = () => {
        if (client == null) {
            toastr.warning("Vuillez inserer un client !");
            return;
        }
        setPaiement({
            ...paiement,
        });

        // Calculate total amount
        const totalAmount = total_ttc.toFixed(2);

        // Check if payment amount is less than total
        const isPartialPayment = parseFloat(paiement.i_montant) < parseFloat(totalAmount);

        // Check if we're adding a payment to an existing sale
        if (paiement.vente_id) {
            // This is a subsequent payment to an existing sale
            let data = {
                vente_id: paiement.vente_id,
                paiement: {
                    ...paiement,
                    i_montant: paiement.i_montant > totalAmount ? totalAmount : paiement.i_montant,
                    i_compte_id: paiement.i_compte_id.value,
                    i_method_key: paiement.i_method_key.value,
                },
                session_id: __session_id,
            };
            setIsLoading(true);
            axios
                .post("ventes-ajouter-paiement", data)
                .then((response) => {
                    setIsLoading(false);
                    $("#paiement-modal").modal("hide");
                    cashSound();

                    // Calculate remaining balance
                    const remainingBalance = (parseFloat(totalAmount) - parseFloat(paiement.i_montant)).toFixed(2);

                    // If there's still a remaining balance, ask if they want to add another payment
                    if (parseFloat(remainingBalance) > 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Paiement partiel enregistré !",
                            html: `${response.data.message}<br><br>Voulez-vous ajouter un autre paiement à cette vente?`,
                            showCancelButton: true,
                            confirmButtonText: "Oui, ajouter un paiement",
                            cancelButtonText: "Non, terminer",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-lg btn-soft-success mx-2",
                                cancelButton: "btn btn-lg btn-soft-secondary mx-2",
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Update the payment amount to the remaining balance
                                setPaiement({
                                    ...paiement,
                                    i_montant: remainingBalance,
                                    i_note: "",
                                    i_method_key: __methodes[0],
                                    i_reference: "",
                                    i_date: "",
                                    i_compte_id: __comptes[0],
                                });

                                // Show the payment modal
                                setTimeout(() => {
                                    $("#paiement-modal").modal("show");
                                }, 500);
                            } else {
                                resetAll();
                                if (response.data.template != null) {
                                    PrintElem(response.data.template);
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Paiement complet !",
                            html: response.data.message,
                            confirmButtonText: "Ok !",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-lg btn-soft-success mx-2",
                            },
                        });
                        resetAll();
                        if (response.data.template != null) {
                            PrintElem(response.data.template);
                        }
                    }
                })
                .catch((error) => {
                    setIsLoading(false);
                    if (error.response && error.response.status == 422) {
                        if (error.response.data && error.response.data.error) {
                            Swal.fire({
                                icon: "warning",
                                title: "Attention !",
                                html: error.response.data.error,
                                confirmButtonText: "Ok !",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-lg btn-soft-danger mx-2",
                                },
                            });
                            return;
                        }
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
        } else {
            // This is the first payment for a new sale
            let data = {
                lignes: items,
                client: client.value,
                type: type,
                exercice: __exercice,
                paiement: {
                    ...paiement,
                    i_montant: paiement.i_montant > totalAmount ? totalAmount : paiement.i_montant,
                    i_compte_id: paiement.i_compte_id.value,
                    i_method_key: paiement.i_method_key.value,
                },
                session_id: __session_id,
            };
            setIsLoading(true);
            axios
                .post("ventes-paiement", data)
                .then((response) => {
                    setIsLoading(false);
                    cashSound();

                    // If it's a partial payment, ask if they want to add another payment
                    if (isPartialPayment) {
                        $("#paiement-modal").modal("hide");

                        // Store the current sale information
                        const venteId = response.data.vente_id;
                        const remainingBalance = (parseFloat(totalAmount) - parseFloat(paiement.i_montant)).toFixed(2);

                        Swal.fire({
                            icon: "success",
                            title: "Paiement partiel enregistré !",
                            html: `${response.data.message}<br><br>Voulez-vous ajouter un autre paiement à cette vente?`,
                            showCancelButton: true,
                            confirmButtonText: "Oui, ajouter un paiement",
                            cancelButtonText: "Non, terminer",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-lg btn-soft-success mx-2",
                                cancelButton: "btn btn-lg btn-soft-secondary mx-2",
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Update the payment amount to the remaining balance and store the vente_id
                                setPaiement({
                                    i_date_paiement: "",
                                    i_montant: remainingBalance,
                                    i_note: "",
                                    i_method_key: __methodes[0],
                                    i_reference: "",
                                    i_date: "",
                                    i_compte_id: __comptes[0],
                                    vente_id: venteId
                                });

                                // Show the payment modal
                                setTimeout(() => {
                                    $("#paiement-modal").modal("show");
                                }, 500);
                            } else {
                                resetAll();
                                if (response.data.template != null) {
                                    PrintElem(response.data.template);
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Action réussie !",
                            html: response.data.message,
                            confirmButtonText: "Ok !",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: "btn btn-lg btn-soft-success mx-2",
                            },
                        });
                        resetAll();
                        $("#paiement-modal").modal("hide");
                        if (response.data.template != null) {
                            PrintElem(response.data.template);
                        }
                    }
                })
                .catch((error) => {
                    setIsLoading(false);
                    if (error.response && error.response.status == 422) {
                        if (error.response.data && error.response.data.error) {
                            Swal.fire({
                                icon: "warning",
                                title: "Attention !",
                                html: error.response.data.error,
                                confirmButtonText: "Ok !",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-lg btn-soft-danger mx-2",
                                },
                            });
                            return;
                        }
                        if (error.response.data && error.response.data.error) {
                            Swal.fire({
                                icon: "warning",
                                title: "Attention !",
                                text: error.response.data.error,
                                confirmButtonText: "Ok !",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-lg btn-soft-danger mx-2",
                                },
                            });
                        } else if (error.response.data && error.response.data.errors) {
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
    };
    const resetAll = () => {
        setItems([]);
        setClient(__default_client);
        setPrixModification(__prixModification);
        setType("bc");
        setPaiement({
            i_date_paiement: "",
            i_montant: 0,
            i_note: "",
            i_method_key: __methodes[0],
            i_date: "",
            i_reference: "",
            i_compte_id:__comptes[0],
            vente_id: null
        });
    };

    useEffect(() => {
        setPaiement({
            ...paiement,
            i_montant: total_ttc.toFixed(2),
        });
    }, [items]);
    // loader element
    const loader = () => (
        <div
            className="cover-spin"
            style={{
                height: "100vh",
                width: "100vw",
                zIndex: 999999,
                top: 0,
                left: 0,
                position: "fixed",
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
                backgroundColor: "rgba(0,0,0,0.5)",
            }}
            dangerouslySetInnerHTML={{__html: __spinner_element_lg}}
        ></div>
    );

    //-------- JSX PAGE -----
    return (
        <>
            <div className="row m-0" id="main-row">
                {isLoading ? loader() : ""}
                <div className="col-12">
                    <div className="card bg-transparent shadow-none pb-0">
                        <div className="card-body pb-1 px-0">
                            <div className="float-end">
                                <button
                                    onClick={() => printCloture()}
                                    className="btn btn-info mx-1">
                                    <i className="fa fa-file-contract "></i>
                                </button>
                                {(__rapport_ac_enabled == 1 || __rapport_as_enabled == 1) && <RapportsPage/>}

                                {__is_depenses && (<button
                                    className="btn btn-soft-danger mx-1"
                                    data-bs-target="#depense-modal" data-bs-toggle="modal">
                                    <i className="fa fa-money-bill-alt "> Dépenses</i>
                                </button>)}

                                {__is_demandes && (
                                    <a href="/point-de-vente/demandes"
                                       className="btn btn-soft-success mx-1"
                                    >
                                        <i className="fas fa-external-link-alt me-2"></i>
                                        Demandes
                                    </a>
                                )}
                                { __is_historique && (
                                    <button
                                        className="btn btn-primary mx-1 shadow-sm"
                                        type="button"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvasExample"
                                    >
                                        <i className="fa fa-history "
                                           data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-primary font-size-10"></div></div>'
                                           data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip"
                                           data-bs-placement="top"
                                           data-bs-original-title="Historique"></i> Historique
                                    </button>
                                )}

                                <ZoomButtons/>
                                <FullScreenButton/>
                                <ShutdownButton setIsLoading={setIsLoading}/>
                            </div>
                            <div className="d-flex">
                                <a href="/" className="text-primary h3 me-3">
                                    <i className="fa fa-arrow-left"></i>
                                </a>
                                <h3>
                                    Point de vente
                                    <span className="h6 ms-1">
                                        ({__magasin_ref})
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-7">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-5">
                                    <div className="input-group flex-nowrap">
                                        <div className="w-100">
                                            <ClientSelect client={client} setClient={setClient}/>
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
                                    <SearchbarWithType
                                        searchValue={searchValue}
                                        setSearchValue={setSearchValue}
                                        validateSearch={validateSearch}
                                        populateData={populateData}
                                        isCodeBarre={__is_code_barre}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-5">
                    <div className="card">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-6">
                                    <button
                                        className={
                                            type === "bc"
                                                ? "btn btn-block w-100 btn-primary border-primary "
                                                : "btn btn-block w-100 border-primary text-primary"
                                        }
                                        onClick={() => {
                                            resetAll();
                                            setType("bc");
                                        }}
                                        style={{
                                            border:
                                                type === "bc"
                                                    ? "solid 3px"
                                                    : "dashed 3px",
                                            transition: "all .s ease-in-out",
                                        }}
                                    >
                                        Vente
                                    </button>
                                </div>
                                <div className="col-6">
                                    <button
                                        className={
                                            type === "br"
                                                ? "btn btn-block w-100 btn-success border-success"
                                                : "btn btn-block w-100 border-success text-success"
                                        }
                                        onClick={() => {
                                            resetAll();
                                            setType("br");
                                        }}
                                        style={{
                                            border:
                                                type === "br"
                                                    ? "solid 3px"
                                                    : "dashed 3px",
                                        }}
                                    >
                                        Retour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <TableCaisse
                    items={items}
                    type={type}
                    cashOut={cashOut}
                    setItems={setItems}
                    resetAll={resetAll}
                    paiement={paiement}
                    is_reduction_on={true}
                    is_prix_editable={prixModification}
                    setIsLoading={setIsLoading}
                    setPaiement={setPaiement}
                    payer={payer}
                    total={total_ttc}
                />

            </div>
            <DepenseModal depense={depense} setDepense={setDepense}/>
            <History reduction={false}/>
            <ClientModal setClient={setClient} setIsLoading={setIsLoading}/>
            <PaiementModal paiement={paiement} setPaiement={setPaiement} payer={payer}/>
        </>
    );
}

axios.defaults.baseURL = __api_url;
axios.defaults.headers.common["X-CSRF-TOKEN"] = sessionStorage.getItem('csrf');
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.headers.Authorization = "Bearer " + sessionStorage.getItem('access_token');

ReactDOM.createRoot(document.getElementById("root")).render(
    <React.StrictMode>
        <PosClassic/>
    </React.StrictMode>
);
