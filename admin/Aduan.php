<?php
// Mulai session
session_start();

// Periksa apakah pengguna memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php"); // Redirect ke halaman login jika bukan admin
    exit();
}

// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "login";

$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data dari tabel aduan
$query = "SELECT * FROM aduan ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aduan-Admin</title>
    <link rel="stylesheet" href="../css/user.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
                vertical-align: top;
            }

            th {
                background-color: #f2f2f2;
            }

            img {
                max-width: 50px;
                max-height: 50px;
            }

            /* Sembunyikan kolom Status dan Action saat mencetak */
            th:nth-child(7), th:nth-child(8),
            td:nth-child(7), td:nth-child(8) {
                display: none;
            }

            .sidebar, .content-header, footer, .action-icons {
                display: none;
            }
        }
    </style>
    <script>
      // Fungsi untuk mencetak aduan spesifik
      function cetakAduan(rowId) {
        const row = document.getElementById(rowId).outerHTML;
        const originalContent = document.body.innerHTML;

        // Ganti konten dengan hanya baris yang dipilih dan kolom yang diinginkan
        document.body.innerHTML = `
          <html>
            <head>
              <title>Cetak Aduan</title>
              <style>
                @media print {
                  body {
                    font-family: Arial, sans-serif;
                  }

                  table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                  }

                  th, td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: left;
                    vertical-align: top;
                  }

                  th {
                    background-color: #f2f2f2;
                  }

                  img {
                    max-width: 50px;
                    max-height: 50px;
                  }

                  /* Sembunyikan kolom Status dan Action saat mencetak */
                  th:nth-child(7), th:nth-child(8),
                  td:nth-child(7), td:nth-child(8) {
                    display: none;
                  }
                }
              </style>
            </head>
            <body>
              <h2 style="text-align: center;">Cetak Aduan</h2>
              <table>
                <thead>
                  <tr>
                    <th>JUDUL ADUAN</th>
                    <th>IMAGE</th>
                    <th>KAMPUNG</th>
                    <th>ISI ADUAN</th>
                    <th>USER</th>
                    <th>TANGGAL PENGADUAN</th>
                  </tr>
                </thead>
                <tbody>
                  ${row}
                </tbody>
              </table>
            </body>
          </html>
        `;

        // Cetak halaman
        window.print();

        // Kembalikan konten asli
        document.body.innerHTML = originalContent;
        window.location.reload();
      }
    </script>
</head>
<body>
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>Administrator</h2>
      </div>
      <div class="sidebar-menu">
        <a href="home.html" class="menu-item"><i class="bi bi-house-heart-fill" style="margin: 5px"></i> Home</a>
        <a href="aduan.php" class="menu-item"><i class="bi bi-clipboard2-fill" style="margin: 5px"></i> Aduan</a>
        <a href="statistik.php" class="menu-item"><i class="bi bi-bar-chart-fill" style="margin: 5px"></i> Statistik</a>
      </div>

      <!-- Tambahkan logout di bagian bawah -->
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
        <div class="main-content">
            <!-- Pesan sukses atau error -->
            <?php
            if (isset($_GET['success'])) {
                $successMessage = htmlspecialchars($_GET['success']);
                if (strpos($successMessage, 'ditolak') !== false) {
                    echo "<div style='color: red; text-align: center; margin-bottom: 10px;'>$successMessage</div>";
                } else {
                    echo "<div style='color: green; text-align: center; margin-bottom: 10px;'>$successMessage</div>";
                }
            }
            if (isset($_GET['error'])) {
                echo "<div style='color: red; text-align: center; margin-bottom: 10px;'>" . htmlspecialchars($_GET['error']) . "</div>";
            }
            ?>

            <!-- Tabel Data Aduan -->
            <h2 style="margin: 10px">Data Aduan</h2>
            <table>
                <thead>
                    <tr>
                        <th>JUDUL ADUAN</th>
                        <th>IMAGE</th>
                        <th>KAMPUNG</th>
                        <th>ISI ADUAN</th>
                        <th>USER</th>
                        <th>TANGGAL PENGADUAN</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowId = "row-" . $row['id'];
                    echo "<tr id='$rowId'>";
                    echo "<td>" . htmlspecialchars($row['judul_aduan']) . "</td>";
                    echo "<td><img src='../uploads/" . htmlspecialchars($row['file_upload']) . "' alt='Image' width='50'></td>";
                    echo "<td>" . htmlspecialchars($row['kampung']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['isi_aduan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td class='action-icons'>
                          <a href='javascript:void(0);' onclick='cetakAduan(\"$rowId\")'><i class='bi bi-printer' style='color: blue;'></i></a>
                          <a href='process_aduan.php?action=accept&id=" . $row['id'] . "'><i class='bi bi-check-circle' style='color: green;'></i></a>
                          <a href='process_aduan.php?action=reject&id=" . $row['id'] . "'><i class='bi bi-x-circle' style='color: red;'></i></a>
                          <a href='process_aduan.php?action=delete&id=" . $row['id'] . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus aduan ini?');\"><i class='bi bi-trash' style='color: gray;'></i></a>
                        </td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
