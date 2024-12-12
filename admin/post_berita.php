<?php
// Mulai session
session_start();

// Periksa apakah pengguna memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php"); // Redirect ke halaman login jika bukan admin
    exit();
}

// Koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "siprakyat";

$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tangani form jika ada data yang dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dari form
    $title = isset($_POST['title']) ? mysqli_real_escape_string($koneksi, trim($_POST['title'])) : null;
    $content = isset($_POST['content']) ? mysqli_real_escape_string($koneksi, trim($_POST['content'])) : null;
    $imagePath = null; // Default jika tidak ada gambar

    // Validasi input wajib
    if (empty($title) || empty($content)) {
        $error = "Judul dan isi berita wajib diisi.";
    } else {
        // Proses upload gambar jika ada
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true); // Buat folder jika belum ada
            }
            $target_file = $target_dir . uniqid() . '-' . basename($_FILES['image']['name']);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

            // Validasi tipe file gambar
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $imagePath = $target_file; // Simpan path file untuk database
                } else {
                    $error = "Gagal mengupload gambar.";
                }
            } else {
                $error = "Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            }
        }

        // Simpan ke database jika tidak ada error
        if (!isset($error)) {
            $query = "INSERT INTO berita (title, content, image) VALUES ('$title', '$content', '$imagePath')";
            if (mysqli_query($koneksi, $query)) {
                $success = "Berita berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan berita: " . mysqli_error($koneksi);
            }
        }
    }
}

// Ambil daftar berita dari database untuk ditampilkan
$query = "SELECT * FROM berita ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

// Tangani penghapusan berita
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Sanitasi ID
    // Ambil informasi gambar untuk dihapus
    $query_image = "SELECT image FROM berita WHERE id = $id";
    $result_image = mysqli_query($koneksi, $query_image);
    $data_image = mysqli_fetch_assoc($result_image);

    // Hapus gambar dari folder jika ada
    if (!empty($data_image['image']) && file_exists($data_image['image'])) {
        unlink($data_image['image']);
    }

    // Hapus berita dari database
    $query_delete = "DELETE FROM berita WHERE id = $id";
    if (mysqli_query($koneksi, $query_delete)) {
        $success = "Berita berhasil dihapus!";
    } else {
        $error = "Gagal menghapus berita: " . mysqli_error($koneksi);
    }

    // Redirect untuk menghindari refresh penghapusan ulang
    header("Location: post_berita.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Berita</title>
    <link rel="stylesheet" href="../css/user.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .post-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-submit {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #c0392b;
        }

        .news-list {
            margin-top: 30px;
            padding-left: 20px;
            padding-top: 20px;
            padding-right: 20px;
        }

        .news-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .news-item img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
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
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
            display: inline-block;
        }

        .btn-delete:hover {
            background-color: darkred;
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
        <div class="main-content fade-in">
            <h2 style="text-align: center;">Post Berita Baru</h2>
            <div class="post-container">
                <?php if (isset($success)): ?>
                    <div style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
                <?php elseif (isset($error)): ?>
                    <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="post_berita.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Judul Berita</label>
                        <input type="text" id="title" name="title" placeholder="Masukkan judul berita" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Isi Berita</label>
                        <textarea id="content" name="content" rows="5" placeholder="Masukkan isi berita" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Upload Gambar</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn-submit">Post Berita</button>
                </form>
            </div>
        </div>
  
            <!-- Daftar Berita -->
            <div class="news-list">
            <h3>Daftar Berita</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Berita</th>
                        <th>Isi Berita</th>
                        <th>Gambar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo substr(htmlspecialchars($row['content']), 0, 50) . '...'; ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Gambar" width="50">
                                    <?php else: ?>
                                        Tidak ada gambar
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="post_berita.php?delete=<?php echo $row['id']; ?>" class="btn-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Belum ada berita.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// Menutup koneksi setelah selesai
mysqli_close($koneksi);
?>