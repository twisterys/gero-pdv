import { useState, useEffect, useRef } from "react";
import axios from "axios";
import { SearchbarDropDown } from "./SearchbarDropDown";

/*eslint-disable */
export const Searchbar = ({ searchValue, setSearchValue, populateData }) => {
    const [isSearching, setIsSearching] = useState(false);
    const [results, setResults] = useState([]);
    const [timer, setTimer] = useState(null);
    const searchbarRef = useRef(null);

    const validateSearchById = (id) => {
        axios.get("articles/" + id).then((response) => {
            populateData({ data: [response.data] });
            setResults([]);
        });
    };

    const search = (searchValue) => {
        setSearchValue(searchValue);
        if (searchValue.length === 0) {
            setResults([]);
            return;
        }
        setIsSearching(true);
        clearTimeout(timer);
        setTimer(
            setTimeout(() => {
                axios
                    .get("articles-liste", {
                        params: {
                            search: searchValue,
                        },
                    })
                    .then((response) => {
                        setIsSearching(false);
                        if (response.data.length === 1) {
                            validateSearchById(response.data[0].id);
                            return;
                        }
                        setResults(response.data);
                    })
                    .catch((error) => {
                        setIsSearching(false);
                    });
            }, 1000)
        );
    };

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (searchbarRef.current && !searchbarRef.current.contains(event.target)) {
                setResults([]);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, [searchbarRef]);

    return (
        <div className="input-group position-relative z-10" ref={searchbarRef}>
            <input
                autoComplete="off"
                id="search"
                className="form-control"
                placeholder="Rechercher un produit..."
                value={searchValue}
                onChange={() => setSearchValue(event.target.value)}
                onInput={() => search(event.target.value)}
                onFocus={() => search(event.target.value)}
            />
            <button className="btn btn-light">
                {!isSearching ? (
                    <i className="fa fa-search"></i>
                ) : (
                    <>
                        <span
                            className="spinner-border spinner-border-sm"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        <span className="sr-only">Loading...</span>
                    </>
                )}
            </button>
            {results && results.length > 0 && (
                <SearchbarDropDown
                    items={results}
                    validateSearchById={validateSearchById}
                />
            )}
        </div>
    );
};
