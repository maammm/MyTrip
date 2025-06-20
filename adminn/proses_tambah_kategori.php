<?php
include_once 'function.php';
include_once 'ceksession.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (empty($name)) {
        header("Location: kategori.php?status=gagal");
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO category (name, description) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $name, $description);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=tambah_sukses");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
}
?>