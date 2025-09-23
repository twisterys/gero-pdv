import React, {useState} from "react";
import Articles from "../../components/articles/articles";
import CartLayout from "../../components/cart/cart-layout";
import TypeToggler from "~/pos/type-toggler";
import {DepenseButton} from "../../components/depense";
import {HistoryOffcanvas} from "../../components/history";
import {Link} from "react-router";
import {useSettingsStore} from "../../stores/settings-store";
import Keyboard from "../../components/keyboard";
import CaissePanel from "../../components/pos/caisse-panel";
import ShutdownButton from "../../components/pos/shutdown-button";
import ClotureButton from "../../components/pos/cloture-button";
import DemandesButton from "../../components/demandes/demandes-button";
import RapportsButton from "../../components/rapports/rapports-button";
import HistoryButton from "../../components/history/history-button";
import { RebutButton } from "../../components/rebut";

export function POS() {
    const [isHistoryOpen, setHistoryOpen] = useState(false);
    const {features, posType, url} = useSettingsStore();
    const returnLink = url ?? "/";

    return (
        <div className="h-full w-full p-4 flex flex-col">
            <div className="flex justify-between items-center mb-6">
                <div className="flex items-center gap-2">
                    <a href={returnLink} className=" text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"
                                  strokeWidth="1.5" d="m4 12l6-6m-6 6l6 6m-6-6h10.5m5.5 0h-2.5"/>
                        </svg>
                    </a>
                    <h1 className="text-2xl font-semibold text-gray-900">Point de vente</h1>
                </div>
                <div className="flex space-x-3">
                    <ClotureButton/>
                    <RapportsButton/>
                    <DemandesButton/>
                    <DepenseButton/>
                    <RebutButton/>
                    <HistoryButton setHistoryOpen={setHistoryOpen}/>
                    <ShutdownButton/>
                </div>
            </div>
            <div className="flex-1 flex flex-col md:flex-row gap-6 overflow-hidden pb-3">
                <div className="w-full md:w-2/3 bg-white rounded-lg shadow-sm flex">
                    <CartLayout/>
                </div>

                <div className="w-full md:w-1/3 overflow-hidden flex flex-col   gap-4 ">
                    <div className="bg-white rounded-lg shadow-sm p-4 w-full flex-none max-h-fit ">
                        <TypeToggler/>
                    </div>
                    <div
                        className={`bg-white rounded-lg shadow-sm p-4 w-full flex-grow  flex flex-col overflow-hidden ${posType === "caisse" ? "hidden" : ""}`}>
                        {posType === "classic" && <Articles/>}
                        {posType === "parfums" && <Keyboard/>}
                    </div>
                    {posType === "caisse" && <CaissePanel/>}
                </div>

            </div>

            {
                features.history && <HistoryOffcanvas isOpen={isHistoryOpen} onClose={() => setHistoryOpen(false)}/>
            }
        </div>
    );
}
