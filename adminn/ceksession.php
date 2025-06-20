<?php
// Pastikan function.php di-include pertama untuk koneksi dan session_start
include_once 'function.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}
?>