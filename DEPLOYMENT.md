# Deployment Guide (2 Handlers Strategy)

This guide explains how to deploy the Kostin application using two separate Nginx server blocks: one for the Laravel API (Backend) and one for the React Frontend.

## Prerequisites

-   Ubuntu Server (or similar Linux distro)
-   Nginx
-   PHP 8.2 (with FPM, MySQL, XML, MBString, CURL, ZIP extensions)
-   Composer
-   Node.js & NPM
-   MySQL / MariaDB

## 1. Clone the Repository

Clone the repository to your server, typically in `/var/www/kostin`.

```bash
cd /var/www
git clone <your-repo-url> kostin
cd kostin
```

## 2. Backend Setup (Laravel)

1.  **Install Dependencies**:
    ```bash
    composer install --optimize-autoloader --no-dev
    ```

2.  **Environment Configuration**:
    ```bash
    cp .env.example .env
    nano .env
    ```
    -   Set `APP_ENV=production`
    -   Set `APP_DEBUG=false`
    -   Set `APP_URL=https://api.yourdomain.com` (Your API domain)
    -   Set `FRONTEND_URL=https://yourdomain.com` (Your Frontend domain)
    -   Configure Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
    -   Set `SANCTUM_STATEFUL_DOMAINS=yourdomain.com` (Your Frontend domain, without protocol)
    -   Set `SESSION_DOMAIN=.yourdomain.com` (Note the leading dot for subdomain sharing)

3.  **Generate Key & Migrate**:
    ```bash
    php artisan key:generate
    php artisan migrate --force
    php artisan storage:link
    ```

4.  **Permissions**:
    ```bash
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    ```

## 3. Frontend Setup (React)

1.  **Install Dependencies**:
    ```bash
    cd frontend
    npm install
    ```

2.  **Build for Production**:
    You need to specify the API URL during the build process.
    ```bash
    VITE_API_BASE_URL=https://api.yourdomain.com npm run build
    ```
    *Replace `https://api.yourdomain.com` with your actual API domain.*

    This will create a `dist` folder in `frontend/dist`.

## 4. Nginx Configuration

1.  **Symlink Configurations**:
    ```bash
    sudo ln -s /var/www/kostin/deploy/nginx/backend.conf /etc/nginx/sites-enabled/kostin-backend
    sudo ln -s /var/www/kostin/deploy/nginx/frontend.conf /etc/nginx/sites-enabled/kostin-frontend
    ```

2.  **Edit Domains**:
    Edit the config files to use your actual domains.
    ```bash
    sudo nano /etc/nginx/sites-enabled/kostin-backend
    # Change server_name to api.yourdomain.com
    
    sudo nano /etc/nginx/sites-enabled/kostin-frontend
    # Change server_name to yourdomain.com
    ```

3.  **Test & Reload**:
    ```bash
    sudo nginx -t
    sudo systemctl reload nginx
    ```

## 5. SSL (Optional but Recommended)

Use Certbot to get free SSL certificates.

```bash
sudo certbot --nginx -d yourdomain.com -d api.yourdomain.com
```

## Troubleshooting

-   **CORS Issues**: Check `config/cors.php` and ensure `supports_credentials` is true and `allowed_origins` includes your frontend domain.
-   **404 on Refresh**: Ensure the frontend Nginx config has `try_files $uri $uri/ /index.html;`.
-   **500 Errors**: Check `/var/log/nginx/kostin-api.error.log` and `storage/logs/laravel.log`.
