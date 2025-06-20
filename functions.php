<?php
// functions.php

date_default_timezone_set('Asia/Jakarta');

// Fungsi baru untuk memformat tanggal & waktu di kartu artikel
function format_card_date_time($datetime_string) {
    if (!$datetime_string) return '';
    
    // Buat objek DateTime dari string YYYY-MM-DD HH:MM:SS
    $timestamp = new DateTime($datetime_string);
    
    // Format tanggal ke format Indonesia lengkap dengan waktu
    // Pola 'eeee, d MMMM yyyy HH:mm' akan menghasilkan: 'Kamis, 19 Juni 2025 13:05'
    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Jakarta', IntlDateFormatter::GREGORIAN, 'eeee, d MMMM yyyy HH:mm');
    
    return $formatter->format($timestamp);
}

// Fungsi baru untuk memformat meta di halaman detail
function format_detail_meta($datetime_string, $author_name) {
    $tanggal_lengkap = format_card_date_time($datetime_string);
    return "Diperbarui pada " . $tanggal_lengkap . " oleh <strong>" . htmlspecialchars($author_name) . "</strong>";
}
?>