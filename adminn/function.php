<?php
// --- Database Configuration & Connection ---
define('DB_HOST', 'localhost'); // Ganti dengan host database Anda
define('DB_USER', 'root');      // Ganti dengan username database Anda
define('DB_PASS', '');          // Ganti dengan password database Anda
define('DB_NAME', 'pemweb');    // Ganti dengan nama database Anda (sesuai gambar awal)

// Buat Koneksi
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- Session Management ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Functions ---
function is_logged_in() {
    return isset($_SESSION['admin']);
}

// Anda bisa menambahkan fungsi lain di sini jika dibutuhkan,
// misalnya fungsi untuk sanitasi input, redirect, dll.
// Contoh:
function sanitize_input($data) {
    global $koneksi; // Perlu akses ke $koneksi untuk mysqli_real_escape_string
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($koneksi, $data); // Untuk proteksi SQL Injection dasar jika tidak pakai prepared statement
    return $data;
}
?>