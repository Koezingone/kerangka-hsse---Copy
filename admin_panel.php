<?php
session_start();

// Proteksi Halaman: Hanya 'admin' yang bisa mengakses
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Sistem Absensi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 960px;
            margin: 0 auto;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-top: 0;
            color: #003366;
        }

        .card p {
            color: #555;
            font-size: 14px;
        }

        .card a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header admin-container">
        <div>
            <h2>Panel Admin</h2>
            <p style="margin:0; color:#555;">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</p>
        </div>
        <div class="header-nav">
            <a href="dashboard.php" class="report-button">Kembali ke Dashboard</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-grid">
            <div class="card">
                <h3>Kelola Acara</h3>
                <p>Tambah, edit, atau hapus data acara dan sesi absensi yang akan datang.</p>
                <a href="kelola_acara.php">Buka Menu</a>
            </div>

            <div class="card">
                <h3>Kelola Pegawai</h3>
                <p>Tambah atau edit data pegawai, kontraktor, kepala, dan admin lainnya.</p>
                <a href="kelola_pegawai.php">Buka Menu</a>
            </div>

            <div class="card">
                <h3>Rekapitulasi Absensi</h3>
                <p>Lihat riwayat kehadiran dari semua acara dan cetak laporan PDF.</p>
                <a href="rekap_absensi.php">Buka Menu</a>
            </div>
        </div>
    </div>

</body>

</html>