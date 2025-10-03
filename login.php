<?php
session_start();
if (isset($_SESSION['pegawai_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="center-screen">

    <div class="form-container">
        <h1>LOGIN PEGAWAI</h1>

        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'registrasi_sukses'): ?>
            <p class="sukses">Registrasi berhasil! Silakan login.</p>
        <?php endif; ?>

        <form action="proses_login.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>

</body>

</html>