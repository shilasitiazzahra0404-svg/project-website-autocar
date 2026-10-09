<?php
$page_title = "Dashboard Sales";
$active = "dashboard";
include "../config/koneksi.php";
include "header.php";

$id_user = (int)$_SESSION['id_user'];
$salesRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_sales FROM sales WHERE id_user = $id_user LIMIT 1"));
$id_sales = (int)($salesRow['id_sales'] ?? 0);

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM penjualan WHERE id_sales = $id_sales"))['total'] ?? 0;
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM penjualan WHERE id_sales = $id_sales AND status='Menunggu Verifikasi'"))['total'] ?? 0;
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM penjualan WHERE id_sales = $id_sales AND status='Selesai'"))['total'] ?? 0;
$insentif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah_insentif),0) total FROM insentif WHERE id_sales = $id_sales"))['total'] ?? 0;
?>
<div class="welcome">
    <div>
        <span class="eyebrow">ID SALES: <?= e($id_sales) ?></span>
        <h1>Selamat Datang, <?= e($_SESSION['nama']) ?>!</h1>
        <p>Berikut adalah ringkasan penjualan dan status transaksi kendaraan Anda hari ini.</p>
    </div>
    <div class="action-group">
        <a class="btn btn-secondary btn-small" href="tambah_customer.php">+ Tambah Customer</a>
        <a class="btn btn-secondary btn-small" href="transaksi.php">+ Buat Transaksi</a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-stat"><small>Total Penjualan</small><strong><?= $total ?> Unit</strong><span>Seluruh transaksi Anda</span></div>
    <div class="dashboard-stat"><small>Menunggu Verifikasi</small><strong><?= $pending ?> Transaksi</strong><span>Perlu otorisasi Admin</span></div>
    <div class="dashboard-stat"><small>Penjualan Selesai</small><strong><?= $selesai ?> Transaksi</strong><span>Unit terjual</span></div>
    <div class="dashboard-stat"><small>Total Insentif</small><strong><?= rupiah($insentif) ?></strong><span>Akumulasi insentif</span></div>
</div>

<div class="dashboard-columns">
    <div class="card">
        <div class="card-title"><h2>Perkembangan Penjualan Sales</h2><span>Data transaksi</span></div>
        <div class="chart">
            <?php
            $heights = [35, 48, 28, 58, 74, 88];
            $months = ['Jan','Feb','Mar','Apr','Mei','Jun'];
            foreach ($heights as $i => $height):
            ?>
                <div class="bar-wrap">
                    <div class="bar <?= $i === 5 ? 'current' : '' ?>" data-height="<?= $height ?>"></div>
                    <span class="bar-label"><?= $months[$i] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-title"><h2>Target Penjualan</h2><span>Kuartal</span></div>
        <div class="progress-value"><strong><?= $selesai ?> / 10 Unit</strong><strong><?= min(100, $selesai * 10) ?>%</strong></div>
        <div class="progress"><div class="progress-bar" data-progress="<?= min(100, $selesai * 10) ?>"></div></div>
        <br>
        <p>Target penjualan dapat disesuaikan dengan periode dan kebijakan dealer.</p>
    </div>
</div>

<div class="card">
    <div class="card-title"><h2>Daftar Transaksi Terbaru Anda</h2><a href="transaksi.php">Lihat Semua</a></div>
    <div class="table-wrap">
        <table class="table">
            <tr><th>No Transaksi</th><th>Customer</th><th>Kendaraan</th><th>Harga</th><th>Tanggal</th><th>Status</th></tr>
            <?php
            $q = "SELECT p.*, c.nama customer, CONCAT(m.nama_merek,' ',t.nama_tipe) kendaraan
                  FROM penjualan p
                  INNER JOIN customer c ON p.id_customer=c.id_customer
                  INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
                  INNER JOIN merek m ON k.id_merek=m.id_merek
                  INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
                  WHERE p.id_sales=$id_sales ORDER BY p.id_penjualan DESC LIMIT 5";
            $r = mysqli_query($conn, $q);
            while ($row = mysqli_fetch_assoc($r)):
            ?>
            <tr>
                <td><?= e($row['no_transaksi']) ?></td>
                <td><?= e($row['customer']) ?></td>
                <td><?= e($row['kendaraan']) ?></td>
                <td><?= rupiah($row['harga']) ?></td>
                <td><?= e($row['tanggal']) ?></td>
                <td><span class="<?= status_transaksi_class($row['status']) ?>"><?= e($row['status']) ?></span></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php include "footer.php"; ?>
