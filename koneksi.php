<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_velisa"; // Sesuaikan dengan nama database kamu di HeidiSQL

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>