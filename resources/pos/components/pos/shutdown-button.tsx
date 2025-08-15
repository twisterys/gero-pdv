import Swal from "sweetalert2";
import {endpoints} from "../../services/api";

const ShutdownButton = () => {
    const shutDown = () => {
        Swal.fire({
            title: "Est-vous sûr?",
            text: "Vous voulez vraiment arreter la session !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Oui, Arreter !",
            buttonsStyling: false,
            customClass: {
                confirmButton: "py-2 px-4 bg-red-500 hover:bg-red-600 mx-2 rounded-md text-white",
                cancelButton: "py-2 px-4 bg-gray-500 hover:bg-gray-600 mx-2 rounded-md text-white",
            },
            preConfirm: async () => {
                Swal.showLoading();
                try {
                    const response = endpoints.system.shutdown()

                    return response;
                } catch (jqXHR:any) {
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
                            confirmButton: "py-2 px-4 bg-red-500 hover:bg-red-600 mx-2 rounded-md text-white",
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
                            confirmButton: "py-2 px-4 bg-green-500 hover:bg-green-600 mx-2 rounded-md text-white",
                        },
                    }).then((result) => {
                        window.location.href = "/";
                    });
                } else {
                    Swal.fire({
                        title: "Erreur",
                        text: "Une erreur s'est produite lors de la demande.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "py-2 px-4 bg-red-500 hover:bg-red-600 mx-2 rounded-md text-white",
                        },
                    });
                }
            }
        });
    };
    return (
        <button className="bg-red-500 text-white rounded-full p-2 shadow-md hover:bg-red-600 transition-colors" onClick={shutDown} >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path stroke="currentColor" strokeLinecap="round" strokeWidth="1.5" d="M12 2v4"/><path fill="currentColor" d="M12.75 2.75a.75.75 0 0 0-1.5 0v4a.75.75 0 0 0 1.5 0z"/><path fill="currentColor" d="M8.792 5.147a.75.75 0 1 0-.584-1.382A9.75 9.75 0 0 0 2.25 12.75c0 5.385 4.365 9.75 9.75 9.75s9.75-4.365 9.75-9.75a9.75 9.75 0 0 0-5.958-8.985a.75.75 0 1 0-.584 1.382A8.253 8.253 0 0 1 12 21A8.25 8.25 0 0 1 8.792 5.147"/></g></svg>
        </button>
    );
};

export default ShutdownButton;
