<?php
$page_title="Kelola Customer";
$active="customer";
include "../config/koneksi.php";
include "header.php";
$result=mysqli_query($conn,"SELECT c.*,COUNT(p.id_penjualan) total_transaksi FROM customer c LEFT JOIN penjualan p ON c.id_customer=p.id_customer GROUP BY c.id_customer ORDER BY c.id_customer DESC");
?>
<div class="page-head"><span class="eyebrow">MASTER DATA</span><h1>Kelola Customer</h1><p>Data customer yang tercatat dalam sistem.</p></div>
<div class="card"><div class="table-wrap"><table class="table">
<tr><th>No</th><th>Nama</th><th>No. HP</th><th>Email</th><th>Alamat</th><th>Total Transaksi</th></tr>
<?php $no=1;while($row=mysqli_fetch_assoc($result)): ?><tr><td><?=$no++?></td><td><?=e($row['nama'])?></td><td><?=e($row['no_hp'])?></td><td><?=e($row['email'])?></td><td><?=e($row['alamat'])?></td><td><?=$row['total_transaksi']?></td></tr><?php endwhile;?>
</table></div></div>
<?php include "footer.php"; ?>
