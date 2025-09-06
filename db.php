<?php
// Konfigurasi koneksi ke database
$dbHost = 'localhost';
$dbUser = 'root'; // Ganti dengan username database Anda
$dbPass = '';     // Ganti dengan password database Anda
$dbName = 'toko_komputer';

// Membuat koneksi
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
