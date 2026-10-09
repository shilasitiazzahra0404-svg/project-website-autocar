<?php
$page_title="Kelola Kendaraan";
$active="kendaraan";
include "../config/koneksi.php";
include "header.php";

if (isset($_GET['hapus'])) {
    $id=(int)$_GET['hapus'];
    mysqli_query($conn,"DELETE FROM kendaraan WHERE id_kendaraan=$id");
    header("Location: kendaraan.php"); exit;
}
$result=mysqli_query($conn,"SELECT k.*,m.nama_merek,t.nama_tipe FROM kendaraan k INNER JOIN merek m ON k.id_merek=m.id_merek INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe ORDER BY k.id_kendaraan DESC");
?>
<div class="page-head"><div class="page-head-row"><div><span class="eyebrow">MASTER DATA</span><h1>Kelola Kendaraan</h1><p>Kelola seluruh data kendaraan dealer.</p></div><a class="btn btn-primary" href="tambah_kendaraan.php">+ Tambah Kendaraan</a></div></div>
<div class="card"><div class="table-wrap"><table class="table">
<tr><th>Kode Unit</th><th>Foto</th><th>Merek</th><th>Tipe</th><th>Tahun</th><th>Warna</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr><td><?=e($row['kode_unit'])?></td><td><?=e($row['foto'])?></td><td><?=e($row['nama_merek'])?></td><td><?=e($row['nama_tipe'])?></td><td><?=e($row['tahun'])?></td><td><?=e($row['warna'])?></td><td><?=rupiah($row['harga'])?></td><td><span class="<?=status_kendaraan_class($row['status'])?>"><?=e($row['status'])?></span></td>
<td class="action-group"><a class="btn btn-secondary btn-small" href="../detail_kendaraan.php?id=<?=$row['id_kendaraan']?>">Detail</a><a class="btn btn-danger btn-small" data-confirm="Hapus kendaraan ini?" href="kendaraan.php?hapus=<?=$row['id_kendaraan']?>">Hapus</a></td></tr>
<?php endwhile; ?></table></div></div>
<?php include "footer.php"; ?>
