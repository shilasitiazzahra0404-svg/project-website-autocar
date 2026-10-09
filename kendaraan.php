<?php
include "config/koneksi.php";
include "config/helpers.php";
include "config/lokasi.php";

$merek = $_GET['merek'] ?? '';
$tipe = $_GET['tipe'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
if ($merek !== '') $where[] = "kendaraan.id_merek = " . (int)$merek;
if ($tipe !== '') $where[] = "kendaraan.id_tipe = " . (int)$tipe;
if ($status !== '') $where[] = "kendaraan.status = '" . mysqli_real_escape_string($conn, $status) . "'";

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT kendaraan.*, merek.nama_merek, tipe_kendaraan.nama_tipe
          FROM kendaraan
          INNER JOIN merek ON kendaraan.id_merek = merek.id_merek
          INNER JOIN tipe_kendaraan ON kendaraan.id_tipe = tipe_kendaraan.id_tipe
          $whereSql
          ORDER BY kendaraan.id_kendaraan DESC";
$result = mysqli_query($conn, $query);

$merekResult = mysqli_query($conn, "SELECT * FROM merek ORDER BY nama_merek");
$tipeResult = mysqli_query($conn, "SELECT * FROM tipe_kendaraan ORDER BY nama_tipe");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan - AutoCar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php"><span class="brand-mark">3SM</span><span>AutoCar<small>SISTEM INFORMASI PENJUALAN</small></span></a>
        <nav class="public-nav">
            <a href="index.php">Home</a>
            <a href="kendaraan.php">Daftar Kendaraan</a>
            <a href="bengkel.php">Daftar Bengkel</a>
            <a class="btn btn-primary btn-small" href="login.php">Login Sistem</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="page-head">
        <div class="page-head-row">
            <div>
                <span class="eyebrow">KATALOG UNIT RESMI</span>
                <h1>Daftar Kendaraan</h1>
                <p>Katalog lengkap seluruh unit kendaraan dealer yang dapat Anda lihat di showroom.</p>
            </div>
            <strong>Total Unit Terdaftar</strong>
        </div>
    </div>

    <form class="filter-bar" method="get">
        <input class="form-control" type="text" name="cari" placeholder="Cari kendaraan..." value="<?= e($_GET['cari'] ?? '') ?>">
        <select class="form-control" name="merek">
            <option value="">Semua Merek</option>
            <?php while ($m = mysqli_fetch_assoc($merekResult)): ?>
                <option value="<?= $m['id_merek'] ?>" <?= $merek == $m['id_merek'] ? 'selected' : '' ?>><?= e($m['nama_merek']) ?></option>
            <?php endwhile; ?>
        </select>
        <select class="form-control" name="tipe">
            <option value="">Semua Tipe</option>
            <?php while ($t = mysqli_fetch_assoc($tipeResult)): ?>
                <option value="<?= $t['id_tipe'] ?>" <?= $tipe == $t['id_tipe'] ? 'selected' : '' ?>><?= e($t['nama_tipe']) ?></option>
            <?php endwhile; ?>
        </select>
        <select class="form-control" name="status">
            <option value="">Semua Status</option>
            <option value="TERSEDIA" <?= $status === 'TERSEDIA' ? 'selected' : '' ?>>Tersedia</option>
            <option value="DIPROSES" <?= $status === 'DIPROSES' ? 'selected' : '' ?>>Diproses</option>
            <option value="TERJUAL" <?= $status === 'TERJUAL' ? 'selected' : '' ?>>Terjual</option>
        </select>
        <button class="btn btn-primary" type="submit">Terapkan Filter</button>
    </form>

    <div class="vehicle-grid">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <article class="vehicle-card">
                <img src="assets/images/<?= e($row['foto'] ?: 'xforce.jpg') ?>" alt="<?= e($row['nama_tipe']) ?>">
                <div class="vehicle-content">
                    <div class="vehicle-brand"><?= e($row['nama_merek']) ?></div>
                    <div class="vehicle-name"><?= e($row['nama_tipe']) ?></div>
                    <div class="vehicle-meta">
                        <span>Tahun <?= e($row['tahun']) ?></span>
                        <span><?= e($row['warna']) ?></span>
                    </div>
                    <span class="<?= status_kendaraan_class($row['status']) ?>"><?= e($row['status']) ?></span>
                    <br><br>
                    <div class="price"><?= rupiah($row['harga']) ?></div>
                    <br>
                    <a class="btn btn-primary btn-small" href="detail_kendaraan.php?id=<?= (int)$row['id_kendaraan'] ?>">Lihat Detail</a>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card empty">Data kendaraan tidak ditemukan.</div>
    <?php endif; ?>
    </div>
</main>


<!-- =====================================================
     LOKASI DEALER
====================================================== -->

<section class="location-section">

    <div class="container">

        <div class="location-box">


            <!-- =========================================
                 INFORMASI DEALER
            ========================================== -->
            <div class="location-info">
                <div class="location-label">
                    📍 Lokasi Kami
                </div>
                <h2 class="location-title">
                    Kunjungi Dealer Kami
                </h2>
                <p class="location-address">
                    Silakan datang langsung ke dealer kami
                    untuk melihat kendaraan dan mendapatkan
                    informasi lebih lanjut dari Sales.
                </p>
                <div class="location-detail">


                    <!-- ALAMAT -->
                    <div class="location-detail-item">
                        <div class="location-detail-icon">
                            📍
                        </div>
                        <div>
                            <small>
                                Alamat Dealer
                            </small>
                            <strong>
                                <?= htmlspecialchars($alamat_dealer) ?>
                            </strong>
                        </div>
                    </div>


                    <!-- JAM OPERASIONAL -->
                    <div class="location-detail-item">
                        <div class="location-detail-icon">
                            🕐
                        </div>
                        <div>
                            <small>
                                Jam Operasional
                            </small>
                            <strong>
                                <?= htmlspecialchars($jam_operasional) ?>
                            </strong>
                        </div>
                    </div>
                    <!-- WHATSAPP -->
                    <div class="location-detail-item">
                        <div class="location-detail-icon">
                            📱
                        </div>
                        <div>
                            <small>
                                WhatsApp / Sales
                            </small>
                            <strong>
                                <?= htmlspecialchars($whatsapp_dealer) ?>
                            </strong>
                        </div>
                    </div>
                </div>


                <!-- TOMBOL GOOGLE MAPS -->
                <a
                    href="<?= htmlspecialchars($maps_link) ?>"
                    target="_blank"
                    class="btn btn-primary location-button"
                >
                    Lihat Lokasi di Google Maps
                </a>
            </div>



            <!-- =========================================
                 GOOGLE MAPS
            ========================================== -->
            <div class="location-map">
                <iframe
                    src="<?= htmlspecialchars($maps_embed) ?>"
                    loading="lazy"
                    allowfullscreen=""
                    referrerpolicy="no-referrer-when-downgrade"
                >
                </iframe>
            </div>
        </div>
    </div>
</section>


<footer class="site-footer">
    <div class="container"><p>© 2026 AutoCar — Sistem Informasi Penjualan Kendaraan.</p></div>
</footer>
<script src="assets/js/script.js"></script>
</body>
</html>
