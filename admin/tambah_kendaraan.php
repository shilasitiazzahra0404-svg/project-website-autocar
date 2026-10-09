<?php
$page_title="Tambah Kendaraan";
$active="kendaraan";
include "../config/koneksi.php";
include "header.php";

$mereks=mysqli_query($conn,"SELECT * FROM merek ORDER BY nama_merek");
$tipes=mysqli_query($conn,"SELECT * FROM tipe_kendaraan ORDER BY nama_tipe");
$error="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $kode=mysqli_real_escape_string($conn,$_POST['kode_unit']);
    $id_merek=(int)$_POST['id_merek'];
    $id_tipe=(int)$_POST['id_tipe'];
    $tahun=(int)$_POST['tahun'];
    $warna=mysqli_real_escape_string($conn,$_POST['warna']);
    $harga=(float)$_POST['harga'];
    $foto=mysqli_real_escape_string($conn,$_POST['foto']);
    $status=mysqli_real_escape_string($conn,$_POST['status']);
    $deskripsi=mysqli_real_escape_string($conn,$_POST['deskripsi']);
    $q="INSERT INTO kendaraan(kode_unit,id_merek,id_tipe,tahun,warna,harga,foto,status,deskripsi) VALUES('$kode',$id_merek,$id_tipe,$tahun,'$warna',$harga,'$foto','$status','$deskripsi')";
    if(mysqli_query($conn,$q)){header("Location: kendaraan.php");exit;}
    $error="Gagal menyimpan kendaraan: ".mysqli_error($conn);
}
?>
<div class="page-head"><span class="eyebrow">MASTER KENDARAAN</span><h1>Tambah Kendaraan</h1><p>Masukkan data unit kendaraan baru.</p></div>
<div class="card form-card">
<?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
<form method="post"><div class="form-grid">
<div class="form-group"><label>Kode Unit</label><input class="form-control" name="kode_unit" required></div>
<div class="form-group"><label>Merek</label><select class="form-control" name="id_merek" required><option value="">Pilih Merek</option><?php while($m=mysqli_fetch_assoc($mereks)): ?><option value="<?=$m['id_merek']?>"><?=e($m['nama_merek'])?></option><?php endwhile;?></select></div>
<div class="form-group"><label>Tipe Kendaraan</label><select class="form-control" name="id_tipe" required><option value="">Pilih Tipe</option><?php while($t=mysqli_fetch_assoc($tipes)): ?><option value="<?=$t['id_tipe']?>"><?=e($t['nama_tipe'])?></option><?php endwhile;?></select></div>
<div class="form-group"><label>Tahun</label><input class="form-control" type="number" name="tahun" value="<?=date('Y')?>" required></div>
<div class="form-group"><label>Warna</label><input class="form-control" name="warna"></div>
<div class="form-group"><label>Harga</label><input class="form-control" type="number" name="harga" required></div>
<div class="form-group"><label>Nama File Foto</label><input class="form-control" name="foto" placeholder="contoh: avanza.jpg"></div>
<div class="form-group"><label>Status</label><select class="form-control" name="status"><option>TERSEDIA</option><option>DIPROSES</option><option>TERJUAL</option></select></div>
<div class="form-group full"><label>Deskripsi</label><textarea class="form-control" name="deskripsi"></textarea></div>
</div><div class="action-group"><button class="btn btn-primary">Simpan Kendaraan</button><a class="btn btn-secondary" href="kendaraan.php">Batal</a></div></form>
</div>
<?php include "footer.php"; ?>
