<?php
require_once 'config/koneksi.php';

// Cek apakah form sudah di-submit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil data dari form
$nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
$nama_surat = mysqli_real_escape_string($conn, $_POST['nama_surat']);
$tanggal_surat = $_POST['tanggal_surat'];

// Validasi input
if (empty($nomor_surat) || empty($nama_surat) || empty($tanggal_surat)) {
    header('Location: index.php?error=' . urlencode('Semua field harus diisi!'));
    exit;
}

// Validasi file upload
if (!isset($_FILES['lampiran']) || $_FILES['lampiran']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?error=' . urlencode('File tidak valid atau terjadi error saat upload!'));
    exit;
}

$file = $_FILES['lampiran'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Validasi ukuran file (max 10MB)
$max_size = 10 * 1024 * 1024; // 10MB dalam bytes
if ($file_size > $max_size) {
    header('Location: index.php?error=' . urlencode('Ukuran file terlalu besar! Maksimal 10MB.'));
    exit;
}

// Validasi tipe file
$allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    header('Location: index.php?error=' . urlencode('Tipe file tidak diizinkan! Hanya PDF, JPG, dan PNG.'));
    exit;
}

// Validasi MIME type untuk keamanan tambahan
$allowed_mimes = [
    'application/pdf',
    'image/jpeg',
    'image/jpg',
    'image/png'
];
$file_mime = mime_content_type($file_tmp);

if (!in_array($file_mime, $allowed_mimes)) {
    header('Location: index.php?error=' . urlencode('Tipe file tidak valid!'));
    exit;
}

// Buat folder uploads jika belum ada
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate nama file unik
$new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
$upload_path = $upload_dir . $new_file_name;

// Pindahkan file ke folder uploads
if (!move_uploaded_file($file_tmp, $upload_path)) {
    header('Location: index.php?error=' . urlencode('Gagal menyimpan file!'));
    exit;
}

// Simpan data ke database
$tipe_file = strtoupper($file_extension);
$query = "INSERT INTO surat (nomor_surat, nama_surat, tanggal_surat, nama_file, tipe_file, ukuran_file) 
          VALUES ('$nomor_surat', '$nama_surat', '$tanggal_surat', '$new_file_name', '$tipe_file', $file_size)";

if (mysqli_query($conn, $query)) {
    header('Location: index.php?success=1');
} else {
    // Jika gagal insert, hapus file yang sudah diupload
    unlink($upload_path);
    header('Location: index.php?error=' . urlencode('Gagal menyimpan data ke database!'));
}

mysqli_close($conn);
exit;
?>

