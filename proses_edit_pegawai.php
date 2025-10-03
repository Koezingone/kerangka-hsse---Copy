<?php
session_start();
require 'koneksi.php';

// Proteksi
if (!isset($_SESSION['pegawai_id']) || $_SESSION['tingkatan'] != 'admin') {
    die("Akses ditolak.");
}

// Ambil data dari form
$id = $_POST['id'];
$nama_lengkap = $_POST['nama_lengkap'];
$jabatan = $_POST['jabatan'];
$perusahaan = $_POST['perusahaan'];
$tingkatan = $_POST['tingkatan'];
$username = $_POST['username'];
$password = $_POST['password'];

// Cek jika password diisi, maka update password. Jika tidak, jangan update.
if (!empty($password)) {
    // Password diisi, hash password baru dan update semua data
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "UPDATE pegawai SET nama_lengkap=?, jabatan=?, perusahaan=?, tingkatan=?, username=?, password=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssssi", $nama_lengkap, $jabatan, $perusahaan, $tingkatan, $username, $password_hashed, $id);
} else {
    // Password kosong, update data selain password
    $stmt = mysqli_prepare($koneksi, "UPDATE pegawai SET nama_lengkap=?, jabatan=?, perusahaan=?, tingkatan=?, username=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $nama_lengkap, $jabatan, $perusahaan, $tingkatan, $username, $id);
}

// Eksekusi query dan redirect
if (mysqli_stmt_execute($stmt)) {
    header("Location: kelola_pegawai.php?status=sukses_edit");
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
