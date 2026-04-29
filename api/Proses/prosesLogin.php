<?php
require __DIR__ . '/../Server/auth.php';  // sesuaikan path relatifnya
auth_session();
// Simpan di: api/Proses/prosesLogin.php

require __DIR__ . '/../Server/auth.php';
require __DIR__ . '/../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query  = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        // Simpan ke signed cookie (pengganti session)
        auth_set(
            (int) $user['id'],
            $user['username'],
            $user['email'],
            $user['role']
        );

        // Redirect berdasarkan role
        if ($user['role'] === 'admin_akun') {
            header("Location: ../admin_akun.php");
        } elseif ($user['role'] === 'admin') {
            header("Location: ../admin_ulasan.php");
        } else {
            header("Location: ../home.php");
        }
        exit();
    } else {
        header("Location: ../login.php?error=password");
        exit();
    }
} else {
    header("Location: ../login.php?error=username");
    exit();
}
