<?php
include_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($nickname) || empty($email) || empty($password)) {
        header("Location: penulis.php?status=gagal_kosong");
        exit;
    }

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah ada
    $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM author WHERE email = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        header("Location: penulis.php?status=gagal_email");
        exit;
    }

    // Insert data penulis baru
    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "sss", $nickname, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt_insert)) {
        header("Location: penulis.php?status=tambah_sukses");
    } else {
        header("Location: penulis.php?status=gagal");
    }
    exit;
}
?>