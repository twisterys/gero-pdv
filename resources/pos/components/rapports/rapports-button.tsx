import React from 'react';
import {useSettingsStore} from "../../stores/settings-store";
import {Link} from "react-router";

const RapportsButton = () => {

    const { rapports } = useSettingsStore();
    if (!rapports || !Object.values(rapports).some(Boolean)) return null;
    return (
        <Link to="/rapports" className="px-4 py-2 rounded-md text-white bg-indigo-500 hover:bg-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" className="inline-block mr-2" width="1em" height="1em"
                 viewBox="0 0 24 24">
                <path fill="currentColor" fillRule="evenodd"
                      d="M14 20.5V4.25c0-.728-.002-1.2-.048-1.546c-.044-.325-.115-.427-.172-.484s-.159-.128-.484-.172C12.949 2.002 12.478 2 11.75 2s-1.2.002-1.546.048c-.325.044-.427.115-.484.172s-.128.159-.172.484c-.046.347-.048.818-.048 1.546V20.5z"
                      clipRule="evenodd"/>
                <path fill="currentColor"
                      d="M8 8.75A.75.75 0 0 0 7.25 8h-3a.75.75 0 0 0-.75.75V20.5H8zm12 5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75v6.75H20z"
                      opacity="0.7"/>
                <path fill="currentColor" d="M1.75 20.5a.75.75 0 0 0 0 1.5h20a.75.75 0 0 0 0-1.5z"
                      opacity="0.5"/>
            </svg>
            <span>Rapports</span>
        </Link>
    );
};

export default RapportsButton;
