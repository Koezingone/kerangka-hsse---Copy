<?php
session_start();
if (!isset($_SESSION['pegawai_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

$acara_id = $_GET['acara_id'];
$stmt = mysqli_prepare($koneksi, "SELECT * FROM acara WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $acara_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$acara = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Absen</title>
    <style>
        /* Style bisa disamakan dengan login.php */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 450px;
        }

        h2 {
            text-align: center;
        }

        p {
            line-height: 1.6;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Konfirmasi Kehadiran</h2>
        <p><strong>Agenda:</strong> <?php echo htmlspecialchars($acara['agenda']); ?></p>
        <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($acara['tanggal']); ?></p>
        <p><strong>Tempat:</strong> <?php echo htmlspecialchars($acara['tempat']); ?></p>
        <p><strong>Nama Anda:</strong> <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></p>
        <br>
        <form action="proses_absen.php" method="post">
            <input type="hidden" name="acara_id" value="<?php echo $acara['id']; ?>">
            <button type="submit">Konfirmasi Kehadiran Saya</button>
        </form>
    </div>
</body>

</html>