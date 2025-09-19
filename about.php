<?php
session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];

// Tanggal pembuatan aplikasi (sesuaikan dengan tanggal Anda membuatnya)
$tanggal_pembuatan = "15 Maret 2024";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Tentang Aplikasi - Sistem Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.container{max-width:1000px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
.header{display:flex;justify-content:space-between;align-items:center}
.menu a{margin-right:12px;text-decoration:none;color:#0d6efd}
.about-content{display:flex;align-items:center;margin-top:20px}
.profile-img{width:150px;height:150px;border-radius:50%;object-fit:cover;margin-right:30px;border:3px solid #0d6efd}
.about-details{flex:1}
.about-details h3{margin-top:0;color:#0d6efd}
.info-item{margin-bottom:10px}
.info-label{font-weight:bold;display:inline-block;width:120px}
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
  
  <h2>Tentang Aplikasi</h2>
  
  <div class="about-content">
    <img src="https://via.placeholder.com/150?text=Foto" alt="Foto Developer" class="profile-img">
    <div class="about-details">
      <h3>Informasi Developer</h3>
      <div class="info-item">
        <span class="info-label">Nama:</span>
        <span>Muhammad Zidane Adlmadani</span>
      </div>
      <div class="info-item">
        <span class="info-label">NIM:</span>
        <span>2231750016</span>
      </div>
      <div class="info-item">
        <span class="info-label">Tanggal Pembuatan:</span>
        <span><?= $tanggal_pembuatan ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Aplikasi:</span>
        <span>Sistem Manajemen Arsip Surat</span>
      </div>
      <div class="info-item">
        <span class="info-label">Deskripsi:</span>
        <span>Aplikasi ini dibuat untuk memenuhi tugas mata kuliah Pemrograman Web. 
        Sistem ini memungkinkan pengguna untuk mengelola arsip surat secara digital 
        dengan fitur tambah, edit, hapus, dan kategorisasi surat.</span>
      </div>
    </div>
  </div>
</div>
</body>
</html>