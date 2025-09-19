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

// Proses tambah kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama_kategori = $_POST['nama_kategori'];
    $keterangan = $_POST['keterangan'];
    
    $query = "INSERT INTO kategori_surat (nama_kategori, keterangan) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nama_kategori, $keterangan);
    $stmt->execute();
    
    header('Location: kategori_surat.php');
    exit;
}

// Proses hapus kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $query = "DELETE FROM kategori_surat WHERE id_kategori = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header('Location: kategori_surat.php');
    exit;
}

// Ambil data kategori
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM kategori_surat 
          WHERE nama_kategori LIKE ? OR keterangan LIKE ? 
          ORDER BY id_kategori";
$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Kategori Surat</title>
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
.form-group{margin-bottom:15px}
.form-group label{display:block;margin-bottom:5px}
.form-group input, .form-group textarea{width:100%;padding:8px;box-sizing:border-box}
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
  
  <h2>Kategori Surat</h2>
  <p>Berikut ini adalah kategori yang bisa digunakan untuk melabeli surat.<br>
  Klik "Tambah" pada kolom aksi untuk menambahkan kategori baru.</p>
  
  <div class="search-form">
    <form method="GET" action="">
      <h3>Cari kategori:</h3>
      <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Cari</button>
    </form>
  </div>
  
  <table>
    <thead>
      <tr>
        <th>ID Kategori</th>
        <th>Nama Kategori</th>
        <th>Keterangan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_kategori'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_kategori']) . "</td>";
        echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
        echo "<td>
                <a href='?hapus=" . $row['id_kategori'] . "' class='btn btn-danger' onclick='return confirm(\"Yakin ingin menghapus kategori ini?\")'>Hapus</a>
                <a href='edit_kategori.php?id=" . $row['id_kategori'] . "' class='btn btn-edit'>Edit</a>
              </td>";
        echo "</tr>";
      }
      
      if ($result->num_rows == 0) {
        echo "<tr><td colspan='4' style='text-align:center'>Tidak ada data kategori</td></tr>";
      }
      ?>
    </tbody>
  </table>
  
  <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
    <h3>Tambah Kategori Baru</h3>
    <form method="POST" action="">
      <div class="form-group">
        <label for="nama_kategori">Nama Kategori:</label>
        <input type="text" id="nama_kategori" name="nama_kategori" required>
      </div>
      <div class="form-group">
        <label for="keterangan">Keterangan:</label>
        <textarea id="keterangan" name="keterangan"></textarea>
      </div>
      <button type="submit" name="tambah_kategori" class="btn btn-primary">Tambah Kategori</button>
    </form>
  </div>
</div>
</body>
</html>