import axios from "axios";
import ReactSelectAsync from "react-select/async";
import React from "react";

export const ClientSelect = ({client,setClient}) => {
    return (<ReactSelectAsync
        classNames={{
            control: () =>
                "border-light shadow-none",
            option: (state) => {
                return state.isFocused
                    ? "bg-primary text-white"
                    : state.isSelected
                        ? "bg-soft-primary text-white"
                        : state.isDisabled
                            ? "bg-soft-light text-muted"
                            : "";
            },

        }}
        cache={false}
        defaultValue={__default_client}
        value={client}
        onChange={(choice) =>
            setClient(choice)
        }
        loadOptions={async (
            inputValue
        ) => {
            const response =
                await axios.get(
                    "clients-liste",
                    {
                        params: {
                            search: inputValue,
                        },
                    }
                );
            return response.data.map(
                (client) => ({
                    value: client.id,
                    label: client.nom,
                })
            );
        }}
    />)
}
