<?php
// Mulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['name'])) {
    header("Location: signin.php");
    exit();
}

// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "siprakyat";

$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID berita dari parameter URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Berita tidak ditemukan.";
    exit();
}

$berita_id = intval($_GET['id']); // Pastikan ID adalah integer

// Query untuk mengambil data berita berdasarkan ID
$query = "SELECT * FROM berita WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $berita_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Berita tidak ditemukan.";
    exit();
}

$berita = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .news-detail {
            margin-top: 50px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .news-image {
            max-height: 400px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .news-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .news-content {
            font-size: 1rem;
            line-height: 1.8;
            color: #555;
        }
        .news-footer {
            margin-top: 20px;
            color: #777;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="news-detail">
            <?php if (!empty($berita['image']) && file_exists('admin/' . $berita['image'])): ?>
                <img src="admin/<?php echo htmlspecialchars($berita['image']); ?>" alt="Gambar Berita" class="img-fluid news-image">
            <?php else: ?>
                <div class="text-center text-muted">Gambar tidak tersedia</div>
            <?php endif; ?>

            <h1 class="news-title"><?php echo htmlspecialchars($berita['title']); ?></h1>
            <div class="news-content">
                <?php echo nl2br(htmlspecialchars($berita['content'])); ?>
            </div>
            <div class="news-footer">
                Diposting pada: <?php echo htmlspecialchars($berita['created_at']); ?>
            </div>
            <a href="dashboard.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi
mysqli_close($koneksi);
?>