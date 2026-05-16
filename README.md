# LibraryMS — Library Management System

# Deskripsi Singkat
  Sistem manajemen perpustakaan digital berbasis web yang dibangun menggunakan Laravel 11.
  Memungkinkan admin mengelola buku, anggota, dan peminjaman secara efisien,
  serta memudahkan anggota dalam mencari dan meminjam buku secara online.

---

## Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Struktur Database](#-struktur-database)
- [Struktur Folder](#-struktur-folder)
- [Cara Instalasi](#-cara-instalasi)
- [Akun Default](#-akun-default)
- [Tim Pengembang](#-tim-pengembang)
- [Timeline Proyek](#-timeline-proyek)
- [Progress Pengerjaan](#-progress-pengerjaan)

---

## Tentang Proyek

**LibraryMS** adalah aplikasi web sistem manajemen perpustakaan digital yang dikembangkan sebagai proyek mata kuliah **Pemrograman Web**. Sistem ini dirancang untuk mempermudah pengelolaan perpustakaan dengan dua peran pengguna utama:

- **Admin** — mengelola seluruh data buku, anggota, kategori, dan proses peminjaman
- **Member** — dapat menelusuri katalog buku dan mengajukan peminjaman secara online

> Proyek ini merupakan tugas kelompok mata kuliah Pemrograman Web.

---

## Fitur Utama

### Admin
| Fitur | Deskripsi |
|---|---|
| Dashboard | Statistik real-time: total buku, anggota aktif, peminjaman berjalan, keterlambatan |
| Kelola Buku | CRUD buku lengkap dengan upload cover, filter kategori, dan pencarian |
| Kelola Anggota | Manajemen data anggota, detail profil, dan riwayat peminjaman |
| Kelola Peminjaman | Approval/reject pengajuan, monitoring status, dan tandai pengembalian |
| Kelola Kategori | CRUD kategori buku |
| Laporan | Grafik aktivitas peminjaman bulanan, kategori terpopuler, dan export data |
| Pengaturan | Manajemen profil dan keamanan akun admin |

### Member
| Fitur | Deskripsi |
|---|---|
| Katalog Buku | Browse buku dengan filter kategori, pencarian, dan cek ketersediaan stok |
| Detail Buku | Informasi lengkap buku + tombol ajukan peminjaman |
| Riwayat Peminjaman | Pantau status peminjaman (pending, aktif, terlambat, selesai) |

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 11 |
| Authentication | Laravel Breeze |
| Frontend Styling | Tailwind CSS 3 |
| Database | MySQL 8.0 |
| Chart / Visualisasi | Chart.js |
| Server Lokal | PHP Built-in / Laragon / XAMPP |
| Version Control | Git + GitHub |
| Design UI | Figma |
| Deployment | GoogieHost (Free Hosting) |

---

## Struktur Database

```
library_management
├── users            → Data pengguna (admin & member) + role, phone, address
├── categories       → Kategori buku (Fiksi, Non-Fiksi, Sains, dll)
├── books            → Data buku lengkap (judul, pengarang, stok, cover)
├── borrowings       → Transaksi peminjaman (status: pending/approved/rejected/returned/overdue)
├── sessions         → Session Laravel (bawaan)
└── migrations       → Riwayat migrasi database (bawaan)
```

### Relasi Antar Tabel
```
users       ──< borrowings >── books
                                 │
categories ──────────────────────┘
```

- Satu **user** bisa memiliki banyak **borrowings**
- Satu **book** bisa memiliki banyak **borrowings**
- Satu **category** bisa memiliki banyak **books**

---

## Struktur Folder

```
library-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── BookController.php
│   │   │   │   ├── MemberController.php
│   │   │   │   ├── BorrowingController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Member/
│   │   │       ├── CatalogController.php
│   │   │       └── BorrowingHistoryController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Book.php
│       ├── Category.php
│       └── Borrowing.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_categories_table.php
│   │   ├── ..._create_books_table.php
│   │   ├── ..._create_borrowings_table.php
│   │   └── ..._add_role_to_users_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── CategorySeeder.php
│       └── BookSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   └── member.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── books/
│       │   ├── members/
│       │   ├── borrowings/
│       │   ├── categories/
│       │   ├── reports/
│       │   └── settings.blade.php
│       └── member/
│           ├── catalog/
│           └── history/
├── routes/
│   └── web.php
├── public/
│   └── storage/ (symlink untuk cover buku)
├── .env.example
├── composer.json
└── README.md
```

---

## Cara Instalasi

### Prasyarat
Pastikan sudah terinstall di komputer kamu:
- PHP >= 8.2
- Composer
- MySQL 8.0
- Node.js & NPM (untuk Tailwind CSS)
- Git

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/2411102441306/library-management-system.git
cd library-management-system
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi Node.js**
```bash
npm install
```

**4. Salin file environment**
```bash
cp .env.example .env
```

**5. Generate application key**
```bash
php artisan key:generate
```

**6. Konfigurasi database**

Buka file `.env` dan sesuaikan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=root
DB_PASSWORD=
```

**7. Jalankan migrasi database**
```bash
php artisan migrate
```

**8. Jalankan seeder (data dummy)**
```bash
php artisan db:seed
```

**9. Buat symlink storage (untuk upload cover buku)**
```bash
php artisan storage:link
```

**10. Build assets Tailwind CSS**
```bash
npm run dev
```

**11. Jalankan server lokal**
```bash
php artisan servea
```

Aplikasi dapat diakses di: **http://127.0.0.1:8000**

---

## Akun Default

Setelah menjalankan seeder, gunakan akun berikut untuk login:

| Role | Email | Password |
|---|---|---|
| Admin | admin@library.id | password |
| Member | member@library.id | password |

> Segera ganti password setelah login pertama kali di halaman Pengaturan.

---

## Tim Pengembang

Proyek ini dikerjakan oleh tim yang terdiri dari 6 orang:

| Nama | NIM | Peran |
|---|---|---|
| Ahmad Saifuddin Dzaki | 2411102441306 | Project Manager & Lead Developer |
| Fara Aulia Ananda Gunawan | 2411102441076 | Frontend Developer |
| Muhammad Naufal Putra | 2411102441246 | Backend Developer |
| Kurnia Dwi Agustina | 2411102441284 | Backend Developer |
| Ariani Sapto | 2411102441262 | UI/UX Designer & QA Tester |
| Satriani | 2411102441122 | QA Tester & Documentation |

---

## Timeline Proyek

| Minggu | Fase | Aktivitas |
|---|---|---|
| **Minggu 1** | Inisiasi & Desain | Setup Laravel, database migration, desain Figma (UI/UX), dokumentasi |
| **Minggu 2** | Backend Development | CRUD buku & kategori, sistem peminjaman, role middleware, seeder |
| **Minggu 3** | Frontend & Integrasi | Blade views, Tailwind styling, Chart.js, integrasi frontend-backend |
| **Minggu 4** | Testing & Deployment | QA testing, bug fixing, optimasi, deployment ke GoogieHost |

---

## Progress Pengerjaan

### Minggu 1 — Inisiasi & Desain
- [x] Setup project Laravel 11
- [x] Install & konfigurasi Laravel Breeze (autentikasi)
- [x] Konfigurasi database MySQL
- [x] Migrasi tabel: `categories`, `books`, `borrowings`
- [x] Tambah kolom `role`, `phone`, `address` ke tabel `users`
- [x] Desain UI Figma — Dashboard Admin
- [ ] Desain UI Figma — semua halaman (10 Admin + 4 Member)
- [ ] Setup repository GitHub & branch strategy
- [ ] Dokumentasi README.md

### Minggu 2 — Backend Development
- [ ] Model + Eloquent relationships (User, Book, Category, Borrowing)
- [ ] CRUD Kategori (Admin)
- [ ] CRUD Buku dengan upload cover (Admin)
- [ ] CRUD Anggota (Admin)
- [ ] Sistem pengajuan & approval peminjaman
- [ ] Role middleware (admin vs member)
- [ ] Database seeder (data dummy)

### Minggu 3 — Frontend & Integrasi
- [ ] Layout admin (sidebar, topbar, responsive)
- [ ] Layout member (navbar, footer)
- [ ] Semua halaman Blade views
- [ ] Integrasi Chart.js di halaman laporan
- [ ] Search & filter buku

### Minggu 4 — Testing & Deployment
- [ ] QA testing semua fitur
- [ ] Bug fixing
- [ ] Optimasi (`php artisan optimize`)
- [ ] Deployment ke GoogieHost
- [ ] Final dokumentasi

---

## Links

| Resource | URL |
|---|---|
| Repository GitHub | https://github.com/2411102441306/library-management-system |
| Desain Figma | [Link Figma — akan diupdate] |
| Live Demo | [Link Deploy — akan diupdate] |

---

## Lisensi

Proyek ini dibuat untuk keperluan tugas akademik mata kuliah Pemrograman Web.

---

<p align="center">
  Dibuat oleh Kelompok 7 LibraryMS | Mata Kuliah Pemrograman Web
</p>