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