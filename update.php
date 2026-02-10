<?php
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data lama
$query = "SELECT * FROM surat WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php?error=' . urlencode('Dokumen tidak ditemukan!'));
    exit;
}

$surat_lama = mysqli_fetch_assoc($result);

// Ambil data dari form
$nomor_surat = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
$nama_surat = mysqli_real_escape_string($conn, $_POST['nama_surat']);
$tanggal_surat = $_POST['tanggal_surat'];

if (empty($nomor_surat) || empty($nama_surat) || empty($tanggal_surat)) {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Semua field harus diisi!'));
    exit;
}

$upload_dir = 'uploads/';
$gunakan_file_lama = true;
$nama_file_baru = $surat_lama['nama_file'];
$tipe_file_baru = $surat_lama['tipe_file'];
$ukuran_file_baru = $surat_lama['ukuran_file'];

// Cek apakah ada file baru yang diupload
if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK && $_FILES['lampiran']['size'] > 0) {
    $file = $_FILES['lampiran'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];

    // Validasi ukuran file (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file_size > $max_size) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Ukuran file terlalu besar! Maksimal 10MB.'));
        exit;
    }

    // Validasi tipe file
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Tipe file tidak diizinkan! Hanya PDF, JPG, dan PNG.'));
        exit;
    }

    // Validasi MIME type
    $allowed_mimes = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    $file_mime = mime_content_type($file_tmp);

    if (!in_array($file_mime, $allowed_mimes)) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Tipe file tidak valid!'));
        exit;
    }

    // Pastikan folder uploads ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate nama file unik
    $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_file_name;

    if (!move_uploaded_file($file_tmp, $upload_path)) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Gagal menyimpan file baru!'));
        exit;
    }

    // Hapus file lama jika ada
    $old_file_path = $upload_dir . $surat_lama['nama_file'];
    if (!empty($surat_lama['nama_file']) && file_exists($old_file_path)) {
        @unlink($old_file_path);
    }

    $gunakan_file_lama = false;
    $nama_file_baru = $new_file_name;
    $tipe_file_baru = strtoupper($file_extension);
    $ukuran_file_baru = $file_size;
}

// Update data di database
if ($gunakan_file_lama) {
    $sql = "
        UPDATE surat
        SET nomor_surat = '$nomor_surat',
            nama_surat = '$nama_surat',
            tanggal_surat = '$tanggal_surat'
        WHERE id = $id
    ";
} else {
    $sql = "
        UPDATE surat
        SET nomor_surat = '$nomor_surat',
            nama_surat = '$nama_surat',
            tanggal_surat = '$tanggal_surat',
            nama_file = '$nama_file_baru',
            tipe_file = '$tipe_file_baru',
            ukuran_file = $ukuran_file_baru
        WHERE id = $id
    ";
}

if (mysqli_query($conn, $sql)) {
    header('Location: detail.php?id=' . $id);
} else {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Gagal mengupdate data di database!'));
}

mysqli_close($conn);
exit;

