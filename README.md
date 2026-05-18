# 🎵 NTBeat

**NTBeat** adalah platform berbasis web terpusat yang dikembangkan untuk mengelola, menyebarkan, dan memfasilitasi akses informasi terkait konser musik di wilayah Nusa Tenggara Barat. Dengan adanya sistem ini, alur penyampaian informasi acara menjadi lebih cepat, akurat, transparan, dan mudah diakses baik oleh masyarakat luas maupun pihak pengelola manajemen konser.

---

## 📂 Struktur Halaman Website

Berikut adalah peta struktur halaman dan tata letak file `.php` yang diimplementasikan pada sistem NTBeat:

```text
NTBeat Website Structure
├── 🌐 Akses Publik
│   ├── Halaman Utama (index.php)
│   ├── Login Akun (login.php)
│   └── Pendaftaran Akun (register.php)
│
├── 👤 Panel Pengguna (Customer Dashboard)
│   └── Dashboard Customer (halaman-awal.php)
│       ├── Daftar Konser Mendatang
│       │   └── Detail Konser (detail-konser.php)
│       ├── Tiket Saya & Riwayat Aktivitas (tiket-saya.php)
│       └── Pengaturan Profil Akun (profil.php)
│
└── 👑 Panel Pengelola (Admin Dashboard)
    └── Dashboard Admin (admin-dashboard.php)
        ├── Tambah Acara Baru (admin-form-konser.php)
        ├── Kelola Data Konser (admin-kelola-konser.php)
        │   └── Ubah Informasi Konser (admin-edit-konser.php)
        ├── Pengaturan Profil Administrator (admin-profil.php)
        └── Arsip Penyelenggaraan (admin-arsip.php)

```

---

## 🎭 Aktor Sistem (Website Actors)

Sistem NTBeat membagi tingkat otorisasi hak akses ke dalam dua jenis pengguna utama:

1. **👑 Administrator:** Berwenang penuh di balik layar untuk melakukan manipulasi data acara (CRUD), memantau ringkasan kuota tiket, melakukan aksi massal (*bulk action*), serta meninjau laporan pendapatan melalui analitik grafik realtime.
2. **👤 Customer:** Pengguna umum yang dapat menjelajahi daftar konser mendatang, menggunakan fitur pencarian instan artis (*live search*), menilik detail info acara, melakukan simulasi pemesanan, serta melacak e-ticket digital pribadi.

---

## 💻 Tech Stack

Teknologi utama yang melandasi pengembangan sistem purwarupa NTBeat meliputi:

* **Frontend:** HTML5, CSS3, JavaScript Native (Vanilla JS)
* **Library Eksternal:** Chart.js (Digunakan untuk visualisasi grafik analitik tren penjualan tiket pada dashboard pengelola)
* **Backend:** PHP Native (Kompatibilitas Versi 8.2+)
* **DBMS & Server:** MySQL & Apache Server (Dioperasikan lokal via XAMPP)

---

## 👥 Tim & Pembagian Tugas (Team & Roles)

Proyek ini dikembangkan secara kolaboratif. Berikut rincian pembagian kerja dari masing-masing pengembang sistem:

| Nama Lengkap | NIM | Peran Utama & Fokus Kontribusi |
| --- | --- | --- |
| **Salsabila Nailafahdi** | F1D02410135 | **Team Leader & Customer-Side Developer**<br>

<br>• Bertanggung jawab penuh atas pengerjaan seluruh bagian panel pengguna (*customer panel*).

<br>• Menyusun struktur kode HTML, penataan visual CSS, dan logika interaktif JavaScript di sisi klien pengguna (`halaman-awal.php`, `detail-konser.php`, `tiket-saya.php`, `profil.php`).

<br>• Membangun gerbang arsitektur logika autentikasi serta alur pemrosesan data untuk sistem **Login** dan **Register** (`login.php`, `register.php`).

| Nama Lengkap | NIM | Peran Utama & Fokus Kontribusi |
| --- | --- | --- |
| **Tegu Ilham Pebrian Saputra** | F1D02410097 | **Admin-Side Developer (Interface & Logic)**<br>

<br>• Bertanggung jawab bersama dalam menyusun modul ekosistem halaman pengelola (administrator).

<br>• Merancang kerangka kode struktur HTML dan penataan desain visual komponen *Admin Dashboard* menggunakan CSS.

<br>• Menangani manipulasi perilaku elemen antarmuka via JavaScript serta menyusun pemrosesan logika bisnis dinamis berbasis PHP di sisi server (`admin-dashboard.php`, `admin-edit-konser.php`, `admin-form-konser.php`).

| Nama Lengkap | NIM | Peran Utama & Fokus Kontribusi |
| --- | --- | --- |
| **M. Alfatih** | F1D02410013 | **Admin-Side & Database Systems Engineer**<br>

<br>• Bertanggung jawab bersama dalam merancang dan memfungsikan seluruh modul kendali utama administrator.

<br>• Menyusun struktur kerangka kode HTML, fungsionalitas visual CSS admin, mekanisme backend PHP server, serta penanganan aksi massal tabel data menggunakan JavaScript (*bulk operations* pada tabel data) (`admin-kelola-konser.php`, `admin-arsip.php`, `admin-profil.php`).

<br>• Merancang arsitektur basis data relasional, pemetaan spesifikasi tabel sistem, serta manajemen kueri SQL (`ntbeat_db.sql`). |

---

## 🗄️ Database Management System

### 1. Konfigurasi Koneksi (`koneksi.php`)

Sistem terhubung ke server basis data lokal menggunakan ekstensi MySQLi dengan parameter konfigurasi bawaan berikut:

```php
$host     = "localhost";
$user     = "root";
$password = "";
$db       = "ntbeat_db";

```

### 2. Spesifikasi Struktur Tabel (`ntbeat_db.sql`)

Skema pangkalan data proyek didukung oleh tiga entitas relasional utama:

#### A. Tabel `users` (Data Kredensial Akun Pengguna)

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| --- | --- | --- | --- |
| `id` | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Identifier unik entitas user |
| `nama` | VARCHAR(100) | NOT NULL | Nama lengkap pemilik akun |
| `email` | VARCHAR(100) | NOT NULL, UNIQUE | Alamat surel (digunakan untuk login) |
| `password` | VARCHAR(255) | NOT NULL | Kata sandi terenkripsi (`password_hash`) |
| `role` | ENUM('admin','user') | DEFAULT 'user' | Pembatasan hak akses sistem |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Waktu registrasi pembuatan akun |

#### B. Tabel `konser` (Data Detail Informasi Pertunjukan)

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| --- | --- | --- | --- |
| `id` | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Identifier unik pertunjukan konser |
| `nama_konser` | VARCHAR(150) | NOT NULL | Judul/Nama agenda acara |
| `tanggal` | DATE | NOT NULL | Tanggal pelaksanaan pertunjukan |
| `waktu` | TIME | NOT NULL | Jam operasional dimulainya acara |
| `lokasi` | VARCHAR(150) | NOT NULL | Venue/Tempat penyelenggaraan |
| `harga` | DECIMAL(10,2) | NOT NULL | Tarif nominal harga per tiket |
| `kapasitas` | INT(11) | NOT NULL | Kuota kapasitas total penonton |
| `tiket_terjual` | INT(11) | DEFAULT 0 | Kuantitas tiket yang sudah dipesan |
| `poster` | VARCHAR(255) | DEFAULT 'default-poster.jpg' | Path berkas gambar poster konser |
| `status` | ENUM(...) | DEFAULT 'Tersedia' | Status kuota ('Tersedia','Hampir Habis','Habis','Selesai','Arsip') |
| `created_at` | TIMESTAMP | DEFAULT current_timestamp() | Jejak penambahan data pertunjukan |

#### C. Tabel `pesanan` (Log Riwayat Transaksi Tiket)

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| --- | --- | --- | --- |
| `id` | INT(11) | PRIMARY KEY, AUTO_INCREMENT | Identifier unik order pesanan |
| `order_id` | VARCHAR(50) | NOT NULL, UNIQUE | Kode invoice struk unik transaksi |
| `user_id` | INT(11) | NOT NULL, **FOREIGN KEY** | Relasi ke `users.id` (ON DELETE CASCADE) |
| `konser_id` | INT(11) | NOT NULL, **FOREIGN KEY** | Relasi ke `konser.id` (ON DELETE CASCADE) |
| `jumlah_tiket` | INT(11) | NOT NULL | Jumlah lembar kupon yang dibeli |
| `total_harga` | DECIMAL(12,2) | NOT NULL | Akumulasi total nominal biaya belanja |
| `status_bayar` | ENUM(...) | DEFAULT 'Lunas' | Status invoice ('Pending','Lunas','Dibatalkan') |
| `tanggal_pesan` | TIMESTAMP | DEFAULT current_timestamp() | Catatan waktu penyelesaian transaksi |

---

## 🚀 Panduan Menjalankan Aplikasi Secara Lokal

Ikuti langkah-langkah di bawah ini untuk mengonfigurasi dan menguji sistem di lingkungan lokal komputer Anda:

1. **Unduh atau Clone Repositori:**
```bash
git clone [https://github.com/naiaero/ntbeat.git](https://github.com/naiaero/ntbeat.git)

```

2. **Pindahkan Direktori Kerja:**
* Salin atau pindahkan folder proyek `NTBeat` Anda ke dalam direktori root server htdocs Anda (biasanya terletak di `C:/xampp/htdocs/`).
* Jalankan panel kontrol *XAMPP Control Panel* lalu nyalakan modul **Apache** beserta **MySQL**.


3. **Import Basis Data:**
* Buka browser dan kunjungi laman pengelolaan database: `http://localhost/phpmyadmin/`.
* Buat basis data baru dengan nama exact: `ntbeat_db`.
* Pilih menu **Import**, cari file skema SQL `ntbeat_db.sql` yang berada di root direktori proyek Anda, kemudian klik tombol **Go / Kirim**.


4. **Akses Sistem Melalui Browser:**
* Buka tab browser baru lalu akses URL: `http://localhost/NTBeat/index.php`.

```