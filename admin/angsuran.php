<?php
$page_title = "Data Angsuran";
$active = "angsuran";

include "../config/koneksi.php";
include "header.php";

$id_kredit = isset($_GET['id_kredit']) ? (int)$_GET['id_kredit'] : 0;


/* =========================
   DATA KREDIT UNTUK DROPDOWN
   ========================= */

$kreditList = mysqli_query($conn, "
    SELECT
        k.id_kredit,
        k.tenor,
        p.no_transaksi,
        c.nama AS customer,
        m.nama_merek,
        t.nama_tipe
    FROM kredit k
    INNER JOIN penjualan p ON k.id_penjualan = p.id_penjualan
    INNER JOIN customer c ON p.id_customer = c.id_customer
    INNER JOIN kendaraan v ON p.id_kendaraan = v.id_kendaraan
    INNER JOIN merek m ON v.id_merek = m.id_merek
    INNER JOIN tipe_kendaraan t ON v.id_tipe = t.id_tipe
    ORDER BY k.id_kredit DESC
");


/* =========================
   DATA KREDIT TERPILIH
   ========================= */

$selectedCredit = null;
$nextPayment = null;
$totalAngsuran = 0;
$totalLunas = 0;
$riwayat = [];

if ($id_kredit > 0) {

    /* Informasi kredit */

    $selectedQuery = mysqli_query($conn, "
        SELECT
            k.*,
            p.no_transaksi,
            c.nama AS customer,
            m.nama_merek,
            t.nama_tipe
        FROM kredit k
        INNER JOIN penjualan p ON k.id_penjualan = p.id_penjualan
        INNER JOIN customer c ON p.id_customer = c.id_customer
        INNER JOIN kendaraan v ON p.id_kendaraan = v.id_kendaraan
        INNER JOIN merek m ON v.id_merek = m.id_merek
        INNER JOIN tipe_kendaraan t ON v.id_tipe = t.id_tipe
        WHERE k.id_kredit = $id_kredit
        LIMIT 1
    ");

    $selectedCredit = mysqli_fetch_assoc($selectedQuery);


    if ($selectedCredit) {

        /* =========================
           TOTAL ANGSURAN
           ========================= */

        $totalQuery = mysqli_query($conn, "
            SELECT
                COUNT(*) AS total,
                SUM(
                    CASE
                        WHEN status = 'Lunas' THEN 1
                        ELSE 0
                    END
                ) AS lunas
            FROM angsuran
            WHERE id_kredit = $id_kredit
        ");

        $totalData = mysqli_fetch_assoc($totalQuery);

        $totalAngsuran = (int)$totalData['total'];
        $totalLunas = (int)$totalData['lunas'];


        /* =========================
           ANGSURAN BERIKUTNYA
           ========================= */

        $nextQuery = mysqli_query($conn, "
            SELECT *
            FROM angsuran
            WHERE id_kredit = $id_kredit
            AND status <> 'Lunas'
            ORDER BY angsuran_ke ASC
            LIMIT 1
        ");

        $nextPayment = mysqli_fetch_assoc($nextQuery);


        /* =========================
           RIWAYAT PEMBAYARAN
           HANYA 5 TERAKHIR
           ========================= */

        $historyQuery = mysqli_query($conn, "
            SELECT *
            FROM angsuran
            WHERE id_kredit = $id_kredit
            AND status = 'Lunas'
            ORDER BY angsuran_ke DESC
            LIMIT 5
        ");

        while ($history = mysqli_fetch_assoc($historyQuery)) {
            $riwayat[] = $history;
        }
    }
}
?>


<div class="page-head">

    <span class="eyebrow">PEMBAYARAN KREDIT</span>

    <h1>Data Angsuran</h1>

    <p>
        Kelola pembayaran angsuran kendaraan.
    </p>

</div>


<!-- =========================
     PILIH TRANSAKSI
     ========================= -->

<div class="card" style="margin-bottom:20px;">

    <div class="form-group">

        <label>
            <strong>Pilih Transaksi Kredit</strong>
        </label>

        <select
            class="form-control"
            id="pilihKredit"
            onchange="pilihTransaksi()"
        >

            <option value="">
                -- Pilih Customer / No Transaksi --
            </option>

            <?php while($k = mysqli_fetch_assoc($kreditList)): ?>

                <option
                    value="<?= $k['id_kredit'] ?>"
                    <?= ($id_kredit == $k['id_kredit']) ? 'selected' : '' ?>
                >

                    <?= e($k['customer']) ?>
                    -
                    <?= e($k['no_transaksi']) ?>
                    -
                    <?= e($k['nama_merek'].' '.$k['nama_tipe']) ?>

                </option>

            <?php endwhile; ?>

        </select>

    </div>

</div>


<?php if($selectedCredit): ?>


<!-- =========================
     INFORMASI KREDIT
     ========================= -->

<div class="card" style="margin-bottom:20px;">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        flex-wrap:wrap;
    ">

        <div>

            <span class="eyebrow">
                INFORMASI KREDIT
            </span>

            <h2 style="margin:5px 0;">

                <?= e($selectedCredit['customer']) ?>

            </h2>

            <p style="
                margin:0;
                color:#64748b;
            ">

                <?= e($selectedCredit['nama_merek'].' '.$selectedCredit['nama_tipe']) ?>

                &nbsp; | &nbsp;

                <?= e($selectedCredit['no_transaksi']) ?>

            </p>

        </div>


        <?php if($selectedCredit['status_kredit'] === 'Lunas'): ?>

            <span class="badge badge-success">
                Kredit Lunas
            </span>

        <?php else: ?>

            <span class="badge badge-warning">
                Kredit Aktif
            </span>

        <?php endif; ?>

    </div>

</div>


<!-- =========================
     PROGRESS PEMBAYARAN
     ========================= -->

<div class="card" style="margin-bottom:20px;">

    <span class="eyebrow">
        PROGRESS PEMBAYARAN
    </span>

    <h2 style="margin:8px 0;">

        <?= $totalLunas ?>
        /
        <?= $totalAngsuran ?>

        <span style="
            font-size:14px;
            font-weight:normal;
            color:#64748b;
        ">
            angsuran lunas
        </span>

    </h2>


    <?php
    $progress = 0;

    if ($totalAngsuran > 0) {
        $progress = ($totalLunas / $totalAngsuran) * 100;
    }
    ?>


    <div style="
        width:100%;
        height:10px;
        background:#e5e7eb;
        border-radius:10px;
        overflow:hidden;
    ">

        <div style="
            width:<?= $progress ?>%;
            height:100%;
            background:#16a34a;
            border-radius:10px;
        "></div>

    </div>


    <p style="
        margin-top:8px;
        color:#64748b;
        font-size:13px;
    ">

        <?= number_format($progress, 0) ?>%
        pembayaran telah diselesaikan.

    </p>

</div>


<!-- =========================
     ANGSURAN BERIKUTNYA
     ========================= -->

<?php if($nextPayment): ?>

<div class="card" style="
    margin-bottom:20px;
    border:2px solid #e5e7eb;
">

    <span class="eyebrow">
        ANGSURAN BERIKUTNYA
    </span>


    <h2 style="margin:8px 0 20px;">

        Angsuran ke-<?= e($nextPayment['angsuran_ke']) ?>

    </h2>


    <div class="form-grid">


        <div class="form-group">

            <label>Jatuh Tempo</label>

            <input
                class="form-control"
                value="<?= e($nextPayment['jatuh_tempo']) ?>"
                readonly
            >

        </div>


        <div class="form-group">

            <label>Jumlah Angsuran</label>

            <input
                class="form-control"
                value="<?= rupiah($nextPayment['jumlah_angsuran']) ?>"
                readonly
            >

        </div>


        <div class="form-group">

            <label>Status</label>

            <input
                class="form-control"
                value="<?= e($nextPayment['status']) ?>"
                readonly
            >

        </div>

    </div>


    <div class="action-group">

        <a
            href="bayar_angsuran.php?id=<?= $nextPayment['id_angsuran'] ?>"
            class="btn btn-success"
        >

            💰 Bayar Sekarang

        </a>

    </div>

</div>


<?php else: ?>


<div class="card" style="margin-bottom:20px;">

    <div class="alert alert-success">

        🎉 Semua angsuran sudah lunas.

    </div>

</div>


<?php endif; ?>


<!-- =========================
     RIWAYAT PEMBAYARAN
     ========================= -->

<?php if(count($riwayat) > 0): ?>

<div class="card">

    <span class="eyebrow">
        RIWAYAT PEMBAYARAN
    </span>

    <h3 style="margin:8px 0 15px;">
        5 Pembayaran Terakhir
    </h3>


    <div class="table-wrap">

        <table class="table">

            <tr>

                <th>Angsuran</th>
                <th>Jumlah Bayar</th>
                <th>Tanggal Bayar</th>
                <th>Status</th>

            </tr>


            <?php foreach($riwayat as $r): ?>

            <tr>

                <td>
                    Angsuran ke-<?= e($r['angsuran_ke']) ?>
                </td>

                <td>
                    <?= rupiah($r['jumlah_bayar']) ?>
                </td>

                <td>
                    <?= e($r['tanggal_bayar']) ?>
                </td>

                <td>

                    <span class="badge badge-success">
                        Lunas
                    </span>

                </td>

            </tr>

            <?php endforeach; ?>

        </table>

    </div>

</div>

<?php endif; ?>


<?php else: ?>


<!-- =========================
     BELUM MEMILIH TRANSAKSI
     ========================= -->

<div class="card">

    <div style="
        text-align:center;
        padding:50px 20px;
    ">

        <h2 style="margin-bottom:10px;">
            Pilih Transaksi Kredit
        </h2>

        <p style="
            color:#64748b;
            margin:0;
        ">

            Pilih customer atau nomor transaksi
            untuk melihat angsuran berikutnya.

        </p>

    </div>

</div>


<?php endif; ?>


<script>

function pilihTransaksi() {

    const id = document.getElementById('pilihKredit').value;

    if (id !== '') {

        window.location.href =
            'angsuran.php?id_kredit=' + id;

    } else {

        window.location.href =
            'angsuran.php';

    }

}

</script>


<?php include "footer.php"; ?>