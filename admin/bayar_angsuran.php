<?php
$page_title = "Pembayaran Angsuran";
$active = "angsuran";

include "../config/koneksi.php";
include "header.php";

$error = "";
$success = "";


/* =========================================================
   AMBIL ID ANGSURAN
   ========================================================= */

$id_angsuran = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


/* =========================================================
   AMBIL DATA ANGSURAN
   ========================================================= */

$query = mysqli_query($conn, "
    SELECT
        a.*,
        k.id_kredit,
        k.id_penjualan,
        k.status_kredit,
        p.no_transaksi,
        p.id_customer,
        c.nama AS customer,
        m.nama_merek,
        t.nama_tipe

    FROM angsuran a

    INNER JOIN kredit k
        ON a.id_kredit = k.id_kredit

    INNER JOIN penjualan p
        ON k.id_penjualan = p.id_penjualan

    INNER JOIN customer c
        ON p.id_customer = c.id_customer

    INNER JOIN kendaraan v
        ON p.id_kendaraan = v.id_kendaraan

    INNER JOIN merek m
        ON v.id_merek = m.id_merek

    INNER JOIN tipe_kendaraan t
        ON v.id_tipe = t.id_tipe

    WHERE a.id_angsuran = $id_angsuran

    LIMIT 1
");


$row = mysqli_fetch_assoc($query);


/* =========================================================
   JIKA DATA TIDAK DITEMUKAN
   ========================================================= */

if(!$row){

    echo '<div class="alert alert-danger">
            Data angsuran tidak ditemukan.
          </div>';

    include "footer.php";
    exit;
}


/* =========================================================
   PROSES PEMBAYARAN
   ========================================================= */

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $tanggal_bayar = mysqli_real_escape_string(
        $conn,
        $_POST['tanggal_bayar']
    );

    $jumlah_bayar = (float)$_POST['jumlah_bayar'];

    $jumlah_angsuran = (float)$row['jumlah_angsuran'];


    /* =====================================================
       VALIDASI
       ===================================================== */

    if($row['status'] === 'Lunas'){

        $error = "Angsuran ini sudah lunas.";

    }elseif($jumlah_bayar < $jumlah_angsuran){

        $error = "Jumlah pembayaran harus minimal sebesar jumlah angsuran.";

    }elseif(empty($tanggal_bayar)){

        $error = "Tanggal pembayaran wajib diisi.";

    }else{

        mysqli_begin_transaction($conn);

        try{

            /* =============================================
               1. UPDATE ANGSURAN
               ============================================= */

            $update = mysqli_query(
                $conn,
                "UPDATE angsuran
                 SET
                     jumlah_bayar = $jumlah_bayar,
                     tanggal_bayar = '$tanggal_bayar',
                     status = 'Lunas'
                 WHERE id_angsuran = $id_angsuran
                 AND status <> 'Lunas'"
            );


            if(!$update){

                throw new Exception(
                    mysqli_error($conn)
                );
            }


            /* =============================================
               2. CEK APAKAH SEMUA ANGSURAN SUDAH LUNAS
               ============================================= */

            $id_kredit = (int)$row['id_kredit'];

            $cekBelumLunas = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT COUNT(*) AS jumlah
                     FROM angsuran
                     WHERE id_kredit = $id_kredit
                     AND status <> 'Lunas'"
                )
            );


            /* =============================================
               3. JIKA SEMUA LUNAS
                  → KREDIT MENJADI LUNAS
               ============================================= */

            if((int)$cekBelumLunas['jumlah'] === 0){

                mysqli_query(
                    $conn,
                    "UPDATE kredit
                     SET status_kredit = 'Lunas'
                     WHERE id_kredit = $id_kredit"
                );
            }


            mysqli_commit($conn);


            header("Location: angsuran.php");
            exit;


        }catch(Throwable $e){

            mysqli_rollback($conn);

            $error = "Pembayaran gagal: " . $e->getMessage();
        }
    }
}
?>


<div class="page-head">

    <span class="eyebrow">
        PEMBAYARAN KREDIT
    </span>

    <h1>
        Pembayaran Angsuran
    </h1>

    <p>
        Catat pembayaran angsuran customer.
    </p>

</div>


<?php if($error): ?>

<div class="alert alert-danger">
    <?= e($error) ?>
</div>

<?php endif; ?>


<div class="card form-card">

    <h3>
        Informasi Angsuran
    </h3>


    <div class="form-grid">

        <div class="form-group">

            <label>No Transaksi</label>

            <input
                class="form-control"
                value="<?= e($row['no_transaksi']) ?>"
                disabled
            >

        </div>


        <div class="form-group">

            <label>Customer</label>

            <input
                class="form-control"
                value="<?= e($row['customer']) ?>"
                disabled
            >

        </div>


        <div class="form-group">

            <label>Kendaraan</label>

            <input
                class="form-control"
                value="<?= e($row['nama_merek'].' '.$row['nama_tipe']) ?>"
                disabled
            >

        </div>


        <div class="form-group">

            <label>Angsuran Ke</label>

            <input
                class="form-control"
                value="<?= e($row['angsuran_ke']) ?>"
                disabled
            >

        </div>


        <div class="form-group">

            <label>Jatuh Tempo</label>

            <input
                class="form-control"
                value="<?= e($row['jatuh_tempo']) ?>"
                disabled
            >

        </div>


        <div class="form-group">

            <label>Jumlah Angsuran</label>

            <input
                class="form-control"
                value="<?= rupiah($row['jumlah_angsuran']) ?>"
                disabled
            >

        </div>

    </div>


    <hr>


    <h3>
        Data Pembayaran
    </h3>


    <form method="post">

        <div class="form-grid">

            <div class="form-group">

                <label>Tanggal Bayar</label>

                <input
                    class="form-control"
                    type="date"
                    name="tanggal_bayar"
                    value="<?= date('Y-m-d') ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Jumlah Bayar</label>

                <input
                    class="form-control"
                    type="number"
                    name="jumlah_bayar"
                    value="<?= (float)$row['jumlah_angsuran'] ?>"
                    min="<?= (float)$row['jumlah_angsuran'] ?>"
                    step="0.01"
                    required
                >

            </div>

        </div>


        <div class="action-group">

            <button
                class="btn btn-success"
                type="submit"
            >
                Simpan Pembayaran
            </button>

            <a
                class="btn btn-secondary"
                href="angsuran.php"
            >
                Batal
            </a>

        </div>

    </form>

</div>


<?php include "footer.php"; ?>