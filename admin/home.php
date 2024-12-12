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

// Ambil jumlah total aduan
$query_total_aduan = "SELECT COUNT(*) AS total_aduan FROM aduan";
$result_total_aduan = mysqli_query($koneksi, $query_total_aduan);
$total_aduan = mysqli_fetch_assoc($result_total_aduan)['total_aduan'];

// Ambil jumlah kampung dengan aduan
$query_kampung_aduan = "SELECT COUNT(DISTINCT kampung) AS kampung_aduan FROM aduan";
$result_kampung_aduan = mysqli_query($koneksi, $query_kampung_aduan);
$kampung_aduan = mysqli_fetch_assoc($result_kampung_aduan)['kampung_aduan'];

// Ambil jumlah total post berita
$query_post_berita = "SELECT COUNT(*) AS total_berita FROM berita";
$result_post_berita = mysqli_query($koneksi, $query_post_berita);
$total_post_berita = mysqli_fetch_assoc($result_post_berita)['total_berita'];

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
        /* Statistik singkat */
        .stat-card {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease;
            width: 100%;
        }

        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .stat-card h2 {
            font-size: 2.5rem;
            margin: 0;
            color: #007bff;
        }

        .stat-card p {
            margin: 5px 0 0;
            font-size: 1.2rem;
            color: #555;
        }

        /* Grid layout untuk 3 statistik */
        .row.text-center {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            gap: 20px;
            padding: 0 5%;
            margin-bottom: 30px;
        }

        .col-md-3 {
            flex: 1;
            max-width: 32%; /* Membuat tiga card sejajar di layar penuh */
        }

        /* Statistik aduan besar */
        .large-stat-card {
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            margin: 0 auto;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .large-stat-card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .large-stat-card canvas {
            max-width: 100%;
            height: auto;
        }

        /* Responsivitas */
        @media (max-width: 768px) {
            .row.text-center {
                flex-wrap: wrap;
                gap: 20px;
            }

            .col-md-3 {
                max-width: 100%;
            }

            .large-stat-card {
                width: 100%;
                height: auto;
            }
        }

        /* Animasi fade-in */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease-in-out forwards;
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
        
        /* Ensure content fills the available space */
        .content {
            flex: 1;
            padding: 150px;
            padding-left: 20px;
            padding-bottom: 20px;
            
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Administrator</h2>
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

        <div class="container mt-4">
            <!-- Statistik Singkat -->
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h2><?php echo $total_aduan; ?></h2>
                        <p>Total Aduan</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h2><?php echo $kampung_aduan; ?></h2>
                        <p>Kampung dengan Aduan</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h2><?php echo $total_post_berita; ?></h2>
                        <p>Total Post Berita</p>
                    </div>
                </div>
            </div>

            <!-- Statistik Aduan Besar -->
            <div class="large-stat-card fade-in">
                <h3>Distribusi Aduan per Kampung</h3>
                <canvas id="aduanChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Konfigurasi Chart.js
        const ctx = document.getElementById('aduanChart').getContext('2d');
        const aduanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($kampung); ?>,
                datasets: [{
                    label: 'Jumlah Aduan',
                    data: <?php echo json_encode($jumlah); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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