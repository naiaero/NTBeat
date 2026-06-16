# 🎵 NTBeat

**NTBeat** adalah platform berbasis web terpusat yang dikembangkan untuk mengelola, menyebarkan, dan memfasilitasi akses informasi terkait konser musik di wilayah Nusa Tenggara Barat. Dengan adanya sistem ini, alur penyampaian informasi acara menjadi lebih cepat, akurat, transparan, dan mudah diakses baik oleh masyarakat luas.

---

## 📂 Struktur Folder & Berkas Proyek

Sistem NTBeat disusun menggunakan arsitektur modular sederhana berbasis PHP Native dengan pembagian direktori sebagai berikut:

```text
NTBeat/
├── 📁 .vscode/
│   ├── 📄 c_cpp_properties.json
│   ├── 📄 launch.json
│   └── 📄 settings.json
│
├── 📁 actions/                      # Berisi skrip pemrosesan logika backend (server-side)
│   ├── 📄 admin-bulk-proses.php     # Memproses aksi massal admin (hapus/arsip konser)
│   ├── 📄 admin-tambah-proses.php   # Memproses validasi upload poster dan input konser baru oleh admin
│   ├── 📄 edit-eo-proses.php        # Memproses perubahan data profile/akun milik Event Organizer
│   ├── 📄 forgot_password_proses.php# Memproses pemulihan kata sandi pengguna
│   ├── 📄 login_proses.php          # Memproses verifikasi kredensial login & pembagian hak akses (3 role)
│   ├── 📄 nonaktif-eo-proses.php    # Memproses penonaktifan akun Event Organizer oleh Admin
│   ├── 📄 pesan-tiket-proses.php    # Memproses transaksi pemesanan tiket dengan database transaction
│   ├── 📄 register_proses.php       # Memproses pendaftaran akun pengguna baru (customer)
│   ├── 📄 tambah-eo-proses.php      # Memproses pembuatan/pendaftaran akun Event Organizer baru oleh Admin
│   ├── 📄 update-konser-proses.php  # Memproses pembaruan data konser dan manipulasi file poster lama
│   ├── 📄 update-profile-proses.php # Memproses pembaruan profil pengguna (nama, foto, & password)
│   ├── 📄 upload-bukti-proses.php   # Memproses unggahan foto bukti pembayaran tiket oleh Customer
│   └── 📄 verifikasi-bayar-proses.php# Memproses persetujuan/penolakan bukti pembayaran oleh EO atau Admin
│
├── 📁 admin/                        # Halaman-halaman panel kendali untuk aktor Administrator
│   ├── 📄 arsip.php                 # Menampilkan data audit/laporan konser masa lalu yang telah selesai
│   ├── 📄 dashboard.php             # Panel utama analitik penjualan, pendapatan, statistik, dan Chart.js
│   ├── 📄 edit-konser.php           # Formulir untuk mengubah informasi detail konser tertentu
│   ├── 📄 form-konser.php           # Formulir pembuatan agenda/acara konser baru
│   ├── 📄 kelola-eo.php             # Pusat manajemen untuk memantau, menambah, dan menonaktifkan akun EO
│   ├── 📄 kelola-konser.php         # Pusat kendali tabel data konser aktif untuk aksi massal
│   └── 📄 profil.php                # Pengaturan akun dan keamanan administrator
│
├── 📁 assets/                       # Direktori aset statis aplikasi
│   ├── 📁 img/                      # Penyimpanan gambar sistem, avatar, poster, dan bukti transfer
│   │   ├── 📁 bukti_bayar/          # Tempat penyimpanan file gambar bukti transfer dari Customer
│   │   ├── 📄 default-avatar.png
│   │   ├── 📄 default-poster.png
│   │   └── 📄 logo.png
│   ├── 📁 script/
│   │   └── 📄 script.js             # Logika interaktif frontend (modal, preview upload, AJAX checkbox)
│   └── 📁 style/
│       ├── 📄 admin-style.css       # Penataan visual grid layout khusus panel admin & EO
│       └── 📄 style.css             # Penataan visual global, landing page, auth, dan customer dashboard
│
├── 📁 auth/                         # Gerbang autentikasi sistem
│   ├── 📄 forgot-password.php       # Halaman pemulihan akun/lupa kata sandi
│   ├── 📄 login.php                 # Halaman masuk akun
│   ├── 📄 logout.php                # Pembersihan data session pengguna
│   └── 📄 register.php              # Halaman pendaftaran akun baru khusus customer
│
├── 📁 config/                       # Konfigurasi sistem dan basis data
│   ├── 📄 koneksi.php               # Jembatan penghubung ke ekstensi MySQLi server lokal
│   └── 📄 ntbeat.sql                # Skema database relational lengkap
│
├── 📁 eo/                           # Halaman panel kendali khusus Event Organizer (Mitra)
│   ├── 📄 arsip.php                 # Rekam jejak acara milik EO yang telah terlaksana atau diarsip
│   ├── 📄 dashboard.php             # Analitik penjualan tiket khusus untuk acara yang dikelola oleh EO
│   ├── 📄 edit-konser.php           # Formulir pengubahan data konser kelolaan EO
│   ├── 📄 form-konser.php           # Formulir bagi EO untuk mendaftarkan acara musik baru
│   ├── 📄 kelola-konser.php         # Tabel manajemen performa penjualan tiket acara milik EO
│   ├── 📄 profil.php                # Manajemen profil instansi/organisasi dan kata sandi EO
│   └── 📄 verifikasi-pembayaran.php # Halaman validasi bukti transfer masuk dari pemesan tiket
│
├── 📁 user/                         # Halaman-halaman panel kendali untuk aktor Customer
│   ├── 📄 beranda.php               # Menampilkan katalog daftar konser mendatang & fitur live-search
│   ├── 📄 detail-konser.php         # Detail informasi acara, deskripsi, & selector kuantitas tiket
│   ├── 📄 profil.php                # Pengaturan nama, foto profil, dan kata sandi customer
│   └── 📄 tiket-saya.php            # Riwayat pemesanan, tempat unggah bukti bayar, dan akses e-ticket (QRCode)
│
├── 📄 index.php                     # Halaman landing utama (Akses Publik)
├── 📄 setup-visitors.php            # Skrip pembantu inisialisasi / counter data statistik pengunjung
└── 📄 README.md                     # Dokumentasi proyek

```

---

## 🎭 Aktor Sistem (Website Actors)

Sistem NTBeat membagi tingkat otorisasi hak akses ke dalam dua jenis pengguna utama:

1. **👑 Administrator:** Berwenang penuh di balik layar untuk melakukan manipulasi data acara (CRUD), memantau ringkasan kuota tiket, melakukan aksi massal (*bulk action*), serta meninjau laporan pendapatan melalui analitik grafik realtime.
2. **🤝 Event Organizer (EO):** Aktor mitra yang memiliki hak akses terbatas untuk mengelola acaranya sendiri. Dapat membuat agenda konser, mengubah informasi acara, memantau grafik pendapatan berkala, serta melakukan verifikasi langsung terhadap bukti pembayaran tiket dari penonton.
3. **👤 Customer:** Pengguna umum yang dapat menjelajahi daftar konser mendatang, menggunakan fitur pencarian instan artis (*live search*), menilik detail info acara, melakukan simulasi pemesanan, serta melacak e-ticket digital pribadi.

---

## 💻 Tech Stack & Pustaka Eksternal

Aplikasi NTBeat dibangun menggunakan kombinasi teknologi modern berbasis web *native* tanpa framework.

### 🎨 Sisi Klien (Frontend)
* **HTML5**
* **CSS3**
* **JavaScript**

### ⚙️ Sisi Server (Backend)
* **PHP Native (Kompatibilitas v8.2+)**

### 🗄️ Manajemen Data & Server Lokal
* **MySQL / MariaDB**
* **Apache HTTP Server**

### 📚 Pustaka Eksternal
* **Chart.js (v4.x)**
* **QRCode.js**

---

## 👥 Tim & Pembagian Tugas (Team & Roles)

Proyek ini dikembangkan secara kolaboratif oleh tim pengembang berikut:

| Nama | NIM | Role |
| --- | --- | --- |
| **Salsabila Nailafahdi** | F1D02410135 | Project Manager & Full-stack Developer |
| **Tegu Ilham Pebrian Saputra** | F1D02410097 | Full-stack Developer |
| **M. Alfatih** | F1D02410013 | Full-stack Developer |

---

## 🗄️ Database Management System

### 1. Konfigurasi Koneksi (`koneksi.php`)

Sistem terhubung ke server basis data lokal menggunakan ekstensi MySQLi dengan parameter konfigurasi bawaan berikut:

```php
$host     = "localhost";
$user     = "root";
$password = "";
$db       = "ntbeat";

```

### 2. Spesifikasi Struktur Tabel (`ntbeat.sql`)

#### A. Tabel `users`

| Field | Type |
| --- | --- |
| `nama` | VARCHAR(255) |
| `email` | VARCHAR(255) |
| `password` | VARCHAR(255) |
| `role` | ENUM('customer', 'admin', 'eo') |
| `alamat` | TEXT |
| `foto` | VARCHAR(255) |
| `status` | VARCHAR(20) |

#### B. Tabel `pesanan`

| Field | Type |
| --- | --- |
| `id` | INT(11) |
| `order_id` | VARCHAR(50) |
| `user_email` | VARCHAR(255) |
| `konser_id` | INT(11) |
| `jumlah_tiket` | INT(11) |
| `total_harga` | DECIMAL(12,2) |
| `status_bayar` | ENUM('Pending', 'Menunggu Verifikasi', 'Lunas', 'Dibatalkan') |
| `tanggal_pesan` | TIMESTAMP |
| `bank` | VARCHAR(50) |
| `va_number` | VARCHAR(50) |
| `waktu_kadaluarsa` | DATETIME |
| `bukti_bayar` | VARCHAR(255) |
| `is_read_eo` | TINYINT(1) |

#### C. Tabel `konser`

| Nama Kolom | Tipe Data |
| --- | --- |
| `id` | INT(11) |
| `eo_email` | VARCHAR(255) |
| `nama_konser` | VARCHAR(150) |
| `lineup` | TEXT |
| `tanggal` | DATE |
| `waktu` | TIME |
| `lokasi` | VARCHAR(150) |
| `deskripsi` | TEXT |
| `harga` | DECIMAL(10,2) |
| `kapasitas` | INT(11) |
| `tiket_terjual` | INT(11) |
| `poster` | VARCHAR(255) |
| `status` | ENUM('Tersedia', 'Hampir Habis', 'Habis', 'Selesai', 'Arsip') |
| `created_at` | TIMESTAMP |

#### C. Tabel `pengunjung`

| Nama Kolom | Tipe Data |
| --- | --- |
| `id` | INT(11) |
| `tanggal` | DATE |
| `jumlah` | INT(11) |

---

## 🚀 Panduan Menjalankan Aplikasi Secara Lokal

Ikuti langkah-langkah di bawah ini untuk mengonfigurasi dan menguji sistem di lingkungan lokal komputer Anda:

1. **Unduh atau Clone Repositori:**
```bash
git clone [https://github.com/naiaero/ntbeat.git]

```

2. **Pindahkan Direktori Kerja:**
* Salin atau pindahkan folder proyek `NTBeat` Anda ke dalam direktori root server htdocs Anda (biasanya terletak di `C:/xampp/htdocs/`).
* Jalankan panel kontrol *XAMPP Control Panel* lalu nyalakan modul **Apache** beserta **MySQL**.


3. **Import Basis Data:**
* Buka browser dan kunjungi laman pengelolaan database: `http://localhost/phpmyadmin/`.
* Buat basis data baru dengan nama exact: `ntbeat`.
* Pilih menu **Import**, cari file skema SQL `ntbeat.sql` yang berada di root direktori proyek Anda, kemudian klik tombol **Go / Kirim**.


4. **Akses Sistem Melalui Browser:**
* Buka tab browser baru lalu akses URL: `http://localhost/NTBeat/index.php`.