import React, { useState } from 'react';
import { FiPlus, FiTrash2, FiImage } from 'react-icons/fi';
import { uploadOwnerPropertyPhoto } from '../../../api/client';

const PropertyPhotos = ({ property, onUpdate }) => {
    const [uploading, setUploading] = useState(false);

    const handleFileChange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setUploading(true);
        try {
            await uploadOwnerPropertyPhoto(property.id, file);
            onUpdate();
        } catch (err) {
            console.error(err);
            alert('Gagal mengupload foto.');
        } finally {
            setUploading(false);
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h3 className="text-xl font-bold font-display">Galeri Foto</h3>
                <span className="text-sm text-text-secondary">
                    {property.photos?.length || 0} Foto
                </span>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                {property.photos?.map((photo, idx) => (
                    <div key={idx} className="aspect-square rounded-xl overflow-hidden border border-white/10 relative group">
                        <img src={photo} alt={`Property ${idx}`} className="w-full h-full object-cover" />
                        <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button className="p-2 bg-white/10 rounded-full hover:bg-red-500/80 text-white transition-colors">
                                <FiTrash2 />
                            </button>
                        </div>
                        {idx === 0 && (
                            <div className="absolute top-2 left-2 px-2 py-1 bg-primary text-black text-xs font-bold rounded">
                                Utama
                            </div>
                        )}
                    </div>
                ))}

                <label className="aspect-square rounded-xl border-2 border-dashed border-white/20 hover:border-primary hover:bg-primary/5 flex flex-col items-center justify-center cursor-pointer transition-colors group">
                    {uploading ? (
                        <div className="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full" />
                    ) : (
                        <>
                            <div className="p-3 rounded-full bg-surface-highlight group-hover:bg-primary/10 mb-3 transition-colors">
                                <FiPlus className="text-2xl text-text-secondary group-hover:text-primary" />
                            </div>
                            <span className="text-sm text-text-secondary font-medium group-hover:text-primary">Upload Foto</span>
                        </>
                    )}
                    <input type="file" className="hidden" accept="image/*" onChange={handleFileChange} disabled={uploading} />
                </label>
            </div>

            <div className="p-4 rounded-xl bg-blue-500/5 border border-blue-500/10 flex gap-3">
                <FiImage className="text-blue-400 flex-shrink-0 mt-0.5" />
                <div className="text-sm text-text-secondary">
                    <p className="text-text-primary font-bold mb-1">Tips Foto Properti</p>
                    <ul className="list-disc list-inside space-y-1">
                        <li>Gunakan pencahayaan yang terang dan alami.</li>
                        <li>Foto setiap ruangan (Kamar, Kamar Mandi, Dapur, Parkiran).</li>
                        <li>Pastikan foto tidak buram dan memiliki resolusi tinggi.</li>
                        <li>Foto pertama akan menjadi foto utama yang muncul di pencarian.</li>
                    </ul>
                </div>
            </div>
        </div>
    );
};

export default PropertyPhotos;
