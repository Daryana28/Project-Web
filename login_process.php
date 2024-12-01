<?php
// Konfigurasi koneksi database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "";
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
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input kosong
    if (empty($email) || empty($password)) {
        header("Location: signin.php?error=" . urlencode("Email atau password tidak boleh kosong"));
        exit();
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signin.php?error=" . urlencode("Format email tidak valid"));
        exit();
    }

    // Query cek user di tabel users
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password tanpa hashing (plaintext)
        if ($password === $user['password']) {
            // Simpan data user ke session
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if (!empty($_POST['remember']) && $_POST['remember'] == '1') {
                setcookie('email', $user['email'], time() + (86400 * 30), "/", "", false, true);
            } else {
                setcookie('email', '', time() - 3600, "/");
            }

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ./admin/home.html");
            } else {
                header("Location: ./dashboard.php");
            }
            exit();
        } else {
            header("Location: signin.php?error=" . urlencode("Password salah"));
            exit();
        }
    } else {
        header("Location: signin.php?error=" . urlencode("Email tidak ditemukan"));
        exit();
    }
}
?>
