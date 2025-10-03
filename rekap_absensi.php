<?php
session_start();
require 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// --- LOGIKA FILTER ---
$where_clauses = [];
$params = [];
$types = '';

// Filter by search keyword
$search_agenda = $_GET['search'] ?? '';
if (!empty($search_agenda)) {
    $where_clauses[] = "agenda LIKE ?";
    $params[] = "%" . $search_agenda . "%";
    $types .= 's';
}

// Filter by date range (berdasarkan kapan acara diinput ke sistem)
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
if (!empty($start_date) && !empty($end_date)) {
    $where_clauses[] = "DATE(dibuat_pada) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

// Bangun query utama
$sql_acara = "SELECT * FROM acara";
if (!empty($where_clauses)) {
    $sql_acara .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql_acara .= " ORDER BY id DESC";

$stmt_acara = mysqli_prepare($koneksi, $sql_acara);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_acara, $types, ...$params);
}
mysqli_stmt_execute($stmt_acara);
$query_acara = mysqli_stmt_get_result($stmt_acara);

// Menyiapkan data untuk ditampilkan
$data_absensi = [];
$total_kehadiran = 0;
while ($acara = mysqli_fetch_assoc($query_acara)) {
    $acara_id = $acara['id'];
    $data_absensi[$acara_id] = ['detail_acara' => $acara, 'peserta' => []];

    $query_peserta = "SELECT p.nama_lengkap, p.jabatan, ps.waktu_konfirmasi 
                      FROM peserta ps JOIN pegawai p ON ps.pegawai_id = p.id 
                      WHERE ps.acara_id = $acara_id ORDER BY ps.waktu_konfirmasi ASC";
    $result_peserta = mysqli_query($koneksi, $query_peserta);
    while ($peserta = mysqli_fetch_assoc($result_peserta)) {
        $data_absensi[$acara_id]['peserta'][] = $peserta;
        $total_kehadiran++;
    }
}
$total_acara = count($data_absensi);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Absensi - Panel Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .filter-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-form label {
            font-weight: bold;
            font-size: 14px;
        }

        .filter-form input {
            width: 100%;
            box-sizing: border-box;
        }

        .filter-form button {
            width: 100%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card h4 {
            margin: 0 0 10px 0;
            color: #555;
        }

        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #003366;
        }

        .rekap-acara {
            background-color: #fff;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .rekap-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .rekap-header h3 {
            margin: 0;
        }

        .rekap-header span {
            font-size: 14px;
            color: #555;
        }

        .peserta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .peserta-table th,
        .peserta-table td {
            border-bottom: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        .peserta-table tr:last-child td {
            border-bottom: none;
        }

        .peserta-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header admin-container">
        <div>
            <h2>Rekapitulasi Absensi</h2>
            <p style="margin:0; color:#555;">Riwayat kehadiran dari semua acara.</p>
        </div>
        <div class="header-nav">
            <a href="admin_panel.php" class="report-button">Kembali ke Panel Admin</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <form method="GET" action="" class="filter-form">
            <div>
                <label for="search">Cari Agenda</label>
                <input type="text" name="search" id="search" placeholder="Contoh: Rapat Koordinasi" value="<?php echo htmlspecialchars($search_agenda); ?>">
            </div>
            <div>
                <label for="start_date">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div>
                <label for="end_date">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            <div>
                <button type="submit" class="btn-primary">Filter</button>
            </div>
        </form>

        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Acara (Sesuai Filter)</h4>
                <div class="value"><?php echo $total_acara; ?></div>
            </div>
            <div class="stat-card">
                <h4>Total Kehadiran (Sesuai Filter)</h4>
                <div class="value"><?php echo $total_kehadiran; ?></div>
            </div>
        </div>

        <?php if (!empty($data_absensi)): ?>
            <?php foreach ($data_absensi as $acara_id => $data): ?>
                <?php
                $detail = $data['detail_acara'];
                $peserta_list = $data['peserta'];
                $jumlah_peserta = count($peserta_list);
                ?>
                <div class="rekap-acara">
                    <div class="rekap-header">
                        <div>
                            <h3><?php echo htmlspecialchars($detail['agenda']); ?></h3>
                            <span><?php echo htmlspecialchars($detail['tanggal']); ?> - <?php echo htmlspecialchars($detail['tempat']); ?></span>
                        </div>
                        <div>
                            <span style="margin-right: 15px;"><strong><?php echo $jumlah_peserta; ?></strong> Peserta Hadir</span>
                            <a href="cetak_laporan.php?acara_id=<?php echo $acara_id; ?>" class="report-button" target="_blank">Cetak PDF</a>
                        </div>
                    </div>

                    <?php if ($jumlah_peserta > 0): ?>
                        <div class="table-responsive">
                            <table class="peserta-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No.</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>Waktu Konfirmasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $nomor = 1; ?>
                                    <?php foreach ($peserta_list as $peserta): ?>
                                        <tr>
                                            <td><?php echo $nomor++; ?>.</td>
                                            <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($peserta['jabatan']); ?></td>
                                            <td><?php echo date('d M Y, H:i:s', strtotime($peserta['waktu_konfirmasi'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px; color: #777;">Belum ada peserta yang hadir untuk acara ini.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="stat-card" style="text-align: center;">
                <h4>Data Tidak Ditemukan</h4>
                <p>Tidak ada data absensi yang sesuai dengan kriteria filter Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>