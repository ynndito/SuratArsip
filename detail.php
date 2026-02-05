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

// Fungsi format tanggal
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $date->format('d') . ' ' . $months[(int)$date->format('m')] . ' ' . $date->format('Y');
}

// Path file
$file_path = 'uploads/' . $surat['nama_file'];
$file_exists = file_exists($file_path);

// Tentukan apakah file adalah gambar atau PDF
$is_image = in_array(strtolower($surat['tipe_file']), ['jpg', 'jpeg', 'png']);
$is_pdf = strtolower($surat['tipe_file']) == 'pdf';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dokumen - Sistem Arsip Dokumen</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #ffffff;
            padding: 20px 0;
            border-bottom: 1px solid #e0e6ed;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        header h1 {
            font-size: 28px;
            color: #1e3a8a;
            font-weight: 600;
        }

        .detail-section {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .detail-header {
            margin-bottom: 25px;
        }

        .detail-header h2 {
            font-size: 22px;
            color: #1e3a8a;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .detail-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }

        .detail-item-label {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .detail-item-value {
            font-size: 16px;
            color: #1f2937;
            font-weight: 600;
        }

        .preview-section {
            margin-bottom: 25px;
        }

        .preview-section h3 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .preview-container {
            background-color: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 6px;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 14px;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
        }

        .preview-container img {
            max-width: 100%;
            max-height: 600px;
            object-fit: contain;
        }

        .preview-container iframe {
            width: 100%;
            height: 600px;
            border: none;
        }

        .preview-icon {
            font-size: 48px;
            opacity: 0.3;
        }

        .detail-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 11px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
            padding: 11px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .error-message {
            text-align: center;
            color: #ef4444;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            header h1 {
                font-size: 22px;
            }

            .detail-info {
                grid-template-columns: 1fr;
            }

            .detail-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
            }

            .preview-container iframe {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>📑 Sistem Arsip Dokumen</h1>
        </div>
    </header>

    <div class="container">
        <div class="detail-section">
            <div class="detail-header">
                <h2>Detail Dokumen</h2>
            </div>

            <div class="detail-info">
                <div class="detail-item">
                    <div class="detail-item-label">Nomor Surat</div>
                    <div class="detail-item-value"><?php echo htmlspecialchars($surat['nomor_surat']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Nama Surat</div>
                    <div class="detail-item-value"><?php echo htmlspecialchars($surat['nama_surat']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Tanggal Surat</div>
                    <div class="detail-item-value"><?php echo formatDate($surat['tanggal_surat']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Tipe File</div>
                    <div class="detail-item-value"><?php echo htmlspecialchars($surat['tipe_file']); ?></div>
                </div>
            </div>

            <div class="preview-section">
                <h3>Preview File</h3>
                <div class="preview-container">
                    <?php if ($file_exists): ?>
                        <?php if ($is_image): ?>
                            <img src="<?php echo htmlspecialchars($file_path); ?>" alt="Preview <?php echo htmlspecialchars($surat['nama_surat']); ?>">
                        <?php elseif ($is_pdf): ?>
                            <iframe src="<?php echo htmlspecialchars($file_path); ?>" type="application/pdf"></iframe>
                        <?php else: ?>
                            <div class="preview-icon">📄</div>
                            <div>File: <?php echo htmlspecialchars($surat['nama_file']); ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="error-message">
                            File tidak ditemukan di server
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-actions">
                <?php if ($file_exists): ?>
                    <a href="download.php?id=<?php echo $surat['id']; ?>" class="btn-primary">⬇ Download File</a>
                <?php endif; ?>
                <a href="index.php" class="btn-secondary">← Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>

