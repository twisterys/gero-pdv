import React from "react";
import {useSettingsStore} from "../../stores/settings-store";

const HistoryButton = ({setHistoryOpen}: { setHistoryOpen: React.Dispatch<React.SetStateAction<boolean>> }) => {
    const {features} = useSettingsStore();
    if (!features.history) return null;
    return (
        <button
            onClick={() => setHistoryOpen(true)}
            className="px-4 py-2 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 transition-colors"
        >
            <svg className="inline-block mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24">
                <path fill="currentColor"
                      d="M11 7a1 1 0 0 1 1-1h5.5a1 1 0 1 1 0 2H12a1 1 0 0 1-1-1m0 10a1 1 0 0 1 1-1h5.5a1 1 0 1 1 0 2H12a1 1 0 0 1-1-1m1-5a1 1 0 0 0 0 2h9.5a1 1 0 1 0 0-2z"/>
                <path fill="currentColor"
                      d="M2 7a2 2 0 0 1 2-2h3.5a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/>
            </svg>
            Historique
        </button>
    );
};

export default HistoryButton;
