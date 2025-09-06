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

// Ambil informasi user yang sedang login
$userId = $_SESSION['user_id'];
$userQuery = "SELECT username FROM users WHERE id = '$userId'";
$userResult = mysqli_query($conn, $userQuery);
$username = "Pengguna"; // Default
if ($userResult && mysqli_num_rows($userResult) > 0) {
    $user = mysqli_fetch_assoc($userResult);
    $username = $user['username'];
}

// Logika untuk pencarian produk
$search_term = '';
$sql_where = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $safe_search_term = mysqli_real_escape_string($conn, $search_term);
    // Menambahkan klausa WHERE untuk memfilter berdasarkan nama produk
    $sql_where = " WHERE product_name LIKE '%$safe_search_term%'";
}

// Ambil produk dari database, dengan atau tanpa filter pencarian
$sql = "SELECT id, product_name, price, image FROM products" . $sql_where . " ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Komputer</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container dashboard-container">
        <div class="header">
            <h1>Manajemen Produk</h1>
            <div class="user-info">
                <span>Halo, <strong><?php echo htmlspecialchars($username); ?></strong>!</span>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="toolbar">
            <div class="toolbar-actions">
                <a href="add_product.php" class="btn btn-add">Tambah Produk Baru</a>
                <a href="export_pdf.php" class="btn btn-export" target="_blank">Export ke PDF</a>
            </div>
            <div class="toolbar-search">
                <form action="dashboard.php" method="get">
                    <input type="text" name="search" placeholder="Cari nama produk..." value="<?php echo htmlspecialchars($search_term); ?>">
                    <button type="submit">Cari</button>
                    <?php if (!empty($search_term)): ?>
                        <a href="dashboard.php" class="btn-clear-search">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <?php else: ?>
                                    <span>Gambar tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                            <td class="actions">
                                <a href="detail_product.php?id=<?php echo $row['id']; ?>" class="detail">Detail</a>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-product">
                            <?php if (!empty($search_term)): ?>
                                Produk dengan nama "<?php echo htmlspecialchars($search_term); ?>" tidak ditemukan.
                            <?php else: ?>
                                Belum ada produk. Silakan tambahkan produk baru.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>