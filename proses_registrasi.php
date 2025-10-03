<?php
require 'koneksi.php';

// Ambil data dari form
$nama_lengkap = $_POST['nama_lengkap'];
$jabatan = $_POST['jabatan'];
$tingkatan = $_POST['tingkatan'];
$username = $_POST['username'];
$password = $_POST['password'];

// Validasi dasar (tidak boleh kosong)
if (empty($nama_lengkap) || empty($jabatan) || empty($tingkatan) || empty($username) || empty($password)) {
    header("Location: registrasi.php?error=Semua kolom wajib diisi");
    exit();
}

// Cek apakah username sudah ada
$stmt_cek = mysqli_prepare($koneksi, "SELECT id FROM pegawai WHERE username = ?");
mysqli_stmt_bind_param($stmt_cek, "s", $username);
mysqli_stmt_execute($stmt_cek);
$result_cek = mysqli_stmt_get_result($stmt_cek);

if (mysqli_num_rows($result_cek) > 0) {
    header("Location: registrasi.php?error=Username sudah digunakan, silakan pilih yang lain");
    exit();
}
mysqli_stmt_close($stmt_cek);

// INI BAGIAN PENTING: Enkripsi password sebelum disimpan
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Siapkan query untuk menyimpan data
$stmt_insert = mysqli_prepare($koneksi, "INSERT INTO pegawai (nama_lengkap, jabatan, tingkatan, username, password) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt_insert, "sssss", $nama_lengkap, $jabatan, $tingkatan, $username, $password_hashed);

if (mysqli_stmt_execute($stmt_insert)) {
    // Jika berhasil, arahkan ke halaman login dengan pesan sukses
    header("Location: login.php?status=registrasi_sukses");
    exit();
} else {
    // Jika gagal, kembali ke halaman registrasi dengan pesan error
    header("Location: registrasi.php?error=Terjadi kesalahan saat menyimpan data");
    exit();
}

mysqli_stmt_close($stmt_insert);
mysqli_close($koneksi);
