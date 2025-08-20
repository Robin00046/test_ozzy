# Laravel Installation Guide

Panduan ini menjelaskan cara menginstal dan menjalankan Laravel dari awal.

---

## 1. Prasyarat

Pastikan Anda telah menginstal:

-   **PHP ≥ 8.1** (`php -v`)
-   **Composer** (`composer -V`)
-   **Database** (MySQL, PostgreSQL, atau SQLite)
-   **Node.js + npm** (untuk frontend & Vite)
-   (Opsional) **Git** untuk version control

> **Tips:**  
> Jika ingin tanpa repot setup environment, gunakan **Laravel Sail** (Docker).

---

## 2. Install Laravel via Composer

```bash
composer create-project laravel/laravel my-project
cd my-project
```

Atau jika menggunakan Laravel Installer:

```bash
composer global require laravel/installer
laravel new my-project
cd my-project
```

---

## 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Atur koneksi database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_database
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Jalankan Server Lokal

```bash
php artisan serve
```

Akses aplikasi di browser: [http://localhost:8000](http://localhost:8000)

## 6. Migrasi Database

Jalankan migrasi untuk membuat tabel default Laravel:

```bash
php artisan migrate
```

---

## Ringkasan Perintah Utama

| Kebutuhan        | Perintah                                      |
| ---------------- | --------------------------------------------- |
| Install project  | `composer create-project laravel/laravel app` |
| Jalankan server  | `php artisan serve`                           |
| Generate key     | `php artisan key:generate`                    |
| Migrasi database | `php artisan migrate`                         |

---
