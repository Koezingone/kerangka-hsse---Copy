<?php
session_start();
// Proteksi halaman: Hanya kepala yang boleh akses
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'kepala') {
    header("Location: dashboard.php");
    exit();
}
// Mengatur zona waktu ke WITA (Waktu Indonesia Tengah)
date_default_timezone_set('Asia/Makassar');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Sesi Absen Baru</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="center-screen">
    <div class="form-container">
        <h1>Mulai Sesi Absen Baru</h1>
        <form action="proses_buat_acara.php" method="post">
            <label for="agenda">Agenda Rapat/Acara</label>
            <input type="text" id="agenda" name="agenda" required>

            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="jam">Jam</label>
            <input type="time" id="jam" name="jam" value="<?php echo date('H:i'); ?>" required>

            <label for="tempat">Tempat</label>
            <input type="text" id="tempat" name="tempat" required>

            <label for="target_peserta">Absen Ini Untuk</label>
            <select id="target_peserta" name="target_peserta" required>
                <option value="semua">Semua (Pegawai & Kontraktor)</option>
                <option value="pegawai">Hanya Pegawai</option>
                <option value="kontraktor">Hanya Kontraktor</option>
            </select>

            <label for="materi">Materi (Opsional)</label>
            <input type="text" id="materi" name="materi">

            <button type="submit" class="btn-primary">Buat dan Mulai Absensi</button>
        </form>
    </div>
</body>

</html>