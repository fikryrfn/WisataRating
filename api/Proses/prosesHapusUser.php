<?php
session_start();
include __DIR__ . '/../Server/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_akun') {
    header("Location: ../index.php");
    exit();
}

$id = (int)$_GET['id'];

if ($id) {
    // Hapus juga semua ulasan milik user ini
    mysqli_query($koneksi, "DELETE FROM ulasan WHERE user_id='$id'");
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
}

header("Location: ../admin_akun.php?msg=user_deleted");
exit();
?>
