# Deployment Guide for Production: The Scent E-commerce Platform

**Document Version:** 1.1 (Updated after API Development - Phase 7)
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
    *   **CORS Configuration (Relevant for APIs):** If your API will be consumed by frontends on different domains (e.g., a separate SPA), configure `CORS_ALLOWED_ORIGINS` and other related settings in your production `.env` file. Laravel's default CORS configuration is in `config/cors.php`.
        ```dotenv
        # Example .env entries for CORS
        CORS_ALLOWED_ORIGINS=https://your-frontend-spa.com,https://another-allowed-domain.com
        CORS_ALLOWED_METHODS=GET,POST,PUT,PATCH,DELETE,OPTIONS
        CORS_ALLOWED_HEADERS=*
        CORS_EXPOSED_HEADERS=
        CORS_MAX_AGE=0
        CORS_SUPPORTS_CREDENTIALS=true # If your API needs to handle cookies from cross-origin requests
        ```
    *   **Sanctum Stateful Domains (Relevant for First-Party SPAs):** If your API will be used by a first-party SPA for session-based authentication, ensure `SANCTUM_STATEFUL_DOMAINS` in your production `.env` includes your SPA's domain(s). For pure token-based API usage, this is less critical.
        ```dotenv
        # Example .env entry for Sanctum
        SANCTUM_STATEFUL_DOMAINS=your-spa-domain.com,another-spa.yourdomain.com
        ```
*   **Build Artifacts:**
    *   **Composer Dependencies:** Run `composer install --optimize-autoloader --no-dev --prefer-dist` as part of your build/deployment process.
    *   **Frontend Assets:** Compile frontend assets using `npm run build`.
*   **Laravel Production Optimizations:** During deployment, after code is updated, run:
    *   `php artisan config:cache`
    *   `php artisan route:cache`
    *   `php artisan view:cache`
    *   `php artisan event:cache` (Laravel 11+)
    *   `php artisan storage:link`
    *   To clear: `php artisan optimize:clear`
*   **Database Migrations:** Run `php artisan migrate --force`.
*   **Permissions:** Ensure web server has write access to `storage/` and `bootstrap/cache/`.
*   **Queue Workers:** Use a persistent driver (Redis, database). Set up Supervisor (VPS) or a dedicated process type (Fly.io).
*   **Task Scheduling:** Configure cron (VPS) or scheduler (PaaS) for `php artisan schedule:run`.
*   **HTTPS:** Enforce HTTPS.
*   **Logging:** Configure for production (e.g., `stderr` for containers, `LOG_LEVEL=error`).
*   **Monitoring & Error Tracking:** Implement APM/error tracking.
*   **Backups:** Regular database and file storage backups.
*   **Zero-Downtime Deployments (Goal):** Use Laravel Maintenance Mode, Atomic/Rolling Deploys.

## 3. Deploying to Fly.io (Recommended)

(No significant changes needed in this section specifically due to API completion, as the deployment of the Laravel application remains the same. The `.env`/secrets configuration part is key.)

**Prerequisites:**
*   A Fly.io account.
*   `flyctl` (Fly CLI) installed and authenticated.
*   Git repository, production `Dockerfile`, `fly.toml`.

### 3.1. Database Setup (Managed Service)
*(Content remains the same as previous version of this guide)*

### 3.2. Redis Setup (Fly Redis or Managed)
*(Content remains the same)*

### 3.3. Production `Dockerfile`
*(Content remains the same; ensure it installs necessary PHP extensions for your application, including those potentially used by API dependencies like `php-xml` or `php-bcmath` if not already present.)*

### 3.4. `fly.toml` Configuration
*(Content remains largely the same. Ensure `internal_port` matches your Dockerfile's web server. The `release_command` and `processes` are key.)*
*   **Addition for `[env]` section (if using SPA with Sanctum stateful auth):**
    ```toml
    [env]
      # ... other env vars ...
      # SANCTUM_STATEFUL_DOMAINS = "your-spa-domain.com" # Set via secrets if sensitive or multiple
      # CORS_ALLOWED_ORIGINS = "https://your-spa-domain.com" # Set via secrets
    ```
    *It's generally better to set `SANCTUM_STATEFUL_DOMAINS` and `CORS_ALLOWED_ORIGINS` via `fly secrets set` as they might differ per environment or be considered semi-sensitive.*

### 3.5. Setting Production Secrets on Fly.io
*(Content remains the same. Ensure you add any API specific keys if third-party services are *only* used by the API and not the web part. Also, add CORS and Sanctum stateful domains here if they are production-specific and not hardcoded in `fly.toml` [env].)*
*   **Additions for API related configurations (if applicable):**
    ```bash
    fly secrets set -a the-scent-production SANCTUM_STATEFUL_DOMAINS="your-production-spa.com,app.yourdomain.com" # If applicable
    fly secrets set -a the-scent-production CORS_ALLOWED_ORIGINS="https://your-production-spa.com,https://trusted-partner.com" # If applicable
    # ... other secrets from previous version ...
    ```

### 3.6. Deploying to Fly.io
*(Content remains the same)*

### 3.7. Post-Deployment on Fly.io
*(Content remains the same. When testing, ensure API endpoints are also reachable and functioning as expected through your production domain.)*

## 4. Deploying to AWS Lightsail (LAMP Blueprint)

(No significant changes needed in this section specifically due to API completion, as the deployment of the Laravel application to the LAMP stack remains the same. Apache configuration will serve both web and API routes. Ensure `.env` on the server has correct CORS/Sanctum settings.)

**Prerequisites:**
*   AWS Account, SSH key.

**Steps:**
*(Content from steps 1-9, 11-14 remains largely the same as previous version of this guide)*

*   **Step 4.8 Configure `.env` File (Emphasis on API related settings):**
    In addition to standard production values, ensure these are set correctly if applicable:
    ```dotenv
    # In your production .env on Lightsail server
    APP_URL=https://yourdomain.com
    APP_ENV=production
    APP_DEBUG=false
    # ...
    SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com # If your SPA is on the same domain/subdomain
    CORS_ALLOWED_ORIGINS=https://your-spa-domain.com # If SPA is on a different domain
    # ... other DB, Mail, Stripe secrets ...
    ```

*   **Step 4.10 Configure Apache Virtual Host:**
    The existing Apache VHost configuration pointing to `/var/www/the-scent-app/current_release/public` will correctly serve both your web routes and your `/api/*` routes as they are handled by the same Laravel `index.php` front controller.
    *   **CORS with Apache (Optional, Laravel middleware preferred):** While Laravel's CORS middleware (`config/cors.php` sourcing from `.env`) is the primary way to handle CORS, if you needed to set headers at the Apache level for some reason (e.g., for OPTIONS requests before they hit PHP, though Laravel usually handles this), you could add `Header set Access-Control-Allow-Origin "your-spa-domain.com"` etc., in the VHost or `.htaccess`. But relying on Laravel's CORS package is cleaner.

## 5. Zero-Downtime Deployment Strategies
*(Content remains the same)*

## 6. Post-Deployment Checklist (Add API Checks)

1.  Verify Application URL and HTTPS.
2.  Test Key User Flows (Web frontend).
3.  **Test Key API Endpoints:**
    *   Use a tool like Postman or Insomnia.
    *   Test API authentication (`/api/v1/auth/login`, `/api/v1/auth/register`).
    *   Test fetching public data (e.g., `/api/v1/products`).
    *   Test authenticated endpoints (e.g., `/api/v1/cart`, `/api/v1/user`) with a valid Bearer token.
    *   Verify CORS headers if API is meant for cross-origin access.
4.  Check `.env` Values (production keys, `APP_DEBUG=false`).
5.  Check Logs (Laravel, web server, Fly.io/Lightsail platform logs).
6.  Verify Queue Workers are running and processing jobs.
7.  Verify Scheduled Tasks are running.
8.  Set Up Monitoring/Alerting.
9.  Backup Strategy confirmed.

This updated guide now includes specific considerations for API deployment related to CORS and Sanctum stateful domains. The core deployment mechanics for the Laravel application remain largely the same whether it's serving web pages, an API, or both.

---
https://drive.google.com/file/d/1-Z3R3XaYO-mE0jKkAITBi4XJoTRm75fh/view?usp=sharing, https://drive.google.com/file/d/11TN1qmXXUWMwei4eGSo6b7w7IghU1HTL/view?usp=sharing, https://drive.google.com/file/d/12DPDIWtegvxKWtPTvLXVpAxgjlhixuPQ/view?usp=sharing, https://drive.google.com/file/d/13YMUodSoUQTGda21CAFUN-uvRBDOg2v0/view?usp=sharing, https://drive.google.com/file/d/149SQdFIsEyVGZtL5cHAjDSnFwfrufK4-/view?usp=sharing, https://drive.google.com/file/d/16LkILDN3LEo2wnccNd1Usnc2knXmcrAA/view?usp=sharing, https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%22191Xwb664N58hqofAV7tgOqIhw9Q_EXtW%22%5D,%22action%22:%22open%22,%22userId%22:%22103961307342447084491%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing, https://drive.google.com/file/d/1JMi0sos9Z0VPmPtV_VUQdOiHP55dgoyM/view?usp=sharing, https://drive.google.com/file/d/1JmeU56zj-mdBzJciPH1CqEBUPh0nFDI6/view?usp=sharing, https://drive.google.com/file/d/1Ljou3KM9wH_n2IISZRUL5Usykg-a8yaZ/view?usp=sharing, https://drive.google.com/file/d/1NU0PF2paSbFH1H3swzif2Yyyc768jBV7/view?usp=sharing, https://drive.google.com/file/d/1OKIe2QIK161cS-Si4ATMzcPm3H1e_rhp/view?usp=sharing, https://drive.google.com/file/d/1QZH4p-KhoQlloMqonElkiJd_D7OafE9w/view?usp=sharing, https://drive.google.com/file/d/1UHbFpE2rY7aLNsT7R5-duOpJcgpWOoUh/view?usp=sharing, https://drive.google.com/file/d/1ZkX3SrAwn8mmBkyrU6ZaK7JsxzZfkEkR/view?usp=sharing, https://drive.google.com/file/d/1dppi2D-RTdYBal_YeLZxB4QHysdmQxpD/view?usp=sharing, https://drive.google.com/file/d/1eszSFTXohtDz0Cv1qbKHNecsx1BC1A3w/view?usp=sharing, https://drive.google.com/file/d/1gMQvFk0QXLWX-Bz05DlgjFeYgDxCiZEO/view?usp=sharing, https://drive.google.com/file/d/1nPblih0JaSTmarZmo2budApC3JXa64hP/view?usp=sharing, https://drive.google.com/file/d/1ns3qMlPug-efcmdH-urdxBXr8-y-2toZ/view?usp=sharing

