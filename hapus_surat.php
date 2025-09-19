<?php
session_start();
if (empty($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

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

// Ambil ID surat yang akan dihapus
$id_surat = $_GET['id'] ?? 0;

if ($id_surat) {
    // Ambil data surat untuk menghapus file terkait
    $query = "SELECT file_path FROM surat WHERE id_surat = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_surat);
    $stmt->execute();
    $result = $stmt->get_result();
    $surat = $result->fetch_assoc();
    
    // Hapus file jika ada
    if (!empty($surat['file_path']) && file_exists($surat['file_path'])) {
        unlink($surat['file_path']);
    }
    
    // Hapus data dari database
    $query = "DELETE FROM surat WHERE id_surat = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_surat);
    
    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit;
    } else {
        die("Gagal menghapus surat: " . $conn->error);
    }
} else {
    die("ID surat tidak valid");
}
?> 