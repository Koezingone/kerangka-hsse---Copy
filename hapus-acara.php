<?php
session_start();
require 'koneksi.php';

// Proteksi
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    die("Akses ditolak.");
}

// Validasi ID
$id = $_GET['id'];
if (!is_numeric($id)) {
    die("ID tidak valid.");
}

// Query DELETE
// Karena kita set ON DELETE CASCADE di database, data peserta terkait akan ikut terhapus.
$stmt = mysqli_prepare($koneksi, "DELETE FROM acara WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

// Eksekusi dan redirect
if (mysqli_stmt_execute($stmt)) {
    header("Location: kelola_acara.php?status=sukses_hapus");
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
