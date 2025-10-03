<?php
session_start();
require 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
if (!is_numeric($id)) die("ID tidak valid.");

// Ambil data acara dari database
$stmt = mysqli_prepare($koneksi, "SELECT * FROM acara WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$acara = mysqli_fetch_assoc($result);

// Ambil tanggal dan jam dari kolom 'waktu_mulai' untuk diisi ke form
$waktu_mulai_obj = new DateTime($acara['waktu_mulai']);
$tanggal_value = $waktu_mulai_obj->format('Y-m-d');
$jam_value = $waktu_mulai_obj->format('H:i');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Acara</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="center-screen">
    <div class="form-container">
        <div class="form-header">
            <h1>Edit Data Acara</h1>
        </div>
        <form action="proses_edit_acara.php" method="post">
            <input type="hidden" name="id" value="<?php echo $acara['id']; ?>">

            <label for="agenda">Agenda Acara</label>
            <div class="input-group">
                <input type="text" id="agenda" name="agenda" placeholder="Contoh: Rapat Koordinasi" value="<?php echo htmlspecialchars($acara['agenda']); ?>" required>
            </div>

            <div class="form-grid">
                <div>
                    <label for="tanggal">Tanggal</label>
                    <div class="input-group">
                        <input type="date" id="tanggal" name="tanggal" value="<?php echo $tanggal_value; ?>" required>
                    </div>
                </div>
                <div>
                    <label for="jam">Jam</label>
                    <div class="input-group">
                        <input type="time" id="jam" name="jam" value="<?php echo $jam_value; ?>" required>
                    </div>
                </div>
            </div>

            <label for="tempat">Tempat Pelaksanaan</label>
            <div class="input-group">
                <input type="text" id="tempat" name="tempat" placeholder="Contoh: Ruang Rapat Lt. 5" value="<?php echo htmlspecialchars($acara['tempat']); ?>" required>
            </div>

            <label for="materi">Materi (Opsional)</label>
            <div class="input-group">
                <input type="text" id="materi" name="materi" placeholder="Topik pembahasan" value="<?php echo htmlspecialchars($acara['materi']); ?>">
            </div>


            <div class="input-group">
                <label for="target_peserta">Target Peserta</label>
                <select id="target_peserta" name="target_peserta" required>
                    <option value="semua" <?php if ($acara['target_peserta'] == 'semua') echo 'selected'; ?>>Semua (Pegawai & Kontraktor)</option>
                    <option value="pegawai" <?php if ($acara['target_peserta'] == 'pegawai') echo 'selected'; ?>>Hanya Pegawai</option>
                    <option value="kontraktor" <?php if ($acara['target_peserta'] == 'kontraktor') echo 'selected'; ?>>Hanya Kontraktor</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-save"></i> Update Acara
            </button>
        </form>
    </div>
</body>

</html>