<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - NTBeat</title>
    <link rel="stylesheet" href="assets/style/style.css" />
    <script src="assets/script/script.js"></script>
  </head>
  <body>
    <div class="auth-container">
      <div class="auth-header">
        <img src="assets/img/logo.png" alt="Logo NTBeat" class="header-logo" />
        <h1>NTBeat</h1>
      </div>

      <div class="auth-card">
        <h2>Login</h2>
        <p class="auth-subtitle">Lorem ipsum dolor sit amet hean</p>

        <form action="#">
          <div class="auth-form-group">
            <label for="email">Email*</label>
            <input
              type="email"
              id="email"
              class="auth-input"
              placeholder="Masukkan Email "
              required
            />
          </div>

          <div class="auth-form-group">
            <div class="auth-label-row">
              <label for="password">Kata Sandi*</label>
              <a href="#" class="auth-forgot-pw">Lupa kata sandi?</a>
            </div>
            <input
              type="password"
              id="password"
              class="auth-input"
              placeholder="Masukkan password"
              required
            />
          </div>

          <button type="submit" class="auth-btn-submit" onclick="window.location.href='halaman-awal.php'">Masuk</button>
        </form>

        <p class="auth-footer">Belum punya akun? <a href="register.php">Daftar</a></p>
      </div>
    </div>
  </body>
</html>
