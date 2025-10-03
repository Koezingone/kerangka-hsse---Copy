<?php
session_start();
if (!isset($_SESSION['pegawai_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

$acara_id = $_POST['acara_id'];
$pegawai_id = $_SESSION['pegawai_id'];

// Cek dulu apakah pegawai sudah absen di acara ini
$stmt_cek = mysqli_prepare($koneksi, "SELECT id FROM peserta WHERE acara_id = ? AND pegawai_id = ?");
mysqli_stmt_bind_param($stmt_cek, "ii", $acara_id, $pegawai_id);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) == 0) {
    // Jika belum ada, masukkan data absen
    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO peserta (acara_id, pegawai_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "ii", $acara_id, $pegawai_id);
    mysqli_stmt_execute($stmt_insert);
}

// Redirect kembali ke dashboard dengan status sukses
header("Location: dashboard.php?status=sukses");
exit();
