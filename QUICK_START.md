# Saucy Wing – Quick Setup

## 1) Install
```bash
composer install
php artisan key:generate
```

## 2) Configure DB
Set your `.env` DB_* values.

## 3) Migrate + Seed (creates demo logins)
```bash
php artisan migrate --seed
```

Demo logins (password: `password`):
- Admin: `admin@saucywing.test`
- Manager: `manager@saucywing.test`
- Cashier: `cashier@saucywing.test`

## 4) First Branch Setup Wizard
Log in as **Admin** and if you have no branches yet, you will be redirected to create the first branch.
