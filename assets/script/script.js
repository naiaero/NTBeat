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

// Unified check-all and action buttons listener handled below in the code.

// document.addEventListener("DOMContentLoaded", () => {
document.addEventListener("DOMContentLoaded", () => {
  const liveBar = document.getElementById("live-bar");
  const soldCountDisplay = document.getElementById("sold-count");
  const remainingCountDisplay = document.getElementById("remaining-count");
  const revenueCountDisplay = document.getElementById("revenue-count");

  let totalSold = window.dbStats ? window.dbStats.totalSold : 1420;
  const totalCapacity = window.dbStats ? window.dbStats.totalCapacity : 2000;
  let totalRevenue = window.dbStats ? window.dbStats.totalRevenue : 213000000;

  // Cek dulu apakah halaman ini punya statistik jualan tiket?
  if (soldCountDisplay && remainingCountDisplay && revenueCountDisplay) {
    function updateStats() {
      const remaining = totalCapacity - totalSold;

      soldCountDisplay.textContent = totalSold.toLocaleString("id-ID");
      remainingCountDisplay.textContent = remaining.toLocaleString("id-ID");

      function formatRupiahRingkas(num) {
        if (num >= 1000000000) {
          let val = (num / 1000000000).toFixed(1);
          if (val.endsWith('.0')) val = val.substring(0, val.length - 2);
          return `Rp ${val.replace('.', ',')} M`;
        } else if (num >= 1000000) {
          let val = (num / 1000000).toFixed(1);
          if (val.endsWith('.0')) val = val.substring(0, val.length - 2);
          return `Rp ${val.replace('.', ',')} jt`;
        } else if (num >= 1000) {
          let val = (num / 1000).toFixed(1);
          if (val.endsWith('.0')) val = val.substring(0, val.length - 2);
          return `Rp ${val.replace('.', ',')} rb`;
        } else {
          return `Rp ${num.toLocaleString('id-ID')}`;
        }
      }
      revenueCountDisplay.textContent = formatRupiahRingkas(totalRevenue);

      const randomHeight = Math.floor(Math.random() * 60) + 20;
      if (liveBar) {
        liveBar.style.height = randomHeight + "%";
      }
    }

    // Tampilkan data awal yang benar sesuai DB
    updateStats();

    // Jalankan timer animasi HANYA untuk tinggi bar (estetika live)
    setInterval(() => {
      const randomHeight = Math.floor(Math.random() * 60) + 20;
      if (liveBar) {
        liveBar.style.height = randomHeight + "%";
      }
    }, 3000);
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
  const posterWrapper = document.querySelector(".poster-upload-wrapper");
  const posterInput = document.getElementById("poster-input");
  const imagePreview = document.getElementById("imagePreview");

  if (posterWrapper && posterInput) {
    posterWrapper.style.cursor = "pointer";
    posterWrapper.addEventListener("click", (e) => {
      // Avoid double trigger if clicking label/input directly
      if (e.target.tagName === 'LABEL' || e.target.closest('label') || e.target.tagName === 'INPUT') {
        return;
      }
      posterInput.click();
    });

    posterInput.addEventListener("change", function () {
      const file = this.files[0];

      if (file) {
        const reader = new FileReader();

        if (imagePreview) {
          imagePreview.innerHTML = '<span style="color: #eee; font-size: 0.8rem;">Memuat...</span>';
        }

        reader.onload = function (e) {
          if (imagePreview) {
            imagePreview.innerHTML = ""; 
            imagePreview.style.backgroundImage = `url(${e.target.result})`;
            imagePreview.style.backgroundSize = "cover";
            imagePreview.style.backgroundPosition = "center";
            imagePreview.style.border = "none";
          }
        };

        reader.readAsDataURL(file);
      }
    });
  }
});

// document.addEventListener("DOMContentLoaded", () => {
//   const posterInput = document.getElementById("poster-input");
//   const imagePreview = document.getElementById("imagePreview");

//   if (posterInput) {
//     posterInput.addEventListener("change", function () {
//       const file = this.files[0];

//       if (file) {
//         const reader = new FileReader();

//         imagePreview.innerHTML =
//           '<span style="color: #eee; font-size: 0.8rem;">Memuat...</span>';

//         reader.onload = function (e) {
//           imagePreview.style.backgroundImage = `url(${e.target.result})`;
//           imagePreview.style.backgroundSize = "cover";
//           imagePreview.style.backgroundPosition = "center";
//           imagePreview.style.border = "none";
//           imagePreview.innerHTML = "";
//         };

//         reader.readAsDataURL(file);
//       } else {
//         imagePreview.style.backgroundImage = "none";
//         imagePreview.innerHTML = "<span>Preview Poster</span>";
//       }
//     });
//   }

//   const concertForm = document.querySelector("concert-form");
//   if (concertForm) {
//     concertForm.addEventListener("submit", (e) => {
//       e.preventDefault();

//       const btnSave = document.querySelector(".btn-ps-save");
//       btnSave.textContent = "Menyimpan...";
//       btnSave.disabled = true;

//       setTimeout(() => {
//         alert("Data Konser Berhasil Disimpan!");
//         window.location.href = "admin-kelola-konser.php";
//       }, 1500);
//     });
//   }
// });

// document.addEventListener("DOMContentLoaded", () => {
document.addEventListener("DOMContentLoaded", () => {
  const checkAll = document.getElementById("check-all");
  const rowCheckboxes = document.querySelectorAll(".row-checkbox");
  const countDisplay = document.getElementById("count-display");

  const btnEdit = document.getElementById("btn-edit-bulk");
  const btnArchive = document.getElementById("btn-archive-bulk");
  const btnDelete = document.getElementById("btn-delete-bulk");

  if (checkAll || rowCheckboxes.length > 0) {
    function updateTableUI() {
      const checkedBoxes = document.querySelectorAll(".row-checkbox:checked");
      const selectedCount = checkedBoxes.length;

      if (countDisplay) {
        countDisplay.textContent = selectedCount;
      }

      if (btnArchive) {
        btnArchive.disabled = selectedCount === 0;
      }
      if (btnDelete) {
        btnDelete.disabled = selectedCount === 0;
      }
      if (btnEdit) {
        btnEdit.disabled = selectedCount !== 1;
      }

      if (checkAll) {
        checkAll.checked = selectedCount === rowCheckboxes.length && rowCheckboxes.length > 0;
        checkAll.indeterminate =
          selectedCount > 0 && selectedCount < rowCheckboxes.length;
      }
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
  }

  if (btnDelete) {
    btnDelete.addEventListener("click", () => {
      const count = document.querySelectorAll(".row-checkbox:checked").length;
      if (
        confirm(
          `Apakah Anda yakin ingin menghapus ${count} data konser yang terpilih?`,
        )
      ) {
        document.getElementById("action-type").value = "delete";
        document.getElementById("bulk-action-form").submit();
      }
    });
  }

  if (btnEdit) {
    btnEdit.addEventListener("click", () => {
      const checkedBox = document.querySelector(".row-checkbox:checked");

      if (checkedBox) {
        const konserId = checkedBox.value;
        window.location.href = `admin-edit-konser.php?id=${konserId}`;
      }
    });
  }

  if (btnArchive) {
    btnArchive.addEventListener("click", () => {
      const count = document.querySelectorAll(".row-checkbox:checked").length;

      if (confirm(`Pindahkan ${count} konser ke arsip?`)) {
        document.getElementById("action-type").value = "archive";
        document.getElementById("bulk-action-form").submit();
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
    // Keep it static according to the database value (countdown disabled to ensure synchronization)
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
      const concertId = btn.getAttribute("data-id");
      const confirmBuy = confirm(
        `Apakah Anda ingin langsung memesan tiket ${concertName}?`,
      );

      if (confirmBuy) {
        window.location.href = "detail-konser.php?id=" + concertId;
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
      const submitBtn = document.querySelector(".auth-btn-submit");

      // 1. Cek apakah ini halaman REGISTER (ada input nama)
      if (nameInput) {
        const password = passwordInput.value;

        if (password.length < 8) {
          e.preventDefault(); // Batalkan submit jika password terlalu pendek
          alert("Kata sandi minimal harus 8 karakter.");
          if (submitBtn) submitBtn.disabled = false;
          return;
        }

        if (submitBtn) {
          submitBtn.innerText = "Membuat Akun...";
          setTimeout(() => {
            submitBtn.disabled = true;
          }, 0);
        }
      } else {
        // Halaman LOGIN
        if (submitBtn) {
          submitBtn.innerText = "Mengecek Akun...";
          setTimeout(() => {
            submitBtn.disabled = true;
          }, 0);
        }
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
  const avatarWrapper = document.querySelector(".ps-avatar-wrapper");
  const avatarInput = document.getElementById("avatar-input");
  const avatarPreview = document.getElementById("avatar-preview");

  if (avatarWrapper && avatarInput) {
    avatarWrapper.style.cursor = "pointer";
    avatarWrapper.addEventListener("click", (e) => {
      // Avoid double trigger if clicking label/input directly
      if (e.target.tagName === 'LABEL' || e.target.closest('label') || e.target.tagName === 'INPUT') {
        return;
      }
      avatarInput.click();
    });

    avatarInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (avatarPreview) {
            if (avatarPreview.tagName.toLowerCase() === 'img') {
              avatarPreview.src = e.target.result;
            } else {
              avatarPreview.style.backgroundImage = `url(${e.target.result})`;
              avatarPreview.style.backgroundSize = "cover";
              avatarPreview.style.backgroundPosition = "center";
              avatarPreview.innerHTML = ""; // Hapus inisial saat preview foto baru
              avatarPreview.style.border = "1px solid #d4af37";
            }
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  const profileForm = document.getElementById("profile-form");

  if (profileForm) {
    profileForm.addEventListener("submit", (e) => {
      const username = document.getElementById("username").value.trim();
      const currentPass = document.getElementById("current_password").value;
      const newPass = document.getElementById("new_password").value;

      if (username === "") {
        e.preventDefault();
        alert("Nama tidak boleh kosong!");
        return;
      }

      if (newPass !== "" && currentPass === "") {
        e.preventDefault();
        alert("Silakan masukkan password saat ini untuk mengonfirmasi perubahan password baru.");
        return;
      }

      const saveBtn = profileForm.querySelector(".btn-ps-save");
      if (saveBtn) {
        saveBtn.innerText = "Menyimpan...";
        setTimeout(() => {
          saveBtn.disabled = true;
        }, 0);
      }
    });
  }

  const adminProfileForm = document.getElementById("admin-profile-form");

  if (adminProfileForm) {
    adminProfileForm.addEventListener("submit", (e) => {
      const username = adminProfileForm.querySelector("#username").value.trim();
      const currentPass = adminProfileForm.querySelector("#current_password").value;
      const newPass = adminProfileForm.querySelector("#new_password").value;

      if (username === "") {
        e.preventDefault();
        alert("Nama tidak boleh kosong!");
        return;
      }

      if (newPass !== "" && currentPass === "") {
        e.preventDefault();
        alert("Silakan masukkan password saat ini untuk mengonfirmasi perubahan password baru.");
        return;
      }

      const saveBtn = adminProfileForm.querySelector(".btn-ps-save");
      if (saveBtn) {
        saveBtn.innerText = "Menyimpan...";
        setTimeout(() => {
          saveBtn.disabled = true;
        }, 0);
      }
    });
  }

  const concertForm = document.getElementById("concert-form");
  if (concertForm) {
    concertForm.addEventListener("submit", (e) => {
      const saveBtn = concertForm.querySelector(".btn-ps-save");
      if (saveBtn) {
        saveBtn.innerText = "Menyimpan...";
        setTimeout(() => {
          saveBtn.disabled = true;
        }, 0);
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const logoutModal = document.getElementById("logoutModal");

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

// --- FITUR GRAFIK GARIS (ADMIN DASHBOARD) ---
document.addEventListener("DOMContentLoaded", () => {
  const lineChartEl = document.getElementById("ntbeatLineChart");

  if (lineChartEl) {
    const ctx = lineChartEl.getContext("2d");

    new Chart(ctx, {
      type: "line",
      data: {
        labels: window.chartData ? window.chartData.labels : ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
        datasets: [
          {
            label: "Tiket Terjual",
            data: window.chartData ? window.chartData.data : [15, 30, 25, 45, 40, 60, 85], // Data simulasi
            borderColor: "#d4af37", // Emas NTBeat
            backgroundColor: "rgba(212, 175, 55, 0.1)", // Efek bayangan emas
            fill: true,
            tension: 0.4, // Garis melengkung halus
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: "#d4af37",
            pointBorderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }, // Sembunyikan legenda agar simpel
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: "rgba(255, 255, 255, 0.05)" },
            ticks: { color: "#888" },
          },
          x: {
            grid: { display: false },
            ticks: { color: "#888" },
          },
        },
      },
    });
  }
});


// lihat profile
function openProfileModal() {
  const modal = document.getElementById("profileModal");
  if(modal) modal.style.display = "flex";
}

function closeProfileModal() {
  const modal = document.getElementById("profileModal");
  if(modal) modal.style.display = "none";
}

window.onclick = function(event) {
  const logoutModal = document.getElementById("logoutModal");
  const profileModal = document.getElementById("profileModal");

  if(event.target == logoutModal) {
    logoutModal.style.display = "none";
  }

  if(event.target == profileModal) {
    profileModal.style.display = "none";
  }
};