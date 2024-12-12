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
$pass_db  = "mysql123";
$nama_db  = "siprakyat";

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
    header("Location: dashboard.php");
    exit();
}

// Ambil berita yang diposting oleh admin
$query_berita = "SELECT * FROM berita ORDER BY created_at DESC";
$result_berita = mysqli_query($koneksi, $query_berita);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Berita</title>
    <link rel="stylesheet" href="./css/user.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        input[type="text"], select, textarea, input[type="file"] {
            border: 1px dashed #ff4d73 !important;
            outline: none;
            margin-bottom: 20px;
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
            background: #fff;
            flex-direction: column;
    justify-content: space-between;
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
            padding: 5px;
            flex: 1;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-title {
            font-size: 1.0rem;
            font-weight: bold;
        }
        .card-footer {
            font-size: 0.875rem;
            color: #6c757d;
            text-align: center;
    background-color: #f8f9fa;
    padding: 10px;
        }
        .btn-primary {
    width: 100%; /* Membuat tombol mengisi lebar penuh */
    margin-top: auto; /* Menyelaraskan posisi tombol di bagian bawah card-body */
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
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .row-equal {
            display: flex;
            flex-wrap: wrap;
        }
        .row-equal > [class*='col-'] {
            display: flex;
            flex-direction: column;
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

    <div class="vertical-line"></div> <!-- Separator between sidebar and content -->

<div class="content">
    <div class="content-header">
        <div class="header-logo">
            <h2>SIP<b>Rakyat!</b></h2> 
        </div>
        <div class="icons">
            <span>Halo, <b><?php echo htmlspecialchars($name); ?></b></span>
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
                <div class="card-footer text-muted d-flex justify-content-between">
                    <small>Diposting pada: <?php echo htmlspecialchars($berita['created_at']); ?></small>
                    <!-- <form action="delete_berita.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?');">
                        <input type="hidden" name="id" value="<?php echo $berita['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form> -->
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
