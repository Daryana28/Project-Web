<?php
session_start(); // Memastikan sesi dimulai

// Sambungkan ke database
$koneksi = mysqli_connect("localhost", "root", "", "login");

// Periksa koneksi
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil informasi pengguna
$email = $_SESSION['email'] ?? ''; // Pastikan email ada
$name = $_SESSION['name'] ?? 'Pengguna'; // Default jika tidak ada

// Query untuk mengambil berita
$query_berita = "SELECT * FROM berita ORDER BY created_at DESC";
$result_berita = mysqli_query($koneksi, $query_berita);

// Periksa jika query gagal
if (!$result_berita) {
    die('Error query: ' . mysqli_error($koneksi));
}

// Ambil notifikasi dari tabel aduan untuk user yang sedang login
$query = "SELECT * FROM aduan WHERE user_email = '$email' AND notification IS NOT NULL AND status = 'Rejected'";
$result = mysqli_query($koneksi, $query);
$notification_count = mysqli_num_rows($result);

// Tandai notifikasi sebagai sudah dibaca
if (isset($_GET['markAsRead']) && $_GET['markAsRead'] === '1') {
    $update_query = "UPDATE aduan SET notification = NULL WHERE user_email = '$email' AND notification IS NOT NULL";
    mysqli_query($koneksi, $update_query);
    header("Location: aduan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - User</title>
    <link rel="stylesheet" href="./css/dashbord.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* CSS Styles */
        .news-card {
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.5s forwards;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
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
        .notification-icon {
            position: relative;
            cursor: pointer;
            margin-left: 20px;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            font-size: 12px;
            border-radius: 50%;
            padding: 2px 6px;
        }
        .notification-dropdown {
            position: absolute;
            top: 40px;
            right: 0;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 300px;
            display: none;
            z-index: 1000;
        }
        .notification-dropdown.active {
            display: block;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item:hover {
            background-color: #f9f9f9;
        }
        .notification-header {
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background-color: #f7f7f7;
        }
        .notification-footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            background-color: #f7f7f7;
            border-top: 1px solid #ddd;
        }
    </style>
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
        <div class="sidebar-footer">
            <a href="landing.html" class="menu-item logout">
                <i class="bi bi-door-open-fill"></i> Logout
            </a>
        </div>
        
    </div>
    <div class="vertical-line"></div>
    <div class="content">
        <div class="content-header fade-in">
            <div class="header-logo">
                <h2>SIP<b>Rakyat!</b></h2>
            </div>
            <div class="icons">
                <i class="bi bi-person-circle"></i>
                <span style="margin-left: 10px;">Halo</span>
                <div class="notification-icon" onclick="toggleNotifications()">
                    <i class="bi bi-bell-fill"></i>
                    <?php if ($notification_count > 0): ?>
                        <span class="notification-badge"><?php echo $notification_count; ?></span>
                    <?php endif; ?>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">Notifikasi</div>
                        <?php if ($notification_count > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <div class="notification-item">
                                    <strong>Jenis Aduan:</strong> <?php echo htmlspecialchars($row['jenis_aduan']); ?><br>
                                    <span>Status: <strong style="color: red;">Ditolak</strong></span>
                                </div>
                            <?php endwhile; ?>
                            <div class="notification-footer">
                                <a href="?markAsRead=1">Tandai Semua Dibaca</a>
                            </div>
                        <?php else: ?>
                            <div class="notification-item">Tidak ada notifikasi.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="container mt-5">
                <?php if (isset($_GET['success'])): ?>
                    <div style="color: green; text-align: center; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
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
    </div>
    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('active');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Menutup koneksi setelah selesai
mysqli_close($koneksi);
?>