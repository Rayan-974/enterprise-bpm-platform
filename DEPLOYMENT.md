# Production Deployment Guide: Enterprise BPM Platform

## Overview
This document outlines production deployment procedures for the **Enterprise BPM Platform**.

---

## Pre-Deployment Checklist

1. **PHP Environment**: Ensure PHP 8.2 or PHP 8.3 is installed with `pdo_mysql`, `openssl`, `mbstring`, `gd`, `bcmath`, `xml` extensions.
2. **Database Setup**: Prepare a production MySQL 8.0+ database instance.
3. **Queue Worker**: Configure Supervisor for background job processing (`php artisan queue:work`).
4. **Scheduled Tasks**: Add `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1` to system crontab for SLA breach monitoring.

---

## Deployment Steps

```bash
# 1. Pull Latest Release
git pull origin main

# 2. Install Production Dependencies
composer install --no-dev --optimize-autoloader

# 3. Cache Configurations & Routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Execute Migrations
php artisan migrate --force

# 5. Restart Queue Workers
php artisan queue:restart
```
