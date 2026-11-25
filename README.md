# UniEvents

University events and clubs platform built with Laravel.

## How to run

**Requirements:** PHP 8.2+, Composer, MySQL (Laragon works great).

```bash
# 1. Clone the project
git clone https://github.com/YOUR_USERNAME/uni-events.git
cd uni-events-modern

# 2. Install dependencies
composer install

# 3. Environment file
copy .env.example .env      # on Mac/Linux: cp .env.example .env
php artisan key:generate

# 4. Create a MySQL database named: uni_events
#    then check these lines in .env
#    DB_CONNECTION=mysql
#    DB_DATABASE=uni_events
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Create tables and sample data
php artisan migrate --seed

# 6. Start the server
php artisan serve
```

Open http://127.0.0.1:8000 (or http://uni-events.test with Laragon).

## Event posters and club logos

After pulling the changes, run `php artisan migrate` to add the image fields. The public storage link is created once with `php artisan storage:link`.

Admins can upload or replace club logos from **Admin → Clubs**. Admins and club managers can upload or replace event posters from **Manage events**. Accepted formats are JPG, PNG and WebP; posters are limited to 5 MB and logos to 2 MB. Leave the file input empty when editing to keep the current image.

## Demo accounts

Password for all accounts: `password`

| Role | Email |
|---|---|
| Admin | admin@univ.test |
| Club manager | ieee.manager@univ.test |
| Student | student1@univ.test |