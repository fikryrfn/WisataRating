<?php
session_start();
include '../Server/koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'admin_akun'])) {
    header("Location: ../index.php");
    exit();
}

$id      = (int)$_GET['id'];
$referer = isset($_GET['ref']) ? $_GET['ref'] : 'admin_ulasan';

if ($id) {
    mysqli_query($koneksi, "DELETE FROM ulasan WHERE id='$id'");
}

if ($referer === 'admin_akun') {
    header("Location: ../admin_akun.php?msg=ulasan_deleted");
} else {
    header("Location: ../admin_ulasan.php?msg=deleted");
}
exit();
?>
