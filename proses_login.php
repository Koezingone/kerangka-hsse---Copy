<?php
session_start();
require 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// DIUBAH: Ambil juga kolom 'tingkatan' saat query
$stmt = mysqli_prepare($koneksi, "SELECT id, nama_lengkap, tingkatan, password FROM pegawai WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $pegawai = mysqli_fetch_assoc($result);

    if (password_verify($password, $pegawai['password'])) {
        // Jika berhasil, simpan info ke session
        $_SESSION['pegawai_id'] = $pegawai['id'];
        $_SESSION['nama_lengkap'] = $pegawai['nama_lengkap'];
        $_SESSION['tingkatan'] = $pegawai['tingkatan']; // BARIS BARU: Simpan tingkatan ke session

        header("Location: dashboard.php");
        exit();
    }
}

header("Location: login.php?error=Username atau Password salah");
exit();
