<?php
include_once 'ceksession.php';
include_once 'function.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM author WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: penulis.php?status=hapus_sukses");
    } else {
        header("Location: penulis.php?status=gagal");
    }
    exit;
} else {
    header("Location: penulis.php");
    exit;
}
?>