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

// Cek apakah ID produk ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: dashboard.php");
  exit();
}

$productId = $_GET['id'];

// Ambil detail produk dari database
$sql = "SELECT product_name, price, image, created_at FROM products WHERE id = '$productId'";
$result = mysqli_query($conn, $sql);

// Cek apakah produk ditemukan
if ($result && mysqli_num_rows($result) > 0) {
  $product = mysqli_fetch_assoc($result);
} else {
  header("Location: dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk: <?php echo htmlspecialchars($product['product_name']); ?></title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container detail-container">
    <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>

    <?php if (!empty($product['image']) && file_exists('uploads/' . $product['image'])): ?>
      <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
    <?php else: ?>
      <p>Gambar tidak tersedia</p>
    <?php endif; ?>

    <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>

    <p class="date">Ditambahkan pada: <?php echo date("d F Y, H:i", strtotime($product['created_at'])); ?></p>

    <a href="dashboard.php" class="back-link">Kembali ke Dashboard</a>
  </div>
</body>

</html>