 <?php
require_once 'config/koneksi.php';

// Ambil ID dari parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Query untuk mengambil data surat
$query = "SELECT * FROM surat WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: index.php?error=' . urlencode('Dokumen tidak ditemukan!'));
    exit;
}

$surat = mysqli_fetch_assoc($result);
$file_path = 'uploads/' . $surat['nama_file'];

// Cek apakah file ada
if (!file_exists($file_path)) {
    header('Location: index.php?error=' . urlencode('File tidak ditemukan di server!'));
    exit;
}

// Set header untuk download
$original_name = $surat['nama_surat'] . '.' . strtolower($surat['tipe_file']);
$original_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name); // Sanitize filename

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $original_name . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Output file
readfile($file_path);
exit;
?>

