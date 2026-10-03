<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../app/config/Database.php';

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname         = trim($_POST['fullname']         ?? '');
    $username         = trim($_POST['username']         ?? '');
    $email            = trim($_POST['email']            ?? '');
    $phone            = trim($_POST['phone']            ?? '');
    $password         = $_POST['password']              ?? '';
    $confirm_password = $_POST['confirm_password']      ?? '';

    if (!$fullname || !$username || !$email || !$phone || !$password) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        if (!isset($conn)) {
            $db_obj = new Database();
            $conn   = $db_obj->getConnection();
        }
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username atau email sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (fullname, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
            $ins->bind_param('sssss', $fullname, $username, $email, $phone, $hashed);
            if ($ins->execute()) {
                $success = true;
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register – MUA</title>
  <link rel="stylesheet" href="assets/css/Register.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<div class="register-container">

  <h1>Create Account</h1>
  <p class="subtitle">Daftar akun reservasi MUA</p>
  <div class="divider"></div>

  <?php if ($error): ?>
    <div class="error-message">
      <i class="fa-solid fa-circle-exclamation"></i>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success-message">
      <i class="fa-solid fa-circle-check"></i>
      Pendaftaran berhasil. <a href="Login.php">Login sekarang</a>.
    </div>
  <?php endif; ?>

  <?php if (!$success): ?>
  <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">

    <div class="input-group">
      <label for="fullname">Nama Lengkap</label>
      <div class="input-wrap">
        <i class="fa-regular fa-user icon-left"></i>
        <input
          type="text" id="fullname" name="fullname"
          placeholder="Masukkan nama lengkap"
          value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
          required
        />
      </div>
    </div>

    <div class="row-2">
      <div class="input-group">
        <label for="username">Username</label>
        <div class="input-wrap">
          <i class="fa-solid fa-at icon-left"></i>
          <input
            type="text" id="username" name="username"
            placeholder="username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            required
          />
        </div>
      </div>
      <div class="input-group">
        <label for="phone">No. Telepon</label>
        <div class="input-wrap">
          <i class="fa-solid fa-phone icon-left"></i>
          <input
            type="tel" id="phone" name="phone"
            placeholder="08xxxxxxxxxx"
            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
            required
          />
        </div>
      </div>
    </div>

    <div class="input-group">
      <label for="email">Email</label>
      <div class="input-wrap">
        <i class="fa-regular fa-envelope icon-left"></i>
        <input
          type="email" id="email" name="email"
          placeholder="nama@email.com"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          required
        />
      </div>
    </div>

    <div class="row-2">
      <div class="input-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock icon-left"></i>
          <input type="password" id="password" name="password"
            placeholder="Min. 8 karakter" required />
          <button type="button" class="pass-toggle"
            onclick="togglePass('password','eye1')"
            aria-label="Tampilkan password">
            <i class="fa-regular fa-eye" id="eye1"></i>
          </button>
        </div>
      </div>
      <div class="input-group">
        <label for="confirm_password">Konfirmasi</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock icon-left"></i>
          <input type="password" id="confirm_password" name="confirm_password"
            placeholder="Ulangi password" required />
          <button type="button" class="pass-toggle"
            onclick="togglePass('confirm_password','eye2')"
            aria-label="Tampilkan konfirmasi">
            <i class="fa-regular fa-eye" id="eye2"></i>
          </button>
        </div>
      </div>
    </div>

    <button type="submit">
      <i class="fa-solid fa-user-plus"></i>
      Daftar Sekarang
    </button>

  </form>
  <?php endif; ?>

  <div class="extra">
    Sudah punya akun? <a href="Login.php">Login</a>
  </div>

</div>

<script>
function togglePass(inputId, eyeId) {
  var inp = document.getElementById(inputId);
  var eye = document.getElementById(eyeId);
  if (inp.type === 'password') {
    inp.type = 'text';
    eye.className = 'fa-regular fa-eye-slash';
  } else {
    inp.type = 'password';
    eye.className = 'fa-regular fa-eye';
  }
}
</script>

</body>
</html>