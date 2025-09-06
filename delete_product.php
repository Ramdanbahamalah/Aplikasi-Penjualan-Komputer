<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil nama file gambar untuk dihapus dari folder
    $sql_select = "SELECT gambar FROM products WHERE id = $id";
    $result = mysqli_query($conn, $sql_select);
    if ($row = mysqli_fetch_assoc($result)) {
        $gambar = $row['gambar'];
        if (!empty($gambar) && file_exists('uploads/' . $gambar)) {
            unlink('uploads/' . $gambar);
        }
    }

    // Hapus record dari database
    $sql_delete = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $sql_delete)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
