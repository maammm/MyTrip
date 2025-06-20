<?php
// Pengaturan untuk koneksi database
$db_host = '127.0.0.1'; // atau 'localhost'
$db_user = 'root';      // username database Anda
$db_pass = '';          // password database Anda, kosongkan jika tidak ada
$db_name = 'pemweb';     // nama database dari file .sql

// Membuat koneksi ke database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa apakah koneksi berhasil atau gagal
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Mengatur character set ke utf8mb4 untuk mendukung karakter yang lebih luas
$conn->set_charset("utf8mb4");
?>