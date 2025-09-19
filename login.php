<?php
session_start();

// Koneksi database
$host = 'localhost';
$dbname = 'arsip_surat';
$username = 'root';
$password = '';

// Buat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password_input = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Bandingkan password langsung (hanya untuk testing)
        if ($password_input === $user['password']) {
            $_SESSION['login'] = true;
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email atau password salah';
        }
    } else {
        $error = 'Email atau password salah';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Login - Sistem Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0;display:flex;justify-content:center;align-items:center;height:100vh}
.login-container{background:#fff;padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);width:300px}
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px}
.form-group input{width:100%;padding:8px;box-sizing:border-box}
.btn{display:block;width:100%;padding:10px;background-color:#0d6efd;color:white;border:none;border-radius:4px;cursor:pointer}
.error{color:red;margin-bottom:15px}
</style>
</head>
<body>
<div class="login-container">
  <h2 style="text-align:center">Login Sistem Arsip Surat</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required value="admin@arsip.com">
    </div>
    
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required value="admin123">
    </div>
    
    <button type="submit" class="btn">Login</button>
  </form>
  
  <p style="text-align:center;margin-top:20px;font-size:12px;">
    <strong>Gunakan kredensial berikut:</strong><br>
    Email: admin@arsip.com<br>
    Password: admin123
  </p>
</div>
</body>
</html>