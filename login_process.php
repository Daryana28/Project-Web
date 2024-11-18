<?php
// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "login";

// Membuat koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tangani input form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cek user di tabel login
    $query = "SELECT * FROM login WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "Login berhasil!";
        
        // Simpan cookie jika checkbox "ingat aku" dicentang
        if (!empty($_POST['remember']) && $_POST['remember'] == '1') {
            setcookie('email', $email, time() + (86400 * 30), "/", "", false, true); // Cookie aktif 30 hari
        }

        // Redirect ke halaman dashboard
        header("Location: dashboard.html");
        exit();
    } else {
        // Jika login gagal, redirect kembali ke form login dengan pesan error
        header("Location: signin.php?error=Email atau password salah");
        exit();
    }
}
?>
