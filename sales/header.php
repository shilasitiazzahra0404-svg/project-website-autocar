<?php
include_once "../config/auth.php";
include_once "../config/helpers.php";
wajib_login('sales');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Sales - AutoDealer') ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-layout">
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-mark">3SM</span>
        <strong>AutoCar</strong>
    </div>
    <div class="sidebar-label">MODUL SALES</div>
    <nav class="sidebar-nav">
        <a class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">▣ Dashboard</a>
        <a class="<?= ($active ?? '') === 'customer' ? 'active' : '' ?>" href="customer.php">♙ Data Customer</a>
        <a class="<?= ($active ?? '') === 'transaksi' ? 'active' : '' ?>" href="transaksi.php">▤ Transaksi Penjualan</a>
        <a class="<?= ($active ?? '') === 'kendaraan' ? 'active' : '' ?>" href="kendaraan_saya.php">▤ Kendaraan Saya</a>
        <a class="<?= ($active ?? '') === 'history' ? 'active' : '' ?>" href="history.php">◷ History Penjualan</a>
        <a class="<?= ($active ?? '') === 'prestasi' ? 'active' : '' ?>" href="prestasi.php">◉ Prestasi</a>
        <a class="<?= ($active ?? '') === 'insentif' ? 'active' : '' ?>" href="insentif.php">◎ Insentif</a>
        <a class="<?= ($active ?? '') === 'kredit' ? 'active' : '' ?>" href="kredit.php">▣ Data Kredit</a>
        <a class="<?= ($active ?? '') === 'angsuran' ? 'active' : '' ?>" href="angsuran.php">▣ Data Angsuran</a>
    </nav>
    <div class="sidebar-bottom">
        <a class="btn btn-secondary btn-small" href="../logout.php">Logout</a>
    </div>
</aside>
<div class="dashboard-main">
<header class="dashboard-header">
    <div>Sales</div>
    <div class="user-mini"><strong><?= e($_SESSION['nama']) ?></strong><span><?= e($_SESSION['email']) ?></span></div>
</header>
<main class="dashboard-content">
