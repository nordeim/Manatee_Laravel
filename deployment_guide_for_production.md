# Deployment Guide for Production: The Scent E-commerce Platform

**Document Version:** 1.0
**Date:** May 13, 2024

## 1. Introduction

This document provides a comprehensive guide for deploying "The Scent" Laravel e-commerce application to a production environment. It covers general deployment principles and provides specific steps for two recommended platforms: **Fly.io** (container-based, generally simpler for Laravel applications designed with Docker) and **AWS Lightsail** (VPS-based, more manual server setup).

**Prerequisites for any deployment:**
*   A fully tested and stable version of the application code in a Git repository.
*   Access to a production-ready database (e.g., managed MariaDB/MySQL service).
*   API keys and credentials for all third-party services used in production (Stripe, mail provider, etc.).
*   A custom domain name configured for your application.

## 2. General Deployment Principles

These principles apply regardless of the chosen hosting platform:

*   **Environment Parity:** Strive to keep your production environment as close as possible to your development (Laravel Sail) and staging environments. This includes PHP version, MariaDB version, Redis version, and key system libraries. Docker helps significantly in achieving this.
*   **Source Control:** All code should be managed via Git. Deploy specific tags or branches.
*   **Configuration Management:**
    *   **NEVER commit your production `.env` file or any sensitive credentials to your Git repository.**
    *   Use environment variables for all configurations that differ between environments (local, staging, production).
    *   Utilize the platform's secret management tools (e.g., Fly.io Secrets, AWS Systems Manager Parameter Store, or a secure method for placing the `.env` file on a VPS).
*   **Build Artifacts:**
    *   **Composer Dependencies:** Run `composer install --optimize-autoloader --no-dev` as part of your build/deployment process. `--no-dev` excludes development dependencies. `--optimize-autoloader` creates a more performant class map.
    *   **Frontend Assets:** Compile frontend assets using `npm run build` (or `yarn build`). This will generate optimized, versioned CSS and JS files in the `public/build` directory. These built assets should be deployed with your application.
*   **Laravel Production Optimizations:** During deployment, after code is updated, run these Artisan commands:
    *   `php artisan config:cache`: Caches all configuration files into one.
    *   `php artisan route:cache`: Caches route definitions.
    *   `php artisan view:cache`: Caches compiled Blade views.
    *   `php artisan event:cache` (Laravel 11+): Caches event listeners if not using auto-discovery or if desired for performance.
    *   `php artisan storage:link`: Creates the symbolic link from `public/storage` to `storage/app/public`.
    *   **To clear all caches (if needed before re-caching):** `php artisan optimize:clear`
*   **Database Migrations:** Run `php artisan migrate --force` as part of your deployment process to apply database schema changes. The `--force` flag is required for running migrations in a non-interactive (production) environment.
*   **Permissions:** Ensure the web server has write access to the `storage/` and `bootstrap/cache/` directories.
*   **Queue Workers:** Set up a robust queue worker process using Supervisor (on a VPS) or as a separate process type (on platforms like Fly.io). Use a persistent queue driver like Redis or a database in production (`QUEUE_CONNECTION=redis` or `database`).
*   **Task Scheduling:** Configure a cron job (on a VPS) or a scheduled task runner (on PaaS) to execute `php artisan schedule:run` every minute.
*   **HTTPS:** Enforce HTTPS for all traffic. Obtain and configure SSL/TLS certificates (Let's Encrypt is a common free option). Platforms like Fly.io often handle this automatically.
*   **Logging:** Configure Laravel to write logs to `stderr` (standard error output) when running in containers so the container platform can collect them, or use a dedicated logging service. Set `LOG_LEVEL` appropriately for production (e.g., `error` or `warning`).
*   **Monitoring & Error Tracking:** Implement application performance monitoring (APM) and error tracking services (e.g., Sentry, Bugsnag, Flare, Datadog).
*   **Backups:** Regularly back up your database and any persistent file storage. Managed database services often provide automated backups.
*   **Zero-Downtime Deployments (Goal):**
    *   **Laravel Maintenance Mode:** Use `php artisan down` before critical changes and `php artisan up` after. Customize the maintenance mode view.
    *   **Atomic Deploys/Rolling Deploys:** Platforms like Fly.io use rolling deploys by default. For VPS, tools like Deployer can implement atomic deploys by switching symbolic links to new release directories.

## 3. Deploying to Fly.io (Recommended)

Fly.io is a container-based platform that simplifies deploying Laravel applications. It uses your project's Dockerfile (or can generate one) to build and run your application in Firecracker micro-VMs.

**Prerequisites:**
*   A Fly.io account.
*   `flyctl` (Fly CLI) installed and authenticated (`fly auth login`).
*   Your application code pushed to a Git repository.
*   A production-ready `Dockerfile` in your project root (see section 3.3).
*   A `fly.toml` configuration file (see section 3.4, often generated by `fly launch`).

### 3.1. Database Setup (Managed Service)

For production, it's highly recommended to use a managed database service rather than running your own database on Fly.io application VMs.
*   **Options:** PlanetScale (MySQL-compatible, good free tier), Aiven for MySQL/MariaDB, Neon (Postgres, if you were using it), AWS RDS, Google Cloud SQL.
*   **Steps:**
    1.  Create a database instance on your chosen provider (e.g., MariaDB 11.x or MySQL 8.x compatible).
    2.  Ensure the database is in a region geographically close to your primary Fly.io application region for low latency.
    3.  Obtain the connection details: **Host, Port, Database Name, Username, Password**.
    4.  Configure firewall rules on the managed database service to allow connections from Fly.io's IP addresses. Fly.io provides dedicated IPv4 and IPv6 addresses for outbound connections, or you can set up private networking (VPC peering) if both Fly.io and your database provider support it for enhanced security.

### 3.2. Redis Setup (Fly Redis or Managed)

*   **Fly Redis:** You can launch a managed Redis instance on Fly.io:
    ```bash
    fly redis create
    ```
    Follow the prompts. It will provide a connection URL (e.g., `redis://default:<password>@your-redis-name.flycast:6379`).
*   **External Managed Redis:** Use services like Upstash, Aiven for Redis, etc.

### 3.3. Production `Dockerfile`

Create a `Dockerfile` in your project root optimized for production. This will be more robust than directly using Sail's development Dockerfiles.

```dockerfile
# Dockerfile for Production (The Scent)

# ---- Base PHP Image ----
# Use an official PHP image with FPM. Choose a specific version.
# Sail uses runtimes/8.4. Consider this or a specific 8.2/8.3 FPM-Alpine image.
# For example, if your Sail runtime is based on Ubuntu:
FROM ubuntu:22.04 AS base

# Avoid prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV PHP_VERSION="8.3" # Or "8.2" to match composer.json strictly

# Install prerequisites
RUN apt-get update && apt-get install -y --no-install-recommends \
    software-properties-common curl unzip zip git \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
       php${PHP_VERSION}-fpm \
       php${PHP_VERSION}-cli \
       php${PHP_VERSION}-mysql \
       php${PHP_VERSION}-pgsql \
       php${PHP_VERSION}-sqlite3 \
       php${PHP_VERSION}-gd \
       php${PHP_VERSION}-curl \
       php${PHP_VERSION}-imap \
       php${PHP_VERSION}-mbstring \
       php${PHP_VERSION}-xml \
       php${PHP_VERSION}-zip \
       php${PHP_VERSION}-bcmath \
       php${PHP_VERSION}-soap \
       php${PHP_VERSION}-intl \
       php${PHP_VERSION}-readline \
       php${PHP_VERSION}-ldap \
       php${PHP_VERSION}-pcov \
       php${PHP_VERSION}-redis \
       php${PHP_VERSION}-igbinary \
       php${PHP_VERSION}-msgpack \
       # Add any other PHP extensions your application needs
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# ---- Dependencies Build Stage ----
FROM base AS dependencies_build

# Copy only composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --optimize-autoloader --prefer-dist

# Copy application code
COPY . .

# Generate optimized autoloader again with all files
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# ---- Frontend Build Stage ----
FROM node:20-alpine AS frontend_build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---- Final Application Stage ----
FROM base AS app_stage

# Install Nginx (or Caddy / Apache)
RUN apt-get update && apt-get install -y --no-install-recommends nginx supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy PHP-FPM config
COPY .docker/production/php/php-fpm.conf /etc/php/${PHP_VERSION}/fpm/php-fpm.conf
COPY .docker/production/php/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
# Add custom php.ini settings if needed (e.g., opcache, memory_limit)
COPY .docker/production/php/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-custom.ini
COPY .docker/production/php/php.ini /etc/php/${PHP_VERSION}/cli/conf.d/99-custom.ini

# Copy Nginx config
COPY .docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/production/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy Supervisor config
COPY .docker/production/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
# Add configurations for php-fpm, nginx, queue workers in /etc/supervisor/conf.d/

WORKDIR /var/www/html

# Copy built application code and dependencies
COPY --chown=www-data:www-data . .
COPY --from=dependencies_build /var/www/html/vendor ./vendor
# COPY --from=dependencies_build /var/www/html/bootstrap/cache ./bootstrap/cache # If caching config during build

# Copy compiled frontend assets
COPY --from=frontend_build --chown=www-data:www-data /app/public/build ./public/build

# Create storage symlink (this is better done via release_command or entrypoint if storage isn't part of image)
# RUN php artisan storage:link # This requires APP_URL to be set at build time or for it to not error.
# Or, handle this in an entrypoint script

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port Nginx will listen on
EXPOSE 8080

# Entrypoint script to run optimizations and start services
COPY .docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

```
*You would need to create the referenced configuration files in `.docker/production/` for PHP, Nginx, Supervisor, and an `entrypoint.sh` script. The entrypoint script would typically run `php artisan config:cache`, `route:cache`, `view:cache`, and `storage:link` (if not already handled) before starting Supervisor.*

**Example `.docker/production/entrypoint.sh`:**
```bash
#!/bin/sh
set -e

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache # If using Laravel 11+ and not auto-discovery for all events

# Create storage link if it doesn't exist (idempotent)
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

# Fix permissions again just in case
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Entrypoint script finished. Starting Supervisor..."
exec "$@"
```

### 3.4. `fly.toml` Configuration

This file configures your Fly.io application. It's often generated by `fly launch` and then customized.
Ensure `build.dockerfile` points to your production `Dockerfile`.

```toml
# fly.toml (Example for The Scent)
app = "the-scent-prod" # Replace with your Fly app name
primary_region = "ams"  # Choose a region close to your users

[build]
  dockerfile = "Dockerfile" # Points to your production Dockerfile
  # You can pass build arguments if your Dockerfile uses them
  # [build.args]
  #   PHP_VERSION = "8.2" # Example, should match Dockerfile's base

[env]
  APP_ENV = "production"
  APP_DEBUG = "false"
  APP_URL = "https://your-app-name.fly.dev" # Will be your actual app URL
  LOG_CHANNEL = "stderr" # Recommended for Fly.io to collect logs
  LOG_LEVEL = "info"     # Or 'error' for less verbosity
  DB_CONNECTION = "mysql" # Matches your .env
  CACHE_DRIVER = "redis"
  SESSION_DRIVER = "redis"
  QUEUE_CONNECTION = "redis" # If using Redis for queues

# Service definition for web traffic
[http_service]
  internal_port = 8080 # The port your app (Nginx/Caddy) listens on INSIDE the container
  force_https = true
  auto_stop_machines = true # Optional: for cost saving on low traffic
  auto_start_machines = true
  min_machines_running = 0 # Or 1 for always-on
  processes = ["app"]    # Matches the [processes.app] group below

[[vm]] # Define machine size for "app" process group
  cpu_kind = "shared"
  cpus = 1
  memory_mb = 512 # Adjust based on needs

# Release command: runs BEFORE new version is made live
[deploy]
  release_command = "php /var/www/html/artisan migrate --force"
  strategy = "rolling" # Or "bluegreen"

# Define processes run by Fly Machines
[processes]
  # 'app' process runs the web server (via Supervisor in this Dockerfile example)
  app = "/usr/bin/supervisord -c /etc/supervisor/supervisord.conf"

  # 'worker' process for Laravel queues (optional)
  # worker = "php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600"

# Optional: Define a dedicated machine size for workers if they have different needs
# [[vm]]
#   memory = "256mb"
#   cpus = 1
#   cpu_kind = "shared"
#   processes = ["worker"]
```

### 3.5. Setting Production Secrets on Fly.io

Use `fly secrets set` for all sensitive values from your production `.env` file.
```bash
# In your project directory, after logging into flyctl
fly secrets set -a the-scent-prod APP_KEY="base64:your_production_app_key" # Generate a fresh one for prod
fly secrets set -a the-scent-prod DB_HOST="your_managed_db_host"
fly secrets set -a the-scent-prod DB_PORT="your_managed_db_port"
fly secrets set -a the-scent-prod DB_DATABASE="the_scent_prod_db_name"
fly secrets set -a the-scent-prod DB_USERNAME="your_prod_db_user"
fly secrets set -a the-scent-prod DB_PASSWORD='your_prod_db_password' # Use single quotes for complex passwords

fly secrets set -a the-scent-prod REDIS_URL="redis://default:your_fly_redis_password@your-redis-name.flycast:6379" # From `fly redis status`

fly secrets set -a the-scent-prod MAIL_MAILER="your_mailer" # e.g., mailgun, ses
fly secrets set -a the-scent-prod MAILGUN_DOMAIN="your_mailgun_domain" # If using Mailgun
fly secrets set -a the-scent-prod MAILGUN_SECRET="your_mailgun_secret" # If using Mailgun
fly secrets set -a the-scent-prod MAIL_FROM_ADDRESS="noreply@yourdomain.com"
fly secrets set -a the-scent-prod MAIL_FROM_NAME="The Scent"

fly secrets set -a the-scent-prod STRIPE_KEY="pk_live_your_stripe_publishable_key"
fly secrets set -a the-scent-prod STRIPE_SECRET="sk_live_your_stripe_secret_key"
fly secrets set -a the-scent-prod STRIPE_WEBHOOK_SECRET="whsec_your_prod_stripe_webhook_secret"

# ... and any other sensitive environment variables
```

### 3.6. Deploying to Fly.io

Once your `Dockerfile`, `fly.toml`, and secrets are configured:
```bash
fly deploy -a the-scent-prod --remote-only
```
*   `--remote-only` uses Fly.io's builders. Remove it to build locally if preferred.
*   This will build your Docker image, push it to Fly.io's registry, run the `release_command` (migrations), and then deploy the new version of your application machines.

### 3.7. Post-Deployment on Fly.io

*   **Custom Domain & TLS:**
    ```bash
    fly domains add www.yourdomain.com -a the-scent-prod
    # Follow DNS instructions
    fly certs create www.yourdomain.com -a the-scent-prod # Automatic Let's Encrypt
    ```
*   **Scaling:**
    ```bash
    fly scale count 2 -a the-scent-prod # Scale web app to 2 machines
    # fly scale count worker=2 -a the-scent-prod # If you defined a 'worker' process
    ```
*   **Monitoring:**
    ```bash
    fly logs -a the-scent-prod
    fly status -a the-scent-prod
    fly dashboard -a the-scent-prod
    ```

## 4. Deploying to AWS Lightsail (LAMP Blueprint)

This is a more manual VPS-style deployment.

**Prerequisites:**
*   AWS Account.
*   SSH key pair configured with Lightsail.

**Steps:**

1.  **Create Lightsail Instance:**
    *   Choose Linux/Unix platform.
    *   Select "LAMP (PHP 8.x)" blueprint (e.g., PHP 8.2 or 8.3 if available).
    *   Choose an instance plan (e.g., $5/month for starting).
    *   Name your instance.
2.  **Assign Static IP:** Create and attach a static IP address to your instance.
3.  **Configure Firewall:**
    *   Edit instance firewall rules.
    *   Ensure HTTP (80) and HTTPS (443) are open to "Anywhere (0.0.0.0/0)".
    *   SSH (22) should be open (default).
4.  **Connect via SSH:** Use the browser-based SSH client or your local SSH client with the downloaded private key.
5.  **Initial Server Setup:**
    *   The Bitnami LAMP stack comes with Apache, MySQL (often older), and PHP.
    *   **Update System:**
        ```bash
        sudo apt update && sudo apt upgrade -y
        ```
    *   **Install/Verify Git, Composer, Node.js, NPM:**
        ```bash
        sudo apt install -y git zip unzip
        # Composer (install globally)
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
        php -r "unlink('composer-setup.php');"
        # Node.js (e.g., NodeSource for specific versions)
        curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
        sudo apt-get install -y nodejs
        ```
    *   **Database (Recommended: Install fresh MariaDB or use RDS):**
        The Bitnami stack includes MySQL, but it might be an older version or configured in a way you don't prefer.
        *   **Option A (Replace Bitnami MySQL with MariaDB):**
            ```bash
            sudo /opt/bitnami/ctlscript.sh stop mysql
            sudo apt purge mysql-server mysql-client mysql-common mysql-server-core-* mysql-client-core-* -y
            sudo apt autoremove -y
            sudo apt install -y mariadb-server mariadb-client
            sudo mysql_secure_installation # Follow prompts
            ```
            Then create your database and user in MariaDB:
            ```sql
            sudo mysql -u root -p
            CREATE DATABASE the_scent_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            CREATE USER 'scent_prod_user'@'localhost' IDENTIFIED BY 'YourStrongDbPassword!';
            GRANT ALL PRIVILEGES ON the_scent_prod.* TO 'scent_prod_user'@'localhost';
            FLUSH PRIVILEGES;
            EXIT;
            ```
        *   **Option B (Use external AWS RDS):** Provision an RDS instance and get its endpoint/credentials. Ensure Lightsail instance can connect to it (VPC peering or public access with security groups).
6.  **Deploy Application Code:**
    *   Choose a deployment directory (e.g., `/opt/bitnami/projects/the-scent` or `/var/www/the-scent`).
    *   Clone your repository: `sudo git clone <your-repo-url> /var/www/the-scent`
    *   `cd /var/www/the-scent`
7.  **Install Dependencies:**
    ```bash
    sudo composer install --optimize-autoloader --no-dev
    sudo npm ci
    sudo npm run build
    ```
8.  **Configure `.env` File:**
    ```bash
    sudo cp .env.example .env
    sudo nano .env # Or your preferred editor
    ```
    Update with **PRODUCTION** values:
    *   `APP_NAME`, `APP_ENV=production`, `APP_KEY` (run `php artisan key:generate` and copy), `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`
    *   `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1` (if MariaDB is local) or RDS endpoint, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (for your prod DB user).
    *   Mail settings, Stripe keys (LIVE keys), etc.
    *   `SESSION_DRIVER=redis` or `database` (if Redis/DB setup for sessions).
    *   `CACHE_DRIVER=redis` or `database`.
    *   `QUEUE_CONNECTION=redis` or `database`.
9.  **Set File Permissions:**
    The web server user (often `daemon` or `www-data` on Bitnami LAMP stacks, check with `ps aux | grep apache`) needs write access to `storage/` and `bootstrap/cache/`.
    ```bash
    sudo chown -R bitnami:daemon /var/www/the-scent # Assuming `bitnami` is your SSH user, `daemon` is web server group
    sudo find /var/www/the-scent/storage -type d -exec chmod 775 {} \;
    sudo find /var/www/the-scent/bootstrap/cache -type d -exec chmod 775 {} \;
    sudo find /var/www/the-scent/storage -type f -exec chmod 664 {} \;
    sudo find /var/www/the-scent/bootstrap/cache -type f -exec chmod 664 {} \;
    ```
10. **Configure Apache Virtual Host:**
    *   The Bitnami LAMP stack usually has Apache config in `/opt/bitnami/apache2/conf/`.
    *   Create a new VHost file, e.g., `/opt/bitnami/apache2/conf/vhosts/the-scent-vhost.conf`:
        ```apache
        <VirtualHost *:80>
            ServerName yourdomain.com
            ServerAlias www.yourdomain.com
            DocumentRoot "/var/www/the-scent/public"
            <Directory "/var/www/the-scent/public">
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
            </Directory>
            ErrorLog "/opt/bitnami/apache2/logs/the-scent-error_log"
            CustomLog "/opt/bitnami/apache2/logs/the-scent-access_log" common
        </VirtualHost>
        ```
    *   Include this VHost file in the main Apache configuration (e.g., in `httpd.conf` or a `vhosts.d` directory). Often Bitnami has `/opt/bitnami/apache2/conf/bitnami/bitnami-apps-vhosts.conf` where you can add an `Include` directive.
    *   Restart Apache: `sudo /opt/bitnami/ctlscript.sh restart apache`
11. **Run Laravel Production Commands:**
    ```bash
    cd /var/www/the-scent
    sudo php artisan config:cache
    sudo php artisan route:cache
    sudo php artisan view:cache
    sudo php artisan event:cache # Laravel 11+
    sudo php artisan storage:link
    sudo php artisan migrate --force
    ```
12. **Configure HTTPS (Let's Encrypt):**
    Bitnami stacks usually provide a tool for this. After pointing your domain's DNS A record to your Lightsail static IP:
    ```bash
    sudo /opt/bitnami/bncert-tool
    ```
    Follow the prompts to enable HTTPS and set up auto-renewal. This will also update your Apache config for HTTPS.
13. **Set Up Cron Job for Scheduler:**
    ```bash
    sudo crontab -e -u bitnami # Or the user your app runs as
    ```
    Add this line (adjust PHP path if different):
    ```cron
    * * * * * cd /var/www/the-scent && /opt/bitnami/php/bin/php artisan schedule:run >> /dev/null 2>&1
    ```
14. **Set Up Queue Workers (Supervisor):**
    *   Install Supervisor: `sudo apt install -y supervisor`
    *   Create a config file for your worker, e.g., `/etc/supervisor/conf.d/the-scent-worker.conf`:
        ```ini
        [program:the-scent-worker]
        process_name=%(program_name)s_%(process_num)02d
        command=/opt/bitnami/php/bin/php /var/www/the-scent/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
        autostart=true
        autorestart=true
        user=bitnami ; Or the user that owns the Laravel files and can write to logs
        numprocs=2    ; Number of worker processes
        redirect_stderr=true
        stdout_logfile=/var/www/the-scent/storage/logs/worker.log
        stopwaitsecs=3600
        ```
    *   Reload Supervisor:
        ```bash
        sudo supervisorctl reread
        sudo supervisorctl update
        sudo supervisorctl start the-scent-worker:*
        ```

## 5. Zero-Downtime Deployment Strategies

*   **Laravel Maintenance Mode:** `php artisan down --refresh=15 --secret="yourbypasssecret"` and `php artisan up`. Use the secret to access the site during maintenance.
*   **Atomic Deploys (Lightsail/VPS):** Tools like [Deployer](https://deployer.org/) or custom scripts can implement this.
    1.  Clone new release into a new timestamped directory (e.g., `releases/YYYYMMDDHHMMSS`).
    2.  Run `composer install`, `npm install`, `npm run build`.
    3.  Copy `.env` file (or link from a shared location outside releases).
    4.  Run `php artisan storage:link` (adjusting for release path).
    5.  Run Laravel optimization commands (`config:cache`, etc.).
    6.  Run `php artisan migrate --force`.
    7.  Update a symbolic link (e.g., `current`) to point to the new release directory. Your web server's DocumentRoot points to this `current` symlink.
*   **Rolling Deploys (Fly.io):** Fly.io does this by default, gradually replacing old machines with new ones. The `release_command` runs before the new version serves traffic.

## 6. Post-Deployment Checklist

1.  **Verify Application URL:** Access your production domain.
2.  **Test Key User Flows:** Registration, login, product browsing, add to cart, checkout (with a real small test payment if possible, then refund).
3.  **Check HTTPS:** Ensure the SSL certificate is active and valid.
4.  **Check `.env` Values:** Ensure production API keys are used and `APP_DEBUG=false`.
5.  **Check Logs:** Monitor Laravel logs (`storage/logs/laravel.log`), web server logs, and Fly.io logs for any errors.
6.  **Verify Queue Workers:** Ensure they are running (Supervisor status on Lightsail, `fly ps` or logs on Fly.io) and processing jobs (e.g., test order confirmation email).
7.  **Verify Scheduled Tasks:** Check if scheduled tasks (if any) are running.
8.  **Set Up Monitoring/Alerting:** For errors and performance.
9.  **Backup Strategy:** Confirm database and file storage backups are in place.

This deployment guide provides a solid starting point. Specifics may vary based on your exact server configuration and chosen services. Always test your deployment process thoroughly in a staging environment before deploying to production.
