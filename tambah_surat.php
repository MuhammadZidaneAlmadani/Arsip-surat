<?php
session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];

// Koneksi database langsung (tanpa config/database.php)
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

// Ambil data kategori untuk dropdown
$kategori_query = "SELECT * FROM kategori_surat ORDER BY nama_kategori";
$kategori_result = $conn->query($kategori_query);

// Proses tambah surat
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $nomor_surat = $_POST['nomor_surat'];
    $tgl_surat = $_POST['tgl_surat'];
    $tgl_diterima = $_POST['tgl_diterima'];
    $pengirim = $_POST['pengirim'];
    $penerima = $_POST['penerima'];
    $perihal = $_POST['perihal'];
    $id_kategori = !empty($_POST['id_kategori']) ? $_POST['id_kategori'] : NULL; // Ubah ini
    
    // Upload file
    $file_path = '';
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['file_surat']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['file_surat']['tmp_name'], $target_path)) {
            $file_path = $target_path;
        }
    }
    
    // Jika id_kategori kosong, set sebagai NULL
    if (empty($id_kategori)) {
        $query = "INSERT INTO surat (judul, nomor_surat, tgl_surat, tgl_diterima, pengirim, penerima, perihal, file_path) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $judul, $nomor_surat, $tgl_surat, $tgl_diterima, $pengirim, $penerima, $perihal, $file_path);
    } else {
        $query = "INSERT INTO surat (judul, nomor_surat, tgl_surat, tgl_diterima, pengirim, penerima, perihal, id_kategori, file_path) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssis", $judul, $nomor_surat, $tgl_surat, $tgl_diterima, $pengirim, $penerima, $perihal, $id_kategori, $file_path);
    }
    
    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Gagal menambahkan surat: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Tambah Surat - Sistem Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.container{max-width:1000px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
.header{display:flex;justify-content:space-between;align-items:center}
.menu a{margin-right:12px;text-decoration:none;color:#0d6efd}
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px}
.form-group input, .form-group textarea, .form-group select{width:100%;padding:8px;box-sizing:border-box}
.btn{display:inline-block;padding:8px 16px;text-decoration:none;border-radius:4px;border:none}
.btn-primary{background-color:#0d6efd;color:white;cursor:pointer}
.error{color:red;margin-bottom:15px}
.success{color:green;margin-bottom:15px}
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
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <hr>
  
  <h2>Arsipkan Surat Baru</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>
  
  <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
      <label for="judul">Judul Surat: *</label>
      <input type="text" id="judul" name="judul" required>
    </div>
    
    <div class="form-group">
      <label for="nomor_surat">Nomor Surat:</label>
      <input type="text" id="nomor_surat" name="nomor_surat">
    </div>
    
    <div class="form-group">
      <label for="tgl_surat">Tanggal Surat:</label>
      <input type="date" id="tgl_surat" name="tgl_surat">
    </div>
    
    <div class="form-group">
      <label for="tgl_diterima">Tanggal Diterima:</label>
      <input type="date" id="tgl_diterima" name="tgl_diterima">
    </div>
    
    <div class="form-group">
      <label for="pengirim">Pengirim:</label>
      <input type="text" id="pengirim" name="pengirim">
    </div>
    
    <div class="form-group">
      <label for="penerima">Penerima:</label>
      <input type="text" id="penerima" name="penerima">
    </div>
    
    <div class="form-group">
      <label for="perihal">Perihal:</label>
      <textarea id="perihal" name="perihal" rows="3"></textarea>
    </div>
    
    <div class="form-group">
      <label for="id_kategori">Kategori:</label>
      <select id="id_kategori" name="id_kategori">
        <option value="">-- Pilih Kategori --</option>
        <?php 
        // Reset pointer result
        $kategori_result->data_seek(0);
        while ($kategori = $kategori_result->fetch_assoc()): ?>
          <option value="<?= $kategori['id_kategori'] ?>"><?= htmlspecialchars($kategori['nama_kategori']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    
    <div class="form-group">
      <label for="file_surat">File Surat (PDF/Doc/Image):</label>
      <input type="file" id="file_surat" name="file_surat">
    </div>
    
    <button type="submit" class="btn btn-primary">Simpan Surat</button>
    <a href="dashboard.php" style="margin-left: 10px;">Batal</a>
  </form>
</div>
</body>
</html>