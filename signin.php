<?php
// koneksi ke database
$host_db  = "localhost";
$user_db  = "root";
$pass_db  = "mysql123";
$nama_db  = "login";
$koneksi  = mysqli_connect ($host_db, $user_db, $pass_db, $nama_db);

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
} else {
  echo "Koneksi berhasil!";
}

//atur variable
$err     = "";
$username= "";
$ingataku= "";
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
      <form>
        <label for="email">Your email</label>
        <input type="email" id="email" placeholder="Enter your email" value="<?php echo $username ?>" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter your valid password" required>
        
        <div class="remember-me">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Remember me</label>
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
