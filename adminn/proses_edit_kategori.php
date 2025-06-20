<?php
include_once 'function.php';
include_once 'ceksession.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (empty($id) || empty($name)) {
        header("Location: kategori.php?status=gagal");
        exit;
    }
    
    $query = "UPDATE category SET name = ?, description = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=edit_sukses");
    } else {
        header("Location: kategori.php?status=gagal");
    }
    exit;
}
?>