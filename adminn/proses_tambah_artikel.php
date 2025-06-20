<?php
include_once 'function.php'; // Mengandung koneksi dan session_start()

// Pastikan hanya bisa diakses via metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Siapkan nama file gambar
    $picture_name = null;
    
    // Proses upload gambar jika ada file yang diunggah
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $target_dir = "uploads/"; // Pastikan folder ini ada
        // Buat nama file unik untuk menghindari duplikasi
        $picture_name = uniqid() . '-' . basename($_FILES["picture"]["name"]);
        $target_file = $target_dir . $picture_name;
        
        // Pindahkan file yang di-upload ke folder 'uploads/'
        if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
            // Jika gagal upload, proses tetap lanjut tanpa gambar
            $picture_name = null; 
        }
    }
    
    // Buat tanggal hari ini dengan format "9 Maret 2025"
    $date = date("j F Y");
    
    // Siapkan query INSERT menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, "INSERT INTO article (date, title, content, picture) VALUES (?, ?, ?, ?)");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $date, $title, $content, $picture_name);
        
        // Eksekusi query
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, redirect kembali ke halaman artikel dengan status sukses
            header("Location: artikel.php?status=sukses");
        } else {
            // Jika gagal, redirect dengan status gagal
            header("Location: artikel.php?status=gagal");
        }
        mysqli_stmt_close($stmt);
    } else {
        // Jika query prepare gagal
        header("Location: artikel.php?status=gagal");
    }
    
    mysqli_close($koneksi);
    exit();

} else {
    // Jika diakses langsung tanpa POST, tendang ke halaman utama
    header("Location: index.php");
    exit();
}
?>