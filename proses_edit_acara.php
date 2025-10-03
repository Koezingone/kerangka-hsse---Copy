<?php
session_start();
require 'koneksi.php';

// Proteksi
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    die("Akses ditolak.");
}

// Ambil data dari form
$id = $_POST['id'];
$agenda = $_POST['agenda'];
$tanggal = $_POST['tanggal']; // Format: Y-m-d
$jam = $_POST['jam'];         // Format: H:i
$tempat = $_POST['tempat'];
$materi = $_POST['materi'];
$target_peserta = $_POST['target_peserta'];

// Gabungkan kembali tanggal dan jam untuk kolom 'waktu_mulai'
$waktu_mulai = $tanggal . ' ' . $jam . ':00';

// Buat juga format tanggal human-readable untuk kolom 'tanggal'
$tanggal_human = date('d F Y', strtotime($tanggal));

// Query UPDATE dengan semua kolom yang relevan
$stmt = mysqli_prepare($koneksi, "UPDATE acara SET agenda=?, tanggal=?, jam=?, waktu_mulai=?, tempat=?, materi=?, target_peserta=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sssssssi", $agenda, $tanggal_human, $jam, $waktu_mulai, $tempat, $materi, $target_peserta, $id);

// Eksekusi dan redirect
if (mysqli_stmt_execute($stmt)) {
    header("Location: kelola_acara.php?status=sukses_edit");
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
