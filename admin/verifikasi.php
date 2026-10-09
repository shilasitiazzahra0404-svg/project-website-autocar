<?php

$page_title = "Verifikasi Penjualan";
$active = "verifikasi";

include "../config/koneksi.php";
include "header.php";


/* =========================================================
   PROSES VERIFIKASI
========================================================= */

if (isset($_GET['verifikasi'])) {

    $id = (int)$_GET['verifikasi'];


    /* =====================================================
       AMBIL DATA PENJUALAN
    ===================================================== */

    $row = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT *
             FROM penjualan
             WHERE id_penjualan=$id
             AND status='Menunggu Verifikasi'
             LIMIT 1"
        )
    );


    if ($row) {

        mysqli_begin_transaction($conn);


        try {

            /* =================================================
               1. UBAH STATUS PENJUALAN
            ================================================= */

            $updatePenjualan = mysqli_query(
                $conn,
                "UPDATE penjualan
                 SET status='Selesai'
                 WHERE id_penjualan=$id"
            );


            if (!$updatePenjualan) {
                throw new Exception(
                    "Gagal mengubah status penjualan."
                );
            }


            /* =================================================
               2. UBAH STATUS KENDARAAN
            ================================================= */

            $updateKendaraan = mysqli_query(
                $conn,
                "UPDATE kendaraan
                 SET status='TERJUAL'
                 WHERE id_kendaraan="
                 . (int)$row['id_kendaraan']
            );


            if (!$updateKendaraan) {
                throw new Exception(
                    "Gagal mengubah status kendaraan."
                );
            }


            /* =================================================
               3. SIMPAN HISTORY PENJUALAN
            ================================================= */

            $history = mysqli_query(
                $conn,
                "INSERT INTO history_penjualan
                 (id_penjualan)
                 VALUES ($id)"
            );


            if (!$history) {
                throw new Exception(
                    "Gagal menyimpan history penjualan."
                );
            }


            /* =================================================
               4. INSENTIF SALES
            ================================================= */

            $cek = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT id_insentif
                     FROM insentif
                     WHERE id_sales="
                     . (int)$row['id_sales']
                     . "
                     LIMIT 1"
                )
            );


            if ($cek) {

                $updateInsentif = mysqli_query(
                    $conn,
                    "UPDATE insentif
                     SET
                        jumlah_penjualan =
                            jumlah_penjualan + 1,

                        jumlah_insentif =
                            jumlah_insentif + 500000

                     WHERE id_insentif="
                     . (int)$cek['id_insentif']
                );


                if (!$updateInsentif) {
                    throw new Exception(
                        "Gagal mengupdate insentif."
                    );
                }

            } else {

                $periode = date('Y-m');


                $insertInsentif = mysqli_query(
                    $conn,
                    "INSERT INTO insentif
                     (
                        id_sales,
                        periode,
                        jumlah_penjualan,
                        jumlah_insentif
                     )
                     VALUES
                     (
                        "
                        . (int)$row['id_sales']
                        . ",
                        '$periode',
                        1,
                        500000
                     )"
                );


                if (!$insertInsentif) {
                    throw new Exception(
                        "Gagal membuat data insentif."
                    );
                }
            }


            /* =================================================
               5. PROSES KREDIT
            ================================================= */

            if ($row['metode_pembayaran'] === 'Kredit') {


                /* =============================================
                   CEK APAKAH DATA KREDIT SUDAH ADA
                ============================================= */

                $cekKredit = mysqli_fetch_assoc(
                    mysqli_query(
                        $conn,
                        "SELECT id_kredit
                         FROM kredit
                         WHERE id_penjualan=$id
                         LIMIT 1"
                    )
                );


                if (!$cekKredit) {


                    /* =========================================
                       AMBIL DATA ASURANSI
                    ========================================= */

                    $asuransiRow = mysqli_fetch_assoc(
                        mysqli_query(
                            $conn,
                            "SELECT
                                jenis_asuransi,
                                biaya_asuransi
                             FROM transaksi_asuransi
                             WHERE id_penjualan=$id
                             LIMIT 1"
                        )
                    );


                    /*
                     * Jika transaksi tidak memiliki data
                     * asuransi, otomatis dianggap tanpa asuransi.
                     */

                    $jenis_asuransi =
                        $asuransiRow['jenis_asuransi']
                        ?? 'Tanpa Asuransi';


                    $biaya_asuransi =
                        (float)(
                            $asuransiRow['biaya_asuransi']
                            ?? 0
                        );


                    /* =========================================
                       DATA KREDIT DARI PENJUALAN
                    ========================================= */

                    $harga_kendaraan =
                        (float)$row['harga'];

                    $dp =
                        (float)$row['dp'];

                    $pokok_pinjaman =
                        (float)$row['pokok_pinjaman'];

                    $tenor =
                        (int)$row['tenor'];

                    $bunga_tahunan =
                        (float)$row['bunga_tahunan'];

                    $total_bunga =
                        (float)$row['total_bunga'];

                    $total_kredit =
                        (float)$row['total_kredit'];

                    $angsuran_per_bulan =
                        (float)$row['angsuran_per_bulan'];

                    $tanggal_mulai =
                        $row['tanggal'];


                    $jenis_asuransi_db =
                        mysqli_real_escape_string(
                            $conn,
                            $jenis_asuransi
                        );


                    /* =========================================
                       INSERT DATA KREDIT
                    ========================================= */

                    $insertKredit = "
                        INSERT INTO kredit
                        (
                            id_penjualan,
                            harga_kendaraan,
                            dp,
                            pokok_pinjaman,
                            tenor,
                            bunga_tahunan,
                            total_bunga,
                            total_kredit,
                            angsuran_per_bulan,
                            tanggal_mulai,
                            status_kredit,
                            jenis_asuransi,
                            biaya_asuransi
                        )
                        VALUES
                        (
                            $id,
                            $harga_kendaraan,
                            $dp,
                            $pokok_pinjaman,
                            $tenor,
                            $bunga_tahunan,
                            $total_bunga,
                            $total_kredit,
                            $angsuran_per_bulan,
                            '$tanggal_mulai',
                            'Aktif',
                            '$jenis_asuransi_db',
                            $biaya_asuransi
                        )
                    ";


                    if (!mysqli_query(
                        $conn,
                        $insertKredit
                    )) {

                        throw new Exception(
                            "Gagal menyimpan data kredit: "
                            . mysqli_error($conn)
                        );
                    }


                    /* =========================================
                       ID KREDIT
                    ========================================= */

                    $id_kredit =
                        mysqli_insert_id($conn);


                    /* =========================================
                       BUAT JADWAL ANGSURAN
                    ========================================= */

                    for (
                        $i = 1;
                        $i <= $tenor;
                        $i++
                    ) {

                        $jatuh_tempo = date(
                            'Y-m-d',
                            strtotime(
                                "+"
                                . $i
                                . " month",
                                strtotime(
                                    $tanggal_mulai
                                )
                            )
                        );


                        $insertAngsuran = mysqli_query(
                            $conn,
                            "INSERT INTO angsuran
                            (
                                id_kredit,
                                angsuran_ke,
                                jatuh_tempo,
                                jumlah_angsuran,
                                jumlah_bayar,
                                status
                            )
                            VALUES
                            (
                                $id_kredit,
                                $i,
                                '$jatuh_tempo',
                                $angsuran_per_bulan,
                                0,
                                'Belum Bayar'
                            )"
                        );


                        if (!$insertAngsuran) {

                            throw new Exception(
                                "Gagal membuat jadwal angsuran."
                            );
                        }
                    }
                }
            }


            /* =================================================
               6. COMMIT
            ================================================= */

            mysqli_commit($conn);


        } catch (Throwable $e) {

            mysqli_rollback($conn);

        }
    }


    header("Location: verifikasi.php");
    exit;
}


/* =========================================================
   AMBIL TRANSAKSI MENUNGGU VERIFIKASI
========================================================= */

$result = mysqli_query(
    $conn,
    "SELECT
        p.*,

        c.nama AS customer,

        s.nama AS sales,

        k.kode_unit,

        m.nama_merek,

        t.nama_tipe

     FROM penjualan p

     INNER JOIN customer c
        ON p.id_customer=c.id_customer

     INNER JOIN sales s
        ON p.id_sales=s.id_sales

     INNER JOIN kendaraan k
        ON p.id_kendaraan=k.id_kendaraan

     INNER JOIN merek m
        ON k.id_merek=m.id_merek

     INNER JOIN tipe_kendaraan t
        ON k.id_tipe=t.id_tipe

     WHERE p.status='Menunggu Verifikasi'

     ORDER BY p.id_penjualan DESC"
);

?>


<!-- =====================================================
     JUDUL
===================================================== -->

<div class="page-head">

    <span class="eyebrow">
        OTORISASI TRANSAKSI
    </span>

    <h1>
        Verifikasi Penjualan
    </h1>

    <p>
        Periksa transaksi yang diajukan oleh Sales
        dan lakukan verifikasi.
    </p>

</div>


<!-- =====================================================
     NOTIFIKASI
===================================================== -->

<div class="notice">

    Pemberitahuan sistem:
    transaksi yang diverifikasi akan menjadi
    Selesai dan kendaraan berubah menjadi TERJUAL.

</div>


<br>


<!-- =====================================================
     TABEL TRANSAKSI
===================================================== -->

<div class="card">

    <div class="table-wrap">

        <table class="table">

            <tr>

                <th>
                    No Transaksi
                </th>

                <th>
                    Tanggal
                </th>

                <th>
                    Customer
                </th>

                <th>
                    Kendaraan
                </th>

                <th>
                    Sales
                </th>

                <th>
                    Harga
                </th>

                <th>
                    Status
                </th>

                <th>
                    Aksi
                </th>

            </tr>


            <?php while (
                $row = mysqli_fetch_assoc($result)
            ): ?>

            <tr>

                <td>
                    <?= e($row['no_transaksi']) ?>
                </td>

                <td>
                    <?= e($row['tanggal']) ?>
                </td>

                <td>
                    <?= e($row['customer']) ?>
                </td>

                <td>

                    <?= e(
                        $row['nama_merek']
                        . ' '
                        . $row['nama_tipe']
                    ) ?>

                </td>

                <td>
                    <?= e($row['sales']) ?>
                </td>

                <td>
                    <?= rupiah($row['harga']) ?>
                </td>

                <td>

                    <span class="badge badge-warning">
                        Menunggu Verifikasi
                    </span>

                </td>

                <td>

                    <a
                        class="btn btn-success btn-small"

                        data-confirm="Verifikasi transaksi ini? Setelah diverifikasi transaksi menjadi Selesai dan kendaraan menjadi TERJUAL."

                        href="verifikasi.php?verifikasi=<?= $row['id_penjualan'] ?>"
                    >

                        Verifikasi

                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>


<?php include "footer.php"; ?>