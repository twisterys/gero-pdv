import React from "react";
import axios from "axios";

export  const ShutdownButton = ({setIsLoading}) =>{
    const shutDown = () => {
        Swal.fire({
            title: "Est-vous sûr?",
            text: "Vous voulez vraiment arreter la session !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Oui, Arreter !",
            buttonsStyling: false,
            customClass: {
                confirmButton: "btn btn-soft-danger mx-2",
                cancelButton: "btn btn-soft-secondary mx-2",
            },
            didOpen: () => {
                $(".btn").blur();
            },
            preConfirm: async () => {
                Swal.showLoading();
                setIsLoading(true);
                try {
                    const [response] = await Promise.all([
                        axios.delete("pos-session/terminer", {
                            headers: {
                                "X-CSRF-TOKEN": __csrf_token,
                            },
                        }),
                    ]);

                    return response;
                } catch (jqXHR) {
                    setIsLoading(false);
                    let errorMessage =
                        "Une erreur s'est produite lors de la demande.";
                    if (jqXHR.status === 404) {
                        errorMessage = "La ressource n'a pas été trouvée.";
                    }
                    Swal.fire({
                        title: "Erreur",
                        text: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-soft-danger mx-2",
                        },
                    });

                    throw jqXHR;
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value) {
                    Swal.fire({
                        title: "Succès",
                        text: "Session est terminée.",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-soft-success mx-2",
                        },
                    }).then((result) => {
                        window.location.reload();
                    });
                } else {
                    setIsLoading(false);
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
    };

    return (
        <button
            onClick={() => shutDown()}
            className="btn btn-danger mx-1 shadow-sm"
        >
            <i className="fa fa-power-off"></i>
        </button>
    )
}
