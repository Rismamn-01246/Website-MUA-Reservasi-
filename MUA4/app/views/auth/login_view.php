<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login MUA</title>

  <link rel="stylesheet" href="assets/css/Login.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

  <div class="login-container">

    <h1>Welcome Back</h1>

    <p class="subtitle">
      Sistem Informasi Reservasi MUA
    </p>

    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">

      <div class="input-group">
        <label>Username</label>
        <input
          type="text"
          name="username"
          placeholder="Masukkan username"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          required
        >
      </div>

      <div class="input-group">
        <label>Password</label>
        <input
          type="password"
          name="password"
          placeholder="Masukkan password"
          required
        >
      </div>

      <button type="submit">
        Login
      </button>

    </form>

    <div class="extra">
      Belum punya akun?
      <a href="Register.php">Daftar</a>
    </div>

  </div>

</body>
</html>
