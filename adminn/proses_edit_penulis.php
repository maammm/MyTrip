<?php
include_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah password diisi atau tidak
    if (!empty($password)) {
        // Jika password baru diisi, hash dan update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE author SET nickname = ?, email = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nickname, $email, $hashed_password, $id);
    } else {
        // Jika password kosong, jangan update password
        $query = "UPDATE author SET nickname = ?, email = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nickname, $email, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: penulis.php?status=edit_sukses");
    } else {
        header("Location: penulis.php?status=gagal");
    }
    exit;
}
?>