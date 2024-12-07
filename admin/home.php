<?php
// Menghubungkan ke database
$koneksi = mysqli_connect("localhost", "root", "", "login");

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Query untuk mengambil berita
$query_berita = "SELECT * FROM berita ORDER BY created_at DESC";
$result_berita = mysqli_query($koneksi, $query_berita);

// Periksa jika query gagal
if (!$result_berita) {
    die('Kesalahan pada query: ' . mysqli_error($koneksi));
}

// Cek jumlah hasil
if (mysqli_num_rows($result_berita) === 0) {
    echo "Tidak ada berita yang tersedia.";
}

// Mulai HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="../css/user.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    
    <style>
        body {
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden; /* Mencegah scroll di body */
        }

        .sidebar {
            width: 250px;
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto; /* Scroll jika konten sidebar lebih panjang */
        }

        .content {
            margin-left: 250px; /* Memberikan ruang untuk sidebar */
            padding: 20px;
            height: 100%; /* Mengatur tinggi konten */
            display: flex;
            flex-direction: column;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .main-content {
            flex-grow: 1; /* Membuat area ini tumbuh untuk mengisi ruang */
            overflow-y: auto; /* Scroll konten */
            padding-right: 15px; /* Menambahkan padding di kanan untuk scrollbar */
        }

        .news-card {
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .news-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 15px;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-footer {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .no-image-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            background-color: #f0f0f0;
            color: #888;
            font-size: 1rem;
        }
       
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 style="font-weight: bold;">Administrator</h2>
        </div>
        <div class="sidebar-menu">
            <a href="home.php" class="menu-item"><i class="bi bi-house-heart-fill" style="margin: 5px"></i> Home</a>
            <a href="Aduan.php" class="menu-item"><i class="bi bi-clipboard2-fill" style="margin: 5px"></i> Aduan</a>
            <a href="statistik.php" class="menu-item"><i class="bi bi-bar-chart-fill" style="margin: 5px"></i> Statistik</a>
            <a href="post_berita.php" class="menu-item"><i class="bi bi-file-earmark-post" style="margin: 5px;"></i> Post</a>
        </div>
        <div class="sidebar-footer">
            <a href="../landing.html" class="menu-item logout"><i class="bi bi-door-open-fill"></i> Logout</a>
        </div>
    </div>

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
            <div class="container">
                <div class="welcome-section">
                    <h1>Halo selamat datang</h1>
                </div>
                <div class="row">
                    <?php if (mysqli_num_rows($result_berita) > 0): ?>
                        <?php while ($berita = mysqli_fetch_assoc($result_berita)): ?>
                            <div class="col-md-4 mb-4">
                                <div class="news-card">
                                    <?php 
                                    $imagePath = 'admin/' . htmlspecialchars($berita['image']);
                                    if (!empty($berita['image']) && file_exists($imagePath)): ?>
                                        <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="Gambar Berita: <?php echo htmlspecialchars($berita['title']); ?>">
                                    <?php else: ?>
                                        <div class="no-image-placeholder">
                                            Gambar tidak tersedia
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Menutup koneksi setelah selesai
mysqli_close($koneksi);
?>