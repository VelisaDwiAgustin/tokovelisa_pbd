<?php
require_once 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Toko PBD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-green-dark: #1b4332;
            --brand-green: #2d6a4f;
            --brand-green-light: #52b788;
            --brand-green-bg: #e8f5e9;
            --brand-yellow: #ffc107;
            --brand-yellow-hover: #e0a800;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #2b2d42;
        }

        .main-wrapper {
            flex: 1 0 auto;
        }

        .navbar-custom {
            background-color: var(--brand-green-dark);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: var(--brand-yellow);
            color: var(--brand-green-dark);
            border-radius: 8px;
            font-weight: 800;
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            padding: 8px 14px !important;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-link.active {
            border-bottom: 3px solid var(--brand-yellow);
            border-radius: 6px 6px 0 0;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }

        .card-header-custom {
            background-color: #ffffff;
            border-bottom: 2px solid var(--brand-green-bg);
            padding: 18px 24px;
            border-radius: 12px 12px 0 0 !important;
        }

        .table-custom thead {
            background-color: #212529;
            color: #ffffff;
        }

        .table-custom th {
            font-weight: 600;
            padding: 12px 16px;
            border: none;
        }

        .table-custom td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .btn-green {
            background-color: var(--brand-green);
            color: #ffffff;
            border: none;
        }
        .btn-green:hover {
            background-color: var(--brand-green-dark);
            color: #ffffff;
        }

        .btn-yellow {
            background-color: var(--brand-yellow);
            color: #212529;
            border: none;
            font-weight: 600;
        }
        .btn-yellow:hover {
            background-color: var(--brand-yellow-hover);
            color: #212529;
        }

        .badge-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
            font-weight: 600;
        }

        .badge-inactive {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            font-weight: 600;
        }

        .user-pill {
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 20px;
            color: #ffffff;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="barang.php">
      <div class="navbar-brand-logo">
        <i class="bi bi-shop fs-5"></i>
      </div>
      <span>TOKO <span style="color: var(--brand-yellow);">VELISA</span></span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto ms-3">
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='barang.php')?'active':''; ?>" href="barang.php">
            <i class="bi bi-box-seam me-1"></i> Barang
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='satuan.php')?'active':''; ?>" href="satuan.php">
            <i class="bi bi-tags me-1"></i> Satuan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='vendor.php')?'active':''; ?>" href="vendor.php">
            <i class="bi bi-truck me-1"></i> Vendor
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='role.php')?'active':''; ?>" href="role.php">
            <i class="bi bi-shield-lock me-1"></i> Role
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='user.php')?'active':''; ?>" href="user.php">
            <i class="bi bi-people me-1"></i> User
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($current_page=='margin.php')?'active':''; ?>" href="margin.php">
            <i class="bi bi-percent me-1"></i> Margin
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <div class="user-pill d-none d-md-block">
          <i class="bi bi-person-circle me-1" style="color: var(--brand-yellow);"></i> <?= htmlspecialchars($_SESSION['username']); ?>
        </div>
        <a href="logout.php" class="btn btn-outline-light btn-sm px-3 rounded-pill">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<div class="container mt-0 pt-0 pb-0"></div>