<?php
$page_title = "Detail Transaksi";
$active = "transaksi";
include "../config/koneksi.php";
include "header.php";

$id = (int)($_GET['id'] ?? 0);
$query = "SELECT p.*, c.nama customer,c.email,c.no_hp,c.alamat,
          s.nama sales,
          k.kode_unit,k.tahun,k.warna,
          m.nama_merek,t.nama_tipe
          FROM penjualan p
          INNER JOIN customer c ON p.id_customer=c.id_customer
          INNER JOIN sales s ON p.id_sales=s.id_sales
          INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
          INNER JOIN merek m ON k.id_merek=m.id_merek
          INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
          WHERE p.id_penjualan=$id LIMIT 1";
$data = mysqli_fetch_assoc(mysqli_query($conn,$query));
if (!$data) { header("Location: transaksi.php"); exit; }
?>
<div class="page-head"><span class="eyebrow">DETAIL TRANSAKSI</span><h1><?= e($data['no_transaksi']) ?></h1></div>
<div class="card">
    <span class="<?= status_transaksi_class($data['status']) ?>"><?= e($data['status']) ?></span>
    <div class="timeline">
        <div class="timeline-step active">Transaksi Dibuat</div>
        <div class="timeline-step <?= $data['status'] !== 'Menunggu Verifikasi' ? 'active' : '' ?>">Menunggu Verifikasi</div>
        <div class="timeline-step <?= $data['status'] === 'Selesai' ? 'active' : '' ?>">Selesai</div>
    </div>
</div>
<br>
<div class="dashboard-columns">
    <div class="card"><div class="card-title"><h2>Data Customer</h2></div><p><strong><?= e($data['customer']) ?></strong></p><p><?= e($data['no_hp']) ?></p><p><?= e($data['email']) ?></p><p><?= e($data['alamat']) ?></p></div>
    <div class="card"><div class="card-title"><h2>Data Kendaraan</h2></div><p><strong><?= e($data['nama_merek'].' '.$data['nama_tipe']) ?></strong></p><p>Kode Unit: <?= e($data['kode_unit']) ?></p><p>Tahun: <?= e($data['tahun']) ?></p><p>Warna: <?= e($data['warna']) ?></p></div>
</div>
<div class="card"><div class="card-title"><h2>Data Penjualan</h2></div><div class="info-list">
<div class="info-item"><small>Harga</small><strong><?= rupiah($data['harga']) ?></strong></div>
<div class="info-item"><small>Tanggal</small><strong><?= e($data['tanggal']) ?></strong></div>
<div class="info-item"><small>Metode Pembayaran</small><strong><?= e($data['metode_pembayaran']) ?></strong></div>
<div class="info-item"><small>Sales</small><strong><?= e($data['sales']) ?></strong></div>
</div></div>
<?php include "footer.php"; ?>
