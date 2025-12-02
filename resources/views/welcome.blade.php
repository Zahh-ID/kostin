<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KostIn') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: radial-gradient(90% 80% at 10% 10%, rgba(13, 110, 253, 0.12), transparent 35%),
                    radial-gradient(60% 60% at 90% 0%, rgba(25, 135, 84, 0.12), transparent 40%),
                    linear-gradient(135deg, #eef6ff, #ffffff);
            }

            .hero-card {
                background: #ffffff;
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
                border: 1px solid #e5ecf5;
                border-radius: 1.5rem;
            }

            .badge-soft {
                padding: 0.35rem 0.75rem;
                border-radius: 999px;
                background: rgba(13, 110, 253, 0.12);
                color: #0d6efd;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .stat-tile {
                border: 1px solid #e9eef5;
                border-radius: 1rem;
                padding: 1rem 1.25rem;
                background: #ffffff;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }

            .section-title {
                letter-spacing: 0.04em;
                text-transform: uppercase;
                font-weight: 700;
                color: #0d6efd;
            }

            .preview-card {
                border-radius: 18px;
                padding: 1.2rem;
                border: 1px solid #e8eef5;
                background: linear-gradient(145deg, #ffffff, #f4f8ff);
                box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
                height: 100%;
            }

            .mini-chart {
                height: 6px;
                border-radius: 999px;
                background: linear-gradient(90deg, #0d6efd, #20c997);
            }

            .dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                display: inline-block;
            }

            .glass {
                background: rgba(255, 255, 255, 0.62);
                border: 1px solid rgba(255, 255, 255, 0.35);
                backdrop-filter: blur(10px);
                border-radius: 14px;
                box-shadow: 0 14px 42px rgba(15, 23, 42, 0.14);
            }
        </style>
    </head>
    <body>
        <header class="py-4">
            <div class="container d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2 fw-bold fs-5 text-dark">
                    <i class="bi bi-house-fill text-primary"></i> {{ config('app.name', 'KostIn') }}
                </a>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-link text-decoration-none" href="{{ route('about') }}">Tentang</a>
                    <a class="btn btn-link text-decoration-none" href="{{ route('faq') }}">FAQ</a>
                    <a class="btn btn-outline-primary" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Daftar</a>
                </div>
            </div>
        </header>

        <main class="pb-5">
            <section class="container pb-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="d-inline-flex align-items-center gap-2 badge-soft mb-3">
                            <i class="bi bi-stars"></i> Platform sewa kos terhubung
                        </div>
                        <h1 class="display-5 fw-bold text-dark mb-3">
                            Temukan kos, kelola kontrak, dan pantau pembayaran dalam satu dashboard.
                        </h1>
                        <p class="text-secondary fs-5 mb-4">
                            KostIn menyatukan tenant, owner, dan admin dengan pembayaran Midtrans QRIS, unggah manual, chat real-time, serta moderasi properti yang aman.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <a class="btn btn-primary btn-lg px-4" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-2"></i> Daftar gratis
                            </a>
                            <a class="btn btn-outline-primary btn-lg px-4" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Mulai masuk
                            </a>
                        </div>
                        <div class="glass p-3 mt-2">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="stat-tile flex-fill">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-check-fill"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">Tenant aktif</div>
                                            <small class="text-secondary">Pantau tagihan & kontrak real-time.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-tile flex-fill">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-buildings-fill"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">Properti terverifikasi</div>
                                            <small class="text-secondary">Moderasi admin & audit trail.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-card p-4 p-lg-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="section-title mb-1">Ringkasan dashboard</div>
                                    <h4 class="mb-0">Tenant & Owner</h4>
                                </div>
                                <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                                    <i class="bi bi-shield-lock me-1"></i> Aman
                                </span>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Tagihan bulan ini</span>
                                            <i class="bi bi-qr-code text-primary"></i>
                                        </div>
                                        <div class="h4 fw-bold mb-0">QRIS + Manual</div>
                                        <small class="text-secondary">Status realtime & history unggah.</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Kontrak aktif</span>
                                            <i class="bi bi-card-checklist text-success"></i>
                                        </div>
                                        <div class="h4 fw-bold mb-0">Perpanjang & PDF</div>
                                        <small class="text-secondary">Kelola terminasi & tanda tangan.</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Tiketing & Chat</span>
                                            <i class="bi bi-chat-dots text-info"></i>
                                        </div>
                                        <div class="h4 fw-bold mb-0">Realtime</div>
                                        <small class="text-secondary">Unread detection & timeline pesan.</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-secondary">Owner Tools</span>
                                            <i class="bi bi-gear-fill text-warning"></i>
                                        </div>
                                        <div class="h4 fw-bold mb-0">Moderasi</div>
                                        <small class="text-secondary">Draft, submit, reject note, unpublish.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-3 flex-wrap">
                                <a class="btn btn-primary" href="{{ route('register') }}">
                                    <i class="bi bi-rocket-takeoff me-2"></i> Coba sekarang
                                </a>
                                <a class="btn btn-outline-secondary" href="{{ route('faq') }}">
                                    <i class="bi bi-question-circle me-2"></i> Lihat FAQ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container pb-5">
                <div class="text-center mb-4">
                    <div class="section-title">Dirancang untuk tiap peran</div>
                    <h2 class="fw-bold">Tenant, Owner, dan Admin sinkron dalam satu alur</h2>
                    <p class="text-secondary">Kontrak, pembayaran, wishlist, builder properti, hingga tiket masuk pipeline yang sama.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="stat-tile h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-door-open"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0">Tenant</h5>
                                    <small class="text-secondary">Fokus bayar dan kontrak</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0 text-secondary">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Timeline tagihan + QRIS + unggah manual</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Wishlist Livewire</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Tiket dan chat dengan unread detection</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="stat-tile h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-building-gear"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0">Owner</h5>
                                    <small class="text-secondary">Builder & manual payment</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0 text-secondary">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Draft, submit, unpublish, republish properti</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Manual payment approval & wallet</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Room types, tasks, tiket assigned</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="stat-tile h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="badge bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0">Admin</h5>
                                    <small class="text-secondary">Moderasi & webhook</small>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0 text-secondary">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Queue moderasi, tiket Kanban, user list</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Simulasi webhook Midtrans settlement/cancel</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Audit note & reviewer metadata</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container pb-5">
                <div class="hero-card p-4 p-lg-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-8">
                            <div class="section-title mb-2">Keamanan & Dukungan</div>
                            <h3 class="fw-bold mb-3">Pembayaran aman, chat responsif, uji otomatis.</h3>
                            <p class="text-secondary mb-4">Integrasi Midtrans QRIS, webhook coverage, perlindungan CSRF, dan suite Pest untuk auth, tenant modules, owner moderation, hingga chat unread guard.</p>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="stat-tile">
                                    <div class="fw-semibold">Pembayaran</div>
                                    <small class="text-secondary">QRIS, webhook expiry, invalid signature guard.</small>
                                </div>
                                <div class="stat-tile">
                                    <div class="fw-semibold">Chat & Tiket</div>
                                    <small class="text-secondary">Polling, unread detection, assigned workflow.</small>
                                </div>
                                <div class="stat-tile">
                                    <div class="fw-semibold">Uji Otomatis</div>
                                    <small class="text-secondary">Pest untuk auth, tenant, owner, admin API.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a class="btn btn-primary btn-lg px-4 mb-2" href="{{ route('register') }}">Mulai gratis</a>
                            <div class="text-secondary">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container pb-5">
                <div class="text-center mb-4">
                    <div class="section-title">Preview dashboard</div>
                    <h2 class="fw-bold">Lihat sekilas tenant, owner, admin</h2>
                    <p class="text-secondary">Snapshot kartu, grafik, dan status yang akan kamu jumpai setelah login.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="preview-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Tenant</h5>
                                <span class="badge bg-primary-subtle text-primary-emphasis">Dashboard</span>
                            </div>
                            <p class="text-secondary small mb-3">Tagihan, kontrak, chat, tiket, dan wishlist dalam satu layar.</p>
                            <div class="stat-tile mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary small">Tagihan aktif</span>
                                    <span class="fw-semibold text-primary">4</span>
                                </div>
                                <div class="mini-chart mt-2"></div>
                            </div>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>QRIS, manual upload, status badge</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Timeline chat & unread detection</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Contracts & PDF download</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="preview-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Owner</h5>
                                <span class="badge bg-success-subtle text-success-emphasis">Workspace</span>
                            </div>
                            <p class="text-secondary small mb-3">Moderasi properti, manual payment approval, tasks & tickets.</p>
                            <div class="stat-tile mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary small">Properties review</span>
                                    <span class="fw-semibold text-success">3</span>
                                </div>
                                <div class="mini-chart mt-2" style="background: linear-gradient(90deg, #198754, #20c997);"></div>
                            </div>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Draft → submit → approve/reject</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Manual payments with approval</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Wallet & tasks, ticket workflow</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="preview-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Admin</h5>
                                <span class="badge bg-dark text-white">Control</span>
                            </div>
                            <p class="text-secondary small mb-3">Moderation queue, Kanban tickets, webhook simulator, user list.</p>
                            <div class="stat-tile mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary small">Tickets pending</span>
                                    <span class="fw-semibold text-dark">5</span>
                                </div>
                                <div class="mini-chart mt-2" style="background: linear-gradient(90deg, #343a40, #0d6efd);"></div>
                            </div>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Midtrans webhook simulator built-in</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>User & invoice list plus breakdowns</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Audit notes + reviewer metadata</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container pb-5">
                <div class="text-center mb-4">
                    <div class="section-title">Preview modul utama</div>
                    <h2 class="fw-bold">Pembayaran, chat, auth, dan navigasi siap pakai</h2>
                    <p class="text-secondary">Lihat elemen UI yang akan digunakan setiap hari.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="preview-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Pembayaran</h6>
                                <span class="badge bg-primary-subtle text-primary-emphasis">QRIS</span>
                            </div>
                            <p class="text-secondary small mb-3">QRIS, upload manual, status badge, timeline.</p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="dot bg-success"></span>
                                <small class="text-secondary">settlement</small>
                                <span class="dot bg-warning"></span>
                                <small class="text-secondary">pending</small>
                                <span class="dot bg-secondary"></span>
                                <small class="text-secondary">expire</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="preview-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Chat & Tiket</h6>
                                <span class="badge bg-info-subtle text-info">Realtime</span>
                            </div>
                            <p class="text-secondary small mb-3">Polling, unread badge, timeline pesan, assigned workflow.</p>
                            <div class="stat-tile mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary small">Unread</span>
                                    <span class="fw-semibold text-info">3</span>
                                </div>
                                <div class="mini-chart mt-2" style="background: linear-gradient(90deg, #0dcaf0, #0d6efd);"></div>
                            </div>
                            <small class="text-secondary">Tenant & owner dapat membuat dan menutup tiket.</small>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="preview-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Auth</h6>
                                <span class="badge bg-dark text-white">OAuth + Form</span>
                            </div>
                            <p class="text-secondary small mb-3">UI login/register baru dengan Google, role pick, show password.</p>
                            <div class="d-flex gap-2">
                                <div class="stat-tile flex-fill">
                                    <div class="text-secondary small">Masuk</div>
                                    <div class="fw-semibold">Google / Email</div>
                                </div>
                                <div class="stat-tile flex-fill">
                                    <div class="text-secondary small">Daftar</div>
                                    <div class="fw-semibold">Tenant / Owner</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="preview-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Navigasi</h6>
                                <span class="badge bg-secondary-subtle text-secondary">Sidebar</span>
                            </div>
                            <p class="text-secondary small mb-3">Sidebar role-aware dengan badge pending tasks dan logout.</p>
                            <ul class="list-unstyled text-secondary small mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Volt navigation component</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Badge counts untuk tiket & moderasi</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Profile header cards</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container pb-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-card p-4 p-lg-5">
                            <div class="section-title mb-2">Alur kerja</div>
                            <h3 class="fw-bold mb-3">Mulai dari daftar sampai pembayaran aman.</h3>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-semibold">Buat akun & pilih peran</div>
                                        <small class="text-secondary">Login Google atau email, pilih tenant/owner, verifikasi otomatis.</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><i class="bi bi-person-plus"></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-semibold">Kelola properti & kontrak</div>
                                        <small class="text-secondary">Owner submit draft, admin moderasi, tenant tandatangani & unduh PDF.</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill"><i class="bi bi-building-check"></i></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-semibold">Pembayaran & tiket</div>
                                        <small class="text-secondary">QRIS Midtrans, unggah manual, webhook simulator, chat & tiket assigned.</small>
                                    </div>
                                    <span class="badge bg-info rounded-pill text-dark"><i class="bi bi-qr-code-scan"></i></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="preview-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Angka singkat</h5>
                                <span class="badge bg-secondary-subtle text-secondary">Realtime</span>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="text-secondary small">Pembayaran sukses</div>
                                        <div class="display-6 fw-bold text-success">96%</div>
                                        <small class="text-secondary">Webhooks tervalidasi signature.</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-tile">
                                        <div class="text-secondary small">Chat aktif</div>
                                        <div class="display-6 fw-bold text-info">+32</div>
                                        <small class="text-secondary">Unread guard & polling stabil.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-tile mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary">Moderasi properti</span>
                                    <span class="fw-semibold">Queue</span>
                                </div>
                                <div class="mini-chart mt-2" style="background: linear-gradient(90deg, #6f42c1, #0d6efd); height: 8px;"></div>
                                <small class="text-secondary">Draft, review, reject dengan catatan terjaga.</small>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a class="btn btn-primary" href="{{ route('register') }}"><i class="bi bi-rocket me-2"></i>Mulai sekarang</a>
                                <a class="btn btn-outline-primary" href="{{ route('about') }}"><i class="bi bi-info-circle me-2"></i>Pelajari lebih lanjut</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-4 text-center text-muted small">
            &copy; {{ now()->year }} {{ config('app.name', 'KostIn') }} · Dibangun dengan Laravel & Livewire
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
