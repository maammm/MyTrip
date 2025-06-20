<?php
session_start(); 

// Panggil template header
require 'parts/header.php'; 
?>

    <main class="main-content">
        <div class="static-page">
            <div class="page-header">
                <h2>Tentang Jalan Santai</h2>
            </div>
            <img src="images/tentang-kami.jpg" alt="Tim Jalan Santai" class="static-page-image">
            <h3>Selamat Datang di Perjalanan Kami!</h3>
            <p><strong>Jalan Santai</strong> lahir dari kecintaan kami terhadap keindahan dan keunikan yang tersembunyi di setiap sudut Indonesia, khususnya di Malang Raya yang menakjubkan. Kami adalah sekelompok sahabat yang gemar berpetualang, mencicipi kuliner lokal, dan mengabadikan momen melalui tulisan dan foto.</p>
            <p>Blog ini adalah catatan digital dari setiap langkah kami. Tujuan kami sederhana: berbagi pengalaman, memberikan tips perjalanan yang jujur, dan menginspirasi Anda untuk mulai menjelajahi dunia di sekitar Anda.</p>
            <h3>Misi Kami</h3>
            <ul>
                <li><strong>Menginspirasi:</strong> Menunjukkan bahwa petualangan seru tidak harus mahal atau jauh.</li>
                <li><strong>Menginformasikan:</strong> Memberikan ulasan dan panduan praktis seputar destinasi wisata.</li>
                <li><strong>Menghubungkan:</strong> Membangun komunitas para pejalan yang saling berbagi cerita.</li>
            </ul>
        </div>
    </main>

    <?php
    // parts/footer.php
    ?>
        </div> <footer class="main-footer">
            <div class="container">
                <p>Copyright © MyTrip 2025</p>
            </div>
        </footer>
    </body>
    </html>
    <?php
    // Menutup koneksi database jika variabel $conn ada
    if (isset($conn)) {
        $conn->close();
    }
    ?>