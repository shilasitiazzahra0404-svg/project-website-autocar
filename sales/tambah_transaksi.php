<?php

$page_title = "Tambah Transaksi";
$active = "transaksi";

include "../config/koneksi.php";
include "header.php";


/* =========================================================
   DATA SALES YANG SEDANG LOGIN
========================================================= */

$id_user = (int)$_SESSION['id_user'];

$salesQuery = mysqli_query(
    $conn,
    "SELECT id_sales, nama
     FROM sales
     WHERE id_user=$id_user
     LIMIT 1"
);

$salesRow = mysqli_fetch_assoc($salesQuery);

$id_sales = (int)($salesRow['id_sales'] ?? 0);


/* =========================================================
   DATA CUSTOMER
========================================================= */

$customers = mysqli_query(
    $conn,
    "SELECT *
     FROM customer
     ORDER BY nama"
);


/* =========================================================
   DATA KENDARAAN TERSEDIA
========================================================= */

$vehicles = mysqli_query(
    $conn,
    "SELECT
        k.*,
        m.nama_merek,
        t.nama_tipe
     FROM kendaraan k
     INNER JOIN merek m
        ON k.id_merek = m.id_merek
     INNER JOIN tipe_kendaraan t
        ON k.id_tipe = t.id_tipe
     WHERE k.status='TERSEDIA'
     ORDER BY m.nama_merek, t.nama_tipe"
);


/* =========================================================
   PENGATURAN KREDIT DAN ASURANSI DARI ADMIN
========================================================= */

$settingQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM pengaturan_kredit
     ORDER BY id_pengaturan ASC
     LIMIT 1"
);

$setting = mysqli_fetch_assoc($settingQuery);


/*
 * Pengaturan default
 */

$dp_persen = (float)($setting['dp_persen'] ?? 10);
$bunga_tahunan_setting = (float)($setting['bunga_tahunan'] ?? 8);

$all_risk = (float)($setting['all_risk'] ?? 0);
$comprehensive = (float)($setting['comprehensive'] ?? 0);
$tlo = (float)($setting['tlo'] ?? 0);


$error = "";


/* =========================================================
   PROSES FORM
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -----------------------------------------------------
       DATA UTAMA
    ----------------------------------------------------- */

    $customer = (int)($_POST['id_customer'] ?? 0);

    $kendaraan = (int)($_POST['id_kendaraan'] ?? 0);

    $harga = (float)($_POST['harga'] ?? 0);

    $tanggal = mysqli_real_escape_string(
        $conn,
        $_POST['tanggal'] ?? date('Y-m-d')
    );

    $metode = mysqli_real_escape_string(
        $conn,
        $_POST['metode_pembayaran'] ?? ''
    );


    /* -----------------------------------------------------
       DATA KREDIT
    ----------------------------------------------------- */

    $tenor = (int)($_POST['tenor'] ?? 0);


    /* -----------------------------------------------------
       DATA ASURANSI
    ----------------------------------------------------- */

    $jenis_asuransi =
        $_POST['jenis_asuransi']
        ?? 'Tanpa Asuransi';


    /*
     * Pilihan asuransi yang diperbolehkan
     */

    $jenis_asuransi_valid = [
        'Tanpa Asuransi',
        'All Risk',
        'Comprehensive',
        'TLO'
    ];


    /*
     * Validasi pilihan asuransi
     */

    if (
        !in_array(
            $jenis_asuransi,
            $jenis_asuransi_valid,
            true
        )
    ) {

        $jenis_asuransi =
            'Tanpa Asuransi';
    }


    /* -----------------------------------------------------
       BIAYA ASURANSI
       
       Harga diambil dari pengaturan admin.
       Sales tidak dapat memasukkan harga sendiri.
       
       Berlaku untuk:
       - Cash
       - BCA / BRI / Mandiri / BNI
       - Kredit
    ----------------------------------------------------- */

    $biaya_asuransi = 0;


    if ($jenis_asuransi === 'All Risk') {

        $biaya_asuransi = $all_risk;

    } elseif ($jenis_asuransi === 'Comprehensive') {

        $biaya_asuransi = $comprehensive;

    } elseif ($jenis_asuransi === 'TLO') {

        $biaya_asuransi = $tlo;

    } else {

        $jenis_asuransi = 'Tanpa Asuransi';

        $biaya_asuransi = 0;
    }


    /* =====================================================
       PERHITUNGAN
    ===================================================== */

    if ($metode === 'Kredit') {

        /*
         * DP otomatis sesuai pengaturan admin
         */

        $dp =
            $harga *
            ($dp_persen / 100);


        /*
         * Sisa harga kendaraan
         */

        $pokok_dasar =
            $harga - $dp;


        if ($pokok_dasar < 0) {

            $pokok_dasar = 0;
        }


        /*
         * Pokok kredit =
         * sisa harga kendaraan + asuransi
         */

        $pokok_pinjaman =
            $pokok_dasar +
            $biaya_asuransi;


        /*
         * Bunga tahunan
         */

        $bunga_tahunan =
            $bunga_tahunan_setting;


        /*
         * Bunga flat
         */

        $total_bunga =
            $pokok_pinjaman
            * ($bunga_tahunan / 100)
            * ($tenor / 12);


        /*
         * Total kredit
         */

        $total_kredit =
            $pokok_pinjaman +
            $total_bunga;


        /*
         * Angsuran per bulan
         */

        $angsuran_per_bulan =
            ($tenor > 0)
            ? $total_kredit / $tenor
            : 0;


        /*
         * Total uang yang dibayarkan customer
         *
         * DP + total kredit
         */

        $total_pembayaran =
            $dp +
            $total_kredit;

    } else {

        /*
         * CASH / TRANSFER BANK
         */

        $dp = 0;
        $tenor = 0;
        $bunga_tahunan = 0;
        $pokok_pinjaman = 0;
        $total_bunga = 0;
        $total_kredit = 0;
        $angsuran_per_bulan = 0;


        /*
         * Cash / Transfer Bank:
         *
         * Harga kendaraan + biaya asuransi
         */

        $total_pembayaran =
            $harga +
            $biaya_asuransi;
    }


    /* =====================================================
       VALIDASI
    ===================================================== */

    if ($customer <= 0) {

        $error =
            "Customer wajib dipilih.";

    } elseif ($kendaraan <= 0) {

        $error =
            "Kendaraan wajib dipilih.";

    } elseif ($harga <= 0) {

        $error =
            "Harga kendaraan tidak valid.";

    } elseif (
        !in_array(
            $metode,
            ['Cash', 'Kredit', 'BCA', 'BRI', 'Mandiri', 'BNI'],
            true
        )
    ) {

        $error =
            "Metode pembayaran tidak valid.";

    } elseif (
        $metode === 'Kredit'
        && $tenor <= 0
    ) {

        $error =
            "Tenor kredit wajib dipilih.";
    }


    /* =====================================================
       SIMPAN TRANSAKSI
    ===================================================== */

    if ($error === "") {

        mysqli_begin_transaction($conn);

        try {

            /* ---------------------------------------------
               NOMOR TRANSAKSI
            --------------------------------------------- */

            $no =
                "TRX-"
                . date("YmdHis");


            /* ---------------------------------------------
               INSERT PENJUALAN
            --------------------------------------------- */

            $insert = "
                INSERT INTO penjualan
                (
                    no_transaksi,
                    id_customer,
                    id_sales,
                    id_kendaraan,
                    harga,
                    total_pembayaran,
                    tanggal,
                    metode_pembayaran,
                    status,
                    dp,
                    tenor,
                    bunga_tahunan,
                    pokok_pinjaman,
                    total_bunga,
                    total_kredit,
                    angsuran_per_bulan
                )
                VALUES
                (
                    '$no',
                    $customer,
                    $id_sales,
                    $kendaraan,
                    $harga,
                    $total_pembayaran,
                    '$tanggal',
                    '$metode',
                    'Menunggu Verifikasi',
                    $dp,
                    $tenor,
                    $bunga_tahunan,
                    $pokok_pinjaman,
                    $total_bunga,
                    $total_kredit,
                    $angsuran_per_bulan
                )
            ";


            if (!mysqli_query($conn, $insert)) {

                throw new Exception(
                    "Gagal menyimpan transaksi: "
                    . mysqli_error($conn)
                );
            }


            /* ---------------------------------------------
               AMBIL ID PENJUALAN
            --------------------------------------------- */

            $id_penjualan =
                mysqli_insert_id($conn);


            /* ---------------------------------------------
               SIMPAN ASURANSI
               
               SEMUA METODE PEMBAYARAN:
               - Cash
               - BCA / BRI / Mandiri / BNI
               - Kredit
            --------------------------------------------- */

            $jenis_asuransi_db =
                mysqli_real_escape_string(
                    $conn,
                    $jenis_asuransi
                );


            $insertAsuransi = "
                INSERT INTO transaksi_asuransi
                (
                    id_penjualan,
                    jenis_asuransi,
                    biaya_asuransi
                )
                VALUES
                (
                    $id_penjualan,
                    '$jenis_asuransi_db',
                    $biaya_asuransi
                )
            ";


            if (!mysqli_query(
                $conn,
                $insertAsuransi
            )) {

                throw new Exception(
                    "Gagal menyimpan asuransi: "
                    . mysqli_error($conn)
                );
            }


            /* ---------------------------------------------
               UBAH STATUS KENDARAAN
            --------------------------------------------- */

            $updateKendaraan = "
                UPDATE kendaraan
                SET status='DIPROSES'
                WHERE id_kendaraan=$kendaraan
            ";


            if (!mysqli_query(
                $conn,
                $updateKendaraan
            )) {

                throw new Exception(
                    "Gagal mengubah status kendaraan: "
                    . mysqli_error($conn)
                );
            }


            /* ---------------------------------------------
               SIMPAN SEMUA PERUBAHAN
            --------------------------------------------- */

            mysqli_commit($conn);


            header(
                "Location: transaksi.php"
            );

            exit;


        } catch (Throwable $e) {

            mysqli_rollback($conn);

            $error =
                $e->getMessage();
        }
    }
}

?>


<!-- =====================================================
     HEADER HALAMAN
===================================================== -->

<div class="page-head">

    <span class="eyebrow">
        TRANSAKSI PENJUALAN
    </span>

    <h1>
        Tambah Transaksi Penjualan
    </h1>

    <p>
        Masukkan data transaksi penjualan kendaraan.
    </p>

</div>


<!-- =====================================================
     FORM
===================================================== -->

<div class="card form-card">


<?php if ($error): ?>

    <div class="alert alert-danger">

        <?= e($error) ?>

    </div>

<?php endif; ?>


<form method="post">


<div class="form-grid">


    <!-- =================================================
         NOMOR TRANSAKSI
    ================================================== -->

    <div class="form-group">

        <label>
            Nomor Transaksi
        </label>

        <input
            class="form-control"
            value="Otomatis saat disimpan"
            disabled
        >

    </div>


    <!-- =================================================
         TANGGAL
    ================================================== -->

    <div class="form-group">

        <label>
            Tanggal Transaksi
        </label>

        <input
            class="form-control"
            type="date"
            name="tanggal"
            value="<?= date('Y-m-d') ?>"
            required
        >

    </div>


    <!-- =================================================
         CUSTOMER
    ================================================== -->

    <div class="form-group">

        <label>
            Customer
        </label>

        <select
            class="form-control"
            name="id_customer"
            required
        >

            <option value="">
                Pilih Customer
            </option>

            <?php while (
                $c = mysqli_fetch_assoc($customers)
            ): ?>

                <option
                    value="<?= $c['id_customer'] ?>"
                >

                    <?= e($c['nama']) ?>

                </option>

            <?php endwhile; ?>

        </select>

    </div>


    <!-- =================================================
         KENDARAAN
    ================================================== -->

    <div class="form-group">

        <label>
            Kendaraan
        </label>

        <select
            class="form-control"
            name="id_kendaraan"
            id="vehicleSelect"
            required
        >

            <option value="">
                Pilih Kendaraan
            </option>

            <?php while (
                $v = mysqli_fetch_assoc($vehicles)
            ): ?>

                <option
                    value="<?= $v['id_kendaraan'] ?>"
                    data-price="<?= $v['harga'] ?>"
                >

                    <?= e(
                        $v['nama_merek']
                        . ' '
                        . $v['nama_tipe']
                        . ' - '
                        . $v['kode_unit']
                    ) ?>

                </option>

            <?php endwhile; ?>

        </select>

    </div>


    <!-- =================================================
         SALES
    ================================================== -->

    <div class="form-group">

        <label>
            Sales
        </label>

        <input
            class="form-control"
            value="<?=
                e(
                    $salesRow['nama']
                    ?? $_SESSION['nama']
                )
            ?>"
            disabled
        >

    </div>


    <!-- =================================================
         HARGA
    ================================================== -->

    <div class="form-group">

        <label>
            Harga
        </label>

        <input
            class="form-control"
            type="number"
            name="harga"
            id="priceInput"
            required
        >

    </div>


    <!-- =================================================
         METODE PEMBAYARAN
    ================================================== -->

    <div class="form-group">

        <label>
            Metode Pembayaran
        </label>

        <select
            class="form-control"
            name="metode_pembayaran"
            id="metodePembayaran"
            required
        >

            <option value="Cash">
                Cash
            </option>

            <option value="BCA">
                Transfer BCA
            </option>

            <option value="BRI">
                Transfer BRI
            </option>

            <option value="Mandiri">
                Transfer Mandiri
            </option>

            <option value="BNI">
                Transfer BNI
            </option>

            <option value="Kredit">
                Kredit
            </option>

        </select>

    </div>

    <!-- INFORMASI REKENING TRANSFER (DATA CONTOH SEMENTARA) -->
    <div class="form-group" id="infoRekening" style="display:none;">
        <div class="alert alert-info">
            <strong>Informasi Rekening Tujuan</strong><br>
            <span id="namaBankRekening"></span><br>
            Nama Rekening: <strong>PT AutoCar Indonesia</strong><br>
            Nomor Rekening: <strong id="nomorRekening"></strong><br>
            <small>Data rekening ini hanya contoh sementara. Ganti dengan rekening resmi sebelum digunakan untuk transaksi nyata.</small>
        </div>
    </div>


    <!-- =================================================
         ASURANSI
         
         SEKARANG BERLAKU UNTUK SEMUA METODE
    ================================================== -->

    <div class="form-group">

        <label>
            Jenis Asuransi
        </label>

        <select
            class="form-control"
            name="jenis_asuransi"
            id="jenis_asuransi"
            required
        >

            <option value="Tanpa Asuransi">
                Tanpa Asuransi
            </option>

            <option value="All Risk">
                All Risk
            </option>

            <option value="Comprehensive">
                Comprehensive
            </option>

            <option value="TLO">
                TLO
            </option>

        </select>

        <small>
            Pilihan asuransi tersedia untuk Cash,
            transfer bank, dan Kredit.
        </small>

    </div>


    <!-- =================================================
         BIAYA ASURANSI
    ================================================== -->

    <div class="form-group">

        <label>
            Biaya Asuransi
        </label>

        <input
            class="form-control"
            type="number"
            id="biaya_asuransi"
            value="0"
            readonly
        >

        <small>
            Biaya ditentukan oleh Admin.
        </small>

    </div>


    <!-- =================================================
         TOTAL PEMBAYARAN
    ================================================== -->

    <div class="form-group">

        <label>
            Total Pembayaran
        </label>

        <input
            class="form-control"
            type="number"
            id="total_pembayaran"
            value="0"
            readonly
        >

        <small id="keteranganTotal">
            Harga kendaraan + biaya asuransi.
        </small>

    </div>


    <!-- =================================================
         STATUS
    ================================================== -->

    <div class="form-group">

        <label>
            Status
        </label>

        <input
            class="form-control"
            value="Menunggu Verifikasi"
            disabled
        >

    </div>


    <!-- =================================================
         FORM KREDIT
    ================================================== -->

    <div
        id="formKredit"
        style="
            display:none;
            grid-column:1 / -1;
        "
    >

        <div
            class="card"
            style="margin-top:15px;"
        >

            <h3>
                Data Kredit
            </h3>


            <div class="form-grid">


                <!-- =====================================
                     DP
                ====================================== -->

                <div class="form-group">

                    <label>
                        DP
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="dp"
                        readonly
                    >

                    

                </div>


                <!-- =====================================
                     TENOR
                ====================================== -->

                <div class="form-group">

                    <label>
                        Tenor
                    </label>

                    <select
                        class="form-control"
                        name="tenor"
                        id="tenor"
                    >

                        <option value="12">
                            12 Bulan
                        </option>

                        <option value="24">
                            24 Bulan
                        </option>

                        <option
                            value="36"
                            selected
                        >
                            36 Bulan
                        </option>

                        <option value="48">
                            48 Bulan
                        </option>

                        <option value="60">
                            60 Bulan
                        </option>

                    </select>

                </div>


                <!-- =====================================
                     BUNGA
                ====================================== -->

                <div class="form-group">

                    <label>
                        Bunga per Tahun (%)
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="bunga_tahunan"
                        value="<?=
                            e(
                                $bunga_tahunan_setting
                            )
                        ?>"
                        readonly
                    >

                </div>


                <!-- =====================================
                     POKOK PINJAMAN
                ====================================== -->

                <div class="form-group">

                    <label>
                        Pokok Pinjaman
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="pokok_pinjaman"
                        readonly
                    >

                    <small>
                        Sisa harga + biaya asuransi.
                    </small>

                </div>


                <!-- =====================================
                     TOTAL BUNGA
                ====================================== -->

                <div class="form-group">

                    <label>
                        Total Bunga
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="total_bunga"
                        readonly
                    >

                </div>


                <!-- =====================================
                     TOTAL KREDIT
                ====================================== -->

                <div class="form-group">

                    <label>
                        Total Kredit
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="total_kredit"
                        readonly
                    >

                </div>


                <!-- =====================================
                     ANGSURAN
                ====================================== -->

                <div class="form-group">

                    <label>
                        Angsuran per Bulan
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="angsuran_per_bulan"
                        readonly
                    >

                </div>


            </div>

        </div>

    </div>


</div>


<!-- =====================================================
     BUTTON
===================================================== -->

<div class="action-group">

    <button
        class="btn btn-primary"
        type="submit"
    >
        Simpan Transaksi
    </button>

    <a
        class="btn btn-secondary"
        href="transaksi.php"
    >
        Batal
    </a>

</div>


</form>

</div>


<script>

/* =========================================================
   ELEMENT
========================================================= */

const metodePembayaran =
    document.getElementById(
        'metodePembayaran'
    );

const formKredit =
    document.getElementById(
        'formKredit'
    );

const vehicleSelect =
    document.getElementById(
        'vehicleSelect'
    );

const priceInput =
    document.getElementById(
        'priceInput'
    );

const dpInput =
    document.getElementById(
        'dp'
    );

const tenorInput =
    document.getElementById(
        'tenor'
    );

const bungaInput =
    document.getElementById(
        'bunga_tahunan'
    );

const asuransiInput =
    document.getElementById(
        'jenis_asuransi'
    );

const biayaAsuransiInput =
    document.getElementById(
        'biaya_asuransi'
    );

const totalPembayaranInput =
    document.getElementById(
        'total_pembayaran'
    );

const keteranganTotal =
    document.getElementById(
        'keteranganTotal'
    );

const pokokInput =
    document.getElementById(
        'pokok_pinjaman'
    );

const totalBungaInput =
    document.getElementById(
        'total_bunga'
    );

const totalKreditInput =
    document.getElementById(
        'total_kredit'
    );

const angsuranInput =
    document.getElementById(
        'angsuran_per_bulan'
    );


/* =========================================================
   BIAYA ASURANSI DARI DATABASE
========================================================= */

const biayaAsuransi = {

    "Tanpa Asuransi": 0,

    "All Risk":
        <?= json_encode($all_risk) ?>,

    "Comprehensive":
        <?= json_encode($comprehensive) ?>,

    "TLO":
        <?= json_encode($tlo) ?>
};


/* =========================================================
   PERSENTASE DP
========================================================= */

const persenDP =
    <?= json_encode($dp_persen) ?>;


/* =========================================================
   BUNGA
========================================================= */

const bungaDefault =
    <?= json_encode(
        $bunga_tahunan_setting
    ) ?>;


/* =========================================================
   HITUNG SEMUA
========================================================= */

function hitungSemua() {

    const harga =
        parseFloat(
            priceInput.value
        ) || 0;


    const jenisAsuransi =
        asuransiInput.value;


    const biaya =
        parseFloat(
            biayaAsuransi[
                jenisAsuransi
            ]
        ) || 0;


    /* -----------------------------------------------
       TAMPILKAN BIAYA ASURANSI
    ----------------------------------------------- */

    biayaAsuransiInput.value =
        Math.round(biaya);


    /* =================================================
       JIKA KREDIT
    ================================================= */

    if (
        metodePembayaran.value === 'Kredit'
    ) {

        const tenor =
            parseInt(
                tenorInput.value
            ) || 0;


        const bunga =
            parseFloat(
                bungaInput.value
            ) || bungaDefault;


        /* -------------------------------------------
           DP
        ------------------------------------------- */

        const dp =
            harga *
            (persenDP / 100);


        /* -------------------------------------------
           POKOK DASAR
        ------------------------------------------- */

        const pokokDasar =
            Math.max(
                harga - dp,
                0
            );


        /* -------------------------------------------
           POKOK + ASURANSI
        ------------------------------------------- */

        const pokok =
            pokokDasar +
            biaya;


        /* -------------------------------------------
           TOTAL BUNGA
        ------------------------------------------- */

        const totalBunga =
            pokok
            * (bunga / 100)
            * (tenor / 12);


        /* -------------------------------------------
           TOTAL KREDIT
        ------------------------------------------- */

        const totalKredit =
            pokok +
            totalBunga;


        /* -------------------------------------------
           ANGSURAN
        ------------------------------------------- */

        const angsuran =
            tenor > 0
                ? totalKredit / tenor
                : 0;


        /* -------------------------------------------
           TOTAL PEMBAYARAN
           
           DP + Total Kredit
        ------------------------------------------- */

        const totalPembayaran =
            dp +
            totalKredit;


        /* -------------------------------------------
           TAMPILKAN
        ------------------------------------------- */

        dpInput.value =
            Math.round(dp);

        pokokInput.value =
            Math.round(pokok);

        totalBungaInput.value =
            Math.round(totalBunga);

        totalKreditInput.value =
            Math.round(totalKredit);

        angsuranInput.value =
            Math.round(angsuran);

        totalPembayaranInput.value =
            Math.round(totalPembayaran);


        keteranganTotal.innerText =
            "DP + Total Kredit (termasuk asuransi dan bunga).";


    } else {

        /* =================================================
           CASH / TRANSFER BANK
        ================================================= */

        const totalPembayaran =
            harga +
            biaya;


        /* -------------------------------------------
           TAMPILKAN TOTAL
        ------------------------------------------- */

        totalPembayaranInput.value =
            Math.round(
                totalPembayaran
            );


        keteranganTotal.innerText =
            "Harga kendaraan + biaya asuransi.";


        /* -------------------------------------------
           KOSONGKAN DATA KREDIT
        ------------------------------------------- */

        dpInput.value = 0;

        pokokInput.value = 0;

        totalBungaInput.value = 0;

        totalKreditInput.value = 0;

        angsuranInput.value = 0;
    }
}


/* =========================================================
   METODE PEMBAYARAN
========================================================= */

const infoRekening = document.getElementById('infoRekening');
const namaBankRekening = document.getElementById('namaBankRekening');
const nomorRekening = document.getElementById('nomorRekening');

const dataRekeningContoh = {
    'BCA': { nama: 'Transfer BCA', nomor: '1234567890' },
    'BRI': { nama: 'Transfer BRI', nomor: '2345678901' },
    'Mandiri': { nama: 'Transfer Mandiri', nomor: '3456789012' },
    'BNI': { nama: 'Transfer BNI', nomor: '4567890123' }
};

metodePembayaran.addEventListener(
    'change',
    function () {

        if (dataRekeningContoh[this.value]) {
            namaBankRekening.textContent = dataRekeningContoh[this.value].nama;
            nomorRekening.textContent = dataRekeningContoh[this.value].nomor;
            infoRekening.style.display = 'block';
        } else {
            infoRekening.style.display = 'none';
        }

        if (
            this.value === 'Kredit'
        ) {

            formKredit.style.display =
                'block';

            tenorInput.required =
                true;

        } else {

            formKredit.style.display =
                'none';

            tenorInput.required =
                false;
        }


        /*
         * Asuransi TETAP muncul
         * untuk semua metode pembayaran.
         */

        hitungSemua();
    }
);


/* =========================================================
   EVENT PERHITUNGAN
========================================================= */

priceInput.addEventListener(
    'input',
    hitungSemua
);


tenorInput.addEventListener(
    'change',
    hitungSemua
);


asuransiInput.addEventListener(
    'change',
    hitungSemua
);


/* =========================================================
   KETIKA KENDARAAN DIPILIH
========================================================= */

vehicleSelect.addEventListener(
    'change',
    function () {

        const option =
            this.options[
                this.selectedIndex
            ];


        const harga =
            option.getAttribute(
                'data-price'
            );


        if (harga) {

            priceInput.value =
                harga;

            hitungSemua();
        }
    }
);


/* =========================================================
   HARGA AWAL
========================================================= */

if (vehicleSelect.value) {

    const option =
        vehicleSelect.options[
            vehicleSelect.selectedIndex
        ];


    const harga =
        option.getAttribute(
            'data-price'
        );


    if (harga) {

        priceInput.value =
            harga;
    }
}


/* =========================================================
   TAMPILAN AWAL
========================================================= */

if (
    metodePembayaran.value === 'Kredit'
) {

    formKredit.style.display =
        'block';

    tenorInput.required =
        true;

} else {

    formKredit.style.display =
        'none';

    tenorInput.required =
        false;
}


hitungSemua();

</script>


<script
    src="../assets/js/transaksi.js"
></script>


<?php include "footer.php"; ?>