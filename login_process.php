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

// Mulai session
session_start();

// Tangani input form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password']; // Tidak perlu escape karena hanya untuk verifikasi hash

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signin.php?error=" . urlencode("Email tidak valid"));
        exit();
    }

    // Cek user di tabel login
    $stmt = $koneksi->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan ke session
            $_SESSION['email'] = $email;

            // Simpan cookie jika checkbox "ingat aku" dicentang
            if (!empty($_POST['remember']) && $_POST['remember'] == '1') {
                setcookie('email', $email, time() + (86400 * 30), "/", "", false, true);
            } else {
                // Hapus cookie jika checkbox tidak dicentang
                setcookie('email', '', time() - 3600, "/");
            }

            // Redirect ke halaman dashboard
            header("Location: dashboard.html");
            exit();
        } else {
            // Password salah
            header("Location: signin.php?error=" . urlencode("Password salah"));
            exit();
        }
    } else {
        // Email tidak ditemukan
        header("Location: signin.php?error=" . urlencode("Email tidak ditemukan"));
        exit();
    }
}
?>
