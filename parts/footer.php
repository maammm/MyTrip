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