function openLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

// Menutup modal jika user klik di area luar kotak
window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function konfirmasiPembelian() {
            // Memunculkan pesan pop-up (alert)
            alert("✅ Pembelian Berhasil!\n\nTiket 'Symphony of Lombok' telah ditambahkan ke akun Anda.");
            
            // Mengarahkan pengguna langsung ke halaman Tiket Saya
            window.location.href = "tiket-saya.html";
}

// admin arsip
document.addEventListener('DOMContentLoaded', () => {
    // 1. Seleksi Elemen
    const checkAll = document.getElementById('check-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const countDisplay = document.getElementById('count-display');
    const btnReportBulk = document.getElementById('btn-report-bulk');
    const logoutModal = document.getElementById('logoutModal');

    // --- FUNGSI CHECKBOX ---

    // Fungsi untuk memperbarui tampilan UI (jumlah terpilih & status tombol)
    function updateSelectionUI() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        
        // Update teks jumlah
        countDisplay.textContent = checkedCount;

        // Aktifkan/Nonaktifkan tombol laporan
        if (checkedCount > 0) {
            btnReportBulk.disabled = false;
        } else {
            btnReportBulk.disabled = true;
        }
    }

    // Event Listener untuk "Select All"
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionUI();
        });
    }

    // Event Listener untuk masing-masing checkbox baris
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            // Jika ada satu yang tidak dicentang, matikan centang "Select All"
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            checkAll.checked = allChecked;
            
            updateSelectionUI();
        });
    });
});

// admin dashboard
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Elemen
    const logoutModal = document.getElementById('logoutModal');
    const liveBar = document.getElementById('live-bar');
    const soldCountDisplay = document.getElementById('sold-count');
    const remainingCountDisplay = document.getElementById('remaining-count');
    const revenueCountDisplay = document.getElementById('revenue-count');

    // Data Awal (Simulasi)
    let totalSold = 1420;
    const totalCapacity = 2000;
    const ticketPrice = 150000; // Contoh harga tiket per lembar

    // --- SIMULASI REAL-TIME DASHBOARD ---

    // Fungsi untuk memperbarui angka statistik secara dinamis
    function updateStats() {
        // Simulasi penambahan tiket terjual (random 0-2 tiket)
        const newSales = Math.floor(Math.random() * 3);
        totalSold += newSales;

        const remaining = totalCapacity - totalSold;
        const revenue = totalSold * ticketPrice;

        // Update Tampilan Angka
        soldCountDisplay.textContent = totalSold.toLocaleString('id-ID');
        remainingCountDisplay.textContent = remaining.toLocaleString('id-ID');
        
        // Update Tampilan Pendapatan (dalam format Juta)
        const revenueInMillion = (revenue / 1000000).toFixed(1);
        revenueCountDisplay.textContent = `Rp ${revenueInMillion}jt`;

        // Simulasi pergerakan grafik "LIVE"
        const randomHeight = Math.floor(Math.random() * 60) + 20; // 20% - 80%
        if (liveBar) {
            liveBar.style.height = randomHeight + '%';
        }
    }

    // Jalankan simulasi update setiap 3 detik
    setInterval(updateStats, 3000);

    // --- SIDEBAR ACTIVE STATE (Opsional) ---
    // Menandai menu aktif berdasarkan URL saat ini
    const currentLocation = window.location.pathname.split("/").pop();
    const menuItems = document.querySelectorAll('.sidebar-menu li');

    menuItems.forEach(item => {
        const onclickAttr = item.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes(currentLocation)) {
            // Hapus semua class active
            menuItems.forEach(i => i.classList.remove('active'));
            // Tambah ke yang sesuai
            item.classList.add('active');
        }
    });
});

// admin-form-konser
document.addEventListener('DOMContentLoaded', () => {
    // === 1. PREVIEW POSTER KONSER ===
    const posterInput = document.getElementById('poster-input');
    const imagePreview = document.getElementById('imagePreview');

    if (posterInput) {
        posterInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                // Menampilkan teks loading atau membersihkan isi sebelumnya
                imagePreview.innerHTML = '<span style="color: #eee; font-size: 0.8rem;">Memuat...</span>';

                reader.onload = function(e) {
                    // Mengubah background div menjadi gambar yang dipilih
                    imagePreview.style.backgroundImage = `url(${e.target.result})`;
                    imagePreview.style.backgroundSize = 'cover';
                    imagePreview.style.backgroundPosition = 'center';
                    imagePreview.style.border = 'none';
                    imagePreview.innerHTML = ''; // Menghilangkan teks "Preview Poster"
                }

                reader.readAsDataURL(file);
            } else {
                // Jika batal memilih gambar
                imagePreview.style.backgroundImage = 'none';
                imagePreview.innerHTML = '<span>Preview Poster</span>';
            }
        });
    }

    // === 2. HANDLING FORM SUBMIT ===
    const concertForm = document.querySelector('.ps-form');
    if (concertForm) {
        concertForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Mencegah reload halaman
            
            // Simulasi proses simpan
            const btnSave = document.querySelector('.btn-ps-save');
            btnSave.textContent = 'Menyimpan...';
            btnSave.disabled = true;

            setTimeout(() => {
                alert('Data Konser Berhasil Disimpan!');
                window.location.href = 'admin-kelola-konser.html';
            }, 1500);
        });
    }
});

// admin-kelola-konser
document.addEventListener('DOMContentLoaded', () => {
    // === 1. SELEKSI ELEMEN ===
    const checkAll = document.getElementById('check-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const countDisplay = document.getElementById('count-display');
    
    // Tombol-tombol aksi
    const btnEdit = document.getElementById('btn-edit-bulk');
    const btnArchive = document.getElementById('btn-archive-bulk');
    const btnDelete = document.getElementById('btn-delete-bulk');

    // === 2. FUNGSI UPDATE UI (CENTANG & TOMBOL) ===
    function updateTableUI() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const selectedCount = checkedBoxes.length;

        // Update angka tampilan
        countDisplay.textContent = selectedCount;

        // Logika Aktifasi Tombol
        if (selectedCount > 0) {
            // Jika ada yang dipilih, tombol Arsip dan Hapus aktif
            btnArchive.disabled = false;
            btnDelete.disabled = false;
            
            // Tombol Edit biasanya hanya aktif jika hanya 1 data yang dipilih
            btnEdit.disabled = (selectedCount !== 1);
        } else {
            // Jika tidak ada yang dipilih, semua tombol mati
            btnEdit.disabled = true;
            btnArchive.disabled = true;
            btnDelete.disabled = true;
        }

        // Sinkronisasi status "Check All"
        checkAll.checked = (selectedCount === rowCheckboxes.length);
        // Tambahkan efek visual indeterminate jika hanya sebagian yang dipilih
        checkAll.indeterminate = (selectedCount > 0 && selectedCount < rowCheckboxes.length);
    }

    // === 3. EVENT LISTENERS ===

    // Event untuk Checkbox Utama (Header)
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateTableUI();
        });
    }

    // Event untuk Checkbox Baris
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateTableUI);
    });

    // Simulasi Aksi Tombol
    btnDelete.addEventListener('click', () => {
        const count = document.querySelectorAll('.row-checkbox:checked').length;
        if (confirm(`Apakah Anda yakin ingin menghapus ${count} data konser yang terpilih?`)) {
            alert('Data berhasil dihapus (Simulasi)');
            location.reload(); 
        }
    });
});

// detail konser
document.addEventListener('DOMContentLoaded', () => {
    // 1. Logika Konfirmasi Pembelian
    // Fungsi ini dipanggil dari atribut onclick="konfirmasiPembelian()" di HTML
    window.konfirmasiPembelian = function() {
        const namaKonser = document.querySelector('.detail-title').innerText;
        const harga = document.querySelector('.price-total').innerText;

        const yakin = confirm(`Konfirmasi Pesanan:\n\nAcara: ${namaKonser}\nTotal: ${harga}\n\nApakah Anda ingin melanjutkan ke pembayaran?`);
        
        if (yakin) {
            // Simulasi proses loading
            const btnPesan = document.querySelector('.btn-pesan-sekarang');
            btnPesan.innerText = "Memproses...";
            btnPesan.disabled = true;

            setTimeout(() => {
                alert("Sukses! Tiket Anda telah dipesan. Silakan cek email atau menu riwayat pesanan.");
                window.location.href = 'halaman-user.html'; // Kembali ke dashboard user
            }, 2000);
        }
    };

    // 2. Simulasi Sisa Tiket Dinamis (FOMO Effect)
    // Menciptakan kesan bahwa tiket terus terjual
    const remainingStat = document.querySelector('.remaining .stat-value');
    if (remainingStat) {
        let currentTickets = 45; // Sesuai data awal di HTML

        const ticketCountdown = setInterval(() => {
            // Random pengurangan 0 atau 1 tiket
            const sold = Math.floor(Math.random() * 2);
            
            if (sold > 0 && currentTickets > 5) {
                currentTickets -= sold;
                remainingStat.innerText = `Hanya ${currentTickets} Tiket!`;
                
                // Tambahkan efek kilau/highlight saat berubah
                remainingStat.style.transition = "color 0.3s";
                remainingStat.style.color = "#ff4d4d";
                setTimeout(() => {
                    remainingStat.style.color = ""; // Kembalikan ke warna asli
                }, 500);
            }

            // Berhenti jika tiket sudah sangat sedikit
            if (currentTickets <= 5) clearInterval(ticketCountdown);
            
        }, 8000); // Update setiap 8 detik
    }

    // 3. Smooth Scroll untuk Link Kembali (Opsional)
    const backLink = document.querySelector('.back-link');
    if (backLink) {
        backLink.addEventListener('mouseover', () => {
            backLink.style.transform = 'translateX(-5px)';
            backLink.style.transition = 'transform 0.2s';
        });
        backLink.addEventListener('mouseout', () => {
            backLink.style.transform = 'translateX(0)';
        });
    }
});

//halaman awal
document.addEventListener('DOMContentLoaded', () => {
    // 1. SELEKSI ELEMEN
    const searchInput = document.querySelector('.search-bar');
    const concertCards = document.querySelectorAll('.concert-card');
    const logoutModal = document.getElementById('logoutModal');
    const btnBeli = document.querySelectorAll('.btn-beli');

    // 2. FITUR PENCARIAN (FILTER KARTU KONSER)
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const keyword = e.target.value.toLowerCase();

            concertCards.forEach(card => {
                const title = card.querySelector('.concert-title').innerText.toLowerCase();
                
                // Jika judul cocok dengan kata kunci, tampilkan. Jika tidak, sembunyikan.
                if (title.includes(keyword)) {
                    card.style.display = 'flex'; // Gunakan flex agar layout tetap rapi
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // 4. LOGIKA TOMBOL PESAN (QUICK BUY)
    btnBeli.forEach(btn => {
        btn.addEventListener('click', () => {
            const concertName = btn.closest('.concert-card').querySelector('.concert-title').innerText;
            const confirmBuy = confirm(`Apakah Anda ingin langsung memesan tiket ${concertName}?`);
            
            if (confirmBuy) {
                // Arahkan ke halaman detail atau langsung ke proses checkout
                window.location.href = 'detail-konser.html';
            }
        });
    });
});

// index
document.addEventListener('DOMContentLoaded', () => {
    // 1. STICKY HEADER EFFECT
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }
    });

    // 2. SCROLL REVEAL ANIMATION (Muncul saat di-scroll)
    const observerOptions = {
        threshold: 0.1 // Elemen muncul jika 10% sudah masuk layar
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
            }
        });
    }, observerOptions);

    // Menargetkan kartu fitur untuk diberikan animasi
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.classList.add('reveal-hidden'); // Tambahkan state awal (tersembunyi)
        observer.observe(card);
    });

    // 3. INTERAKSI TOMBOL (Efek Klik Sederhana)
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mousedown', () => {
            btn.style.transform = 'scale(0.95)';
        });
        btn.addEventListener('mouseup', () => {
            btn.style.transform = 'scale(1)';
        });
    });
});

//lihat-profile
document.addEventListener('DOMContentLoaded', () => {
    // 1. LOGIKA TOMBOL BACK
    const btnBack = document.querySelector('.btn-back');
    if (btnBack) {
        btnBack.addEventListener('click', () => {
            // Kembali ke halaman sebelumnya dalam history browser
            window.history.back();
        });
    }

    // 2. LOGIKA TOGGLE PASSWORD
    // Data dummy password (karena biasanya ini ditarik dari database/session)
    const realPassword = "PasswordSangatRahasia123";
    let isPasswordVisible = false;

    // Kita buat fungsi ini tersedia secara global agar bisa diakses 'onclick' dari HTML
    window.togglePassword = function() {
        // Cari elemen span yang ada setelah label 'Password'
        // Kita asumsikan strukturnya: <span class="label">Password</span> <span>: ...</span>
        const passwordSpan = document.querySelector('.info-row:nth-child(2) span:nth-child(2)');
        const toggleBtn = document.querySelector('.toggle-btn');

        if (!isPasswordVisible) {
            // Tampilkan password asli
            passwordSpan.innerText = `: ${realPassword}`;
            toggleBtn.innerText = "Sembunyikan";
            isPasswordVisible = true;
        } else {
            // Tutup kembali dengan asterisk
            passwordSpan.innerText = ": **********";
            toggleBtn.innerText = "Lihat";
            isPasswordVisible = false;
        }
    };
});

//login
document.addEventListener('DOMContentLoaded', () => {
    // 1. Seleksi Elemen Form
    const loginForm = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.querySelector('.auth-btn-submit');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            // Mencegah reload halaman secara default
            e.preventDefault();

            const email = emailInput.value;
            const password = passwordInput.value;

            // Simulasi Efek Loading pada Tombol
            submitBtn.innerText = "Mengecek Akun...";
            submitBtn.disabled = true;

            setTimeout(() => {
                // 2. LOGIKA OTENTIKASI (Simulasi)
                if (email === "admin@ntbeat.com" && password === "admin123") {
                    // Jika Login sebagai Admin
                    alert("Selamat datang, Administrator!");
                    window.location.href = 'admin-dashboard.html';
                } 
                else if (email !== "" && password.length >= 8) {
                    // Jika Login sebagai User Biasa (email apa saja, pass min 8 karakter)
                    alert("Login Berhasil! Selamat datang di NTBeat.");
                    window.location.href = 'halaman-user.html'; 
                } 
                else {
                    // Jika Login Gagal
                    alert("Email atau Kata Sandi salah. Silakan coba lagi!");
                    submitBtn.innerText = "Masuk";
                    submitBtn.disabled = false;
                }
            }, 1500); // Penundaan 1,5 detik untuk kesan realistik
        });
    }

    // 3. LOGIKA LUPA KATA SANDI
    const forgotPwLink = document.querySelector('.auth-forgot-pw');
    if (forgotPwLink) {
        forgotPwLink.addEventListener('click', (e) => {
            e.preventDefault();
            alert("Fitur pemulihan kata sandi akan dikirimkan ke email Anda.");
        });
    }
});

//profil
document.addEventListener('DOMContentLoaded', () => {
    // === 2. UPDATE FOTO PROFIL (AVATAR) ===
    const editIcon = document.querySelector('.ps-edit-icon');
    const avatarImg = document.querySelector('.ps-avatar-wrapper img');
    
    // Buat elemen input file secara tersembunyi
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';

    if (editIcon) {
        editIcon.addEventListener('click', () => fileInput.click());
    }

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarImg.src = e.target.result; // Ganti gambar profil dengan preview
            }
            reader.readAsDataURL(file);
        }
    });

    // === 3. PENANGANAN FORM PROFIL (SAVE) ===
    const profileForm = document.querySelector('.ps-form');
    
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Mencegah reload halaman

            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const currentPass = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;

            // Validasi Sederhana
            if (username === "" || email === "") {
                alert("Username dan Email tidak boleh kosong!");
                return;
            }

            // Jika mencoba ganti password
            if (newPass !== "" && currentPass === "") {
                alert("Silakan masukkan password saat ini untuk mengonfirmasi perubahan password baru.");
                return;
            }

            // Simulasi proses simpan
            const saveBtn = document.querySelector('.btn-ps-save');
            saveBtn.innerText = "Saving...";
            saveBtn.disabled = true;

            setTimeout(() => {
                alert("Profil berhasil diperbarui!");
                saveBtn.innerText = "Save";
                saveBtn.disabled = false;
                // Kosongkan field password setelah simpan
                document.getElementById('current_password').value = "";
                document.getElementById('new_password').value = "";
            }, 1500);
        });
    }
});

//register
document.addEventListener('DOMContentLoaded', () => {
    // 1. Seleksi Elemen
    const registerForm = document.querySelector('form');
    const nameInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.querySelector('.auth-btn-submit');

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            // Mencegah form melakukan reload otomatis
            e.preventDefault();

            // 2. Logika Validasi Sederhana
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;

            if (password.length < 8) {
                alert("Keamanan itu penting! Kata sandi minimal harus 8 karakter.");
                return;
            }

            // 3. Efek Visual Saat Proses Pendaftaran
            submitBtn.innerText = "Membuat Akun...";
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.7";

            // 4. Simulasi Pengiriman Data (Delay 2 detik)
            setTimeout(() => {
                alert(`Selamat datang di NTBeat, ${name}! Akun Anda berhasil dibuat.`);
                
                // Arahkan ke halaman login setelah berhasil
                window.location.href = 'login.html';
            }, 2000);
        });
    }

    // Tambahan: Logika untuk membersihkan spasi di input nama/email
    [nameInput, emailInput].forEach(input => {
        if(input) {
            input.addEventListener('blur', () => {
                input.value = input.value.trim();
            });
        }
    });
});

// tiket-saya
document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Elemen
    const logoutModal = document.getElementById('logoutModal');
    const btnUnduh = document.querySelector('.btn-unduh');

    // --- FITUR UNDUH PDF (SIMULASI) ---
    
    if (btnUnduh) {
        btnUnduh.addEventListener('click', () => {
            const orderId = document.querySelector('.order-id').innerText;
            const concertTitle = document.querySelector('.ticket-main-info h3').innerText;

            // Efek visual tombol saat diklik
            btnUnduh.innerText = "Mengunduh...";
            btnUnduh.disabled = true;

            setTimeout(() => {
                alert(`E-Ticket ${concertTitle} (${orderId}) berhasil diunduh dalam format PDF.`);
                btnUnduh.innerText = "Unduh PDF";
                btnUnduh.disabled = false;
            }, 1500);
        });
    }

    // --- LOGIKA QR CODE (OPSIONAL/VISUAL) ---
    // Memberikan efek sedikit interaktif pada QR placeholder
    const qrPlaceholder = document.querySelector('.qr-placeholder');
    if (qrPlaceholder) {
        qrPlaceholder.addEventListener('mouseenter', () => {
            qrPlaceholder.style.borderColor = '#d4af37';
            qrPlaceholder.style.cursor = 'pointer';
        });
        qrPlaceholder.addEventListener('mouseleave', () => {
            qrPlaceholder.style.borderColor = '#eee';
        });
    }
});