<?php
$page_title = "Insentif Sales";
$active = "insentif";
include "../config/koneksi.php";
include "header.php";

$id_user=(int)$_SESSION['id_user'];
$s=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_sales FROM sales WHERE id_user=$id_user LIMIT 1"));
$id_sales=(int)($s['id_sales']??0);
$total=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(jumlah_insentif),0) total FROM insentif WHERE id_sales=$id_sales"))['total']??0;
$result=mysqli_query($conn,"SELECT * FROM insentif WHERE id_sales=$id_sales ORDER BY id_insentif DESC");
?>
<div class="page-head"><span class="eyebrow">PENGHARGAAN SALES</span><h1>Insentif Sales</h1><p>Ringkasan dan riwayat insentif berdasarkan penjualan selesai.</p></div>
<div class="dashboard-grid">
<div class="dashboard-stat"><small>Total Insentif</small><strong><?=rupiah($total)?></strong></div>
<div class="dashboard-stat"><small>Insentif Bulan Ini</small><strong><?=rupiah($total)?></strong></div>
<div class="dashboard-stat"><small>Jumlah Data</small><strong><?=mysqli_num_rows($result)?></strong></div>
<div class="dashboard-stat"><small>Status</small><strong>Aktif</strong></div>
</div>
<div class="card"><div class="card-title"><h2>Riwayat Insentif</h2></div><div class="table-wrap"><table class="table">
<tr><th>Periode</th><th>Jumlah Penjualan</th><th>Insentif</th><th>Status</th></tr>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr><td><?=e($row['periode']??'-')?></td><td><?=e($row['jumlah_penjualan']??'-')?></td><td><?=rupiah($row['jumlah_insentif']??0)?></td><td><span class="badge badge-success">Dibayarkan</span></td></tr>
<?php endwhile; ?>
</table></div></div>
<?php include "footer.php"; ?>
