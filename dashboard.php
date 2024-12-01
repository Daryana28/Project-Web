<?php
// Mulai session untuk mengambil data login
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['name'])) {
    // Jika belum login, redirect ke halaman login
    header("Location: signin.php");
    exit();
}

// Ambil nama pengguna dari session
$name = $_SESSION['name'];

// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "";
$nama_db  = "login";

$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil notifikasi dari tabel aduan untuk user yang sedang login
$email = $_SESSION['email'];
$query = "SELECT * FROM aduan WHERE user_email = '$email' AND notification IS NOT NULL AND status = 'Rejected'";
$result = mysqli_query($koneksi, $query);
$notification_count = mysqli_num_rows($result);

// Tandai notifikasi sebagai sudah dibaca
if (isset($_GET['markAsRead']) && $_GET['markAsRead'] === '1') {
    $update_query = "UPDATE aduan SET notification = NULL WHERE user_email = '$email' AND notification IS NOT NULL";
    mysqli_query($koneksi, $update_query);
    header("Location: dashboard.php"); // Refresh halaman setelah menandai sebagai terbaca
    exit();
}

// Ambil berita dari database
$query_berita = "SELECT * FROM berita ORDER BY created_at DESC";
$result_berita = mysqli_query($koneksi, $query_berita);
if (!$result_berita) {
    die("Error mengambil data berita: " . mysqli_error($koneksi)); // Cek jika query gagal
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard-user</title>
    <link rel="stylesheet" href="./css/dashbord.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
        rel="stylesheet"
    />
    <style>
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
        .news-container {
            margin-top: 20px;
        }

        .news-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .news-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .news-content {
            margin: 10px 0;
            font-size: 14px;
            color: #555;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa;
            padding-top: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .vertical-line {
            position: fixed;
            top: 0;
            left: 250px;
            height: 100%;
            width: 1px;
            background-color: #ddd;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
            overflow: auto;
        }

        .news-container {
            max-width: 800px;
            margin: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .name {
            font-size: 24px;
            font-weight: bold;
            color:  #ff4d73;
            font-family: 'Arial', sans-serif;
            margin: 20px 0;
        }

        .header h2 {
            font-size: 20px;
            font-weight: 400;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>User</h2>
    </div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item">
            <i class="bi bi-house-heart-fill"></i> Home
        </a>
        <a href="Aduan.php" class="menu-item">
            <i class="bi bi-clipboard2-fill"></i> Buat Aduan
        </a>
        <a href="riwayat_aduan.php" class="menu-item">
            <i class="bi bi-clock-fill"></i> Riwayat Aduan
        </a>
    </div>
    <div class="sidebar-footer">
        <a href="landing.html" class="menu-item logout">
            <i class="bi bi-door-open-fill"></i> Logout
        </a>
    </div>
</div>

<div class="main-content">
    <header class="header">
        <h2>Halo selamat datang,</h2>
        <h1 class="name">Berita terbaru saat ini<span>!</span></h1>
    </header>
                <!-- Tambahkan berita di bawah ini -->
                <div class="news-container">
                    <?php if (mysqli_num_rows($result_berita) > 0): ?>
                        <?php while ($berita = mysqli_fetch_assoc($result_berita)): ?>
                            <div class="news-item">
                                <h3 class="news-title"><?php echo htmlspecialchars($berita['title']); ?></h3>
                                <p class="news-content"><?php echo nl2br(htmlspecialchars($berita['content'])); ?></p>
                                <?php if (!empty($berita['image']) && file_exists('admin/' . $berita['image'])): ?>
                                    <img src="<?php echo 'admin/' . htmlspecialchars($berita['image']); ?>" alt="Gambar Berita" style="max-width: 100px; max-height: 100px; width: auto; height: auto; margin-top: 5px;">
                                <?php else: ?>
                                    <p>Gambar tidak tersedia.</p>
                                <?php endif; ?>
                                <small>Diposting pada: <?php echo htmlspecialchars($berita['created_at']); ?></small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Tidak ada berita yang tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
