<?php
// function.php sudah mengurus session_start() dan koneksi ($koneksi)
include_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "<script>alert('Username dan password tidak boleh kosong!'); window.location='login.php';</script>";
        exit;
    }

    // Gunakan prepared statement untuk keamanan
    // Hapus 'photo' dari SELECT query
    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password FROM admin WHERE username = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($admin && password_verify($password, $admin['password'])) {
            // Menyimpan data ke dalam session (tanpa 'photo')
            $_SESSION['admin'] = [
                'id' => $admin['id'],
                'username' => $admin['username']
            ];
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Login gagal! Username atau password salah.'); window.location='login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.'); window.location='login.php';</script>";
        error_log("MySQLi prepare failed: " . mysqli_error($koneksi));
        exit;
    }

} else {
    header("Location: login.php");
    exit;
}
?>