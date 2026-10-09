<?php
$page_title = "Data Customer";
$active = "customer";
include "../config/koneksi.php";
include "header.php";

$result = mysqli_query($conn, "SELECT * FROM customer ORDER BY id_customer DESC");
?>
<div class="page-head">
    <div class="page-head-row">
        <div><span class="eyebrow">MODUL SALES</span><h1>Data Customer</h1><p>Kelola data customer yang ditangani oleh Sales.</p></div>
        <a class="btn btn-primary" href="tambah_customer.php">+ Tambah Customer</a>
    </div>
</div>

<div class="card">
    <div class="card-title"><h2>Daftar Customer</h2><input class="form-control" id="tableSearch" placeholder="Cari customer..."></div>
    <div class="table-wrap">
        <table class="table" id="dataTable">
            <tr><th>No</th><th>Nama</th><th>No. HP</th><th>Email</th><th>Alamat</th><th>Aksi</th></tr>
            <?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td><td><?= e($row['nama']) ?></td><td><?= e($row['no_hp']) ?></td>
                <td><?= e($row['email']) ?></td><td><?= e($row['alamat']) ?></td>
                <td><a class="btn btn-secondary btn-small" href="detail_customer.php?id=<?= $row['id_customer'] ?>">Detail</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<script src="../assets/js/table-search.js"></script>
<?php include "footer.php"; ?>
