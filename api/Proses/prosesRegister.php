<?php
include '../Server/koneksi.php';

$username      = mysqli_real_escape_string($koneksi, $_POST['username']);
$email         = mysqli_real_escape_string($koneksi, $_POST['email']);
$password      = $_POST['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Cek username sudah ada
$cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: ../register.php?error=username_taken");
    exit();
}

// Cek email sudah ada
$cek_email = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($cek_email) > 0) {
    header("Location: ../register.php?error=email_taken");
    exit();
}

// Role default = 'user' untuk pendaftar baru
$query  = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password_hash', 'user')";
$result = mysqli_query($koneksi, $query);

if ($result) {
    header("Location: ../login.php?success=register");
    exit();
} else {
    header("Location: ../register.php?error=failed");
    exit();
}
?>
