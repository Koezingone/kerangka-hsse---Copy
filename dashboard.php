<?php
session_start();
if (!isset($_SESSION['pegawai_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';
// Query untuk mengambil semua acara
$query_acara = mysqli_query($koneksi, "SELECT * FROM acara ORDER BY id DESC");
$pegawai_id = $_SESSION['pegawai_id'];

// Query untuk mengecek acara mana saja yang sudah diikuti oleh pegawai yang login
$query_sudah_absen = mysqli_query($koneksi, "SELECT acara_id FROM peserta WHERE pegawai_id = $pegawai_id");
$acara_sudah_diikuti = [];
while ($row = mysqli_fetch_assoc($query_sudah_absen)) {
    $acara_sudah_diikuti[] = $row['acara_id'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="header">
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h2>
        <div class="header-nav">
            <?php // PENERAPAN HAK AKSES UNTUK KEPALA 
            ?>

            <?php if ($_SESSION['tingkatan'] == 'kepala'): ?>
                <a href="buat_acara.php" class="start-button">Mulai Absen Baru</a>
            <?php endif; ?>

            <?php if ($_SESSION['tingkatan'] == 'admin'): ?>
                <a href="admin_panel.php" class="admin-button">Panel Admin</a>
            <?php endif; ?>

            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <p class="message">Absensi berhasil dicatat!</p>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'acara_dibuat'): ?>
        <p class="message">Sesi absen baru berhasil dibuat dan sekarang tersedia untuk semua pegawai.</p>
    <?php endif; ?>

    <h3>Silakan Pilih Acara untuk Absen</h3>
    <table>
        <thead>
            <tr>
                <th>Agenda</th>
                <th>Tanggal</th>
                <th>Tempat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($acara = mysqli_fetch_assoc($query_acara)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($acara['agenda']); ?></td>
                    <td><?php echo htmlspecialchars($acara['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($acara['tempat']); ?></td>
                    <td id="aksi-<?php echo $acara['id']; ?>">
                        <?php
                        $tingkatan_user = $_SESSION['tingkatan'];
                        $acara_id = $acara['id'];
                        $target_acara = $acara['target_peserta'];

                        if ($tingkatan_user == 'kepala' || $tingkatan_user == 'admin') {
                            echo '<a href="cetak_laporan.php?acara_id=' . $acara_id . '" class="report-button" target="_blank">Cetak Laporan</a>';
                        }

                        if ($tingkatan_user == 'pegawai' || $tingkatan_user == 'kontraktor') {
                            if (in_array($acara_id, $acara_sudah_diikuti)) {
                                echo '<span class="disabled">Sudah Absen</span>';
                            } else {
                                $absen_terbuka = false;
                                $waktu_selesai_iso = null;

                                if (!empty($acara['waktu_mulai'])) {
                                    $waktu_mulai = new DateTime($acara['waktu_mulai'], new DateTimeZone('Asia/Makassar'));
                                    $waktu_selesai = (clone $waktu_mulai)->modify('+30 minutes');
                                    $waktu_sekarang = new DateTime('now', new DateTimeZone('Asia/Makassar'));

                                    if ($waktu_sekarang >= $waktu_mulai && $waktu_sekarang <= $waktu_selesai) {
                                        $absen_terbuka = true;
                                        // Simpan waktu selesai dalam format ISO 8601 untuk JavaScript
                                        $waktu_selesai_iso = $waktu_selesai->format('c');
                                    }
                                }

                                $boleh_absen = ($target_acara == 'semua' || $target_acara == $tingkatan_user);

                                if ($boleh_absen && $absen_terbuka) {
                                    // Tampilkan tombol DAN elemen untuk countdown
                                    echo '<a href="form_absen.php?acara_id=' . $acara_id . '" class="absen">Isi Absen</a>';
                                    echo '<span class="countdown-timer" data-endtime="' . $waktu_selesai_iso . '"></span>';
                                } elseif ($boleh_absen && !$absen_terbuka) {
                                    echo '<span class="disabled">Absen Ditutup</span>';
                                } else {
                                    echo '<span class="not-applicable">Tidak Berlaku</span>';
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua elemen timer yang ada di halaman
        const timers = document.querySelectorAll('.countdown-timer');

        timers.forEach(timer => {
            // Ambil waktu selesai dari atribut data-endtime
            const endTime = new Date(timer.dataset.endtime).getTime();

            // Update timer setiap satu detik
            const interval = setInterval(function() {
                const now = new Date().getTime();
                const distance = endTime - now;

                // Jika waktu masih tersisa
                if (distance > 0) {
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format agar selalu 2 digit (misal: 09, 08, 07)
                    const displaySeconds = seconds < 10 ? '0' + seconds : seconds;

                    timer.innerHTML = `Sisa Waktu: ${minutes}:${displaySeconds}`;
                } else {
                    // Jika waktu sudah habis
                    clearInterval(interval); // Hentikan countdown

                    // Ganti seluruh isi kolom "Aksi" dengan status "Waktu Habis"
                    const aksiCell = timer.closest('td');
                    if (aksiCell) {
                        aksiCell.innerHTML = '<span class="disabled">Waktu Habis</span>';
                    }
                }
            }, 1000);
        });
    });
</script>

</html>