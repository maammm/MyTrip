<?php
// Memastikan sesi telah dimulai dan pengguna sudah login
include_once 'ceksession.php';
include_once 'function.php';

// Pastikan request adalah metode POST untuk keamanan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form (termasuk yang dari CKEditor yang sudah di-sync oleh JavaScript)
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $gambar_lama = $_POST['gambar_lama'];
    
    // Siapkan nama file gambar untuk diupdate. Defaultnya adalah gambar yang lama.
    $picture_to_update = $gambar_lama;

    // Cek apakah ada file gambar baru yang di-upload
    // $_FILES['picture']['error'] == 0 berarti upload berhasil tanpa error
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $target_dir = "uploads/"; // Folder tujuan untuk menyimpan gambar
        
        // Buat nama file yang unik untuk mencegah nama file yang sama
        $nama_gambar_baru = uniqid() . '-' . basename($_FILES["picture"]["name"]);
        $target_file = $target_dir . $nama_gambar_baru;

        // Pindahkan file yang baru di-upload ke folder 'uploads'
        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
            // Jika upload file baru berhasil, hapus file gambar yang lama jika ada
            if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                unlink($target_dir . $gambar_lama);
            }
            // Gunakan nama gambar yang baru untuk diupdate ke database
            $picture_to_update = $nama_gambar_baru;
        }
    }

    // Siapkan query UPDATE menggunakan prepared statement untuk mencegah SQL Injection
    $query = "UPDATE article SET title = ?, content = ?, picture = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        // Bind parameter ke query: s = string, i = integer
        mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $picture_to_update, $id);

        // Eksekusi query
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, redirect kembali ke halaman artikel dengan status sukses
            header("Location: artikel.php?status=edit_sukses");
        } else {
            // Jika gagal, redirect dengan status gagal
            header("Location: artikel.php?status=edit_gagal");
        }
        mysqli_stmt_close($stmt);
    } else {
        // Jika query prepare gagal
        header("Location: artikel.php?status=gagal");
    }

    mysqli_close($koneksi);
    exit();

} else {
    // Jika halaman ini diakses langsung (bukan via POST), tendang ke halaman utama
    header("Location: index.php");
    exit();
}
?>