<?php
$page_title = "History Penjualan";
$active = "history";
include "../config/koneksi.php";
include "header.php";

$id_user=(int)$_SESSION['id_user'];
$s=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_sales FROM sales WHERE id_user=$id_user LIMIT 1"));
$id_sales=(int)($s['id_sales']??0);
$result=mysqli_query($conn,"SELECT p.*,c.nama customer,m.nama_merek,t.nama_tipe
FROM penjualan p INNER JOIN customer c ON p.id_customer=c.id_customer INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
INNER JOIN merek m ON k.id_merek=m.id_merek INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
WHERE p.id_sales=$id_sales AND p.status='Selesai' ORDER BY p.tanggal DESC");
?>
<div class="page-head"><span class="eyebrow">RIWAYAT</span><h1>History Penjualan</h1><p>Riwayat transaksi penjualan kendaraan yang telah selesai.</p></div>
<div class="card"><div class="table-wrap"><table class="table">
<tr><th>No Transaksi</th><th>Tanggal</th><th>Customer</th><th>Kendaraan</th><th>Harga</th><th>Status</th></tr>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr><td><?=e($row['no_transaksi'])?></td><td><?=e($row['tanggal'])?></td><td><?=e($row['customer'])?></td><td><?=e($row['nama_merek'].' '.$row['nama_tipe'])?></td><td><?=rupiah($row['harga'])?></td><td><span class="badge badge-success">Selesai</span></td></tr>
<?php endwhile; ?>
</table></div></div>
<?php include "footer.php"; ?>
