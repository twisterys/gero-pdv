import axios from "axios";
export const HistoryCard = ({ item, setHistory }) => {
    const printTicket = (id) =>{
        axios.get('ventes/ticket/'+id).then(response=>{
           PrintElem(response.data);
        }).catch(response=>{

        })
    }
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
    return (
        <a
            className="col-12"
            key={item.id}
            data-bs-dismiss="offcanvas"
        >
            <div className="card py-0 border flex-row">
                <div className="card-body py-2"
                     onClick={() => {
                         setHistory(item);
                         setTimeout(() => {
                             $("#history-modal").modal("show");
                         }, 500);
                     }}>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="text-black-50 col-4 m-0">{item.reference}</h5>
                        <h5 className="text-black-50 col-4 m-0">{item.time}</h5>
                        <h5 className="m-0 col-4 text-end">{item.total}</h5>
                    </div>
                </div>
                <button className="btn btn-primary" onClick={()=>printTicket(item.id)} > <i className="fa fa-print"></i> </button>
            </div>
        </a>
    );
};
