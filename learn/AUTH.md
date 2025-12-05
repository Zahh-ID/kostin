# Authentication System

## Overview
Authentication is handled by **Laravel Sanctum** using a stateful configuration for the SPA (Single Page Application). This ensures secure, cookie-based authentication without handling raw JWTs in local storage.

## Authentication Flow

1.  **CSRF Protection**:
    -   Frontend requests `/sanctum/csrf-cookie` to set the `XSRF-TOKEN`.
    -   Axios automatically includes this token in headers for subsequent requests.

2.  **Login**:
    -   Endpoint: `POST /api/v1/auth/login`
    -   Controller: `AuthController@login`
    -   Action: Validates credentials, regenerates session, and returns user data.

3.  **Registration**:
    -   Endpoint: `POST /api/v1/auth/register`
    -   Controller: `AuthController@register`
    -   Action: Creates user, assigns role (Tenant/Owner), and logs them in.

4.  **Password Reset**:
    -   **Forgot Password**: `POST /api/v1/auth/forgot-password` (`ForgotPasswordController`) - Sends email with link.
    -   **Reset Password**: `POST /api/v1/auth/reset-password` (`ResetPasswordController`) - Resets password using token.

## Middleware

### `auth:sanctum`
Ensures the user is logged in. Used for all protected routes.

### `role:{role_name}`
Custom middleware (`App\Http\Middleware\RoleMiddleware`) to restrict access based on user type.

-   **Usage**: `middleware('role:admin')`
-   **Roles**:
    -   `tenant`: Regular users looking for rooms.
    -   `owner`: Property owners managing listings.
    -   `admin`: System administrators.

## Controllers

| Feature | Controller | Path |
|---------|------------|------|
| Login/Register | `AuthController` | `app/Http/Controllers/Api/V1/AuthController.php` |
| Forgot Password | `ForgotPasswordController` | `app/Http/Controllers/Api/V1/Auth/ForgotPasswordController.php` |
| Reset Password | `ResetPasswordController` | `app/Http/Controllers/Api/V1/Auth/ResetPasswordController.php` |
