<?php
$page_title = "Kendaraan Saya";
$active = "kendaraan";
include "../config/koneksi.php";
include "header.php";

$id_user=(int)$_SESSION['id_user'];
$s=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_sales FROM sales WHERE id_user=$id_user LIMIT 1"));
$id_sales=(int)($s['id_sales']??0);
$result=mysqli_query($conn,"SELECT p.*,c.nama customer,k.kode_unit,k.status kendaraan_status,m.nama_merek,t.nama_tipe
FROM penjualan p INNER JOIN customer c ON p.id_customer=c.id_customer INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
INNER JOIN merek m ON k.id_merek=m.id_merek INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
WHERE p.id_sales=$id_sales ORDER BY p.id_penjualan DESC");
?>
<div class="page-head"><span class="eyebrow">MODUL SALES</span><h1>Kendaraan Saya</h1><p>Daftar kendaraan yang ditangani dan berhasil dijual oleh Anda.</p></div>
<div class="card"><div class="table-wrap"><table class="table">
<tr><th>Kode Unit</th><th>Kendaraan</th><th>Customer</th><th>Harga</th><th>Tanggal</th><th>Status</th></tr>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr><td><?=e($row['kode_unit'])?></td><td><?=e($row['nama_merek'].' '.$row['nama_tipe'])?></td><td><?=e($row['customer'])?></td><td><?=rupiah($row['harga'])?></td><td><?=e($row['tanggal'])?></td><td><span class="<?=status_kendaraan_class($row['kendaraan_status'])?>"><?=e($row['kendaraan_status'])?></span></td></tr>
<?php endwhile; ?>
</table></div></div>
<?php include "footer.php"; ?>
