# 🚀 AgilePM — Modern Agile Project Management Studio

[![Latest Version](https://img.shields.io/badge/release-v1.4.0-indigo.svg?style=flat-square)](https://github.com/myasirnovals/management-project-apps/releases/tag/v1.4.0)
[![License](https://img.shields.io/badge/License-Apache_2.0-blue.svg?style=flat-square)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D_8.2-purple.svg?style=flat-square&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3.x-38B2AC.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)

**AgilePM** adalah aplikasi manajemen proyek modern berbasis web yang dirancang khusus untuk memfasilitasi alur kerja metodologi **Agile**, **Scrum**, dan **Kanban**. Dilengkapi dengan otomatisasi **GitHub Webhooks**, pengingat **WhatsApp langsung**, notifikasi email SMTP dinamis, serta sistem kolaborasi tiket (lampiran & diskusi progres) untuk tim pengembang perangkat lunak.

---

## 🌟 Fitur Utama

### 1. 📋 Papan Agile Fleksibel (Scrum & Kanban)
* **Scrum Board**: Alur kerja berbasis sprint dengan batas WIP (*Work-in-Progress constraints*), *swimlanes* berdasarkan Assignee atau Epic, dan filter instan.
* **Kanban Board**: Alur kerja kontinu dengan fitur *drag-and-drop* intuitif antar kolom status (*To Do*, *In Progress*, *In Review*, *Done*).
* **Indikator Pull Request Langsung**: Kartu tiket secara otomatis menampilkan badge PR GitHub aktif (contoh: `PR #12`) saat terhubung dengan kode repositori.

### 2. 🐙 Integrasi GitHub Per-Proyek & Otomatisasi Webhooks
* **Terisolasi & Opsional**: Setiap proyek dapat dihubungkan ke repositori GitHub masing-masing secara independen tanpa memengaruhi proyek lain.
* **Otomatisasi Status Tiket**:
  * Ketika developer membuka Pull Request dengan kode tiket (contoh: `[PRK-42]`), status tiket otomatis beralih ke **In Review**.
  * Ketika Pull Request di-merge ke branch utama, status tiket otomatis diselesaikan (**Done**).
* **Riwayat & Catatan Otomatis**: Log aktivitas PR tercatat otomatis di kolom komentar tiket dan pada dashboard riwayat integrasi.
* **Verifikasi Keamanan**: Didukung validasi tanda tangan HMAC SHA-256 (*Webhook Secret*).

### 3. 💬 Kolaborasi Tiket (Lampiran & Diskusi)
* **Detail Tiket Slideover & Pop-up Modal**: Tampilan detail tiket yang komprehensif tanpa perlu meninggalkan halaman papan pengerjaan.
* **Lampiran Bukti Pengerjaan**: Unggah screenshot kendala, dokumen analisis (gambar, PDF, Word, ZIP hingga 25 MB) dengan kontrol hak akses aman.
* **Catatan Progres & Diskusi**: Diskusi interaktif antar anggota tim dengan dukungan tautan otomatis (*auto-link URL*).

### 4. 📲 Notifikasi WhatsApp & SMTP Dinamis
* **WhatsApp Direct Service**: Kirim pengingat tugas, peringatan keterlambatan (*overdue*), dan eskalasi langsung ke nomor WhatsApp pengembang hanya dengan satu klik.
* **Pengaturan SMTP Dinamis**: Konfigurasi kredensial SMTP (Brevo, Gmail, Mailtrap, dll.) yang tersimpan aman pada storage aplikasi tanpa perlu me-restart server.

### 5. 🗂️ Manajemen Backlog & Siklus Hidup Sprint
* **Backlog Planning**: Pengelompokan tiket ke dalam sprint, estimasi *Story Points*, prioritas tiket, serta penetapan komponen.
* **Aksi Massal (*Bulk Actions*)**: Pindahkan beberapa tiket sekaligus, pembersihan backlog otomatis, dan ekspor data ke format CSV.
* **Siklus Sprint**: Validasi sprint aktif ganda dan pemindahan tiket yang belum selesai secara otomatis ke backlog atau sprint berikutnya.

### 6. 📊 Laporan Analitik Agile
* **Burndown Chart**: Melacak sisa pekerjaan vs target waktu sprint.
* **Burnup Chart**: Mengukur total cakupan pekerjaan yang diselesaikan seiring waktu.
* **Velocity Chart**: Menghitung kapasitas dan kecepatan tim per sprint.
* **Cumulative Flow Diagram (CFD)**: Menganalisis stabilitas alur kerja dan hambatan (*bottleneck*).
* **Epic Report**: Pemantauan progres penyelesaian inisiatif besar (*Epics*).

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

* **Backend**: [Laravel 12](https://laravel.com), PHP 8.2+
* **Frontend**: Blade Templates, [Tailwind CSS](https://tailwindcss.com), Alpine.js / Vanilla JavaScript
* **Database**: MySQL / SQLite (dengan integritas relasional dan *cascading delete*)
* **Pengujian**: PHPUnit / Pest (70+ pengujian unit & fitur otomatis)
* **Autentikasi**: Laravel Session Auth berbasis peran (*Role-Based Access Control*)

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat
* PHP >= 8.2 (ekstensi: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`)
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/) & NPM
* Server Database (MySQL / MariaDB)

### Langkah-langkah:

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/myasirnovals/management-project-apps.git
   cd management-project-apps
   ```

2. **Pasang Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Pasang Dependensi Frontend & Build Aset**:
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment**:
   Salin file `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan koneksi database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Kunci Aplikasi**:
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi & Seeder Data Awal**:
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Penyimpanan Simbolik (Storage Link)**:
   ```bash
   php artisan storage:link
   ```

8. **Nyalakan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi siap diakses melalui peramban di: **`http://127.0.0.1:8000`** (atau port yang tertera pada terminal).

---

## 🔑 Akun Demo / Pengguna Awal

Setelah menjalankan migrasi dengan `--seed`, Anda dapat masuk menggunakan akun bawaan berikut:

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| **Project Manager / Admin** | `admin@project.local` | `password123` |
| **Product Owner** | `po@project.local` | `password123` |
| **Developer** | `yessi@project.local` | `password123` |

---

## 🔗 Menghubungkan Proyek ke GitHub

1. Masuk ke aplikasi dan pilih proyek yang ingin dihubungkan.
2. Pada menu sidebar kiri, klik **GitHub & PR**.
3. Masukkan nama repositori GitHub Anda (contoh: `username/nama-repo`) lalu klik **Simpan Konfigurasi**.
4. Salin **Payload URL** dan **Secret Token** yang ditampilkan di halaman tersebut.
5. Di repositori GitHub Anda:
   * Masuk ke menu **Settings** &rarr; **Webhooks** &rarr; klik **Add webhook**.
   * Tempelkan **Payload URL** dan **Secret**.
   * Ubah *Content type* menjadi `application/json`.
   * Pada pilihan event, pilih *"Let me select individual events"* dan beri centang pada **Pull requests**.
   * Simpan webhook. Selesai!

---

## 🧪 Menjalankan Pengujian (Testing)

Aplikasi dilengkapi dengan rangkaian pengujian fitur otomatis untuk menjamin integritas transaksi, keamanan, dan alur kerja:

```bash
php artisan test
```

---

## 📄 Lisensi (License)

Proyek ini merupakan perangkat lunak sumber terbuka (*open-source software*) yang dilisensikan di bawah lisensi [Apache License, Version 2.0](LICENSE).

Hak Cipta © 2026 **Muhamad Yasir Noval**.
