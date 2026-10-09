<?php
$page_title = "Data Kredit";
$active = "kredit";

include "../config/koneksi.php";
include "header.php";


$result = mysqli_query($conn, "
    SELECT
        k.*,
        p.no_transaksi,
        p.tanggal,
        c.nama AS customer,
        s.nama AS sales,
        m.nama_merek,
        t.nama_tipe

    FROM kredit k

    INNER JOIN penjualan p
        ON k.id_penjualan = p.id_penjualan

    INNER JOIN customer c
        ON p.id_customer = c.id_customer

    INNER JOIN sales s
        ON p.id_sales = s.id_sales

    INNER JOIN kendaraan v
        ON p.id_kendaraan = v.id_kendaraan

    INNER JOIN merek m
        ON v.id_merek = m.id_merek

    INNER JOIN tipe_kendaraan t
        ON v.id_tipe = t.id_tipe

    ORDER BY k.id_kredit DESC
");
?>

<div class="page-head">

    <span class="eyebrow">
        DATA PEMBIAYAAN
    </span>

    <h1>
        Data Kredit
    </h1>

    <p>
        Daftar transaksi kendaraan yang menggunakan pembayaran kredit.
    </p>

</div>


<div class="card">

    <div class="table-wrap">

        <table class="table">

            <tr>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Kendaraan</th>
                <th>Harga</th>
                <th>DP</th>
                <th>Tenor</th>
                <th>Bunga</th>
                <th>Angsuran/Bulan</th>
                <th>Status</th>
            </tr>


            <?php while($row = mysqli_fetch_assoc($result)): ?>

            <tr>

                <td>
                    <?= e($row['no_transaksi']) ?>
                </td>

                <td>
                    <?= e($row['customer']) ?>
                </td>

                <td>
                    <?= e($row['nama_merek'].' '.$row['nama_tipe']) ?>
                </td>

                <td>
                    <?= rupiah($row['harga_kendaraan']) ?>
                </td>

                <td>
                    <?= rupiah($row['dp']) ?>
                </td>

                <td>
                    <?= e($row['tenor']) ?> Bulan
                </td>

                <td>
                    <?= e($row['bunga_tahunan']) ?>%
                </td>

                <td>
                    <?= rupiah($row['angsuran_per_bulan']) ?>
                </td>

                <td>

                    <?php if($row['status_kredit'] === 'Aktif'): ?>

                        <span class="badge badge-warning">
                            Aktif
                        </span>

                    <?php elseif($row['status_kredit'] === 'Lunas'): ?>

                        <span class="badge badge-success">
                            Lunas
                        </span>

                    <?php else: ?>

                        <span class="badge badge-danger">
                            <?= e($row['status_kredit']) ?>
                        </span>

                    <?php endif; ?>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>


<?php include "footer.php"; ?>