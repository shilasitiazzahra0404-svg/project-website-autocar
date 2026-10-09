<?php
include "config/koneksi.php";
include "config/helpers.php";
include "config/lokasi.php";

$query = "SELECT kendaraan.*, merek.nama_merek, tipe_kendaraan.nama_tipe
          FROM kendaraan
          INNER JOIN merek ON kendaraan.id_merek = merek.id_merek
          INNER JOIN tipe_kendaraan ON kendaraan.id_tipe = tipe_kendaraan.id_tipe
          ORDER BY kendaraan.id_kendaraan DESC
          LIMIT 4";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoCar - Sistem Informasi Penjualan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">
            <span class="brand-mark">3SM</span>
            <span>AutoCar<small>SISTEM INFORMASI PENJUALAN</small></span>
        </a>
        <nav class="public-nav">
            <a href="index.php">Home</a>
            <a href="kendaraan.php">Daftar Kendaraan</a>
            <a href="bengkel.php">Daftar Bengkel</a>
            <a class="btn btn-primary btn-small" href="login.php">Login Sistem</a>
        </nav>
    </div>
</header>

<main>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">SHOWROOM RESMI & TERORGANISASI</span>
            <h1>Temukan Kendaraan Impian Anda</h1>
            <p>Temukan berbagai pilihan kendaraan yang tersedia sesuai kebutuhan Anda dengan informasi unit yang jelas dan proses transaksi yang terdata.</p>
            <a class="btn btn-primary" href="kendaraan.php">Lihat Kendaraan</a>
            <div class="stats-public">
                <div class="stat-card"><strong>38+ Unit</strong><span>Unit tersedia di showroom</span></div>
                <div class="stat-card"><strong>15+ Tipe</strong><span>Inspeksi dan informasi kendaraan</span></div>
                <div class="stat-card"><strong>Ramah Sales</strong><span>Konsultasi langsung di dealer</span></div>
            </div>
        </div>
        <div>
            <img class="hero-image" src="assets/images/city.jpg" alt="Kendaraan showroom">
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <span class="eyebrow">READY ON SHOWROOM</span>
            <h2>Kendaraan Pilihan</h2>
            <p>Pilihan unit populer dengan kondisi siap Anda lihat langsung di showroom.</p>
        </div>

        <div class="vehicle-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <article class="vehicle-card">
                <img src="assets/images/<?= e($row['foto'] ?: 'city.jpg') ?>" alt="<?= e($row['nama_tipe']) ?>">
                <div class="vehicle-content">
                    <span class="<?= status_kendaraan_class($row['status']) ?>"><?= e($row['status']) ?></span>
                    <div class="vehicle-brand"><?= e($row['nama_merek']) ?></div>
                    <div class="vehicle-name"><?= e($row['nama_tipe']) ?></div>
                    <div class="vehicle-meta">
                        <span>Tahun <?= e($row['tahun']) ?></span>
                        <span><?= e($row['warna']) ?></span>
                    </div>
                    <div class="price"><?= rupiah($row['harga']) ?></div>
                    <br>
                    <a class="btn btn-primary btn-small" href="detail_kendaraan.php?id=<?= (int)$row['id_kendaraan'] ?>">Lihat Detail</a>
                </div>
            </article>
        <?php endwhile; ?>
        </div>
    </div>
</section>
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
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>


<footer class="site-footer">
    <div class="container">
        <p>© 2026 AutoCar — Sistem Informasi Penjualan Kendaraan.</p>
    </div>
</footer>
<script src="assets/js/script.js"></script>
</body>
</html>
