import axios from "axios";
import { useEffect, useState } from "react";

export const MethodeLivraison = ({
    methodeLivraison,
    setMethodeLivraison,
    setIsLoading,
}) => {
    const [methodesLivraison, setmethodesLivraison] = useState([]);
    const populateCommercials = () => {
        if (methodesLivraison.length === 0) {
            return (
                <h4 className="text-center text-muted">
                    Il y'a aucune methode de livraison
                </h4>
            );
        }
        return methodesLivraison.map(function (methode) {
            return (
                <div
                    className="p-2 col-3 align-items-stretch"
                    key={methode.id}
                    style={{ cursor: "pointer" }}
                    onClick={() =>
                        methode.id === methodeLivraison
                            ? setMethodeLivraison(null)
                            : setMethodeLivraison(methode.id)
                    }
                >
                    <div
                        className={
                            methodeLivraison === methode.id
                                ? "article-card border-2 border d-flex flex-column align-items-center rounded overflow-hidden h-100  shadow-sm selected"
                                : "article-card border-2 border d-flex flex-column align-items-center rounded overflow-hidden h-100  shadow-sm"
                        }
                    >
                        <div
                            className="article-card-header mb-1 w-100 overflow-hidden d-flex align-items-center"
                            style={{ maxHeight: "150px" }}
                        >
                            <img src={methode.image} alt="" className="w-100" />
                        </div>
                        <div className="p-1">
                            <h4 className="text-capitalize text-center m-0">
                                {methode.name}
                            </h4>
                        </div>
                    </div>
                </div>
            );
        });
    };

    const fetchMethodesLivraison = () => {
        setIsLoading(true);
        axios
            .get("methodes-livraison", {})
            .then((response) => {
                setIsLoading(false);
                setmethodesLivraison(response.data);
            })
            .catch((error) => {
                setIsLoading(false);
                toastr.error("Une erreur est survenue");
            });
    };
    useEffect(() => {
        axios
            .get("methodes-livraison", {})
            .then((response) => {
                setIsLoading(false);
                setmethodesLivraison(response.data);
            })
            .catch((error) => {
                setIsLoading(false);
                toastr.error("Une erreur est survenue");
            });
    }, []);

    return (
        <div className="card position-absolute" style={{ inset: ".3rem" }}>
            <div className="card-body position-relative d-flex flex-column h-100">
                <div className="card-title">
                    <div className="d-flex align-items-center justify-content-between">
                        <h4 className="m-0">Méthodes de livraison</h4>
                        <i
                            className="fa fa-sync"
                            style={{
                                cursor: "pointer",
                            }}
                            onClick={() => fetchMethodesLivraison()}
                        ></i>
                    </div>
                    <hr />
                </div>
                <div
                    className="row overflow-y-auto scrollbar h-100"
                    style={{ maxHeight: "100%" }}
                >
                    {populateCommercials()}
                </div>
            </div>
        </div>
    );
};
