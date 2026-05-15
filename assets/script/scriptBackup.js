function openLogoutModal() {
  document.getElementById("logoutModal").style.display = "flex";
}

function closeLogoutModal() {
  document.getElementById("logoutModal").style.display = "none";
}

window.onclick = function (event) {
  const modal = document.getElementById("logoutModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

function konfirmasiPembelian() {
  alert(
    "✅ Pembelian Berhasil!\n\nTiket 'Symphony of Lombok' telah ditambahkan ke akun Anda.",
  );

  window.location.href = "tiket-saya.php";
}

document.addEventListener("DOMContentLoaded", () => {
  const checkAll = document.getElementById("check-all");
  const rowCheckboxes = document.querySelectorAll(".row-checkbox");
  const countDisplay = document.getElementById("count-display");
  const btnReportBulk = document.getElementById("btn-report-bulk");
  const logoutModal = document.getElementById("logoutModal");

  function updateSelectionUI() {
    const checkedCount = document.querySelectorAll(
      ".row-checkbox:checked",
    ).length;

    countDisplay.textContent = checkedCount;

    if (checkedCount > 0) {
      btnReportBulk.disabled = false;
    } else {
      btnReportBulk.disabled = true;
    }
  }

  if (checkAll) {
    checkAll.addEventListener("change", function () {
      rowCheckboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
      });
      updateSelectionUI();
    });
  }

  rowCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const allChecked = Array.from(rowCheckboxes).every((cb) => cb.checked);
      checkAll.checked = allChecked;

      updateSelectionUI();
    });
  });
});

// document.addEventListener("DOMContentLoaded", () => {
document.addEventListener("DOMContentLoaded", () => {
  const liveBar = document.getElementById("live-bar");
  const soldCountDisplay = document.getElementById("sold-count");
  const remainingCountDisplay = document.getElementById("remaining-count");
  const revenueCountDisplay = document.getElementById("revenue-count");

  let totalSold = 1420;
  const totalCapacity = 2000;
  const ticketPrice = 150000;

  // Cek dulu apakah halaman ini punya statistik jualan tiket?
  if (soldCountDisplay && remainingCountDisplay && revenueCountDisplay) {
    function updateStats() {
      const newSales = Math.floor(Math.random() * 3);
      totalSold += newSales;

      const remaining = totalCapacity - totalSold;
      const revenue = totalSold * ticketPrice;

      soldCountDisplay.textContent = totalSold.toLocaleString("id-ID");
      remainingCountDisplay.textContent = remaining.toLocaleString("id-ID");

      const revenueInMillion = (revenue / 1000000).toFixed(1);
      revenueCountDisplay.textContent = `Rp ${revenueInMillion}jt`;

      const randomHeight = Math.floor(Math.random() * 60) + 20;
      if (liveBar) {
        liveBar.style.height = randomHeight + "%";
      }
    }

    // Jalankan timer animasi HANYA jika kita di halaman statistik
    setInterval(updateStats, 3000);
  }

  // Fitur highlight menu sidebar aktif
  const currentLocation = window.location.pathname.split("/").pop();
  const menuItems = document.querySelectorAll(".sidebar-menu li");

  if (menuItems.length > 0) {
    menuItems.forEach((item) => {
      const onclickAttr = item.getAttribute("onclick");
      if (onclickAttr && onclickAttr.includes(currentLocation)) {
        menuItems.forEach((i) => i.classList.remove("active"));
        item.classList.add("active");
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const posterInput = document.getElementById("poster-input");
  const imagePreview = document.getElementById("imagePreview");

  if (posterInput) {
    posterInput.addEventListener("change", function () {
      const file = this.files[0];

      if (file) {
        const reader = new FileReader();

        imagePreview.innerHTML =
          '<span style="color: #eee; font-size: 0.8rem;">Memuat...</span>';

        reader.onload = function (e) {
          imagePreview.style.backgroundImage = `url(${e.target.result})`;
          imagePreview.style.backgroundSize = "cover";
          imagePreview.style.backgroundPosition = "center";
          imagePreview.style.border = "none";
          imagePreview.innerHTML = "";
        };

        reader.readAsDataURL(file);
      } else {
        imagePreview.style.backgroundImage = "none";
        imagePreview.innerHTML = "<span>Preview Poster</span>";
      }
    });
  }

  const concertForm = document.querySelector(".ps-form");
  if (concertForm) {
    concertForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const btnSave = document.querySelector(".btn-ps-save");
      btnSave.textContent = "Menyimpan...";
      btnSave.disabled = true;

      setTimeout(() => {
        alert("Data Konser Berhasil Disimpan!");
        window.location.href = "admin-kelola-konser.php";
      }, 1500);
    });
  }
});

// document.addEventListener("DOMContentLoaded", () => {
document.addEventListener("DOMContentLoaded", () => {
  const checkAll = document.getElementById("check-all");
  const rowCheckboxes = document.querySelectorAll(".row-checkbox");
  const countDisplay = document.getElementById("count-display");

  const btnEdit = document.getElementById("btn-edit-bulk");
  const btnArchive = document.getElementById("btn-archive-bulk");
  const btnDelete = document.getElementById("btn-delete-bulk");

  // CEK DULU: Apakah tombol-tombol admin ini ada di halaman?
  if (btnDelete && btnEdit && btnArchive) {
    function updateTableUI() {
      const checkedBoxes = document.querySelectorAll(".row-checkbox:checked");
      const selectedCount = checkedBoxes.length;

      countDisplay.textContent = selectedCount;

      if (selectedCount > 0) {
        btnArchive.disabled = false;
        btnDelete.disabled = false;
        btnEdit.disabled = selectedCount !== 1;
      } else {
        btnEdit.disabled = true;
        btnArchive.disabled = true;
        btnDelete.disabled = true;
      }

      checkAll.checked = selectedCount === rowCheckboxes.length;
      checkAll.indeterminate =
        selectedCount > 0 && selectedCount < rowCheckboxes.length;
    }

    if (checkAll) {
      checkAll.addEventListener("change", function () {
        rowCheckboxes.forEach((cb) => {
          cb.checked = this.checked;
        });
        updateTableUI();
      });
    }

    rowCheckboxes.forEach((cb) => {
      cb.addEventListener("change", updateTableUI);
    });

    btnDelete.addEventListener("click", () => {
      const count = document.querySelectorAll(".row-checkbox:checked").length;
      if (
        confirm(
          `Apakah Anda yakin ingin menghapus ${count} data konser yang terpilih?`,
        )
      ) {
        alert("Data berhasil dihapus (Simulasi)");
        location.reload();
      }
    });

    btnEdit.addEventListener("click", () => {
      const checkedBox = document.querySelector(".row-checkbox:checked");

      if (checkedBox) {
        const konserId = checkedBox.getAttribute("data-id");
        // PERBAIKAN TYPO: Menggunakan backtick (`) agar variabel terbaca
        window.location.href = `admin-form-konser.php?id=${konserId}`;
      }
    });

    btnArchive.addEventListener("click", () => {
      const checkedBoxes = document.querySelectorAll(".row-checkbox:checked");

      if (confirm(`Pindahkan ${checkedBoxes.length} konser ke arsip?`)) {
        let currentArchive =
          JSON.parse(localStorage.getItem("ntbeat_archive")) || [];

        checkedBoxes.forEach((cb) => {
          const row = cb.closest("tr");

          const concertData = {
            id: cb.value,
            nama: row.cells[2].querySelector("strong").innerText,
            tanggal: row.cells[3].childNodes[0].textContent.trim(),
            penjualan: "N/A",
            status: "Selesai",
          };

          currentArchive.push(concertData);
          row.remove();
        });

        localStorage.setItem("ntbeat_archive", JSON.stringify(currentArchive));
        alert("Konser berhasil diarsipkan!");
        updateTableUI();
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  window.konfirmasiPembelian = function () {
    const namaKonser = document.querySelector(".detail-title").innerText;
    const harga = document.querySelector(".price-total").innerText;

    const yakin = confirm(
      `Konfirmasi Pesanan:\n\nAcara: ${namaKonser}\nTotal: ${harga}\n\nApakah Anda ingin melanjutkan ke pembayaran?`,
    );

    if (yakin) {
      const btnPesan = document.querySelector(".btn-pesan-sekarang");
      btnPesan.innerText = "Memproses...";
      btnPesan.disabled = true;

      setTimeout(() => {
        alert(
          "Sukses! Tiket Anda telah dipesan. Silakan cek email atau menu riwayat pesanan.",
        );
        window.location.href = "halaman-user.php"; // Kembali ke dashboard user
      }, 2000);
    }
  };

  const remainingStat = document.querySelector(".remaining .stat-value");
  if (remainingStat) {
    let currentTickets = 45; // Sesuai data awal di HTML

    const ticketCountdown = setInterval(() => {
      const sold = Math.floor(Math.random() * 2);

      if (sold > 0 && currentTickets > 5) {
        currentTickets -= sold;
        remainingStat.innerText = `Hanya ${currentTickets} Tiket!`;

        remainingStat.style.transition = "color 0.3s";
        remainingStat.style.color = "#ff4d4d";
        setTimeout(() => {
          remainingStat.style.color = "";
        }, 500);
      }

      if (currentTickets <= 5) clearInterval(ticketCountdown);
    }, 8000);
  }

  const backLink = document.querySelector(".back-link");
  if (backLink) {
    backLink.addEventListener("mouseover", () => {
      backLink.style.transform = "translateX(-5px)";
      backLink.style.transition = "transform 0.2s";
    });
    backLink.addEventListener("mouseout", () => {
      backLink.style.transform = "translateX(0)";
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-bar");
  const concertCards = document.querySelectorAll(".concert-card");
  const logoutModal = document.getElementById("logoutModal");
  const btnBeli = document.querySelectorAll(".btn-beli");

  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const keyword = e.target.value.toLowerCase();

      concertCards.forEach((card) => {
        const title = card
          .querySelector(".concert-title")
          .innerText.toLowerCase();

        if (title.includes(keyword)) {
          card.style.display = "flex";
          card.style.animation = "fadeIn 0.3s ease";
        } else {
          card.style.display = "none";
        }
      });
    });
  }

  btnBeli.forEach((btn) => {
    btn.addEventListener("click", () => {
      const concertName = btn
        .closest(".concert-card")
        .querySelector(".concert-title").innerText;
      const confirmBuy = confirm(
        `Apakah Anda ingin langsung memesan tiket ${concertName}?`,
      );

      if (confirmBuy) {
        window.location.href = "detail-konser.php";
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector(".header");

  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        header.classList.add("header-scrolled");
      } else {
        header.classList.remove("header-scrolled");
      }
    });
  }

  const observerOptions = {
    threshold: 0.1,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("reveal-visible");
      }
    });
  }, observerOptions);

  const featureCards = document.querySelectorAll(".feature-card");
  featureCards.forEach((card) => {
    card.classList.add("reveal-hidden");
    observer.observe(card);
  });

  const buttons = document.querySelectorAll(".btn");
  buttons.forEach((btn) => {
    btn.addEventListener("mousedown", () => {
      btn.style.transform = "scale(0.95)";
    });
    btn.addEventListener("mouseup", () => {
      btn.style.transform = "scale(1)";
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const btnBack = document.querySelector(".btn-back");
  if (btnBack) {
    btnBack.addEventListener("click", () => {
      window.history.back();
    });
  }

  const realPassword = "PasswordSangatRahasia123";
  let isPasswordVisible = false;

  window.togglePassword = function () {
    const passwordSpan = document.querySelector(
      ".info-row:nth-child(2) span:nth-child(2)",
    );
    const toggleBtn = document.querySelector(".toggle-btn");

    if (!isPasswordVisible) {
      passwordSpan.innerText = `: ${realPassword}`;
      toggleBtn.innerText = "Sembunyikan";
      isPasswordVisible = true;
    } else {
      passwordSpan.innerText = ": **********";
      toggleBtn.innerText = "Lihat";
      isPasswordVisible = false;
    }
  };
});

document.addEventListener("DOMContentLoaded", () => {
  // 1. Ambil elemen yang dibutuhkan
  const authForm = document.querySelector("form");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const nameInput = document.getElementById("nama"); // Cuma ada di halaman Register

  // 2. Cek apakah form, email, dan password ada di halaman yang sedang dibuka
  if (authForm && emailInput && passwordInput) {
    authForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const submitBtn = document.querySelector(".auth-btn-submit");
      submitBtn.disabled = true;

      // 3. Pengecekan: Apakah kita di halaman Register (karena ada input nama)?
      if (nameInput) {
        // --- INI LOGIKA REGISTER ---
        const name = nameInput.value.trim();
        const password = passwordInput.value;

        if (password.length < 8) {
          alert("Keamanan itu penting! Kata sandi minimal harus 8 karakter.");
          submitBtn.disabled = false;
          return;
        }

        submitBtn.innerText = "Membuat Akun...";
        submitBtn.style.opacity = "0.7";

        setTimeout(() => {
          alert(
            `Selamat datang di NTBeat, ${name}! Akun Anda berhasil dibuat.`,
          );
          window.location.href = "login.php";
        }, 2000);
      } else {
        // --- INI LOGIKA LOGIN (karena tidak ada input nama) ---
        const email = emailInput.value.trim();
        const password = passwordInput.value;

        submitBtn.innerText = "Mengecek Akun...";

        setTimeout(() => {
          if (email === "admin@ntbeat.com" && password === "admin123") {
            alert("Selamat datang, Administrator!");
            window.location.href = "admin-dashboard.html";
          } else if (email !== "" && password.length >= 8) {
            alert("Login Berhasil! Selamat datang di NTBeat.");
            window.location.href = "halaman-user.php";
          } else {
            alert("Email atau Kata Sandi salah. Silakan coba lagi!");
            submitBtn.innerText = "Masuk";
            submitBtn.disabled = false;
          }
        }, 1500);
      }
    });
  }

  // Fitur Lupa Password (dibiarkan di luar agar tidak nyangkut saat submit form)
  const forgotPwLink = document.querySelector(".auth-forgot-pw");
  if (forgotPwLink) {
    forgotPwLink.addEventListener("click", (e) => {
      e.preventDefault();
      alert("Fitur pemulihan kata sandi akan dikirimkan ke email Anda.");
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const editIcon = document.querySelector(".ps-edit-icon");
  const avatarImg = document.querySelector(".ps-avatar-wrapper img");

  const fileInput = document.createElement("input");
  fileInput.type = "file";
  fileInput.accept = "image/*";

  if (editIcon) {
    editIcon.addEventListener("click", () => fileInput.click());
  }

  fileInput.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        avatarImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });

  const profileForm = document.querySelector(".ps-form");

  if (profileForm) {
    profileForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const username = document.getElementById("username").value;
      const email = document.getElementById("email").value;
      const currentPass = document.getElementById("current_password").value;
      const newPass = document.getElementById("new_password").value;

      if (username === "" || email === "") {
        alert("Username dan Email tidak boleh kosong!");
        return;
      }

      if (newPass !== "" && currentPass === "") {
        alert(
          "Silakan masukkan password saat ini untuk mengonfirmasi perubahan password baru.",
        );
        return;
      }

      const saveBtn = document.querySelector(".btn-ps-save");
      saveBtn.innerText = "Saving...";
      saveBtn.disabled = true;

      setTimeout(() => {
        alert("Profil berhasil diperbarui!");
        saveBtn.innerText = "Save";
        saveBtn.disabled = false;
        document.getElementById("current_password").value = "";
        document.getElementById("new_password").value = "";
      }, 1500);
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const logoutModal = document.getElementById("logoutModal");
  const btnUnduh = document.querySelector(".btn-unduh");

  if (btnUnduh) {
    btnUnduh.addEventListener("click", () => {
      const orderId = document.querySelector(".order-id").innerText;
      const concertTitle = document.querySelector(
        ".ticket-main-info h3",
      ).innerText;

      btnUnduh.innerText = "Mengunduh...";
      btnUnduh.disabled = true;

      setTimeout(() => {
        alert(
          `E-Ticket ${concertTitle} (${orderId}) berhasil diunduh dalam format PDF.`,
        );
        btnUnduh.innerText = "Unduh PDF";
        btnUnduh.disabled = false;
      }, 1500);
    });
  }

  const qrPlaceholder = document.querySelector(".qr-placeholder");
  if (qrPlaceholder) {
    qrPlaceholder.addEventListener("mouseenter", () => {
      qrPlaceholder.style.borderColor = "#d4af37";
      qrPlaceholder.style.cursor = "pointer";
    });
    qrPlaceholder.addEventListener("mouseleave", () => {
      qrPlaceholder.style.borderColor = "#eee";
    });
  }
});
