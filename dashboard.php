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
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard Arsip Surat</title>
<style>
body{font-family:Arial;background:#f6f7fb;margin:0}
.container{max-width:1000px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
.header{display:flex;justify-content:space-between;align-items:center}
.menu a{margin-right:12px;text-decoration:none;color:#0d6efd}
table{width:100%;border-collapse:collapse;margin-top:20px}
th, td{padding:12px;text-align:left;border-bottom:1px solid #ddd}
th{background-color:#f2f2f2}
.btn{display:inline-block;padding:6px 12px;margin:2px;text-decoration:none;border-radius:4px}
.btn-primary{background-color:#0d6efd;color:white}
.btn-danger{background-color:#dc3545;color:white}
.btn-edit{background-color:#ffc107;color:black}
.search-form{margin-bottom:20px}
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
  
  <h3>Daftar Surat</h3>
  
  <div class="search-form">
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Cari surat..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button type="submit">Cari</button>
    </form>
  </div>
  
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Nomor Surat</th>
        <th>Tanggal</th>
        <th>Pengirim/Penerima</th>
        <th>Kategori</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Query untuk mengambil data surat
      $search = isset($_GET['search']) ? $_GET['search'] : '';
      $query = "SELECT s.*, k.nama_kategori 
                FROM surat s 
                LEFT JOIN kategori_surat k ON s.id_kategori = k.id_kategori 
                WHERE s.judul LIKE ? OR s.nomor_surat LIKE ? OR s.perihal LIKE ?
                ORDER BY s.created_at DESC";
      $stmt = $conn->prepare($query);
      $searchParam = "%$search%";
      $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
      $stmt->execute();
      $result = $stmt->get_result();
      
      $no = 1;
      while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nomor_surat']) . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['tgl_surat'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['pengirim'] ?: $row['penerima']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_kategori']) . "</td>";
        echo "<td>
                <a href='lihat_surat.php?id=" . $row['id_surat'] . "' class='btn btn-primary'>Lihat</a>
                <a href='edit_surat.php?id=" . $row['id_surat'] . "' class='btn btn-edit'>Edit</a>
                <a href='hapus_surat.php?id=" . $row['id_surat'] . "' class='btn btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
              </td>";
        echo "</tr>";
      }
      
      if ($no == 1) {
        echo "<tr><td colspan='7' style='text-align:center'>Tidak ada data surat</td></tr>";
      }
      ?>
    </tbody>
  </table>
  
  <p style="margin-top: 20px;">
    <a href="tambah_surat.php" class="btn btn-primary">+ Tambah Surat Baru</a>
  </p>
</div>
</body>
</html>