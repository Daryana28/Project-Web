<?php
// Mulai session untuk mengambil data login
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['email'])) {
    // Jika belum login, redirect ke halaman login
    header("Location: signin.php");
    exit();
}

// Ambil nama pengguna dan email dari session
$name = $_SESSION['name'];
$email = $_SESSION['email'];

// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "login";

$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil notifikasi dari tabel aduan untuk user yang sedang login
$query = "SELECT * FROM aduan WHERE user_email = '$email' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aduan</title>
    <link rel="stylesheet" href="./css/user.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
            <h2>User</h2>
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
            <a href="landing.html" class="menu-item logout"><i class="bi bi-door-open-fill"></i> Logout</a>
        </div>
    </div>

    <div class="vertical-line"></div>

    <div class="content">
        <div class="content-header">
            <div class="header-logo">
                <h2>SIP<b>Rakyat!</b></h2>
            </div>
            <div class="icons">
                <i class="bi bi-person-circle"></i> Halo, <?php echo htmlspecialchars($name); ?>
            </div>
        </div>
        <div class="main-content">
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
</body>
</html>
