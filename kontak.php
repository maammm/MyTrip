<?php
// Mulai session di baris paling atas agar bisa menampilkan notifikasi
session_start(); 

// Panggil template header
require 'parts/header.php'; 
?>

<main class="main-content">
    <div class="static-page">
        <div class="page-header">
            <h2>Hubungi Kami</h2>
        </div>

        <?php
        // --- BLOK UNTUK MENAMPILKAN NOTIFIKASI ---
        if (isset($_SESSION['notification'])) {
            $notification_type = $_SESSION['notification']['type']; // 'success' or 'error'
            $notification_message = $_SESSION['notification']['message'];
            // Tampilkan notifikasi
            echo "<div class='notification {$notification_type}'>{$notification_message}</div>";
            // Hapus notifikasi dari session agar tidak tampil lagi
            unset($_SESSION['notification']);
        }
        ?>

        <p>
            Punya pertanyaan, kritik, saran, atau ingin berkolaborasi? Jangan ragu untuk menghubungi kami melalui form di bawah ini atau melalui kanal media sosial kami. Kami senang mendengar dari Anda!
        </p>

        <form action="kirim_pesan.php" method="POST" class="contact-form">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="subject">Subjek</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Pesan Anda</label>
                <textarea id="message" name="message" rows="6" required></textarea>
            </div>
            <button type="submit" class="button-primary">Kirim Pesan</button>
        </form>
    </div>
</main>

<?php 
// Panggil template sidebar dan footer
require 'parts/sidebar.php'; 
require 'parts/footer.php'; 
?>