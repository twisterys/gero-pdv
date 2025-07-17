import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import Select from 'react-select';
import { SearchbarDropDown } from "./SearchbarDropDown";

export const SearchbarWithType = ({ searchValue, setSearchValue, populateData, isCodeBarre }) => {
    const [isSearching, setIsSearching] = useState(false);
    const [results, setResults] = useState([]);
    const [timer, setTimer] = useState(null);
    const [searchType, setSearchType] = useState("designation");
    const searchbarRef = useRef(null);

    const searchOptions = [
        { value: "designation", label: "Désignation" },
        { value: "reference", label: "Référence" },
        ...(isCodeBarre == 1 ? [{ value: "code_barre", label: "Code barre" }] : [])
    ];

    const validateSearchById = (id) => {
        axios.get(`articles/${id}`).then((response) => {
            populateData({ data: [response.data] });
            setResults([]);
        });
    };

    const search = (searchValue) => {
        setSearchValue(searchValue);

        if (!searchValue) {
            setResults([]);
            return;
        }

        setIsSearching(true);
        clearTimeout(timer);

        setTimer(
            setTimeout(() => {
                axios
                    .get("articles-liste-type", {
                        params: {
                            search: searchValue,
                            type: searchType
                        },
                    })
                    .then((response) => {
                        setIsSearching(false);

                        if (response.data.length === 1) {
                            validateSearchById(response.data[0].id);
                            return;
                        }
                        if(response.data.length === 0){
                            toastr.warning("Aucun article trouvé");
                        }
                        setResults(response.data);
                    })
                    .catch((error) => {
                        setIsSearching(false);
                        console.error("Search error:", error);
                    });
            }, 300)
        );
    };

    const handleSearch = () => {
        if (searchType === "designation") {
            search(searchValue);
        } else {
            if (searchValue) {
                search(searchValue);
            }
        }
    };

    const customStyles = {
        control: (base, state) => ({
            ...base,
            minHeight: '38px',
            borderRadius: '0.375rem 0 0 0.375rem',
            borderRight: 0,
            borderColor: '#dee2e6',
            backgroundColor: '#e9ecef',
            color: '#495057',
            boxShadow: 'none',
            '&:hover': {
                borderColor: '#dee2e6',
                backgroundColor: '#dee2e6'
            },
            '&:focus-within': {
                borderColor: '#dee2e6',
                boxShadow: 'none'
            }
        }),
        menu: (base) => ({
            ...base,
            zIndex: 20
        }),
        valueContainer: (base) => ({
            ...base,
            padding: '0 8px'
        }),
        option: (base, state) => ({
            ...base,
            backgroundColor: state.isSelected ? '#adb5bd' : 'white',
            color: '#212529',
            '&:hover': {
                backgroundColor: '#dee2e6'
            }
        }),
        indicatorSeparator: (base) => ({
            ...base,
            display: 'none'
        })
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
            <div className="w-auto" style={{ minWidth: '120px' }}>
                <Select
                    value={searchOptions.find(option => option.value === searchType)}
                    onChange={(option) => {
                        setSearchType(option.value);
                        setResults([]);
                    }}
                    options={searchOptions}
                    styles={customStyles}
                    isSearchable={false}
                    className="select-container"
                    classNamePrefix="select"
                />
            </div>
            <input
                autoComplete="off"
                id="search"
                className="form-control"
                placeholder="Rechercher un produit..."
                value={searchValue}
                onChange={(event) => setSearchValue(event.target.value)}
                onInput={(event) =>
                    searchType === "designation" && search(event.target.value)
                }
                onFocus={(event) =>
                    searchType === "designation" && search(event.target.value)
                }
            />
            <button
                className="btn btn-light"
                onClick={handleSearch}
            >
                {!isSearching ? (
                    <i className="fa fa-search"></i>
                ) : (
                    <span
                        className="spinner-border spinner-border-sm"
                        role="status"
                        aria-hidden="true"
                    />
                )}
            </button>
            {searchType === "designation" && results.length > 0 && (
                <SearchbarDropDown
                    items={results}
                    validateSearchById={validateSearchById}
                />
            )}
        </div>
    );
};

export default SearchbarWithType;
