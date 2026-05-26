<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar</title>
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script src="../assets/script/script.js"></script>
  </head>
  <body>
    <div class="auth-container">
      <div class="auth-header">
        <img src="../assets/img/logo.png" alt="Logo NTBeat" class="header-logo" />
        <h1>NTBeat</h1>
      </div>

      <div class="auth-card">
        <h2>Register</h2>

       <form action="../actions/register_proses.php" method="POST">
          <div class="auth-form-group">
            <label for="nama">Nama*</label>
            <input
              type="text"
              id="nama"
              name="nama" 
              class="auth-input"
              placeholder="Masukkan Nama "
              required
            />
          </div>

          <div class="auth-form-group">
            <label for="email">Email*</label>
            <input
              type="email"
              id="email"
              name="email" 
              class="auth-input"
              placeholder="example@gmail.com "
              required
            />
          </div>

          <div class="auth-form-group">
            <div class="auth-label-row">
              <label for="password">Kata Sandi*</label>
            </div>
            <input
              type="password"
              id="password"
              name="password" 
              class="auth-input"
              placeholder="Masukkan password"
              required
            />
          </div>

          <button type="submit" class="auth-btn-submit">Daftar</button>
        </form>

        <p class="auth-footer">Sudah punya akun? <a href="login.php">Log in</a></p>
      </div>
    </div>
  </body>
</html>
