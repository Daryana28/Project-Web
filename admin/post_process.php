<?php
session_start();

// Pastikan hanya admin yang dapat mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $isi = mysqli_real_escape_string($koneksi, trim($_POST['isi']));
    $gambar = null;
    $created_at = date('Y-m-d H:i:s');

    // Validasi input
    if (empty($judul) || empty($isi)) {
        die("Judul dan isi berita tidak boleh kosong.");
    }

    // Proses upload gambar jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_type = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $max_size = 2 * 1024 * 1024;

        // Validasi tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            die("Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.");
        }

        // Validasi ukuran file
        if ($_FILES['gambar']['size'] > $max_size) {
            die("Ukuran file terlalu besar. Maksimal 2MB.");
        }

        // Buat nama file unik dan pindahkan file
        $unique_name = uniqid() . '.' . $file_type;
        $target_file = $target_dir . $unique_name;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $target_file; // Simpan path file ke database
        } else {
            die("Gagal mengupload gambar.");
        }
    }

    // Simpan data ke database
    $query = "INSERT INTO berita (judul, isi, gambar, created_at) VALUES ('$judul', '$isi', '$gambar', '$created_at')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: post.php?success=Berita berhasil dipost!");
        exit();
    } else {
        $error_message = "Gagal menyimpan berita: " . mysqli_error($koneksi);
        header("Location: post.php?error=" . urlencode($error_message));
        exit();
    }
}
?>
