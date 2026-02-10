<?php
require_once 'config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data surat untuk mendapatkan nama file
$query = "SELECT * FROM surat WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php?error=' . urlencode('Dokumen tidak ditemukan!'));
    exit;
}

$surat = mysqli_fetch_assoc($result);

$upload_dir = 'uploads/';
$file_path = $upload_dir . $surat['nama_file'];

// Hapus file fisik jika ada
if (!empty($surat['nama_file']) && file_exists($file_path)) {
    @unlink($file_path);
}

// Hapus data dari database
$delete_query = "DELETE FROM surat WHERE id = $id";

if (mysqli_query($conn, $delete_query)) {
    header('Location: index.php');
} else {
    header('Location: detail.php?id=' . $id . '&error=' . urlencode('Gagal menghapus data dari database!'));
}

mysqli_close($conn);
exit;


