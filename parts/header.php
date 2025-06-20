<?php
// parts/header.php
require_once __DIR__ . '/../config.php'; // Hubungkan ke DB
require_once __DIR__ . '/../functions.php'; // Hubungkan ke fungsi

// Logika Judul Halaman Dinamis
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Jalan Santai - Blog Wisata"; // Judul default

switch ($current_page) {
    case 'detail.php':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT title FROM article WHERE id = ?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $page_title = ($result->num_rows > 0) ? $result->fetch_assoc()['title'] : "Artikel Tidak Ditemukan";
        }
        break;
    case 'kategori.php':
        if (isset($_GET['category_id'])) {
            $stmt = $conn->prepare("SELECT name FROM category WHERE id = ?");
            $stmt->bind_param("i", $_GET['category_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $page_title = ($result->num_rows > 0) ? "Kategori: " . $result->fetch_assoc()['name'] : "Kategori Tidak Ditemukan";
        }
        break;
    case 'cari.php':
        if (isset($_GET['q'])) {
            $page_title = "Pencarian: '" . htmlspecialchars($_GET['q']) . "'";
        }
        break;
    case 'tentang.php':
        $page_title = "Tentang Kami";
        break;
    case 'kontak.php':
        $page_title = "Hubungi Kami";
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($page_title); ?> - Jalan Santai</title>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Selamat Datang di Blog Kami!</h1>
            <p>Blog Catatan Wisata dan Jalan-Jalan</p>
        </div>
    </header>
    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php">Tentang</a></li>
                <li><a href="kontak.php">Kontak</a></li>
                <li><a href="adminn/index.php">Login/SignUp</a></li>
            </ul>
        </div>
    </nav>
    <div class="content-wrapper container"></div>