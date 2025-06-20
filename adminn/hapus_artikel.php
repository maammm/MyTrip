<?php
include_once 'function.php'; // Untuk koneksi ke database
include_once 'ceksession.php'; // Untuk memastikan hanya admin yang login bisa akses

// 1. Validasi ID Artikel
// Pastikan ID ada dan merupakan angka
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika tidak valid, redirect ke halaman artikel
    header("Location: artikel.php?status=id_tidak_valid");
    exit;
}

$id = $_GET['id'];

// 2. Ambil nama file gambar sebelum menghapus data dari database
$query_select = "SELECT picture FROM article WHERE id = ?";
$stmt_select = mysqli_prepare($koneksi, $query_select);

if ($stmt_select) {
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    
    if ($row = mysqli_fetch_assoc($result_select)) {
        $gambar_lama = $row['picture'];

        // 3. Hapus data artikel dari database menggunakan prepared statement
        $query_delete = "DELETE FROM article WHERE id = ?";
        $stmt_delete = mysqli_prepare($koneksi, $query_delete);

        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $id);

            if (mysqli_stmt_execute($stmt_delete)) {
                // Jika query delete berhasil, lanjutkan menghapus file gambar
                
                // 4. Hapus file gambar dari folder 'uploads' jika file tersebut ada
                if (!empty($gambar_lama) && file_exists("uploads/" . $gambar_lama)) {
                    unlink("uploads/" . $gambar_lama);
                }

                // Redirect dengan status sukses
                header("Location: artikel.php?status=hapus_sukses");
            } else {
                // Jika eksekusi query gagal
                header("Location: artikel.php?status=hapus_gagal");
            }
            mysqli_stmt_close($stmt_delete);
        } else {
             // Jika prepare statement delete gagal
            header("Location: artikel.php?status=hapus_gagal");
        }
    } else {
        // Jika artikel dengan ID tersebut tidak ditemukan
        header("Location: artikel.php?status=id_tidak_ditemukan");
    }
    mysqli_stmt_close($stmt_select);
} else {
    // Jika prepare statement select gagal
    header("Location: artikel.php?status=gagal");
}

mysqli_close($koneksi);
exit();
?>