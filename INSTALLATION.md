# Panduan Instalasi Web Laravel

## Prasyarat

-   PHP >= 8.2
-   Composer
-   Node.js & npm
-   Database (MySQL/PostgreSQL/SQLite, sesuai konfigurasi)

## Langkah Instalasi

### 1. Clone Repository

```bash
git clone <url-repo-anda>
cd <nama-folder-repo>
```

### 2. Instalasi Dependency Backend

```bash
composer install
```

### 3. Instalasi Dependency Frontend

```bash
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Atur konfigurasi database dan variabel lain di file `.env` sesuai kebutuhan Anda.

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Migrasi & Seeder Database

```bash
php artisan migrate --seed
```

### 7. Menjalankan Server Lokal

#### Jalankan Backend Laravel

```bash
php artisan serve
```
