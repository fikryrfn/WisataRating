<?php
require __DIR__ . '/../Server/auth.php';  // sesuaikan path relatifnya
auth_session();
include __DIR__ . '/../Server/koneksi.php';

// Hanya admin_akun yang bisa kelola akun pengguna
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_akun') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)$_POST['id'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = trim($_POST['password']);

    if ($id && $username && $email) {
        if (!empty($password)) {
            // Update beserta password baru
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $q = "UPDATE users SET username='$username', email='$email', role='$role', password='$hash' WHERE id='$id'";
        } else {
            // Update tanpa ubah password
            $q = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id='$id'";
        }
        mysqli_query($koneksi, $q);
    }

    header("Location: ../admin_akun.php?msg=user_updated");
    exit();
}
?>
