<?php


require_once __DIR__ . '/../models/UserModel.php';

// Variabel untuk View
$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname        = trim($_POST['fullname'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullname === '' || $username === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        $error = 'Semua bidang harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        $userModel = new UserModel();

        if ($userModel->usernameOrEmailExists($username, $email)) {
            $error = 'Username atau email sudah digunakan. Silakan pilih yang lain.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            if ($userModel->createUser($fullname, $username, $email, $phone, $hash, 'customer')) {
                // Langsung login-kan user yang baru daftar
                $newUser = $userModel->findByUsername($username);

                $_SESSION['user_id']  = $newUser['id'];
                $_SESSION['user']     = $newUser['username'];
                $_SESSION['fullname'] = $newUser['fullname'];
                $_SESSION['role']     = $newUser['role'];

                header('Location: customer-dashboard.php');
                exit;
            }

            $error = 'Gagal menyimpan data. Coba lagi nanti.';
        }
    }
}
