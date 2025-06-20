<?php
// Mulai session untuk menyimpan pesan notifikasi
session_start();

// Sertakan file konfigurasi database
require 'config.php';

// Cek apakah request datang dari method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil dan bersihkan data dari form
    $nama = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subject']);
    $pesan = trim($_POST['message']);

    // Validasi dasar
    if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
        // Jika ada field yang kosong
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Semua kolom wajib diisi.'
        ];
        header('Location: kontak.php');
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Jika format email tidak valid
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Format alamat email tidak valid.'
        ];
        header('Location: kontak.php');
        exit();
    } else {
        // Jika semua data valid, siapkan untuk dimasukkan ke database
        $stmt = $conn->prepare("INSERT INTO pesan (nama, email, subjek, isi_pesan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);

        // Eksekusi query
        if ($stmt->execute()) {
            // Jika berhasil
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Pesan Anda telah berhasil terkirim. Terima kasih!'
            ];
        } else {
            // Jika gagal
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Maaf, terjadi kesalahan saat mengirim pesan. Silakan coba lagi.'
            ];
        }
        $stmt->close();
        $conn->close();
        header('Location: kontak.php');
        exit();
    }

} else {
    // Jika halaman diakses langsung, alihkan ke halaman kontak
    header('Location: kontak.php');
    exit();
}
?>