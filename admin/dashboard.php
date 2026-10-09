<?php
$page_title="Dashboard Admin";
$active="dashboard";
include "../config/koneksi.php";
include "header.php";

$kendaraan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM kendaraan"))['total']??0;
$tersedia=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM kendaraan WHERE status='TERSEDIA'"))['total']??0;
$customer=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM customer"))['total']??0;
$sales=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM sales"))['total']??0;
$penjualan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan"))['total']??0;
$pending=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan WHERE status='Menunggu Verifikasi'"))['total']??0;
?>
<div class="welcome">
<div><span class="eyebrow">EXECUTIVE DESK</span><h1>Selamat Datang, <?=e($_SESSION['nama'])?>!</h1><p>Kelola kendaraan, customer, sales, verifikasi transaksi, dan laporan dealer.</p></div>
<a class="btn btn-secondary btn-small" href="verifikasi.php">Verifikasi <?=$pending?> Transaksi</a>
</div>
<div class="dashboard-grid">
<div class="dashboard-stat"><small>Total Kendaraan</small><strong><?=$kendaraan?> Unit</strong></div>
<div class="dashboard-stat"><small>Kendaraan Tersedia</small><strong><?=$tersedia?> Unit</strong></div>
<div class="dashboard-stat"><small>Total Customer</small><strong><?=$customer?></strong></div>
<div class="dashboard-stat"><small>Total Sales</small><strong><?=$sales?></strong></div>
<div class="dashboard-stat"><small>Total Penjualan</small><strong><?=$penjualan?></strong></div>
<div class="dashboard-stat"><small>Menunggu Verifikasi</small><strong><?=$pending?></strong></div>
</div>
<div class="dashboard-columns">
<div class="card"><div class="card-title"><h2>Perkembangan Penjualan</h2><span>Ringkasan</span></div><div class="chart">
<?php foreach ([38,52,43,64,76,91] as $i=>$h): ?><div class="bar-wrap"><div class="bar <?=$i===5?'current':''?>" data-height="<?=$h?>"></div><span class="bar-label"><?=['Jan','Feb','Mar','Apr','Mei','Jun'][$i]?></span></div><?php endforeach; ?>
</div></div>
<div class="card"><div class="card-title"><h2>Status Verifikasi</h2></div><h1><?=$pending?></h1><p>Transaksi menunggu otorisasi Admin/Atasan.</p><br><a class="btn btn-primary btn-small" href="verifikasi.php">Buka Verifikasi</a></div>
</div>
<div class="card"><div class="card-title"><h2>Transaksi Terbaru</h2><a href="verifikasi.php">Lihat Semua</a></div>
<div class="table-wrap"><table class="table"><tr><th>No Transaksi</th><th>Customer</th><th>Kendaraan</th><th>Sales</th><th>Harga</th><th>Status</th></tr>
<?php
$q="SELECT p.*,c.nama customer,s.nama sales,m.nama_merek,t.nama_tipe FROM penjualan p
INNER JOIN customer c ON p.id_customer=c.id_customer INNER JOIN sales s ON p.id_sales=s.id_sales INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
INNER JOIN merek m ON k.id_merek=m.id_merek INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe ORDER BY p.id_penjualan DESC LIMIT 5";
$r=mysqli_query($conn,$q);
while($row=mysqli_fetch_assoc($r)): ?>
<tr><td><?=e($row['no_transaksi'])?></td><td><?=e($row['customer'])?></td><td><?=e($row['nama_merek'].' '.$row['nama_tipe'])?></td><td><?=e($row['sales'])?></td><td><?=rupiah($row['harga'])?></td><td><span class="<?=status_transaksi_class($row['status'])?>"><?=e($row['status'])?></span></td></tr>
<?php endwhile; ?></table></div></div>
<?php include "footer.php"; ?>
