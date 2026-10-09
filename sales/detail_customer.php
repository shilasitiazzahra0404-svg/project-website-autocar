<?php
$page_title = "Detail Customer";
$active = "customer";
include "../config/koneksi.php";
include "header.php";

$id = (int)($_GET['id'] ?? 0);
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer WHERE id_customer=$id LIMIT 1"));
if (!$customer) { header("Location: customer.php"); exit; }

$history = mysqli_query($conn, "SELECT p.*, CONCAT(m.nama_merek,' ',t.nama_tipe) kendaraan
    FROM penjualan p
    INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
    INNER JOIN merek m ON k.id_merek=m.id_merek
    INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
    WHERE p.id_customer=$id ORDER BY p.id_penjualan DESC");
?>
<div class="page-head"><span class="eyebrow">DATA CUSTOMER</span><h1>Detail Customer</h1></div>
<div class="dashboard-columns">
    <div class="card">
        <div class="card-title"><h2><?= e($customer['nama']) ?></h2><span>Customer</span></div>
        <div class="info-list">
            <div class="info-item"><small>No. HP</small><strong><?= e($customer['no_hp']) ?></strong></div>
            <div class="info-item"><small>Email</small><strong><?= e($customer['email']) ?></strong></div>
            <div class="info-item full"><small>Alamat</small><strong><?= e($customer['alamat']) ?></strong></div>
        </div>
    </div>
    <div class="card">
        <div class="card-title"><h2>Ringkasan</h2></div>
        <?php $total = mysqli_num_rows($history); ?>
        <h1><?= $total ?></h1><p>Total transaksi customer.</p>
    </div>
</div>
<div class="card">
    <div class="card-title"><h2>Riwayat Pembelian</h2></div>
    <div class="table-wrap">
        <table class="table">
            <tr><th>No Transaksi</th><th>Kendaraan</th><th>Harga</th><th>Tanggal</th><th>Status</th></tr>
            <?php while($row=mysqli_fetch_assoc($history)): ?>
            <tr><td><?= e($row['no_transaksi']) ?></td><td><?= e($row['kendaraan']) ?></td><td><?= rupiah($row['harga']) ?></td><td><?= e($row['tanggal']) ?></td><td><span class="<?= status_transaksi_class($row['status']) ?>"><?= e($row['status']) ?></span></td></tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php include "footer.php"; ?>
