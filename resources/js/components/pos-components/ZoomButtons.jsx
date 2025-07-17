import {useEffect , useState} from "react";

export const ZoomButtons = () => {
    const [fontSize, setFontSize] = useState(16);

    useEffect(() => {
        document.documentElement.style.fontSize = fontSize + "px";
    }, [fontSize]);
    return (
        <>
            <button
                onClick={() => setFontSize(fontSize + 1)}
                className="btn btn-soft-info mx-1 shadow-sm"
            >
                <i className="fa fa-search-plus"></i>
            </button>
            <button
                onClick={() => setFontSize(fontSize - 1)}
                className="btn btn-soft-purple mx-1 shadow-sm"
            >
                <i className="fa fa-search-minus"></i>
            </button>
        </>
    );
}
