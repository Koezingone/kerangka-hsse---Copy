<?php
session_start();
require 'koneksi.php';

// Proteksi Halaman: Hanya admin yang bisa akses
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Mengambil semua data acara untuk ditampilkan
$query = mysqli_query($koneksi, "SELECT * FROM acara ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Acara - Panel Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .action-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 18px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .edit-btn {
            background-color: #ffc107;
            color: #333;
        }

        .hapus-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header admin-container">
        <div>
            <h2>Kelola Acara</h2>
            <p style="margin:0; color:#555;">Manajemen semua sesi absensi.</p>
        </div>
        <div class="header-nav">
            <a href="admin_panel.php" class="report-button">Kembali ke Panel Admin</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <a href="buat_acara.php" class="action-button">Tambah Acara Baru</a>

        <?php if (isset($_GET['status'])): ?>
            <p class="sukses">
                <?php
                if ($_GET['status'] == 'sukses_edit') echo "Data acara berhasil diperbarui.";
                if ($_GET['status'] == 'sukses_hapus') echo "Data acara berhasil dihapus.";
                ?>
            </p>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Agenda</th>
                        <th>Tanggal & Jam</th>
                        <th>Tempat</th>
                        <th>Target Peserta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while ($acara = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($acara['agenda']); ?></td>
                                <td><?php echo htmlspecialchars($acara['tanggal']); ?> - <?php echo htmlspecialchars($acara['jam']); ?></td>
                                <td><?php echo htmlspecialchars($acara['tempat']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($acara['target_peserta'])); ?></td>
                                <td>
                                    <a href="edit_acara.php?id=<?php echo $acara['id']; ?>" class="edit-btn">Edit</a>
                                    <a href="hapus_acara.php?id=<?php echo $acara['id']; ?>" class="hapus-btn" onclick="return confirm('Anda yakin ingin menghapus acara ini? Ini akan menghapus semua data kehadiran terkait.');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Belum ada data acara.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>