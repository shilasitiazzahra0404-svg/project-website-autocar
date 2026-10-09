<?php
include_once "../config/auth.php";
include_once "../config/helpers.php";
wajib_login('admin');


/* =========================================================
   RESET AKSES REPORT PRESTASI & INSENTIF
   Jika admin berpindah ke menu lain,
   PIN harus diminta kembali.
========================================================= */

if (($active ?? '') !== 'insentif') {
    unset($_SESSION['akses_laporan']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Admin - AutoDealer') ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-layout">
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-mark">3SM</span>
        <strong>AutoCar</strong>
    </div>
    <div class="sidebar-label">MODUL MANAJEMEN DEALER</div>
    <nav class="sidebar-nav">
        <a class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">▣ Dashboard</a>
        <a class="<?= ($active ?? '') === 'kendaraan' ? 'active' : '' ?>" href="kendaraan.php">▤ Kelola Kendaraan</a>
        <a class="<?= ($active ?? '') === 'customer' ? 'active' : '' ?>" href="customer.php">♙ Kelola Customer</a>
        <a class="<?= ($active ?? '') === 'sales' ? 'active' : '' ?>" href="sales.php">♙ Data Sales</a>
        <a class="<?= ($active ?? '') === 'verifikasi' ? 'active' : '' ?>" href="verifikasi.php">✓ Verifikasi Penjualan</a>
        <a class="<?= ($active ?? '') === 'laporan' ? 'active' : '' ?>" href="laporan_penjualan.php">▥ Report Penjualan</a>
        <a class="<?= ($active ?? '') === 'kredit' ? 'active' : '' ?>" href="kredit.php">▣ Data Kredit</a>
        <a class="<?= ($active ?? '') === 'angsuran' ? 'active' : '' ?>" href="angsuran.php">▣ Data Angsuran</a>
        <a class="<?= ($active ?? '') === 'insentif' ? 'active' : '' ?>" href="laporan_insentif.php">▥ Report Prestasi & Insentif</a>
    </nav>
    <div class="sidebar-bottom">
        <a class="btn btn-secondary btn-small" href="../logout.php">Logout</a>
    </div>
</aside>
<div class="dashboard-main">
<header class="dashboard-header">
    <div>Admin</div>
    <div class="user-mini"><strong><?= e($_SESSION['nama']) ?></strong><span><?= e($_SESSION['email']) ?></span></div>
</header>
<main class="dashboard-content">
