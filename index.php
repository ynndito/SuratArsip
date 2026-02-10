<?php
require_once 'config/koneksi.php';

// Ambil parameter filter
$searchNama = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filterFromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$filterToDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Query untuk mengambil data surat
$query = "SELECT * FROM surat WHERE 1=1";

if (!empty($searchNama)) {
    $query .= " AND nama_surat LIKE '%$searchNama%'";
}

if (!empty($filterFromDate)) {
    $query .= " AND tanggal_surat >= '$filterFromDate'";
}

if (!empty($filterToDate)) {
    $query .= " AND tanggal_surat <= '$filterToDate'";
}

$query .= " ORDER BY tanggal_surat DESC, created_at DESC";

$result = mysqli_query($conn, $query);
$surat = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $surat[] = $row;
    }
}

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Arsip Dokumen</title>
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

        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        /* INPUT SECTION */
        .input-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: fit-content;
        }

        .input-section h2 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 20px;
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

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }

        /* OUTPUT SECTION */
        .output-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .output-section h2 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .filter-section {
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group input {
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
        }

        .filter-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        thead {
            background-color: #f3f4f6;
        }

        th {
            padding: 14px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        .action-btn {
            background-color: #3b82f6;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .action-btn:hover {
            background-color: #2563eb;
        }

        .empty-message {
            text-align: center;
            color: #9ca3af;
            padding: 40px 20px;
            font-size: 14px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .filter-section {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            header h1 {
                font-size: 22px;
            }

            .filter-section {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
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
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="alert alert-success">
                Dokumen berhasil disimpan!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <!-- INPUT SECTION -->
            <div class="input-section">
                <h2>Input Dokumen</h2>
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nomor_surat">Nomor Surat</label>
                        <input type="text" id="nomor_surat" name="nomor_surat" placeholder="Contoh: 001/ADM/2024" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_surat">Nama Surat</label>
                        <input type="text" id="nama_surat" name="nama_surat" placeholder="Masukkan nama surat" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_surat">Tanggal Surat</label>
                        <input type="date" id="tanggal_surat" name="tanggal_surat" required>
                    </div>
                    <div class="form-group">
                        <label for="lampiran">Lampiran (PDF, JPG, PNG)</label>
                        <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="file-info">Maksimal ukuran: 10MB</div>
                    </div>
                    <button type="submit" class="submit-btn">Simpan Dokumen</button>
                </form>
            </div>

            <!-- OUTPUT SECTION -->
            <div class="output-section">
                <h2>Daftar Dokumen</h2>
                
                <!-- FILTER SECTION -->
                <form method="GET" action="" id="filterForm">
                    <div class="filter-section">
                        <div class="filter-group">
                            <label for="searchNama">Cari Nama Surat</label>
                            <input type="text" id="searchNama" name="search" placeholder="Ketik nama surat..." value="<?php echo htmlspecialchars($searchNama); ?>">
                        </div>
                        <div class="filter-group">
                            <label for="filterFromDate">Dari Tanggal</label>
                            <input type="date" id="filterFromDate" name="from_date" value="<?php echo htmlspecialchars($filterFromDate); ?>">
                        </div>
                        <div class="filter-group">
                            <label for="filterToDate">Sampai Tanggal</label>
                            <input type="date" id="filterToDate" name="to_date" value="<?php echo htmlspecialchars($filterToDate); ?>">
                        </div>
                    </div>
                </form>

                <!-- TABLE SECTION -->
                <div class="table-wrapper">
                    <table id="documentsTable">
                        <thead>
                            <tr>
                                <th>Nomor Surat</th>
                                <th>Nama Surat</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (count($surat) > 0): ?>
                                <?php foreach ($surat as $s): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['nomor_surat']); ?></td>
                                        <td><?php echo htmlspecialchars($s['nama_surat']); ?></td>
                                        <td><?php echo formatDate($s['tanggal_surat']); ?></td>
                                        <td>
                                            <a href="detail.php?id=<?php echo $s['id']; ?>" class="action-btn">Lihat Surat</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="empty-message">Tidak ada dokumen yang tersedia</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto submit filter form on change
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('#filterForm input');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
                input.addEventListener('input', function() {
                    // Debounce untuk search
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 500);
                });
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>


