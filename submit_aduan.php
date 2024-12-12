<?php
// Mulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['email'])) {
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

// Periksa apakah form disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $kampung = mysqli_real_escape_string($koneksi, $_POST['kampung']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $user_email = $_SESSION['email']; // Ambil email pengguna dari session
    $image = "";

    // Proses upload file gambar
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/"; // Folder untuk menyimpan file
        $image = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;

        // Periksa apakah folder upload ada, jika tidak, buat foldernya
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Periksa dan pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image; // Simpan nama file jika berhasil diupload
        } else {
            echo "<script>alert('Gagal mengupload file!');</script>";
            $image = ""; // Kosongkan jika gagal upload
        }
    }

    // Masukkan data ke tabel aduan
    $query = "INSERT INTO aduan (judul_aduan, jenis_aduan, kampung, isi_aduan, file_upload, user_email, created_at) 
              VALUES ('$title', '$jenis', '$kampung', '$isi', '$image', '$user_email', NOW())";

    if (mysqli_query($koneksi, $query)) {
        // Redirect ke halaman aduan dengan notifikasi sukses
        header("Location: aduan.php?success=Aduan berhasil disimpan!");
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan aduan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>
