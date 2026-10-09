<?php
require_once 'koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: barang.php");
    exit();
}

$error = "";
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        // Memeriksa password (teks biasa atau hash)
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['iduser'] = $row['iduser'];
            header("Location: barang.php");
            exit();
        }
    }
    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Toko PBD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
<div class="card shadow-sm p-4" style="width: 360px;">
    <h4 class="text-center mb-3">Login Sistem</h4>
    <?php if ($error): ?>
        <div class="alert alert-danger p-2 small"><?= $error; ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
    </form>
</div>
</body>
</html>