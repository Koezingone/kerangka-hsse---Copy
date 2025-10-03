<?php
session_start();
require 'koneksi.php';

// ===================================================================
//  1. KEAMANAN
// ===================================================================

// Proteksi halaman: Hanya 'kepala' atau 'admin' yang bisa membuat acara
if (!isset($_SESSION['pegawai_id']) || !in_array($_SESSION['tingkatan'], ['kepala', 'admin'])) {
    header("Location: dashboard.php");
    exit();
}

// ===================================================================
//  2. PENGAMBILAN & VALIDASI DATA DARI FORM
// ===================================================================

// Ambil semua data dari form
$agenda = $_POST['agenda'] ?? '';
$tanggal = $_POST['tanggal'] ?? ''; // Format: Y-m-d
$jam = $_POST['jam'] ?? '';         // Format: H:i
$tempat = $_POST['tempat'] ?? '';
$materi = $_POST['materi'] ?? '';
$target_peserta = $_POST['target_peserta'] ?? '';

// Validasi data tidak boleh kosong
if (empty($agenda) || empty($tanggal) || empty($jam) || empty($tempat) || empty($target_peserta)) {
    header("Location: buat_acara.php?error=Data tidak lengkap");
    exit();
}

// Gabungkan tanggal dan jam untuk membuat timestamp DATETIME yang presisi
$waktu_mulai = $tanggal . ' ' . $jam . ':00';

// ===================================================================
//  3. PROSES PENYIMPANAN KE DATABASE
// ===================================================================

// Siapkan query INSERT dengan kolom `waktu_mulai`
$stmt = mysqli_prepare($koneksi, "INSERT INTO acara (agenda, tanggal, jam, waktu_mulai, tempat, materi, target_peserta) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Ikat parameter ke query (s = string)
mysqli_stmt_bind_param($stmt, "sssssss", $agenda, $tanggal, $jam, $waktu_mulai, $tempat, $materi, $target_peserta);

// Eksekusi query dan berikan respons
if (mysqli_stmt_execute($stmt)) {
    // Jika berhasil, kembali ke dashboard dengan pesan sukses
    header("Location: dashboard.php?status=acara_dibuat");
} else {
    // Jika gagal, kembali ke form dengan pesan error
    header("Location: buat_acara.php?error=Gagal menyimpan data ke database");
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
exit();
