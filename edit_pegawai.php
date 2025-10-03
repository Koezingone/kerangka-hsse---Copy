<?php
session_start();
require 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Ambil ID dari URL
$id = $_GET['id'];
if (!is_numeric($id)) {
    die("ID tidak valid.");
}

// Ambil data pegawai dari database
$stmt = mysqli_prepare($koneksi, "SELECT * FROM pegawai WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pegawai = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="center-screen">
    <div class="form-container">
        <h1>Edit Data Pegawai</h1>
        <form action="proses_edit_pegawai.php" method="post">
            <input type="hidden" name="id" value="<?php echo $pegawai['id']; ?>">

            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($pegawai['nama_lengkap']); ?>" required>

            <label>Jabatan</label>
            <input type="text" name="jabatan" value="<?php echo htmlspecialchars($pegawai['jabatan']); ?>" required>

            <label>Perusahaan</label>
            <input type="text" name="perusahaan" value="<?php echo htmlspecialchars($pegawai['perusahaan']); ?>" required>

            <label>Tingkatan</label>
            <select name="tingkatan" required>
                <option value="pegawai" <?php if ($pegawai['tingkatan'] == 'pegawai') echo 'selected'; ?>>Pegawai</option>
                <option value="kontraktor" <?php if ($pegawai['tingkatan'] == 'kontraktor') echo 'selected'; ?>>Kontraktor</option>
                <option value="kepala" <?php if ($pegawai['tingkatan'] == 'kepala') echo 'selected'; ?>>Kepala</option>
                <option value="admin" <?php if ($pegawai['tingkatan'] == 'admin') echo 'selected'; ?>>Admin</option>
            </select>

            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($pegawai['username']); ?>" required>

            <label>Password Baru (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password">

            <button type="submit" class="btn-primary">Update Data</button>
        </form>
    </div>
</body>

</html>