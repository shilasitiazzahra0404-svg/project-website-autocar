<?php
$page_title="Data Sales";
$active="sales";
include "../config/koneksi.php";
include "header.php";
$result=mysqli_query($conn,"SELECT s.*,COUNT(p.id_penjualan) total_penjualan,COALESCE(SUM(i.jumlah_insentif),0) total_insentif
FROM sales s LEFT JOIN penjualan p ON s.id_sales=p.id_sales LEFT JOIN insentif i ON s.id_sales=i.id_sales
GROUP BY s.id_sales ORDER BY s.id_sales DESC");
?>
<div class="page-head"><span class="eyebrow">MASTER DATA</span><h1>Data Sales</h1><p>Kelola data Sales yang terdaftar pada dealer.</p></div>
<div class="card"><div class="table-wrap"><table class="table">
<tr><th>Nama Sales</th><th>Email</th><th>No. HP</th><th>Tanggal Bergabung</th><th>Status</th><th>Total Penjualan</th><th>Total Insentif</th></tr>
<?php while($row=mysqli_fetch_assoc($result)): ?><tr><td><?=e($row['nama'])?></td><td><?=e($row['email'])?></td><td><?=e($row['no_hp'])?></td><td><?=e($row['tanggal_bergabung'])?></td><td><span class="badge badge-success"><?=e($row['status'])?></span></td><td><?=$row['total_penjualan']?></td><td><?=rupiah($row['total_insentif'])?></td></tr><?php endwhile;?>
</table></div></div>
<?php include "footer.php"; ?>
