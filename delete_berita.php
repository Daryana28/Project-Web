<?php
session_start();

// Periksa apakah pengguna memiliki akses admin
if (!isset($_SESSION['name'])) {
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

// Periksa apakah ID berita diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Cek apakah berita ada
    $query_check = "SELECT * FROM berita WHERE id = ?";
    $stmt_check = $koneksi->prepare($query_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $berita = $result_check->fetch_assoc();

        // Hapus gambar dari server jika ada
        if (!empty($berita['image']) && file_exists('admin/' . $berita['image'])) {
            unlink('admin/' . $berita['image']);
        }

        // Hapus berita dari database
        $query_delete = "DELETE FROM berita WHERE id = ?";
        $stmt_delete = $koneksi->prepare($query_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            header("Location: dashboard.php?success=Berita berhasil dihapus.");
        } else {
            header("Location: dashboard.php?error=Gagal menghapus berita.");
        }
    } else {
        header("Location: dashboard.php?error=Berita tidak ditemukan.");
    }
} else {
    header("Location: dashboard.php?error=Permintaan tidak valid.");
}

// Tutup koneksi
mysqli_close($koneksi);
?>