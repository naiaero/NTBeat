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
│   ├── 📄 admin-tambah-proses.php   # Memproses validasi upload poster dan input konser baru
│   ├── 📄 login_proses.php          # Memproses verifikasi kredensial login & pembagian hak akses (role)
│   ├── 📄 pesan-tiket-proses.php    # Memproses transaksi pemesanan tiket dengan database transaction
│   ├── 📄 register_proses.php       # Memproses pendaftaran akun pengguna baru (customer)
│   ├── 📄 update-konser-proses.php  # Memproses pembaruan data konser dan manipulasi file poster lama
│   └── 📄 update-profile-proses.php # Memproses pembaruan profil pengguna (nama, foto, & password)
│
├── 📁 admin/                        # Halaman-halaman panel kendali untuk aktor Administrator
│   ├── 📄 arsip.php                 # Menampilkan data audit/laporan konser masa lalu yang telah selesai
│   ├── 📄 dashboard.php             # Panel utama analitik penjualan, pendapatan, statistik, dan Chart.js
│   ├── 📄 edit-konser.php           # Formulir untuk mengubah informasi detail konser tertentu
│   ├── 📄 form-konser.php           # Formulir pembuatan agenda/acara konser baru
│   ├── 📄 kelola-konser.php         # Pusat kendali tabel data konser aktif untuk aksi massal
│   └── 📄 profil.php                # Pengaturan akun dan keamanan administrator
│
├── 📁 assets/                      # Direktori aset statis aplikasi
│   ├── 📁 img/                     # Penyimpanan gambar sistem, avatar pengguna, dan poster konser
│   │   ├── 📄 default-avatar.png
│   │   ├── 📄 default-poster.png
│   │   └── 📄 logo.png
│   ├── 📁 script/
│   │   └── 📄 script.js             # Logika interaktif frontend (modal, preview upload, AJAX checkbox)
│   └── 📁 style/
│       ├── 📄 admin-style.css       # Penataan visual grid layout khusus panel admin
│       └── 📄 style.css             # Penataan visual global, landing page, auth, dan customer dashboard
│
├── 📁 auth/                         # Gerbang autentikasi sistem
│   ├── 📄 login.php                 # Halaman masuk akun
│   ├── 📄 logout.php                # Pembersihan data session pengguna
│   └── 📄 register.php              # Halaman pendaftaran akun baru
│
├── 📁 config/                       # Konfigurasi sistem dan basis data
│   ├── 📄 koneksi.php               # Jembatan penghubung ke ekstensi MySQLi server lokal
│   ├── 📄 ntbeat.sql                # Skema database relational (tabel: users, konser, pesanan)
│   └── 📄 setup-db.php              # Skrip penginisialisasi otomatis pangkalan data dan data sampel
│
├── 📁 user/                         # Halaman-halaman panel kendali untuk aktor Customer
│   ├── 📄 beranda.php               # Menampilkan katalog daftar konser mendatang & fitur live-search
│   ├── 📄 detail-konser.php         # Halaman detail informasi acara, deskripsi, & selector kuantitas tiket
│   ├── 📄 profil.php                # Pengaturan nama, foto profil, dan kata sandi customer
│   └── 📄 tiket-saya.php            # Menampilkan e-ticket aktif (dengan QRCode) dan riwayat masa lalu
│
├── 📄 index.php                    # Halaman landing utama (Akses Publik)
└── 📄 README.md                    # Dokumentasi proyek

```

---

## 🎭 Aktor Sistem (Website Actors)

Sistem NTBeat membagi tingkat otorisasi hak akses ke dalam dua jenis pengguna utama:

1. **👑 Administrator:** Berwenang penuh di balik layar untuk melakukan manipulasi data acara (CRUD), memantau ringkasan kuota tiket, melakukan aksi massal (*bulk action*), serta meninjau laporan pendapatan melalui analitik grafik realtime.
2. **👤 Customer:** Pengguna umum yang dapat menjelajahi daftar konser mendatang, menggunakan fitur pencarian instan artis (*live search*), menilik detail info acara, melakukan simulasi pemesanan, serta melacak e-ticket digital pribadi.

---

## 💻 Tech Stack & Pustaka Eksternal

Aplikasi NTBeat dibangun menggunakan kombinasi teknologi modern berbasis web *native* tanpa framework berat, guna menjamin performa eksekusi kode yang cepat dan ringan pada sisi server maupun peramban:

### 🎨 Sisi Klien (Frontend)
* **HTML5:** Kerangka dasar pengorganisasian elemen struktur halaman, formulir autentikasi, dan komponen tabel transaksional.
* **CSS3 (Custom Grid Layout):** Penataan estetika visual global menggunakan metode *gradient flow* maroon-gelap (`radial-gradient`), interaksi responsif media queries, serta arsitektur tata letak khusus panel admin menggunakan fitur **CSS Grid**.
* **JavaScript (Vanilla JS):** Logika interaktif pada antarmuka pengguna tanpa dependensi jQuery. Menangani operasi manipulasi DOM, fitur *live search filter* nama konser, fungsionalitas tombol aksi massal (*bulk checkbox UI*), peninjau instan unggahan poster (*FileReader Image Preview*), serta manajemen kontrol visual elemen modal jendela.

### ⚙️ Sisi Server & Logika Bisnis (Backend)
* **PHP Native (Kompatibilitas v8.2+):** Mesin pemroses logika sisi server utama. Mengimplementasikan beberapa teknik fungsionalitas tingkat lanjut seperti:
    * **Ekstensi MySQLi:** Jembatan kueri relasional berorientasi objek/prosedural aman.
    * **State Management Sessions:** Pemanfaatan `session_start()` untuk menjaga data persisten kredensial login dan pembatasan hak otorisasi multi-role (*Admin & Customer*).
    * **Database Transaction (ACID System):** Menggunakan `mysqli_begin_transaction()`, `mysqli_commit()`, dan `mysqli_rollback()` pada skrip pemesanan tiket guna mencegah anomali data (kondisi balapan kuota/ *race condition*).
    * **Keamanan Kriptografi:** Validasi akun menggunakan fungsi `password_hash()` (algoritma `PASSWORD_DEFAULT`) dan `password_verify()` untuk memitigasi kebocoran kredensial di pangkalan data.
    * **Manipulasi Fail Sistem Lokal:** Operasi `move_uploaded_file()` untuk manajemen berkas gambar dinamis (poster konser dan avatar pengguna) serta `unlink()` untuk membersihkan berkas sampah yang sudah dihapus/diganti dari disk server.

### 🗄️ Manajemen Data & Server Lokal
* **MySQL / MariaDB:** Sistem Manajemen Basis Data Relasional (*RDBMS*) untuk menyimpan entitas tabel relasi pengguna, konser musik, dan log pesanan.
* **Apache HTTP Server:** Server web lokal yang dijalankan melalui paket *XAMPP Control Panel*.

### 📚 Pustaka Eksternal Terintegrasi (via CDN)
* **Chart.js (v4.x):** Library visualisasi grafik JavaScript untuk merender bagan tren penjualan tiket per hari secara dinamis dan interaktif menggunakan elemen `<canvas>` pada dasbor administrator.
* **QRCode.js:** Library utilitas penampil enkripsi teks untuk mengubah kode invoice `order_id` transaksi pesanan menjadi bentuk visual kode QR (QR Code) siap-pindai pada tiket aktif pengguna.

---

## 👥 Tim & Pembagian Tugas (Team & Roles)

Proyek ini dikembangkan secara kolaboratif oleh tim pengembang berikut:

| Nama | NIM | Role |
| --- | --- | --- |
| **Salsabila Nailafahdi** | F1D02410135 | Project Manager & Full-stack Developer |
| **Tegu Ilham Pebrian Saputra** | F1D02410097 | Front-end Developer |
| **M. Alfatih** | F1D02410013 | Front-end Developer |

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
| `role` | ENUM('admin','user') |
| `foto` | VARCHAR(255) |

#### B. Tabel `pesanan`

| Field | Type |
| --- | --- |
| `id` | INT(11) |
| `order_id` | VARCHAR(50) |
| `user_email` | VARCHAR(255) |
| `konser_id` | INT(11) |
| `jumlah_tiket` | INT(11) |
| `total_harga` | DECIMAL(12,2) |
| `status_bayar` | ENUM('Pending', 'Lunas', 'Dibatalkan') |
| `tanggal_pesan` | TIMESTAMP |

#### C. Tabel `konser`

| Nama Kolom | Tipe Data |
| --- | --- |
| `id` | INT(11) |
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
* Buat basis data baru dengan nama exact: `ntbeat`.
* Pilih menu **Import**, cari file skema SQL `ntbeat.sql` yang berada di root direktori proyek Anda, kemudian klik tombol **Go / Kirim**.


4. **Akses Sistem Melalui Browser:**
* Buka tab browser baru lalu akses URL: `http://localhost/NTBeat/index.php`.