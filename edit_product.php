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

// Cek apakah ada ID produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$productId = $_GET['id'];
$error = '';

// Ambil data produk yang akan diedit
$sql_select = "SELECT product_name, price, image FROM products WHERE id = '$productId'";
$result = mysqli_query($conn, $sql_select);
if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
    $old_image = $product['image'];
} else {
    // Produk tidak ditemukan
    header("Location: dashboard.php");
    exit();
}


// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $new_image_name = $old_image;

    // Cek apakah ada gambar baru yang diupload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "uploads/";
        $new_image_name = uniqid() . '_' . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $new_image_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada
            if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                unlink($target_dir . $old_image);
            }
        } else {
            $error = "Gagal mengunggah gambar baru.";
            $new_image_name = $old_image; // Kembalikan ke nama gambar lama jika gagal
        }
    }

    if (empty($error)) {
        $sql_update = "UPDATE products SET product_name='$product_name', price='$price', image='$new_image_name' WHERE id='$productId'";
        if (mysqli_query($conn, $sql_update)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error saat memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Toko Komputer</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container form-container">
        <h2>Edit Produk</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="edit_product.php?id=<?php echo $productId; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Nama Produk</label>
                <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_image">Ganti Gambar Produk (Opsional)</label>
                <?php if (!empty($old_image) && file_exists('uploads/' . $old_image)): ?>
                    <p>Gambar saat ini: <img src="uploads/<?php echo $old_image; ?>" width="100"></p>
                <?php endif; ?>
                <input type="file" name="product_image" id="product_image">
            </div>
            <button type="submit" class="btn">Update Produk</button>
        </form>
        <a href="dashboard.php" class="link">Batal</a>
    </div>
</body>

</html>