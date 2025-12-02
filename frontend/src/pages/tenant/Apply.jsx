import React, { useEffect, useMemo, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { FiMapPin, FiHome, FiDollarSign, FiCheck, FiX, FiInfo, FiUser, FiPhone, FiBriefcase, FiTruck, FiCalendar, FiArrowLeft, FiArrowRight, FiSend } from 'react-icons/fi';
import { fetchTenantProperty, submitRentalApplication } from '../../api/client.js';
import MapPreview, { toCoords } from './components/MapPreview.jsx';

const formatPrice = (value) => (typeof value === 'number' ? `Rp${value.toLocaleString('id-ID')}` : 'Rp0');
const formatText = (text) => text || '—';

const TenantApply = () => {
  const { propertyId } = useParams();
  const [searchParams] = useSearchParams();
  const initialRoomId = searchParams.get('room') ? Number(searchParams.get('room')) : null;
  const [property, setProperty] = useState(null);
  const [selectedRoomId, setSelectedRoomId] = useState(initialRoomId);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [applyOpen, setApplyOpen] = useState(false);
  const [unavailableOpen, setUnavailableOpen] = useState(false);

  useEffect(() => {
    setLoading(true);
    setError('');
    fetchTenantProperty(propertyId)
      .then((data) => {
        setProperty(data);
        if (initialRoomId) {
          setSelectedRoomId(initialRoomId);
        } else {
          const rooms = flattenRooms(data);
          if (rooms[0]) {
            setSelectedRoomId(rooms[0].id);
          }
        }
      })
      .catch(() => setError('Gagal memuat properti.'))
      .finally(() => setLoading(false));
  }, [propertyId, initialRoomId]);

  const rooms = useMemo(() => flattenRooms(property), [property]);
  const selectedRoom = rooms.find((room) => room.id === selectedRoomId) ?? rooms[0];

  return (
    <div className="page min-h-screen bg-bg relative overflow-x-hidden pt-32 pb-20">
      {/* Immersive Background */}
      <div className="fixed inset-0 pointer-events-none z-0">
        <div className="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px] opacity-50" />
        <div className="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] bg-secondary/5 rounded-full blur-[120px] opacity-50" />
        <div className="absolute inset-0 bg-[url('/noise.svg')] opacity-[0.02] mix-blend-overlay" />
      </div>

      <div className="container relative z-10 max-w-7xl mx-auto px-6">
        {/* Header */}
        <div className="flex flex-col md:flex-row justify-between items-end gap-6 mb-12">
          <div>
            <motion.div
              initial={{ opacity: 0, y: -10 }}
              animate={{ opacity: 1, y: 0 }}
              className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface border border-white/10 text-text-secondary text-xs font-medium mb-4 backdrop-blur-sm"
            >
              <span className="w-1.5 h-1.5 rounded-full bg-primary animate-pulse" />
              Pengajuan Sewa
            </motion.div>
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="text-4xl md:text-5xl font-display font-bold mb-4"
            >
              Ajukan <span className="text-primary">Sewa Kost</span>
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="text-text-secondary text-lg max-w-2xl"
            >
              Lengkapi data diri dan pilih kamar impianmu untuk memulai pengalaman ngekos yang lebih baik.
            </motion.p>
          </div>
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.2 }}
          >
            <a href="/tenant/search" className="btn ghost">
              <FiArrowLeft className="mr-2" /> Cari Kost Lain
            </a>
          </motion.div>
        </div>

        {error && (
          <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 mb-8 text-center">
            {error}
          </div>
        )}

        {loading ? (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 h-96 rounded-3xl bg-surface/50 animate-pulse" />
            <div className="h-96 rounded-3xl bg-surface/50 animate-pulse" />
          </div>
        ) : property && selectedRoom ? (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Left Column: Property Info */}
            <div className="lg:col-span-2 space-y-8">
              <PropertySummary property={property} />

              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.3 }}
                className="space-y-4"
              >
                <h3 className="text-xl font-bold font-display px-2">Pilih Kamar</h3>
                <RoomCards rooms={rooms} selectedRoomId={selectedRoomId} onSelectRoom={setSelectedRoomId} />
              </motion.div>
            </div>

            {/* Right Column: Action Panel */}
            <div className="lg:col-span-1">
              <div className="sticky top-32 space-y-6">
                <motion.div
                  initial={{ opacity: 0, x: 20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: 0.4 }}
                  className="p-6 rounded-3xl bg-surface/80 backdrop-blur-xl border border-white/10 shadow-2xl"
                >
                  <div className="mb-6">
                    <div className="text-sm text-text-secondary mb-1">Kamar Dipilih</div>
                    <div className="font-bold text-xl text-white mb-1">
                      {selectedRoom.roomType?.name ?? 'Kamar'} · {selectedRoom.room_code ?? '—'}
                    </div>
                    <div className="text-sm text-primary font-medium">
                      {formatPrice(selectedRoom.custom_price ?? selectedRoom.roomType?.base_price ?? property.base_price)} / bulan
                    </div>
                  </div>

                  <div className="space-y-4 mb-8">
                    <div className="flex justify-between text-sm">
                      <span className="text-text-secondary">Deposit</span>
                      <span className="font-medium text-white">{formatPrice(selectedRoom.roomType?.deposit ?? 0)}</span>
                    </div>
                    <div className="flex justify-between text-sm">
                      <span className="text-text-secondary">Luas Kamar</span>
                      <span className="font-medium text-white">{selectedRoom.roomType?.area_m2 ? `${selectedRoom.roomType.area_m2} m²` : '—'}</span>
                    </div>
                    <div className="pt-4 border-t border-white/10 flex justify-between items-center">
                      <span className="font-bold text-white">Total Awal</span>
                      <span className="font-bold text-xl text-primary">
                        {formatPrice((selectedRoom.custom_price ?? selectedRoom.roomType?.base_price ?? property.base_price ?? 0) + (selectedRoom.roomType?.deposit ?? 0))}
                      </span>
                    </div>
                  </div>

                  <button
                    onClick={() => {
                      if (!selectedRoom) return;
                      if (selectedRoom.status && selectedRoom.status !== 'available') {
                        setUnavailableOpen(true);
                        return;
                      }
                      setApplyOpen(true);
                    }}
                    disabled={!selectedRoom}
                    className="btn primary w-full justify-center py-4 text-lg font-bold shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all"
                  >
                    Ajukan Sewa Sekarang
                  </button>
                  <p className="text-xs text-center text-text-tertiary mt-4">
                    Dengan mengajukan, Anda menyetujui syarat & ketentuan yang berlaku.
                  </p>
                </motion.div>
              </div>
            </div>
          </div>
        ) : null}
      </div>

      {/* Modals */}
      <AnimatePresence>
        {applyOpen && selectedRoom && (
          <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/80 backdrop-blur-sm"
              onClick={() => setApplyOpen(false)}
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="relative bg-[#121214] border border-white/10 rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col"
            >
              <div className="sticky top-0 bg-[#121214]/90 backdrop-blur-md border-b border-white/10 p-6 flex justify-between items-center z-10">
                <div>
                  <div className="text-xs text-text-secondary uppercase tracking-wider mb-1">Formulir Pengajuan</div>
                  <h2 className="text-xl font-bold font-display text-white">{property.name}</h2>
                </div>
                <button onClick={() => setApplyOpen(false)} className="p-2 hover:bg-white/5 rounded-full transition-colors">
                  <FiX className="text-xl text-text-secondary hover:text-white" />
                </button>
              </div>

              <div className="p-6 md:p-8">
                <RoomApplyPanel property={property} rooms={rooms} selectedRoomId={selectedRoomId} onSelectRoom={setSelectedRoomId} onClose={() => setApplyOpen(false)} />
              </div>
            </motion.div>
          </div>
        )}

        {unavailableOpen && (
          <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/80 backdrop-blur-sm"
              onClick={() => setUnavailableOpen(false)}
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-md p-8 text-center shadow-2xl"
            >
              <div className="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-6 text-red-500 text-3xl">
                <FiX />
              </div>
              <h3 className="text-2xl font-bold font-display mb-2">Kamar Tidak Tersedia</h3>
              <p className="text-text-secondary mb-8">
                Maaf, kamar yang Anda pilih sedang tidak tersedia atau sudah dipesan orang lain. Silakan pilih kamar lain.
              </p>
              <button onClick={() => setUnavailableOpen(false)} className="btn ghost w-full justify-center">
                Tutup
              </button>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};

const PropertySummary = ({ property }) => {
  const price = property.room_types?.[0]?.base_price ?? property.base_price ?? property.price;
  const coords = toCoords(property.lat, property.lng);

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 0.2 }}
      className="p-6 md:p-8 rounded-3xl bg-surface/50 backdrop-blur-xl border border-white/5"
    >
      <div className="flex flex-col md:flex-row gap-8">
        {/* Photos */}
        <div className="w-full md:w-1/3">
          <div className="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-highlight relative group">
            {property.photos?.length ? (
              <img src={property.photos[0]} alt={property.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-text-tertiary">
                <FiHome className="text-4xl" />
              </div>
            )}
          </div>
          {property.photos?.length > 1 && (
            <div className="flex gap-2 mt-2 overflow-x-auto pb-2">
              {property.photos.slice(1).map((photo, idx) => (
                <img key={idx} src={photo} alt={`Foto ${idx + 2}`} className="w-16 h-16 object-cover rounded-lg border border-white/10 shrink-0" />
              ))}
            </div>
          )}
        </div>

        {/* Details */}
        <div className="flex-1">
          <div className="flex justify-between items-start mb-4">
            <div>
              <h2 className="text-2xl font-bold font-display mb-2">{property.name}</h2>
              <div className="flex items-center gap-2 text-text-secondary text-sm">
                <FiMapPin className="shrink-0" />
                {formatText(property.address)}
              </div>
            </div>
          </div>

          <div className="p-4 rounded-2xl bg-surface border border-white/5 mb-6">
            <h3 className="text-sm font-bold mb-2 text-white">Deskripsi & Aturan</h3>
            <p className="text-sm text-text-secondary leading-relaxed mb-2">
              {property.description || 'Tidak ada deskripsi.'}
            </p>
            {property.rules_text && (
              <div className="flex items-start gap-2 text-xs text-text-tertiary mt-2 pt-2 border-t border-white/5">
                <FiInfo className="shrink-0 mt-0.5" />
                Aturan: {property.rules_text}
              </div>
            )}
          </div>

          {coords && (
            <div className="h-40 rounded-2xl overflow-hidden border border-white/5">
              <MapPreview lat={coords.lat} lng={coords.lng} name={property.name} height="100%" />
            </div>
          )}
        </div>
      </div>
    </motion.div>
  );
};

const RoomCards = ({ rooms, selectedRoomId, onSelectRoom }) => {
  if (!rooms.length) {
    return (
      <div className="p-8 rounded-3xl bg-surface/50 border border-dashed border-white/10 text-center text-text-secondary">
        Tidak ada kamar tersedia.
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
      {rooms.map((room) => {
        const roomPrice = room.custom_price ?? room.roomType?.base_price;
        const photo = getRoomPhoto(room);
        const area = room.roomType?.area_m2 ? `${room.roomType.area_m2} m²` : '—';
        const isSelected = room.id === selectedRoomId;
        const isAvailable = room.status === 'available';

        return (
          <button
            type="button"
            key={room.id}
            onClick={() => onSelectRoom(room.id)}
            className={`relative p-4 rounded-2xl border text-left transition-all duration-300 group overflow-hidden ${isSelected
                ? 'bg-primary/5 border-primary shadow-[0_0_20px_rgba(204,255,0,0.1)]'
                : 'bg-surface border-white/5 hover:border-white/20 hover:bg-surface-highlight'
              }`}
          >
            <div className="flex gap-4">
              <div className="w-24 h-24 rounded-xl overflow-hidden bg-surface-highlight shrink-0">
                <img src={photo} alt={room.room_code} className="w-full h-full object-cover" />
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex justify-between items-start mb-1">
                  <h4 className={`font-bold truncate ${isSelected ? 'text-primary' : 'text-white'}`}>
                    {room.roomType?.name ?? 'Kamar'} · {room.room_code}
                  </h4>
                  {isSelected && <div className="w-5 h-5 rounded-full bg-primary text-black flex items-center justify-center text-xs"><FiCheck /></div>}
                </div>
                <div className="text-xs text-text-secondary mb-2">
                  Luas {area} · {room.roomType?.bathroom_type ?? 'Kamar Mandi Dalam'}
                </div>
                <div className="flex items-center gap-2 mb-3">
                  <span className={`text-[10px] px-2 py-0.5 rounded-full border uppercase font-bold ${isAvailable ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'
                    }`}>
                    {room.status}
                  </span>
                </div>
                <div className="font-bold text-white">
                  {formatPrice(roomPrice)} <span className="text-xs font-normal text-text-tertiary">/ bln</span>
                </div>
              </div>
            </div>
          </button>
        );
      })}
    </div>
  );
};

const RoomApplyPanel = ({ property, rooms, selectedRoomId, onSelectRoom, onClose }) => {
  const selectedRoom = rooms.find((room) => room.id === selectedRoomId) ?? rooms[0];
  const price = selectedRoom?.custom_price ?? selectedRoom?.roomType?.base_price ?? property.room_types?.[0]?.base_price ?? property.base_price ?? property.price;
  const deposit = selectedRoom?.roomType?.deposit ?? 0;
  const totalFirstMonth = (price ?? 0) + (deposit ?? 0);

  const [form, setForm] = useState({
    preferred_start_date: '',
    duration_months: 12,
    occupants_count: 1,
    budget_per_month: price ?? 0,
    full_name: '',
    national_id: '',
    employment_status: '',
    company_name: '',
    job_title: '',
    monthly_income: price ? price * 3 : 0,
    contact_phone: '',
    contact_email: '',
    has_vehicle: false,
    vehicle_notes: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    tenant_notes: '',
    terms_agreed: true,
  });

  const [submitting, setSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState('');
  const [submitSuccess, setSubmitSuccess] = useState('');
  const [step, setStep] = useState(1);
  const [stepError, setStepError] = useState('');
  const maxStep = 5;

  const validateStep = () => {
    if (step === 1) {
      if (!form.preferred_start_date) return 'Tanggal mulai sewa wajib diisi.';
      if (!form.duration_months || form.duration_months < 1) return 'Durasi sewa minimal 1 bulan.';
      if (!form.occupants_count || form.occupants_count < 1) return 'Jumlah penghuni minimal 1 orang.';
    }
    if (step === 2) {
      if (!form.full_name?.trim()) return 'Nama lengkap wajib diisi.';
      if (!form.national_id?.trim()) return 'NIK wajib diisi.';
    }
    if (step === 3) {
      if (!form.contact_phone?.trim()) return 'Nomor telepon wajib diisi.';
      if (!form.emergency_contact_name?.trim()) return 'Nama kontak darurat wajib diisi.';
      if (!form.emergency_contact_phone?.trim()) return 'Nomor kontak darurat wajib diisi.';
    }
    if (step === 4) {
      if (!form.employment_status?.trim()) return 'Status pekerjaan wajib diisi.';
      if (!form.monthly_income || form.monthly_income <= 0) return 'Pendapatan bulanan wajib diisi.';
    }
    return '';
  };

  const goNext = () => {
    const msg = validateStep();
    if (msg) {
      setStepError(msg);
      return;
    }
    setStepError('');
    setStep((prev) => Math.min(maxStep, prev + 1));
  };

  const goPrev = () => setStep((prev) => Math.max(1, prev - 1));

  const handleSubmit = async () => {
    setSubmitting(true);
    setSubmitError('');
    setSubmitSuccess('');
    try {
      await submitRentalApplication({
        property_id: property.id,
        room_type_id: selectedRoom.roomType?.id,
        room_id: selectedRoom.id,
        ...form,
      });
      setSubmitSuccess('Pengajuan berhasil dikirim! Pemilik akan segera meninjau.');
    } catch (err) {
      const msg = err?.response?.data?.message ?? 'Gagal mengirim pengajuan. Silakan coba lagi.';
      setSubmitError(msg);
    } finally {
      setSubmitting(false);
    }
  };

  if (submitSuccess) {
    return (
      <div className="flex flex-col items-center justify-center py-12 text-center">
        <div className="w-20 h-20 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center mb-6 text-4xl">
          <FiCheck />
        </div>
        <h3 className="text-2xl font-bold font-display mb-2">Pengajuan Terkirim!</h3>
        <p className="text-text-secondary max-w-md mb-8">
          Terima kasih telah mengajukan sewa. Pemilik properti akan meninjau data Anda dan kami akan memberitahu Anda melalui email atau WhatsApp.
        </p>
        <button onClick={onClose} className="btn primary px-8">
          Kembali ke Dashboard
        </button>
      </div>
    );
  }

  return (
    <div className="flex flex-col lg:flex-row gap-8">
      {/* Sidebar Stepper */}
      <div className="w-full lg:w-64 shrink-0">
        <div className="space-y-1 relative">
          <div className="absolute left-3 top-2 bottom-2 w-0.5 bg-white/5" />
          {[
            { id: 1, label: 'Rencana Sewa', icon: <FiCalendar /> },
            { id: 2, label: 'Identitas Diri', icon: <FiUser /> },
            { id: 3, label: 'Kontak', icon: <FiPhone /> },
            { id: 4, label: 'Pekerjaan', icon: <FiBriefcase /> },
            { id: 5, label: 'Info Tambahan', icon: <FiTruck /> },
          ].map((s) => (
            <button
              key={s.id}
              onClick={() => {
                if (s.id < step) setStep(s.id);
              }}
              className={`relative flex items-center gap-3 w-full p-3 rounded-xl transition-all text-left ${step === s.id
                  ? 'bg-primary/10 text-primary font-bold'
                  : step > s.id
                    ? 'text-white hover:bg-white/5'
                    : 'text-text-tertiary'
                }`}
            >
              <div className={`w-6 h-6 rounded-full flex items-center justify-center text-xs border z-10 ${step === s.id ? 'bg-primary text-black border-primary' :
                  step > s.id ? 'bg-green-500 text-black border-green-500' : 'bg-surface border-white/10'
                }`}>
                {step > s.id ? <FiCheck /> : s.id}
              </div>
              <span className="text-sm">{s.label}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Form Content */}
      <div className="flex-1">
        <div className="mb-6">
          <h3 className="text-2xl font-bold font-display mb-1">
            {step === 1 && 'Rencana Sewa'}
            {step === 2 && 'Identitas Diri'}
            {step === 3 && 'Informasi Kontak'}
            {step === 4 && 'Pekerjaan & Keuangan'}
            {step === 5 && 'Informasi Tambahan'}
          </h3>
          <p className="text-text-secondary text-sm">
            Lengkapi data berikut untuk melanjutkan proses pengajuan.
          </p>
        </div>

        <form className="space-y-6">
          {step === 1 && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InputGroup label="Tanggal Mulai Sewa" type="date" value={form.preferred_start_date} onChange={(e) => setForm({ ...form, preferred_start_date: e.target.value })} required />
              <InputGroup label="Durasi (Bulan)" type="number" min="1" max="36" value={form.duration_months} onChange={(e) => setForm({ ...form, duration_months: Number(e.target.value) })} required />
              <InputGroup label="Jumlah Penghuni" type="number" min="1" max="4" value={form.occupants_count} onChange={(e) => setForm({ ...form, occupants_count: Number(e.target.value) })} required />
              <InputGroup label="Budget per Bulan" type="number" value={form.budget_per_month} onChange={(e) => setForm({ ...form, budget_per_month: Number(e.target.value) })} required prefix="Rp" />
            </div>
          )}

          {step === 2 && (
            <div className="grid grid-cols-1 gap-4">
              <InputGroup label="Nama Lengkap (Sesuai KTP)" value={form.full_name} onChange={(e) => setForm({ ...form, full_name: e.target.value })} required placeholder="Contoh: Budi Santoso" />
              <InputGroup label="Nomor Induk Kependudukan (NIK)" value={form.national_id} onChange={(e) => setForm({ ...form, national_id: e.target.value })} required placeholder="16 digit angka" />
            </div>
          )}

          {step === 3 && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <InputGroup label="Nomor Telepon / WhatsApp" type="tel" value={form.contact_phone} onChange={(e) => setForm({ ...form, contact_phone: e.target.value })} required placeholder="0812..." />
              <InputGroup label="Alamat Email" type="email" value={form.contact_email} onChange={(e) => setForm({ ...form, contact_email: e.target.value })} placeholder="email@example.com" />
              <div className="md:col-span-2 pt-4 border-t border-white/5">
                <h4 className="font-bold text-sm mb-4 text-white">Kontak Darurat</h4>
              </div>
              <InputGroup label="Nama Kontak Darurat" value={form.emergency_contact_name} onChange={(e) => setForm({ ...form, emergency_contact_name: e.target.value })} required />
              <InputGroup label="Nomor Kontak Darurat" type="tel" value={form.emergency_contact_phone} onChange={(e) => setForm({ ...form, emergency_contact_phone: e.target.value })} required />
            </div>
          )}

          {step === 4 && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-text-secondary mb-2">Status Pekerjaan</label>
                <div className="grid grid-cols-3 gap-3">
                  {['Karyawan', 'Mahasiswa', 'Wirausaha'].map((opt) => (
                    <button
                      key={opt}
                      type="button"
                      onClick={() => setForm({ ...form, employment_status: opt })}
                      className={`py-3 px-4 rounded-xl border text-sm font-medium transition-all ${form.employment_status === opt
                          ? 'bg-primary text-black border-primary'
                          : 'bg-surface border-white/10 text-text-secondary hover:bg-white/5'
                        }`}
                    >
                      {opt}
                    </button>
                  ))}
                </div>
              </div>
              <InputGroup label="Nama Perusahaan / Kampus" value={form.company_name} onChange={(e) => setForm({ ...form, company_name: e.target.value })} />
              <InputGroup label="Jabatan / Jurusan" value={form.job_title} onChange={(e) => setForm({ ...form, job_title: e.target.value })} />
              <InputGroup label="Pendapatan Bulanan (Perkiraan)" type="number" value={form.monthly_income} onChange={(e) => setForm({ ...form, monthly_income: Number(e.target.value) })} required prefix="Rp" />
            </div>
          )}

          {step === 5 && (
            <div className="space-y-4">
              <div className="p-4 rounded-xl bg-surface border border-white/10">
                <label className="flex items-center gap-3 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={form.has_vehicle}
                    onChange={(e) => setForm({ ...form, has_vehicle: e.target.checked })}
                    className="w-5 h-5 rounded border-white/20 bg-surface-highlight text-primary focus:ring-primary"
                  />
                  <span className="text-white font-medium">Saya membawa kendaraan</span>
                </label>
                {form.has_vehicle && (
                  <input
                    type="text"
                    value={form.vehicle_notes}
                    onChange={(e) => setForm({ ...form, vehicle_notes: e.target.value })}
                    placeholder="Jenis kendaraan (Motor/Mobil) & Plat Nomor"
                    className="mt-3 w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-2 text-white focus:border-primary outline-none transition-colors"
                  />
                )}
              </div>

              <div>
                <label className="block text-sm font-medium text-text-secondary mb-2">Catatan Tambahan untuk Pemilik</label>
                <textarea
                  rows={4}
                  value={form.tenant_notes}
                  onChange={(e) => setForm({ ...form, tenant_notes: e.target.value })}
                  placeholder="Contoh: Saya berencana pindah tanggal 15, apakah bisa masuk barang dulu?"
                  className="w-full bg-surface border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary outline-none transition-colors resize-none"
                />
              </div>
            </div>
          )}

          {stepError && (
            <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 text-sm flex items-center gap-2">
              <FiInfo /> {stepError}
            </div>
          )}

          {submitError && (
            <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 text-sm flex items-center gap-2">
              <FiInfo /> {submitError}
            </div>
          )}

          <div className="flex gap-4 pt-6 mt-6 border-t border-white/5">
            {step > 1 ? (
              <button
                type="button"
                onClick={goPrev}
                className="btn ghost px-6"
              >
                Kembali
              </button>
            ) : (
              <button type="button" onClick={onClose} className="btn ghost px-6">Batal</button>
            )}

            {step < maxStep ? (
              <button
                type="button"
                onClick={goNext}
                className="btn primary flex-1 justify-center"
              >
                Lanjut <FiArrowRight className="ml-2" />
              </button>
            ) : (
              <button
                type="button"
                onClick={handleSubmit}
                disabled={submitting}
                className="btn primary flex-1 justify-center shadow-lg shadow-primary/20"
              >
                {submitting ? 'Mengirim...' : 'Kirim Pengajuan'} <FiSend className="ml-2" />
              </button>
            )}
          </div>
        </form>
      </div>
    </div>
  );
};

const InputGroup = ({ label, prefix, ...props }) => (
  <div>
    <label className="block text-sm font-medium text-text-secondary mb-2">{label}</label>
    <div className="relative">
      {prefix && (
        <div className="absolute left-4 top-1/2 -translate-y-1/2 text-text-tertiary text-sm font-medium">
          {prefix}
        </div>
      )}
      <input
        {...props}
        className={`w-full bg-surface border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary outline-none transition-colors ${prefix ? 'pl-10' : ''}`}
      />
    </div>
  </div>
);

const flattenRooms = (property) => {
  if (!property?.room_types) return [];
  return property.room_types.flatMap((rt) =>
    (rt.rooms ?? []).map((room) => ({
      ...room,
      roomType: rt,
    })),
  );
};

const getRoomPhoto = (room) => {
  if (room.photos?.length) {
    return room.photos[0];
  }
  const label = room.room_code ?? room.roomType?.name ?? 'Room';
  return `https://via.placeholder.com/300x200.png?text=${encodeURIComponent(label)}`;
};

export default TenantApply;
