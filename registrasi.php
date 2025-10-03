<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pegawai Baru</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="center-screen">

    <div class="form-container">
        <h1>Registrasi Pegawai</h1>

        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="proses_registrasi.php" method="post">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>

            <label for="jabatan">Jabatan</label>
            <input type="text" id="jabatan" name="jabatan" required>

            <label for="perusahaan">Perusahaan</label>
            <input type="text" id="perusahaan" name="perusahaan" value="PT Pertamina Patra Niaga" required>

            <label for="tingkatan">Tingkatan</label>
            <select id="tingkatan" name="tingkatan" required>
                <option value="pegawai">Pegawai</option>
                <option value="kontraktor">Kontraktor</option>
                <option value="kepala">Kepala</option>
                <option value="admin">Admin</option>
            </select>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-success">Daftar</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>

</html>