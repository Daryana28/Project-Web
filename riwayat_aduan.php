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

// Koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses penghapusan data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM aduan WHERE id = $delete_id AND user_email = '{$_SESSION['email']}'";
    if (mysqli_query($koneksi, $delete_query)) {
        header("Location: riwayat_aduan.php?success=Data berhasil dihapus");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Ambil email pengguna dari session
$email = $_SESSION['email'];

// Query untuk mengambil data aduan dengan status 'Rejected' dan ada notifikasi
$query = "SELECT * FROM aduan WHERE user_email = '$email' AND notification IS NOT NULL AND status = 'Rejected'";
$result = mysqli_query($koneksi, $query);

// Hitung jumlah notifikasi
$notification_count = mysqli_num_rows($result);

// Ambil data aduan untuk riwayat
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
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
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
        <div class="main-content fade-in">
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
                                <th>Action</th>
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
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus aduan ini?')">Delete</button>
                                        </form>
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