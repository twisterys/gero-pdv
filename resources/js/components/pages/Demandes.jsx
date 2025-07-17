import { useEffect, useState } from "react";
import axios from "axios";
import React from "react";
import ReactDOM from "react-dom";
import {MesDemandes} from "../pos-components/demande/MesDemandes.jsx";
import {DemandesExterne} from "../pos-components/demande/DemandesExterne.jsx";
import {AddDemande} from "../pos-components/demande/AddDemande.jsx";

function PosParfums() {
    // set all variable needed
    const [fontSize, setFontSize] = useState(16);
    const [isExpanded, setIsExpanded] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const [refreshMesDemande,setRefreshMesDemande] = useState(false);
    const [refreshDemandesExterne,setRefreshDemandesExterne] = useState(false);

    const [addDemande, setAddDemande] = useState(false)


    // toggle full screen mode
    const toggleFullScreen = () => {
        setIsExpanded(!isExpanded);
        if (!isExpanded) {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    };
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
            dangerouslySetInnerHTML={{ __html: __spinner_element_lg }}
        ></div>
    );

    // update font size
    useEffect(() => {
        document.documentElement.style.fontSize = fontSize + "px";
    }, [fontSize]);



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
                                    onClick={() => setFontSize(fontSize + 1)}
                                    className="btn btn-soft-info mx-1 shadow-sm"
                                >
                                    <i className="fa fa-search-plus"></i>
                                </button>
                                <button
                                    onClick={() => setFontSize(fontSize - 1)}
                                    className="btn btn-soft-purple mx-1 shadow-sm"
                                >
                                    <i className="fa fa-search-minus"></i>
                                </button>
                                <button
                                    className="btn btn-soft-warning mx-1 shadow-sm  "
                                    onClick={() => toggleFullScreen()}
                                >
                                    <i
                                        className={
                                            isExpanded
                                                ? "fa fa-compress"
                                                : "fa fa-expand"
                                        }
                                    ></i>
                                </button>
                            </div>
                            <div className="d-flex">
                                <a href="/point-de-vente" className="text-primary h3 me-3">
                                    <i className="fa fa-arrow-left"></i>
                                </a>
                                <h3>
                                    Demandes
                                    <span className="h6 ms-1">
                                        ({__magasin_ref})
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                {addDemande ? <AddDemande setAddDemande={setAddDemande}/> :
                    <>
                        <div
                            className="col-6 d-flex flex-column"
                            style={{ height: "calc(100vh - 10rem)" }}
                        >
                            <div className="card position-absolute" style={{ inset: ".3rem" }}>
                                <div className="card-body pb-0 flex-grow-0">
                                    <div className="d-flex justify-content-between align-items-center">
                                        <h5 className="m-0">Mes demandes</h5>
                                      <div>
                                          <button className="btn btn-rounded btn-soft-warning me-2" onClick={()=>setRefreshMesDemande(!refreshMesDemande)} ><i className="fa fa-sync"></i></button>
                                          <button className="btn btn-soft-success btn-rounded" onClick={()=> setAddDemande(true)} ><i className="fa fa-plus"></i></button>
                                      </div>
                                    </div>
                                    <hr/>
                                </div>
                                <div className="card-body overflow-y-auto scrollbar">
                                     <MesDemandes refresh={refreshMesDemande}/>
                                </div>
                            </div>
                        </div>
                        <div
                            className="col-6 d-flex flex-column"
                            style={{ height: "calc(100vh - 10rem)" }}
                        >
                            <div className="card position-absolute" style={{ inset: ".3rem" }}>
                                <div className="card-body pb-0 flex-grow-0">
                                    <div className="d-flex justify-content-between align-items-center">
                                        <h5 className="m-0">Demandes externe</h5>
                                        <button className="btn btn-rounded btn-soft-warning" onClick={()=>setRefreshDemandesExterne(!refreshDemandesExterne)} ><i className="fa fa-sync"></i></button>
                                    </div>
                                    <hr/>
                                </div>
                                <div className="card-body overflow-y-auto scrollbar">
                                  <DemandesExterne  refresh={refreshDemandesExterne} />
                                </div>
                            </div>
                        </div>
                    </>
                }
            </div>
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
        <PosParfums />
    </React.StrictMode>
);
