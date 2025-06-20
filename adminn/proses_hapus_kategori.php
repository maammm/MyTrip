<?php
include_once 'ceksession.php';
include_once 'function.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Karena di database Anda ada ON DELETE CASCADE, relasi di article_category akan terhapus otomatis.
    // Kita hanya perlu menghapus dari tabel category utama.
    $query = "DELETE FROM category WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=hapus_sukses");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
} else {
    header("Location: kategori.php");
    exit;
}
?>