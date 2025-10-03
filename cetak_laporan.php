<?php
// Memulai session dan memanggil file-file yang diperlukan
session_start();
require 'koneksi.php';
require 'fpdf/fpdf.php';

// ===================================================================
//  1. KEAMANAN & VALIDASI INPUT
// ===================================================================

// Proteksi halaman: Hanya 'kepala' & 'admin' yang bisa akses
if (!isset($_SESSION['pegawai_id']) || !in_array($_SESSION['tingkatan'], ['kepala', 'admin'])) {
    die("Akses ditolak. Anda tidak memiliki hak untuk mengakses halaman ini.");
}

// Validasi ID Acara dari URL
if (!isset($_GET['acara_id']) || !is_numeric($_GET['acara_id'])) {
    die("ID Acara tidak valid atau tidak ditemukan.");
}
$acara_id = (int)$_GET['acara_id'];

// ===================================================================
//  2. PENGAMBILAN DATA DARI DATABASE
// ===================================================================

// Ambil data detail acara dari tabel `acara`
$stmt_acara = mysqli_prepare($koneksi, "SELECT * FROM acara WHERE id = ?");
mysqli_stmt_bind_param($stmt_acara, "i", $acara_id);
mysqli_stmt_execute($stmt_acara);
$result_acara = mysqli_stmt_get_result($stmt_acara);
$acara = mysqli_fetch_assoc($result_acara);

if (!$acara) {
    die("Data acara dengan ID $acara_id tidak ditemukan.");
}

// Ambil data peserta yang hadir dan urutkan berdasarkan tingkatan
$query_peserta = "
    SELECT 
        p.nama_lengkap, p.jabatan, p.perusahaan, ps.waktu_konfirmasi, p.tingkatan
    FROM peserta ps
    JOIN pegawai p ON ps.pegawai_id = p.id 
    WHERE ps.acara_id = ?
    ORDER BY 
        CASE p.tingkatan
            WHEN 'kepala' THEN 1
            WHEN 'pegawai' THEN 2
            WHEN 'kontraktor' THEN 3
            ELSE 4
        END, 
        ps.waktu_konfirmasi ASC
";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
mysqli_stmt_bind_param($stmt_peserta, "i", $acara_id);
mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);

// ===================================================================
//  3. PEMBUATAN DOKUMEN PDF DENGAN FPDF
// ===================================================================

// Inisiasi PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// KOP DOKUMEN
$pdf->Image('logo.png', 170, 10, 30);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'DAFTAR HADIR', 0, 1, 'C');
$pdf->Ln(5);

// DETAIL ACARA
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'Hari/Tanggal', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $acara['tanggal'], 0, 1);

$pdf->Cell(35, 7, 'Jam', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $acara['jam'], 0, 1);

$pdf->Cell(35, 7, 'Agenda', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->Cell(0, 7, $acara['agenda'], 0, 1);

$pdf->Cell(35, 7, 'Tempat', 0, 0);
$pdf->Cell(5, 7, ':', 0, 0);
$pdf->MultiCell(0, 7, $acara['tempat']); // Gunakan MultiCell jika teks tempat panjang

// KOTAK MATERI (diisi dengan data 'materi')
$pdf->Cell(35, 7, 'Materi', 0, 0);
$pdf->Cell(5, 7, '', 0, 1);
$y_pos = $pdf->GetY(); // Ambil posisi Y saat ini
$pdf->Rect(10, $y_pos, 190, 25); // Gambar kotak (x, y, width, height)
$pdf->SetXY(16, $y_pos + 1); // Set posisi kursor di dalam kotak
$pdf->MultiCell(178, 6, $acara['materi']); // Tulis teks 'materi' di dalam kotak
$pdf->Ln(25); // Beri spasi vertikal setinggi kotak

// HEADER TABEL DAFTAR PESERTA
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(0, 92, 169); // Warna biru Pertamina
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 10, 'No.', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Nama', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Jabatan', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Perusahaan', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Waktu Absensi', 1, 1, 'C', true);

// ISI TABEL DAFTAR PESERTA
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$nomor = 1;

if (mysqli_num_rows($result_peserta) > 0) {
    while ($peserta = mysqli_fetch_assoc($result_peserta)) {
        // Format waktu agar lebih mudah dibaca
        $waktu = date('d-m-Y, H:i:s', strtotime($peserta['waktu_konfirmasi']));

        $pdf->Cell(10, 10, $nomor++, 1, 0, 'C');
        $pdf->Cell(50, 10, $peserta['nama_lengkap'], 1, 0, 'L');
        $pdf->Cell(45, 10, $peserta['jabatan'], 1, 0, 'L');
        $pdf->Cell(45, 10, $peserta['perusahaan'], 1, 0, 'L');
        $pdf->Cell(40, 10, $waktu, 1, 1, 'C');
    }
} else {
    // Jika tidak ada peserta, buat 10 baris kosong
    for ($i = 0; $i < 10; $i++) {
        $pdf->Cell(10, 10, $nomor++, 1, 0, 'C');
        $pdf->Cell(50, 10, '', 1, 0);
        $pdf->Cell(45, 10, '', 1, 0);
        $pdf->Cell(45, 10, '', 1, 0);
        $pdf->Cell(40, 10, '', 1, 1);
    }
}

// ===================================================================
//  4. OUTPUT FILE PDF
// ===================================================================

// Buat nama file yang aman dari karakter aneh
$nama_file = 'Daftar_Hadir_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $acara['agenda']) . '.pdf';
$pdf->Output('I', $nama_file); // 'I' = tampilkan di browser, 'D' = paksa download

// Tutup koneksi
mysqli_stmt_close($stmt_acara);
mysqli_stmt_close($stmt_peserta);
mysqli_close($koneksi);
