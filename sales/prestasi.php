<?php
$page_title = "Prestasi Sales";
$active = "prestasi";
include "../config/koneksi.php";
include "header.php";

$id_user=(int)$_SESSION['id_user'];
$s=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_sales FROM sales WHERE id_user=$id_user LIMIT 1"));
$id_sales=(int)($s['id_sales']??0);
$selesai=(int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM penjualan WHERE id_sales=$id_sales AND status='Selesai'"))['total']??0);
$target=10;
$persen=min(100,($selesai/$target)*100);
?>
<div class="page-head"><span class="eyebrow">PERFORMA</span><h1>Prestasi Sales</h1><p>Ringkasan pencapaian penjualan Sales berdasarkan transaksi selesai.</p></div>
<div class="dashboard-grid">
<div class="dashboard-stat"><small>Total Penjualan</small><strong><?=$selesai?> Unit</strong></div>
<div class="dashboard-stat"><small>Target Penjualan</small><strong><?=$target?> Unit</strong></div>
<div class="dashboard-stat"><small>Pencapaian</small><strong><?=number_format($persen,0)?>%</strong></div>
<div class="dashboard-stat"><small>Status</small><strong><?= $selesai >= $target ? 'Target Tercapai' : 'Berjalan' ?></strong></div>
</div>
<div class="card">
<div class="card-title"><h2>Pencapaian Target Penjualan</h2><span><?=$selesai?> / <?=$target?> Unit</span></div>
<div class="progress-value"><span>Progress</span><strong><?=number_format($persen,0)?>%</strong></div>
<div class="progress"><div class="progress-bar" data-progress="<?=$persen?>"></div></div>
</div>
<?php include "footer.php"; ?>
