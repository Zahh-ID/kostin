# General System Architecture

## Technology Stack

### Backend (Laravel 11)
- **Language**: PHP 8.2+
- **Framework**: Laravel 11
- **Database**: MySQL / MariaDB
- **Web Server**: Nginx

### Frontend (React 18)
- **Framework**: React (Vite)
- **Language**: JavaScript (ES6+)
- **Styling**: Tailwind CSS
- **State Management**: React Context / Hooks

## Key Libraries & Packages

### Backend
| Package | Purpose |
|---------|---------|
| `laravel/sanctum` | SPA Authentication (Cookie-based) & API Tokens |
| `laravel/socialite` | OAuth Authentication (Google Login) |
| `laravel/reverb` | WebSocket Server for Real-time features |
| `barryvdh/laravel-dompdf` | Generating PDF Contracts |
| `darkaonline/l5-swagger` | API Documentation (Swagger/OpenAPI) |
| `resend/resend-php` | Transactional Emails (Invoice Reminders, Password Reset) |
| `midtrans/midtrans-php` | Payment Gateway Integration (QRIS, Bank Transfer) |

### Frontend
| Package | Purpose |
|---------|---------|
| `axios` | HTTP Client for API Requests |
| `react-router-dom` | Client-side Routing |
| `framer-motion` | UI Animations & Transitions |
| `gsap` | Advanced Animations |
| `react-helmet-async` | Dynamic SEO (Meta Tags, Titles) |
| `react-icons` | Icon Library |

## Architecture Overview

The application follows a **Decoupled Architecture**:

1.  **API Layer (Backend)**:
    -   Serves JSON data via RESTful endpoints.
    -   Handles business logic, database interactions, and authentication.
    -   Stateless (mostly), relying on Sanctum cookies for session management.

2.  **Client Layer (Frontend)**:
    -   Single Page Application (SPA).
    -   Consumes the API.
    -   Handles UI/UX, routing, and state.

3.  **Communication**:
    -   **CORS**: Configured to allow requests from the frontend domain.
    -   **Sanctum**: Uses `laravel_session` and `XSRF-TOKEN` cookies for secure authentication.
