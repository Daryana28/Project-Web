<?php
session_start();

// Periksa apakah pengguna memiliki role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    error_log("Unauthorized access attempt by: " . ($_SESSION['email'] ?? 'unknown user'));
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

// Periksa apakah parameter action dan id ada
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    // Validasi aksi (accept, reject, atau delete)
    $valid_actions = ['accept', 'reject', 'delete'];
    if (!in_array($action, $valid_actions)) {
        header("Location: aduan.php?error=Aksi tidak valid");
        exit();
    }

    // Tindakan berdasarkan aksi
    if ($action === 'accept') {
        $status = 'Accepted';
        $notification = "Aduan Anda telah diterima!";
        $success_message = "Aduan diterima!";
        $success_color = "green";
    } elseif ($action === 'reject') {
        $status = 'Rejected';
        $notification = "Aduan Anda telah ditolak.";
        $success_message = "Aduan ditolak!";
        $success_color = "red";
    } elseif ($action === 'delete') {
        // Hapus aduan berdasarkan ID
        $stmt = $koneksi->prepare("DELETE FROM aduan WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: aduan.php?success=Aduan berhasil dihapus!");
            exit();
        } else {
            header("Location: aduan.php?error=Gagal menghapus aduan: " . $stmt->error);
            exit();
        }
    }

    // Jika bukan delete, update status dan notifikasi di database
    if ($action !== 'delete') {
        $stmt = $koneksi->prepare("UPDATE aduan SET status = ?, notification = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $koneksi->error);
        }

        $stmt->bind_param("ssi", $status, $notification, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Tambahkan parameter untuk pesan sukses dengan warna
            header("Location: aduan.php?success=$success_message&color=$success_color");
            exit();
        } else {
            header("Location: aduan.php?error=Gagal memperbarui aduan: ID tidak ditemukan atau error");
            exit();
        }
    }
} else {
    header("Location: aduan.php?error=Parameter tidak lengkap");
    exit();
}
?>
