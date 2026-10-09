<?php
session_start();
include "config/koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM users WHERE email = '$email' AND role IN ('admin','sales') LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: sales/dashboard.php");
        }
        exit;
    }

    $error = "Email atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - AutoCar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<div class="auth-card">
    <a class="brand" href="index.php"><span class="brand-mark">3SM</span><span>AutoCar<small>SISTEM INFORMASI PENJUALAN</small></span></a>
    <h1>Login Sistem</h1>
    <p>Gunakan akun Admin atau Sales untuk masuk ke sistem.</p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input class="form-control" type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Masuk ke Sistem</button>
    </form>

    <br>
    <a href="index.php">← Kembali ke Website</a>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
