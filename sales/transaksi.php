<?php
$page_title = "Transaksi Penjualan";
$active = "transaksi";
include "../config/koneksi.php";
include "header.php";

$id_user = (int)$_SESSION['id_user'];
$salesRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_sales FROM sales WHERE id_user=$id_user LIMIT 1"));
$id_sales = (int)($salesRow['id_sales'] ?? 0);

$result = mysqli_query($conn, "SELECT p.*, c.nama customer, CONCAT(m.nama_merek,' ',t.nama_tipe) kendaraan
    FROM penjualan p
    INNER JOIN customer c ON p.id_customer=c.id_customer
    INNER JOIN kendaraan k ON p.id_kendaraan=k.id_kendaraan
    INNER JOIN merek m ON k.id_merek=m.id_merek
    INNER JOIN tipe_kendaraan t ON k.id_tipe=t.id_tipe
    WHERE p.id_sales=$id_sales ORDER BY p.id_penjualan DESC");
?>
<div class="page-head">
    <div class="page-head-row">
        <div><span class="eyebrow">MODUL SALES</span><h1>Transaksi Penjualan</h1><p>Kelola transaksi penjualan kendaraan yang ditangani.</p></div>
        <a class="btn btn-primary" href="tambah_transaksi.php">+ Buat Transaksi Penjualan</a>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table class="table">
            <tr><th>No Transaksi</th><th>Tanggal</th><th>Customer</th><th>Kendaraan</th><th>Harga</th><th>Metode Bayar</th><th>Status</th><th>Aksi</th></tr>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= e($row['no_transaksi']) ?></td><td><?= e($row['tanggal']) ?></td><td><?= e($row['customer']) ?></td>
                <td><?= e($row['kendaraan']) ?></td><td><?= rupiah($row['harga']) ?></td><td><?= e($row['metode_pembayaran']) ?></td>
                <td><span class="<?= status_transaksi_class($row['status']) ?>"><?= e($row['status']) ?></span></td>
                <td><a class="btn btn-secondary btn-small" href="detail_transaksi.php?id=<?= $row['id_penjualan'] ?>">Detail</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php include "footer.php"; ?>
