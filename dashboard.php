<?php
// Sambungkan ke database
$koneksi = mysqli_connect("localhost", "root", "", "login");

// Periksa koneksi
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query untuk mengambil berita
$query_berita = "SELECT * FROM berita ORDER BY created_at DESC";
$result_berita = mysqli_query($koneksi, $query_berita);

// Periksa jika query gagal
if (!$result_berita) {
    die('Error query: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard-User</title>
    <link rel="stylesheet" href="./css/dashbord.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 style="font-weight: bold;">User</h2>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item">
                <i class="bi bi-house-heart-fill" style="margin: 5px;"></i> Home
            </a>
            <a href="Aduan.php" class="menu-item">
                <i class="bi bi-clipboard2-fill" style="margin: 5px;"></i>Buat Aduan
            </a>
            <a href="riwayat_aduan.php" class="menu-item">
                <i class="bi bi-clock-fill" style="margin: 5px;"></i> Riwayat Aduan
            </a>
        </div>
        <!-- Tambahkan logout di bagian bawah -->
        <div class="sidebar-footer">
            <a href="../landing.html" class="menu-item logout">
                <i class="bi bi-door-open-fill"></i> Logout
            </a>
        </div>
    </div>
    <div class="vertical-line"></div>
    <div class="content">
        <div class="content-header">
            <div class="header-logo">
                <h2>SIP<b>Rakyat!</b></h2>
            </div>
            <div class="icons">
                <i class="bi bi-person-circle"></i>
            </div>
        </div>
        <div class="main-content">
            <!-- Konten utama akan ditampilkan di sini -->
            <div class="container">
                <div class="welcome-section">
                    <p>Halo selamat datang,</p>
                </div>
                <!-- Tambahkan berita di bawah ini -->
                <div class="row">
                    <?php if (mysqli_num_rows($result_berita) > 0): ?>
                        <?php while ($berita = mysqli_fetch_assoc($result_berita)): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <?php if (!empty($berita['image']) && file_exists('admin/' . $berita['image'])): ?>
                                        <img src="<?php echo 'admin/' . htmlspecialchars($berita['image']); ?>" class="card-img-top" alt="Gambar Berita" style="max-height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top" style="height: 200px; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center;">
                                            <p>Gambar tidak tersedia</p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($berita['title']); ?></h5>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($berita['content'], 0, 150)) . '...'); ?></p>
                                        <a href="berita_detail.php?id=<?php echo $berita['id']; ?>" class="btn btn-primary">Baca Selengkapnya</a>
                                    </div>
                                    <div class="card-footer text-muted">
                                        Diposting pada: <?php echo htmlspecialchars($berita['created_at']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Tidak ada berita yang tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> 
    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('active');
        }
    </script>
</body>
</html>

<?php
// Menutup koneksi setelah selesai
mysqli_close($koneksi);
?>
