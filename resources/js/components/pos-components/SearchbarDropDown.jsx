
import {PropTypes} from "prop-types";


export const SearchbarDropDown = ({items, validateSearchById}) => {

    const list = items.map((item)=>(
        <div className="p-2 w-100 btn btn-soft-light text-start text-black rounded-0" onClick={()=>validateSearchById(item.id)} key={item.id} >
             {item.name} ({item.reference})
        </div>
    )
  );
  return (
    <div className="position-absolute mt-1 top-100 bg-white py-1 right-0 w-100 border border-light rounded shadow-sm overflow-y-scroll"
         style={{zIndex: 100,maxHeight:'30rem'}}
    >
        {list}
    </div>
  );
};
SearchbarDropDown.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            name: PropTypes.string.isRequired,
            prix: PropTypes.number,
            reference :PropTypes.reference
        })
    ).isRequired,
    validateSearchById: PropTypes.func.isRequired
};
