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


$err = "";
$username = '';
$ingataku = "";

// Tangani input form saat metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['email'] ?? '';
    $ingataku = $_POST['remember'] ?? '';
}

// Tangani notifikasi error dari login_process.php
if (isset($_GET['error'])) {
    $err = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/signin.css">
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <img src="img/logo.png" alt="Login Illustration">
        </div>
        <div class="login-form">
            <h2>Log in<span style="color: #ff4d73;">!</span></h2>

            <!-- Menampilkan error jika ada -->
            <?php if (!empty($err)): ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                <?php echo $err; ?>
            </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="login_process.php" method="POST" autocomplete="off">
                <label for="email">Your email</label>
                <input type="email" id="login-email" placeholder="Enter your email" name="email"
                    value="<?php echo htmlspecialchars($username); ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">

                <label for="password">Password</label>
                <input type="password" id="login-password" placeholder="Enter your valid password" name="password"
                    required>

                <div class="remember-me">
                    <label>
                        <input type="checkbox" id="login-remember" value="1" name="remember"
                            <?php if ($ingataku == '1') { echo "checked"; }?>> Ingat aku
                    </label>
                </div>

                <div class="forgot-password">
                    <a href="#">Forgot password?</a>
                </div>

                <button type="submit">Log in</button>
            </form>

            <div class="signup-link">
                Don’t have an account? <a href="signup.html">Sign up</a>
            </div>
        </div>
    </div>
</body>

</html>