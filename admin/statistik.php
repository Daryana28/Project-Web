<?php
// Mulai session
session_start();

// Periksa apakah pengguna memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

// Ambil data statistik aduan berdasarkan kampung
$query = "SELECT kampung, COUNT(*) AS jumlah FROM aduan GROUP BY kampung";
$result = mysqli_query($koneksi, $query);

$kampung = [];
$jumlah = [];

// Loop hasil query untuk data kampung dan jumlah aduan
while ($row = mysqli_fetch_assoc($result)) {
    $kampung[] = $row['kampung'];
    $jumlah[] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Aduan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/user.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .chart-container {
            width: 80%;
            margin: 20px auto;
            text-align: center;
        }

        canvas {
            max-width: 100%;
            height: auto;
        }

        /* Animasi fade-in */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Administrator</h2>
        </div>
        <div class="sidebar-menu">
            <a href="home.php" class="menu-item"
          ><i class="bi bi-house-heart-fill" style="margin: 5px"></i> Home</a
        >
        <a href="Aduan.php" class="menu-item"
          ><i class="bi bi-clipboard2-fill" style="margin: 5px"></i> Aduan</a
        >
        <a href="statistik.php" class="menu-item"
          ><i class="bi bi-bar-chart-fill" style="margin: 5px"></i> Statistik</a
        >
        <a href="post_berita.php" class="menu-item"
          ><i class="bi bi-file-earmark-post " style="margin: 5px;"></i> Post</a
        >
        </div>
        <div class="sidebar-footer">
            <a href="../landing.html" class="menu-item logout"><i class="bi bi-door-open-fill"></i> Logout</a>
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
        <div class="main-content fade-in"> <!-- Tambahkan kelas fade-in di sini -->
            <h2 style="text-align: center;">Statistik Aduan Berdasarkan Kampung</h2>
            <div class="chart-container">
                <canvas id="statistikChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const kampung = <?php echo json_encode($kampung); ?>;
        const jumlah = <?php echo json_encode($jumlah); ?>;

        // Buat grafik menggunakan Chart.js
        const ctx = document.getElementById('statistikChart').getContext('2d');
        const statistikChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: kampung,
                datasets: [{
                    label: 'Jumlah Aduan',
                    data: jumlah,
                    backgroundColor: [
                        '#ff6384',
                        '#36a2eb',
                        '#ffce56',
                        '#4caf50',
                        '#ff9f40'
                    ],
                    borderColor: '#ccc',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
// Menutup koneksi setelah selesai
mysqli_close($koneksi);
?>