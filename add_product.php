<?php
// Menampilkan semua error untuk membantu debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image_name = '';

    // Logika untuk upload gambar
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (!is_writable($target_dir)) {
            $error = "Error: Folder 'uploads' tidak dapat ditulisi. Periksa izin folder.";
        } else {
            $image_name = uniqid() . '_' . basename($_FILES["product_image"]["name"]);
            $target_file = $target_dir . $image_name;

            if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $error = "Terjadi kesalahan saat mengunggah gambar.";
                $image_name = ''; // Kosongkan nama gambar jika gagal
            }
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO products (product_name, price, image) VALUES ('$product_name', '$price', '$image_name')";

        if (mysqli_query($conn, $sql)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error saat menyimpan ke database: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Toko Komputer</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container form-container">
        <h2>Tambah Produk Baru</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Nama Produk</label>
                <input type="text" name="product_name" id="product_name" required>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" name="price" id="price" required>
            </div>
            <div class="form-group">
                <label for="product_image">Gambar Produk</label>
                <input type="file" name="product_image" id="product_image">
            </div>
            <button type="submit" class="btn">Simpan Produk</button>
        </form>
        <a href="dashboard.php" class="link">Batal</a>
    </div>
</body>

</html>