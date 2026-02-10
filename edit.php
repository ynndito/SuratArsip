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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumen - Sistem Arsip Dokumen</title>
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

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        input[type="file"] {
            padding: 8px 12px;
        }

        .file-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        .detail-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-top: 10px;
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

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            header h1 {
                font-size: 22px;
            }

            .detail-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
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
                <h2>Edit Dokumen</h2>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $surat['id']; ?>">

                <div class="form-group">
                    <label for="nomor_surat">Nomor Surat</label>
                    <input type="text" id="nomor_surat" name="nomor_surat" required
                           value="<?php echo htmlspecialchars($surat['nomor_surat']); ?>">
                </div>

                <div class="form-group">
                    <label for="nama_surat">Nama Surat</label>
                    <input type="text" id="nama_surat" name="nama_surat" required
                           value="<?php echo htmlspecialchars($surat['nama_surat']); ?>">
                </div>

                <div class="form-group">
                    <label for="tanggal_surat">Tanggal Surat</label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" required
                           value="<?php echo htmlspecialchars($surat['tanggal_surat']); ?>">
                </div>

                <div class="form-group">
                    <label for="lampiran">Lampiran (PDF, JPG, PNG) - Opsional</label>
                    <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="file-info">
                        File saat ini:
                        <strong><?php echo htmlspecialchars($surat['nama_file']); ?></strong><br>
                        Kosongkan jika tidak ingin mengganti file. Maksimal ukuran: 10MB.
                    </div>
                </div>

                <div class="detail-actions">
                    <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                    <a href="detail.php?id=<?php echo $surat['id']; ?>" class="btn-secondary">← Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>


