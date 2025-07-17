export const HistoryModal = ({ item, reduction }) => {
    return (
        <div
            class={"modal fade"}
            data-bs-backdrop="static"
            tabindex="-1"
            id="history-modal"
            style={{ display: "none" }}
        >
            <div class="modal-dialog modal-dialog-centered modal-lg">
                {item ? (
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title align-self-center">
                                {item.type} {item.reference}
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="row py-1 px-1 mx-0">
                                <div class=" col-sm-6 col-md-4 mt-3 d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                        style={{ width: "49px" }}
                                    >
                                        <i class="fa fa-id-card fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">
                                            Reference
                                        </span>
                                        <p class="mb-0 h5 text-black">
                                            {item.reference}
                                        </p>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-md-4 mt-3 d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                        style={{ width: "49px" }}
                                    >
                                        <i class="fa fa-building fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">
                                            Client
                                        </span>
                                        <p class="mb-0 h5 text-black text-capitalize">
                                            {item.client_nom}
                                        </p>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-md-4   mt-3  d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-danger p-2 d-flex align-items-center justify-content-center"
                                        style={{ width: "49px" }}
                                    >
                                        <i class="fa fa-calendar-alt fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">
                                            Date d'emission
                                        </span>
                                        <p class="mb-0 h5 text-black text-capitalize">
                                            {item.time}
                                        </p>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-md-4 mt-3 d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                        style={{ width: "49px" }}
                                    >
                                        <i class="fa fa-star fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">
                                            Statut
                                        </span>
                                        <p class="mb-0 h5 text-black text-capitalize">
                                            {item.statut}
                                        </p>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                                    <div
                                        class="rounded bg-soft-purple p-2 d-flex align-items-center justify-content-center"
                                        style={{ width: "49px" }}
                                    >
                                        <i class="fa fa-coins fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">
                                            Montant TTC{" "}
                                        </span>
                                        <p class="mb-0 h5 text-black text-capitalize">
                                            {item.total}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <table className="table table-bordered table-striped mt-3">
                                <thead>
                                    <tr className="bg-primary text-white">
                                        <th className="text-white">Produit</th>
                                        <th className="text-white">Quantité</th>
                                        <th className="text-white">Prix</th>
                                        {reduction ? (
                                            <th className="text-white">
                                                Reduction
                                            </th>
                                        ) : (
                                            ""
                                        )}
                                        <th className="text-white">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {item.lignes.map((item, index) => (
                                        <tr key={index}>
                                            <td>{item.article}</td>
                                            <td>
                                                {item.quantity} {item.unite}
                                            </td>
                                            <td>{item.price}</td>
                                            {reduction ? (
                                                <td>{item.reduction}</td>
                                            ) : (
                                                ""
                                            )}
                                            <td>{item.total_ttc}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal"
                            >
                                Fermer
                            </button>
                        </div>
                    </div>
                ) : (
                    ""
                )}
            </div>
        </div>
    );
};
