<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  // Jika belum login, hentikan proses
  die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Memanggil pustaka FPDF yang sudah diunduh
// Pastikan path-nya benar sesuai dengan struktur folder Anda
require('fpdf/fpdf.php');

// Ambil semua produk dari database
$sql = "SELECT product_name, price FROM products ORDER BY product_name ASC";
$result = mysqli_query($conn, $sql);

// Buat instance objek PDF
// P = Portrait, mm = unit milimeter, A4 = ukuran kertas
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Atur font untuk judul
$pdf->SetFont('Arial', 'B', 16);
// Buat sel untuk judul. Lebar 190mm, tinggi 10mm, teks, tanpa border (0), pindah baris (1), rata tengah ('C')
$pdf->Cell(190, 10, 'Daftar Produk Komputer', 0, 1, 'C');
$pdf->Ln(10); // Tambah spasi

// Atur font untuk header tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230); // Warna latar belakang header

// Buat header tabel
$pdf->Cell(15, 10, 'No', 1, 0, 'C', true);
$pdf->Cell(125, 10, 'Nama Produk', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Harga', 1, 1, 'C', true); // Angka 1 terakhir berarti pindah baris

// Atur font untuk isi tabel
$pdf->SetFont('Arial', '', 12);

if ($result && mysqli_num_rows($result) > 0) {
  $no = 1;
  while ($row = mysqli_fetch_assoc($result)) {
    // Tambahkan baris data
    $pdf->Cell(15, 10, $no++, 1, 0, 'C');
    $pdf->Cell(125, 10, $row['product_name'], 1, 0, 'L');
    $pdf->Cell(50, 10, 'Rp ' . number_format($row['price'], 0, ',', '.'), 1, 1, 'R');
  }
} else {
  $pdf->Cell(190, 10, 'Tidak ada data produk.', 1, 1, 'C');
}

// Keluarkan PDF ke browser dan paksa unduh dengan nama 'daftar-produk.pdf'
$pdf->Output('D', 'daftar-produk.pdf');
exit();
