import {printHtml, printReport} from "../../utils/helpers";
import Swal from "sweetalert2";
import {endpoints} from "../../services/api";
import {useSettingsStore} from "../../stores/settings-store";

const ClotureButton = () => {
    const { features} = useSettingsStore();

    if (!features.cloture) return null;
    const printCloture = async () => {
        await endpoints.system.cloture().then(response => {
            Swal.fire({
                title: "Clôture de session",
                html: "<div class='bg-body p-3' >" + response.data + "</div>",
                showCancelButton: true,
                confirmButtonText: "Imprimer",
                cancelButtonText: "Annuler",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "py-2 px-4 bg-green-600 hover:bg-green-700 mx-2 rounded-md text-white",
                    cancelButton: "py-2 px-4 bg-gray-500 hover:bg-gray-600 mx-2 rounded-md text-white",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        printHtml(response.data)
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
        });
    }
    return (
        <button onClick={printCloture} className="bg-primary text-white rounded-md py-2 px-4  hover:bg-primary-600 transition-colors" >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path stroke="currentColor" strokeWidth="1.5" d="M6 17.983c-1.553-.047-2.48-.22-3.121-.862C2 16.243 2 14.828 2 12s0-4.243.879-5.121C3.757 6 5.172 6 8 6h8c2.828 0 4.243 0 5.121.879C22 7.757 22 9.172 22 12s0 4.243-.879 5.121c-.641.642-1.567.815-3.121.862"/><path stroke="currentColor" strokeLinecap="round" strokeWidth="1.5" d="M9 10H6" opacity="0.5"/><path stroke="currentColor" strokeLinecap="round" strokeWidth="1.5" d="M19 14H5m13 0v2c0 2.828 0 4.243-.879 5.121C16.243 22 14.828 22 12 22s-4.243 0-5.121-.879C6 20.243 6 18.828 6 16v-2"/><path stroke="currentColor" strokeWidth="1.5" d="M17.983 6c-.047-1.553-.22-2.48-.862-3.121C16.243 2 14.828 2 12 2s-4.243 0-5.121.879C6.237 3.52 6.064 4.447 6.017 6" opacity="0.5"/><circle cx="17" cy="10" r="1" fill="currentColor" opacity="0.5"/><path stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M15 16.5H9m4 2.5H9" opacity="0.5"/></g></svg>
        </button>
    );
};

export default ClotureButton;
