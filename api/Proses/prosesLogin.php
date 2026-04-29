<?php
session_start();
include '../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        // Set semua data session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['role']     = $user['role'];

        if (isset($_POST['remember'])) {
            setcookie("username", $username, time() + 3600, "/");
        }

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
?>
