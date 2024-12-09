<?php
// Konfigurasi koneksi database
$host_db = "localhost";
$user_db = "root";
$pass_db = "";
$nama_db = "login";

// Membuat koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tangani form signup jika metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $first_name = mysqli_real_escape_string($koneksi, $_POST['first-name']);
    $last_name = mysqli_real_escape_string($koneksi, $_POST['last-name']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Mengenkripsi password menggunakan password_hash()
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah ada di database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika email sudah terdaftar
        header("Location: signup.php?error=Email already exists.");
        exit();
    } else {
        // Jika email belum terdaftar, simpan data ke database
        $insert_query = "INSERT INTO users (first_name, last_name, email, password) 
                         VALUES ('$first_name', '$last_name', '$email', '$hashed_password')";

        if (mysqli_query($koneksi, $insert_query)) {
            // Jika berhasil, arahkan ke halaman login
            header("Location: signin.php?success=Account created successfully.");
            exit();
        } else {
            // Jika gagal, tampilkan pesan error
            header("Location: signup.php?error=Error while creating account.");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="css/signup.css" />
</head>

<body>
    <div class="signup-section">
        <div class="signup-image">
            <img src="img/logo.png" alt="Illustration" />
        </div>

        <!-- Right Section for Form -->
        <div class="signup-form">
            <h2>Sign up<span>!</span></h2>

            <!-- Menampilkan error atau success -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="color: red;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php elseif (isset($_GET['success'])): ?>
                <div class="success-message" style="color: green;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Form Signup -->
            <form action="signup.php" method="POST">
                <label for="first-name">First name</label>
                <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required />

                <label for="last-name">Last name</label>
                <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required />

                <label for="email">Your email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required />

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your valid password" required />

                <button type="submit">Sign up</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="signin.php">Log in</a>
            </div>
        </div>
    </div>
</body>

</html>