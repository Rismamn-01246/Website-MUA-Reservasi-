<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/UserModel.php';

// Variabel yang akan dipakai oleh View (login_view.php)
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $userModel = new UserModel();
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil -> simpan session
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user']       = $user['username'];
            $_SESSION['fullname']   = $user['fullname'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['email']      = $user['email'];
            $_SESSION['phone']      = $user['phone'];
            $_SESSION['address']    = $user['address'];
            $_SESSION['created_at'] = $user['created_at'];

            if ($user['role'] === 'admin') {
                header('Location: dashboard-admin1.php');
            } else {
                header('Location: customer-dashboard.php');
            }
            exit;
        }

        $error = 'Username atau password salah.';
    }
}