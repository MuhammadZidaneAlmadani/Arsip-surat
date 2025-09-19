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

// Ambil data surat berdasarkan ID
$id_surat = $_GET['id'] ?? 0;
$surat = null;

if ($id_surat) {
    $query = "SELECT s.*, k.nama_kategori 
              FROM surat s 
              LEFT JOIN kategori_surat k ON s.id_kategori = k.id_kategori 
              WHERE s.id_surat = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_surat);
    $stmt->execute();
    $result = $stmt->get_result();
    $surat = $result->fetch_assoc();
}

// Jika surat tidak ditemukan
if (!$surat) {
    die("Surat tidak ditemukan");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Lihat Surat - Sistem Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.container{max-width:1000px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.menu a{margin-right:15px;text-decoration:none;color:#0d6efd;font-weight:bold}
.page-title{color:#0d6efd;border-bottom:2px solid #0d6efd;padding-bottom:10px}
.surat-header{background:#f8f9fa;padding:15px;border-radius:5px;margin-bottom:20px}
.surat-info{margin-bottom:20px}
.info-row{margin-bottom:10px;display:flex}
.info-label{font-weight:bold;width:120px;color:#555}
.info-value{flex:1}
.surat-content{background:#fff;border:1px solid #ddd;padding:20px;border-radius:5px;min-height:200px}
.file-actions{margin-top:20px;padding-top:20px;border-top:1px solid #eee;text-align:center}
.btn{display:inline-block;padding:8px 16px;text-decoration:none;border-radius:4px;border:none;margin:0 5px}
.btn-secondary{background-color:#6c757d;color:white}
.btn-primary{background-color:#0d6efd;color:white}
.btn-success{background-color:#198754;color:white}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div>
      <h1>Arsip Surat &raquo; Lihat</h1>
    </div>
    <div class="menu">
      <a href="dashboard.php">Arsip</a>
      <a href="kategori_surat.php">Kategori Surat</a>
      <a href="about.php">About</a>
    </div>
  </div>

  <div class="surat-header">
    <div class="info-row">
      <span class="info-label">Nomor:</span>
      <span class="info-value"><?= !empty($surat['nomor_surat']) ? htmlspecialchars($surat['nomor_surat']) : '-' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Kategori:</span>
      <span class="info-value"><?= !empty($surat['nama_kategori']) ? htmlspecialchars($surat['nama_kategori']) : 'Tidak berkategori' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Judul:</span>
      <span class="info-value"><?= htmlspecialchars($surat['judul']) ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Waktu Unggah:</span>
      <span class="info-value"><?= date('Y-m-d H:i', strtotime($surat['created_at'])) ?></span>
    </div>
  </div>

  <div class="surat-content">
    <h3 style="text-align:center;color:#0d6efd"><?= !empty($surat['nomor_surat']) ? htmlspecialchars($surat['nomor_surat']) : 'SURAT TANPA NOMOR' ?></h3>
    
    <div class="info-row">
      <span class="info-label">Tanggal Surat:</span>
      <span class="info-value"><?= !empty($surat['tgl_surat']) ? date('d/m/Y', strtotime($surat['tgl_surat'])) : '-' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Tanggal Diterima:</span>
      <span class="info-value"><?= !empty($surat['tgl_diterima']) ? date('d/m/Y', strtotime($surat['tgl_diterima'])) : '-' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Pengirim:</span>
      <span class="info-value"><?= !empty($surat['pengirim']) ? htmlspecialchars($surat['pengirim']) : '-' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Penerima:</span>
      <span class="info-value"><?= !empty($surat['penerima']) ? htmlspecialchars($surat['penerima']) : '-' ?></span>
    </div>
    
    <div class="info-row">
      <span class="info-label">Perihal:</span>
      <span class="info-value"><?= !empty($surat['perihal']) ? nl2br(htmlspecialchars($surat['perihal'])) : '-' ?></span>
    </div>
  </div>

  <div class="file-actions">
    <a href="dashboard.php" class="btn btn-secondary">&laquo; Kembali</a>
    
    <?php if (!empty($surat['file_path'])): ?>
    <a href="<?= $surat['file_path'] ?>" target="_blank" class="btn btn-primary">Unduh</a>
    <?php endif; ?>
    
    <a href="edit_surat.php?id=<?= $surat['id_surat'] ?>" class="btn btn-success">Edit/Ganti File</a>
  </div>
</div>
</body>
</html>