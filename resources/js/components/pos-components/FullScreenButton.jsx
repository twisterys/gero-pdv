import React, {useState} from "react";

export const FullScreenButton = () => {
    const [isExpanded, setIsExpanded] = useState(false);
    const toggleFullScreen = () => {
        setIsExpanded(!isExpanded);
        if (!isExpanded) {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    };
    return (
        <button
            className="btn btn-soft-warning mx-1 shadow-sm  "
            onClick={() => toggleFullScreen()}
        >
            <i
                className={
                    isExpanded
                        ? "fa fa-compress"
                        : "fa fa-expand"
                }
            ></i>
        </button>
    )
}
