<?php
$page_title="Report Penjualan";
$active="laporan";
include "../config/koneksi.php";
include "header.php";

$total=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan"))['total']??0;
$selesai=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan WHERE status='Selesai'"))['total']??0;
$nilai=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(harga),0) total FROM penjualan WHERE status='Selesai'"))['total']??0;
$result=mysqli_query($conn,"SELECT p.*,c.nama customer,s.nama sales,m.nama_merek,t.nama_tipe FROM penjualan p
INNER JOIN customer c ON p.id_customer=c.id_customer INNER JOIN sales s ON p.id_sales=s.id_sales INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
INNER JOIN merek m ON k.id_merek=m.id_merek INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe ORDER BY p.tanggal DESC");
?>
<div class="page-head"><div class="page-head-row"><div><span class="eyebrow">LAPORAN</span><h1>Report Penjualan</h1><p>Laporan transaksi penjualan kendaraan.</p></div><button class="btn btn-primary btn-small" onclick="window.print()">Cetak Laporan</button></div></div>
<div class="dashboard-grid"><div class="dashboard-stat"><small>Total Transaksi</small><strong><?=$total?></strong></div><div class="dashboard-stat"><small>Transaksi Selesai</small><strong><?=$selesai?></strong></div><div class="dashboard-stat"><small>Total Nilai Penjualan</small><strong><?=rupiah($nilai)?></strong></div></div>
<div class="card"><div class="table-wrap"><table class="table"><tr><th>No</th><th>No Transaksi</th><th>Tanggal</th><th>Customer</th><th>Kendaraan</th><th>Sales</th><th>Harga</th><th>Status</th></tr>
<?php $no=1;while($row=mysqli_fetch_assoc($result)): ?><tr><td><?=$no++?></td><td><?=e($row['no_transaksi'])?></td><td><?=e($row['tanggal'])?></td><td><?=e($row['customer'])?></td><td><?=e($row['nama_merek'].' '.$row['nama_tipe'])?></td><td><?=e($row['sales'])?></td><td><?=rupiah($row['harga'])?></td><td><span class="<?=status_transaksi_class($row['status'])?>"><?=e($row['status'])?></span></td></tr><?php endwhile;?>
</table></div></div>
<?php include "footer.php"; ?>
