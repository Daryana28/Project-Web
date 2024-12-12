<?php
// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "siprakyat";

// Membuat koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mulai session untuk menyimpan data login
session_start();

// Tangani form login jika metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cek apakah email ada di database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Ambil data pengguna
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan nama pengguna (username) dan data lainnya ke session
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['username'];  // Menyimpan username di session
            $_SESSION['role'] = $user['role'];

            // Redirect ke dashboard
            header("Location: dashboard.php?success=Login successful.");
            exit();
        } else {
            // Password salah
            header("Location: signin.php?error=Invalid password.");
            exit();
        }
    } else {
        // Email tidak ditemukan
        header("Location: signin.php?error=Email not found.");
        exit();
    }
}
?>
