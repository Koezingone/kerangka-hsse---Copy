<?php
session_start();
require 'koneksi.php';

// Proteksi Halaman (tetap sama)
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Mengambil data (tetap sama)
$query = mysqli_query($koneksi, "SELECT id, nama_lengkap, jabatan, perusahaan, tingkatan, username FROM pegawai ORDER BY nama_lengkap ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai - Panel Admin</title>
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

        /* Style untuk tombol Edit & Hapus */
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
    </div>

    <div class="admin-container">
        <a href="registrasi.php" class="action-button">Tambah Pegawai Baru</a>
        <a href="admin_panel.php" class="action-button">kembali Ke Admin Panel</a>


        <?php if (isset($_GET['status'])): ?>
            <p class="sukses">
                <?php
                if ($_GET['status'] == 'sukses_edit') echo "Data pegawai berhasil diperbarui.";
                if ($_GET['status'] == 'sukses_hapus') echo "Data pegawai berhasil dihapus.";
                ?>
            </p>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>Username</th>
                        <th>Tingkatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php $nomor = 1; ?>
                        <?php while ($pegawai = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($pegawai['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($pegawai['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($pegawai['username']); ?></td>
                                <td><?php echo htmlspecialchars($pegawai['tingkatan']); ?></td>
                                <td>
                                    <a href="edit_pegawai.php?id=<?php echo $pegawai['id']; ?>" class="edit-btn">Edit</a>
                                    <a href="hapus_pegawai.php?id=<?php echo $pegawai['id']; ?>" class="hapus-btn" onclick="return confirm('Anda yakin ingin menghapus data ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Belum ada data pegawai.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>