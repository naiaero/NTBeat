<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ubah Kata Sandi - NTBeat</title>
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script>
      function validateForm() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password !== confirmPassword) {
          alert("Kata sandi baru dan konfirmasi kata sandi tidak cocok!");
          return false;
        }
        if (password.length < 6) {
          alert("Kata sandi harus minimal 6 karakter!");
          return false;
        }
        return true;
      }
    </script>
  </head>
  <body>
    <div class="auth-container">
      <div class="auth-header">
        <img src="../assets/img/logo.png" alt="Logo NTBeat" class="header-logo" />
        <h1>NTBeat</h1>
      </div>

      <div class="auth-card">
        <h2>Ubah Kata Sandi</h2>
        <p class="auth-subtitle">Masukkan email terdaftar Anda beserta kata sandi baru</p>
        
        <form action="../actions/forgot_password_proses.php" method="POST" onsubmit="return validateForm()">
          <div class="auth-form-group">
            <label for="email">Email Terdaftar*</label>
            <input
              type="email"
              id="email"
              name="email" 
              class="auth-input"
              placeholder="example@gmail.com"
              required
            />
          </div>

          <div class="auth-form-group">
            <label for="password">Kata Sandi Baru*</label>
            <input
              type="password"
              id="password"
              name="password" 
              class="auth-input"
              placeholder="Minimal 6 karakter"
              required
            />
          </div>

          <div class="auth-form-group">
            <label for="confirm_password">Konfirmasi Kata Sandi Baru*</label>
            <input
              type="password"
              id="confirm_password"
              name="confirm_password" 
              class="auth-input"
              placeholder="Ulangi kata sandi baru"
              required
            />
          </div>

          <button type="submit" class="auth-btn-submit">Simpan Kata Sandi</button>
        </form>
        
        <p class="auth-footer">Sudah ingat password Anda? <a href="login.php">Log in</a></p>
      </div>
    </div>
  </body>
</html>
