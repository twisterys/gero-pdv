export const DepenseCard = ({ item }) => {
    return (
        <div
            className="col-6"
            key={item.id}
        >
            <div className="card border">
                <div className="card-body">
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="text-black-50">{item.reference}</h5>
                        <h5 className="text-black-50">{item.date}</h5>
                    </div>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="m-0">
                            <span className="text-black-50">Dépense:</span>
                        </h5>
                        <h5 className="m-0">{item.nom}</h5>
                    </div>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="m-0">
                            <span className="text-black-50">Bénéficiaire:</span>
                        </h5>
                        <h5 className="m-0">{item.beneficiaire}</h5>
                    </div>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="m-0">
                            <span className="text-black-50">Catégorie:</span>
                        </h5>
                        <h5 className="m-0">{item.category}</h5>
                    </div>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="m-0">
                            <span className="text-black-50">Montant TTC:</span>
                        </h5>
                        <h5 className="m-0">{item.total} MAD</h5>
                    </div>
                </div>
            </div>
        </div>
    );
};
