import React, { useEffect, useMemo, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import '../css/landing.css';

gsap.registerPlugin(ScrollTrigger);

const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const formatPrice = (property) => {
    const roomTypes = property.room_types ?? [];
    const basePrices = roomTypes
        .map((roomType) => Number(roomType.base_price))
        .filter((price) => Number.isFinite(price));

    if (!basePrices.length) {
        return null;
    }

    return currency.format(Math.min(...basePrices));
};

const initials = (name) =>
    name
        ?.split(' ')
        .slice(0, 2)
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase();

const StatTile = ({ label, value, tone }) => (
    <div className="stat-card reveal-card" data-tone={tone}>
        <div className="stat-label">{label}</div>
        <div className="stat-value">{value}</div>
    </div>
);

const PropertyCard = ({ property }) => {
    const price = formatPrice(property);
    const photo = property.photos?.[0];

    return (
        <article className="property-card reveal-card">
            <div className="property-media">
                <div className="status-chip">{property.status ?? 'tersedia'}</div>
                {photo ? (
                    <img src={photo} alt={property.name} className="property-photo" />
                ) : (
                    <div className="property-photo" style={{ background: 'rgba(255,255,255,0.04)' }} />
                )}
            </div>
            <div className="property-body">
                <div className="property-name">{property.name}</div>
                <div className="property-meta">{property.address}</div>
                <div className="property-meta">Mulai {price ?? 'Hubungi owner'}</div>
                <div className="owner">
                    <div className="avatar">{initials(property.owner?.name ?? 'KI')}</div>
                    <div>
                        <div>{property.owner?.name ?? 'Owner KostIn'}</div>
                        <div className="property-meta">{property.owner?.email ?? 'Pemilik tervalidasi'}</div>
                    </div>
                </div>
            </div>
        </article>
    );
};

const StepCard = ({ step, title, body }) => (
    <div className="step-card reveal-card">
        <div className="step-number">{step}</div>
        <div className="property-name">{title}</div>
        <div className="property-meta">{body}</div>
    </div>
);

const LandingPage = ({ properties = [], routes = {} }) => {
    const rootRef = useRef(null);

    const curatedProperties = useMemo(
        () =>
            properties.map((property) => ({
                ...property,
                status: property.status ? property.status.toUpperCase() : 'AKTIF',
            })),
        [properties],
    );

    useEffect(() => {
        if (!rootRef.current) {
            return;
        }

        const ctx = gsap.context(() => {
            gsap.from('.hero-title span', {
                y: 50,
                opacity: 0,
                duration: 1.1,
                ease: 'power3.out',
                stagger: 0.08,
            });

            gsap.from('.hero-body', {
                opacity: 0,
                y: 24,
                delay: 0.1,
                duration: 1,
                ease: 'power3.out',
            });

            gsap.from('.hero-actions > *', {
                opacity: 0,
                y: 16,
                duration: 0.8,
                ease: 'power3.out',
                stagger: 0.08,
                delay: 0.25,
            });

            gsap.to('.orb-1', {
                y: 26,
                duration: 16,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
            });

            gsap.to('.orb-2', {
                y: -18,
                duration: 14,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
            });

            gsap.to('.orb-3', {
                y: 22,
                duration: 18,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
            });

            gsap.utils.toArray('.reveal-card').forEach((card, index) => {
                gsap.from(card, {
                    scrollTrigger: {
                        trigger: card,
                        start: 'top 88%',
                    },
                    opacity: 0,
                    y: 32,
                    duration: 0.9,
                    ease: 'power3.out',
                    delay: index * 0.02,
                });
            });
        }, rootRef);

        return () => ctx.revert();
    }, []);

    const roles = [
        {
            title: 'Tenant',
            tag: 'QRIS + Timeline',
            points: ['QRIS Midtrans & unggah manual', 'Status badge + riwayat pembayaran', 'Chat + tiket dengan unread guard'],
        },
        {
            title: 'Owner',
            tag: 'Moderasi & Wallet',
            points: ['Draft → submit → approve/reject', 'Manual payment approval & saldo', 'Tasks, room types, ticket assigned'],
        },
        {
            title: 'Admin',
            tag: 'Control Center',
            points: ['Moderation queue dengan audit note', 'Kanban tiket + webhook simulator', 'User & invoice breakdown realtime'],
        },
    ];

    const steps = [
        {
            title: 'Buat akun & pilih peran',
            body: 'Mulai dengan login Google atau email, pilih tenant/owner, dan lanjutkan ke dashboard.',
        },
        {
            title: 'Kelola properti & kontrak',
            body: 'Owner membangun properti, admin moderasi, tenant menandatangani dan unduh PDF kontrak.',
        },
        {
            title: 'Pembayaran & support',
            body: 'QRIS settlement, unggah manual diverifikasi, plus tiket dan chat dengan badge unread.',
        },
    ];

    return (
        <div className="landing-shell" ref={rootRef}>
            <div className="bg-grid" />
            <div className="orb orb-1" />
            <div className="orb orb-2" />
            <div className="orb orb-3" />

            <header className="nav-bar">
                <a className="brand" href={routes.home ?? '#'}>
                    <span className="brand-mark">KI</span>
                    <span>KostIn</span>
                </a>
                <div className="nav-links">
                    <a className="nav-link" href={routes.about ?? '#'}>
                        Tentang
                    </a>
                    <a className="nav-link" href={routes.faq ?? '#'}>
                        FAQ
                    </a>
                    <a className="nav-link" href="#properties">
                        Koleksi Kost
                    </a>
                </div>
                <div className="cta-group">
                    <a className="btn-ghost" href={routes.login ?? '#'}>
                        Masuk
                    </a>
                    <a className="btn-primary" href={routes.register ?? '#'}>
                        Daftar Gratis
                    </a>
                </div>
            </header>

            <section className="hero">
                <div>
                    <span className="pill">React + GSAP landing · Full motion</span>
                    <h1 className="hero-title">
                        <span>Dashboard kost futuristik</span> <span>untuk tenant, owner, admin.</span>
                    </h1>
                    <p className="hero-body">
                        KostIn hadir sebagai front-end React yang kaya animasi dengan Midtrans QRIS, unggah manual, tiket,
                        dan moderasi properti yang terhubung langsung ke backend Laravel 11 + Livewire.
                    </p>
                    <div className="hero-actions">
                        <a className="btn-primary" href={routes.register ?? '#'}>
                            Mulai gratis
                        </a>
                        <a className="btn-ghost" href={routes.faq ?? '#'}>
                            Lihat alur lengkap
                        </a>
                    </div>
                    <div className="stat-row">
                        <StatTile label="Pembayaran sukses" value="96% settlement QRIS" tone="primary" />
                        <StatTile label="Kontrak aktif" value="> 320 kontrak" tone="accent" />
                        <StatTile label="Chat & tiket" value="+32 kanal live" tone="muted" />
                    </div>
                </div>

                <div className="hero-visual">
                    <div className="visual-grid">
                        {[...Array(24)].map((_, index) => (
                            <div key={index} className={`visual-cell ${index % 5 === 0 ? 'accent' : ''}`} />
                        ))}
                    </div>
                    <div className="mini-bar" />
                    <div className="stat-row">
                        <StatTile label="Tenant dashboard" value="Tagihan + QRIS" />
                        <StatTile label="Owner space" value="Moderasi & wallet" />
                    </div>
                </div>
            </section>

            <div className="divider">
                <div className="section-header">
                    <div>
                        <div className="pill">Peran lengkap</div>
                        <div className="section-title">Tenant, Owner, Admin berkolaborasi</div>
                        <div className="section-note">
                            Wishlist, kontrak, ticketing, dan builder properti bergerak sinkron di UI React ini.
                        </div>
                    </div>
                    <a className="btn-ghost" href={routes.login ?? '#'}>
                        Masuk ke dashboard
                    </a>
                </div>
            </div>

            <section className="feature-grid">
                {roles.map((role) => (
                    <div key={role.title} className="feature-card reveal-card">
                        <div className="tag">{role.tag}</div>
                        <div className="property-name">{role.title}</div>
                        <ul className="property-meta" style={{ listStyle: 'none', padding: 0, margin: 0, display: 'grid', gap: '8px' }}>
                            {role.points.map((point) => (
                                <li key={point}>{point}</li>
                            ))}
                        </ul>
                    </div>
                ))}
            </section>

            <div id="properties" className="divider">
                <div className="section-header">
                    <div>
                        <div className="pill">Pilih kost</div>
                        <div className="section-title">Properti terverifikasi yang siap dihuni</div>
                        <div className="section-note">Data real dari backend KostIn: status, owner, harga awal, dan foto.</div>
                    </div>
                </div>
            </div>

            <section className="property-grid">
                {curatedProperties.length ? (
                    curatedProperties.map((property) => <PropertyCard key={property.id} property={property} />)
                ) : (
                    <>
                        <div className="feature-card reveal-card">
                            <div className="tag">Belum ada properti</div>
                            <div className="property-name">Owner dapat mendaftarkan kost melalui portal</div>
                            <div className="property-meta">Moderasi admin memastikan listing aman dan berkualitas.</div>
                        </div>
                        <div className="feature-card reveal-card">
                            <div className="tag">Realtime</div>
                            <div className="property-name">Pembayaran QRIS + unggah manual</div>
                            <div className="property-meta">Status badge, riwayat, dan webhook Midtrans terintegrasi.</div>
                        </div>
                    </>
                )}
            </section>

            <div className="divider">
                <div className="section-header">
                    <div>
                        <div className="pill">Alur onboarding</div>
                        <div className="section-title">Mulai hingga pembayaran aman</div>
                    </div>
                </div>
            </div>

            <section className="timeline">
                {steps.map((item, index) => (
                    <StepCard key={item.title} step={index + 1} title={item.title} body={item.body} />
                ))}
            </section>

            <section className="cta-panel">
                <div>
                    <div className="section-title">Siap bergerak cepat?</div>
                    <div className="section-note">React + GSAP landing ini terhubung langsung dengan fitur KostIn.</div>
                </div>
                <div className="cta-group">
                    <a className="btn-primary" href={routes.register ?? '#'}>
                        Coba sekarang
                    </a>
                    <a className="btn-ghost" href={routes.about ?? '#'}>
                        Lihat dokumentasi
                    </a>
                </div>
            </section>

            <div className="footer">KostIn · React + GSAP · {new Date().getFullYear()}</div>
        </div>
    );
};

const boot = () => {
    const rootElement = document.getElementById('landing-root');

    if (!rootElement) {
        return;
    }

    const payload = JSON.parse(rootElement.dataset.props || '{}');

    createRoot(rootElement).render(
        <React.StrictMode>
            <LandingPage properties={payload.properties ?? []} routes={payload.routes ?? {}} />
        </React.StrictMode>,
    );
};

boot();
