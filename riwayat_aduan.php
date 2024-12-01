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

// Koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil email pengguna dari session
$email = $_SESSION['email'];

// Query untuk mengambil data aduan dengan status 'Rejected' dan ada notifikasi
$query = "SELECT * FROM aduan WHERE user_email = '$email' AND notification IS NOT NULL AND status = 'Rejected'";

// Debugging: Tampilkan query yang dijalankan
// echo $query;  // Un-comment jika ingin melihat query

$result = mysqli_query($koneksi, $query);

// Debugging: Cek hasil query
if (mysqli_num_rows($result) > 0) {
    // Jika ada data
    $notification_count = mysqli_num_rows($result);
} else {
    // Jika tidak ada data
    $notification_count = 0;
}

// Ambil notifikasi dari tabel aduan untuk user yang sedang login
$query = "SELECT * FROM aduan WHERE user_email = '$email' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

// Tandai notifikasi sebagai sudah dibaca jika ada parameter 'markAsRead' di URL
if (isset($_GET['markAsRead']) && $_GET['markAsRead'] === '1') {
    $update_query = "UPDATE aduan SET notification = NULL WHERE user_email = '$email' AND notification IS NOT NULL";
    mysqli_query($koneksi, $update_query);
    header("Location: riwayat_aduan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aduan</title>
    <link rel="stylesheet" href="./css/user.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        input[type="text"], select, textarea, input[type="file"] {
            border: 1px dashed #ff4d73 !important;
            outline: none;
            margin-bottom: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .status-accepted {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
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
                <i class="bi bi-clipboard2-fill" style="margin: 5px;"></i> Buat Aduan
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
        <div class="content-header">
            <div class="header-logo">
                <h2>SIP<b>Rakyat!</b></h2>
            </div>
            <div class="icons">
                <i class="bi bi-person-circle"></i>
                <span style="margin-left: 10px;">Halo, <?php echo htmlspecialchars($name); ?>!</span>

                <!-- Ikon Notifikasi -->
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
                <h2>Riwayat Aduan Anda</h2>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Aduan</th>
                                <th>Kampung</th>
                                <th>Isi Aduan</th>
                                <th>Tanggal Pengaduan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['judul_aduan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kampung']); ?></td>
                                    <td><?php echo htmlspecialchars($row['isi_aduan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <?php
                                        if (strtolower($row['status']) === 'accepted') {
                                            echo "<span class='status-accepted'>Diterima</span>";
                                        } elseif (strtolower($row['status']) === 'rejected') {
                                            echo "<span class='status-rejected'>Ditolak</span>";
                                        } else {
                                            echo "<span class='status-pending'>Menunggu</span>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Tidak ada riwayat aduan.</p>
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
