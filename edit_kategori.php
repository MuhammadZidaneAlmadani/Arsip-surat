<?php
session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];

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

// Ambil data kategori berdasarkan ID
$id_kategori = $_GET['id'] ?? 0;
$kategori = null;

if ($id_kategori) {
    $query = "SELECT * FROM kategori_surat WHERE id_kategori = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_kategori);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategori = $result->fetch_assoc();
}

// Jika kategori tidak ditemukan
if (!$kategori) {
    die("Kategori tidak ditemukan");
}

// Proses update kategori
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = $_POST['nama_kategori'];
    $keterangan = $_POST['keterangan'];
    
    $query = "UPDATE kategori_surat SET nama_kategori=?, keterangan=? WHERE id_kategori=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $nama_kategori, $keterangan, $id_kategori);
    
    if ($stmt->execute()) {
        header('Location: kategori_surat.php');
        exit;
    } else {
        $error = "Gagal mengupdate kategori: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Kategori - Sistem Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.container{max-width:1000px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
.header{display:flex;justify-content:space-between;align-items:center}
.menu a{margin-right:12px;text-decoration:none;color:#0d6efd}
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px}
.form-group input, .form-group textarea{width:100%;padding:8px;box-sizing:border-box}
.btn{display:inline-block;padding:8px 16px;text-decoration:none;border-radius:4px;border:none}
.btn-primary{background-color:#0d6efd;color:white;cursor:pointer}
.error{color:red;margin-bottom:15px}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div>
      <h2>Dashboard Arsip Surat</h2>
      <div>Selamat datang, <?= htmlspecialchars($user['name']) ?></div>
    </div>
    <div class="menu">
      <a href="dashboard.php">Arsip</a>
      <a href="kategori_surat.php">Kategori</a>
      <a href="tambah_surat.php">Arsipkan Surat</a>
      <a href="about.php">Tentang</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <hr>
  
  <h2>Edit Kategori</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="form-group">
      <label for="nama_kategori">Nama Kategori: *</label>
      <input type="text" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($kategori['nama_kategori']) ?>" required>
    </div>
    
    <div class="form-group">
      <label for="keterangan">Keterangan:</label>
      <textarea id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($kategori['keterangan']) ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Update Kategori</button>
    <a href="kategori_surat.php" style="margin-left: 10px;">Batal</a>
  </form>
</div>
</body>
</html>