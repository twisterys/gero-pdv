import {useEffect, useState} from "react";
import axios from "axios";

export const Articles = ({items,setItems}) => {
    const [articles, setArticles] = useState([]);
    const [nextLink,setNextLink] = useState(null);
    const [isFetching,setIsFetching] = useState(false);
    const [familles,setFamilles] = useState([]);
    const [marques,setMarques] = useState([]);
    const  [famille,setFamille] = useState("");
    const  [marque,setMarque] = useState("");
    useEffect(() => {
        setIsFetching(true)
        getFamille();
        getMarques();
        axios({
            url:'articles-all',
            method:'get',
            params: {
                famille:famille,
                marque:marque,
            }
        }).then(response => {
            return response.data;
        }).then(response => {
            setIsFetching(false)
            setNextLink(response.links.next)
            setArticles(response.data);
        }).catch()

    }, [marque,famille]);
    const getFamille = ()=>{
        axios.get('familles').then(response=>{
            return response.data;
        }).then(response=>{
            setFamilles(response);
        })
    }
    const getMarques = ()=>{
        axios.get('marques').then(response=>{
            return response.data;
        }).then(response=>{
            setMarques(response);
        })
    }
    function fetchNewArticlesOnScroll(){
        if(nextLink){
            setIsFetching(true)

            axios.get(nextLink).then(response => {
                return response.data;
            }).then(response => {
                setIsFetching(false)

                setNextLink(response.links.next)
                setArticles([...articles,...response.data])
            }).catch()
        }
    }
    const handleScroll = (event)=>{
        if (event.nativeEvent.srcElement.scrollHeight - event.nativeEvent.srcElement.offsetHeight === event.nativeEvent.srcElement.scrollTop){
            fetchNewArticlesOnScroll()
        }
    }
    const addArticle = (article) =>{
        let newItem = article;
        newItem.name = article.designation;
        newItem.id = article.id;
        newItem.prix = article.prix;
        newItem.reference = article.reference;
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
    }
    const populate =
        articles.length > 0 ?
            articles.map(article => {
                return (
                    <div className="p-2 col-xl-3 col-md-4 col-sm-6 col-12 align-items-stretch" onClick={()=>addArticle(article)} style={{cursor:'pointer'}}  key={article.id}>
                        <div
                            className="article-card border-2 border d-flex flex-column align-items-center rounded overflow-hidden h-100  shadow-sm">
                            <div className="article-card-header mb-1 w-100 overflow-hidden d-flex align-items-center" style={{maxHeight:'120px'}}
                            >
                                <img className="w-100"  src={article.image} alt={article.reference}/>
                            </div>
                            <div className="article-card-content w-100 p-1">
                                <h6 className="text-capitalize text-center">{article.designation}</h6>
                                <p className="m-0 font-size-12 text-center text-muted">{article.reference}</p>
                                {/*<p className="m-0 font-size-12 text-center text-muted">*/}
                                {/*    {article.quantity}*/}
                                {/*    <span className="ms-1">{article.unit}</span></p>*/}
                                <p className="m-0 text-center fw-bold my1 text-primary">
                                    {article.prix} MAD</p>
                            </div>
                        </div>
                    </div>
                )
            })
            :
            <h5 className="text-center pt-4" >Aucun élément trouvé</h5>
return (<>
    <div className="col-12">
        <h5>Articles</h5>
        <hr/>
    </div>
    <div className=" row m-0">
        <div className="col-6 px-0">
            <label htmlFor="famille" className="form-label" >Familles</label>
            <select id="famille" className="form-select" value={famille} onChange={e=>setFamille(e.target.value)}  >
                <option value="">Tous</option>
                {familles.map(famille =>{
                    return ( <option value={famille.id} key={famille.id}>{famille.nom}</option>);
                })}
            </select>
        </div>
        <div className="col-6">
            <label htmlFor="marque" className="form-label">Marques</label>

            <select className="form-select"  value={marque} onChange={e=>setMarque(e.target.value)} >
                <option value="">Tous</option>
                {marques.map(marque =>{
                    return ( <option value={marque.id} key={marque.id}>{marque.nom}</option>);
                })}
            </select>

        </div>
    </div>
    <div className="row w-100 overflow-y-scroll" onScroll={handleScroll} >
        {populate}
    </div>
    {
        isFetching ? <div className="d-flex justify-content-center py-3">
            <div className="spinner-border" role="status">
                <span className="visually-hidden">Loading...</span>
            </div>
        </div> : null
    }

</>)
}
