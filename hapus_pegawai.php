<?php
session_start();
require 'koneksi.php';

// Proteksi
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    die("Akses ditolak.");
}

// Ambil ID dari URL dan validasi
$id = $_GET['id'];
if (!is_numeric($id)) {
    die("ID tidak valid.");
}

// Jalankan query DELETE
$stmt = mysqli_prepare($koneksi, "DELETE FROM pegawai WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

// Eksekusi dan redirect
if (mysqli_stmt_execute($stmt)) {
    header("Location: kelola_pegawai.php?status=sukses_hapus");
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
