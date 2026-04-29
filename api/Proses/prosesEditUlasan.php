<?php
session_start();
include __DIR__ . '/../Server/koneksi.php';

// Hanya admin dan admin_akun yang bisa edit ulasan
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'admin_akun'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int)$_POST['id'];
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $rating  = (int)$_POST['rating'];
    $isi     = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $referer = isset($_POST['referer']) ? $_POST['referer'] : 'admin_ulasan';

    if ($id && $nama && $rating > 0 && $isi) {
        $q = "UPDATE ulasan SET nama='$nama', rating='$rating', isi='$isi' WHERE id='$id'";
        mysqli_query($koneksi, $q);
    }

    // Redirect balik ke halaman asal
    if ($referer === 'admin_akun') {
        header("Location: ../admin_akun.php?msg=ulasan_updated");
    } else {
        header("Location: ../admin_ulasan.php?msg=updated");
    }
    exit();
}
?>
