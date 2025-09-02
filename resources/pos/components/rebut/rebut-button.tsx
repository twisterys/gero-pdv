import React, { useState } from 'react';
import RebutModal from './rebut-modal';
import { toast } from 'react-toastify';
import { useSettingsStore } from '../../stores/settings-store';
const RebutButton: React.FC<{ className?: string }> = ({ className = '' }) => {
    const [open, setOpen] = useState(false);
    const { features } = useSettingsStore();

    if (!features.rebut) return null;

    return (
        <>
            <button
                onClick={() => setOpen(true)}
                className={`px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:red-500 ${className}`}
            >
                <svg className="inline-block mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M7 4h10l1 4H6z"/><path fill="currentColor" d="M6 8h12l-1 11H7z"/></svg>
                <span>Rebut</span>
            </button>

            <RebutModal
                isOpen={open}
                onClose={() => setOpen(false)}
                onSuccess={(text) => toast.success(text)}
            />
        </>
    );
};

export default RebutButton;
