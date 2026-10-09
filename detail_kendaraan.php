<?php

include "config/koneksi.php";
include "config/helpers.php";
include "config/lokasi.php";


/* =========================================================
   AMBIL ID KENDARAAN
========================================================= */

$id = (int)($_GET['id'] ?? 0);


/* =========================================================
   AMBIL DATA KENDARAAN DARI DATABASE
========================================================= */

$query = "SELECT kendaraan.*, merek.nama_merek, tipe_kendaraan.nama_tipe
          FROM kendaraan
          INNER JOIN merek 
              ON kendaraan.id_merek = merek.id_merek
          INNER JOIN tipe_kendaraan 
              ON kendaraan.id_tipe = tipe_kendaraan.id_tipe
          WHERE kendaraan.id_kendaraan = $id
          LIMIT 1";


$result = mysqli_query($conn, $query);

$data = mysqli_fetch_assoc($result);


/* =========================================================
   JIKA DATA TIDAK DITEMUKAN
========================================================= */

if (!$data) {

    header("Location: kendaraan.php");

    exit;
}


/* =========================================================
   GAMBAR UTAMA
========================================================= */

$fotoUtama = $data['foto'] ?: 'xforce.jpg';


/* =========================================================
   AMBIL NAMA FILE TANPA EXTENSION

   Contoh:
   xpander.jpg
   menjadi:
   xpander
========================================================= */

$namaFile = pathinfo(
    $fotoUtama,
    PATHINFO_FILENAME
);


/* =========================================================
   FOLDER GAMBAR
========================================================= */

$folderGambar = __DIR__ . '/assets/images/';


/* =========================================================
   WARNA UTAMA DARI DATABASE
========================================================= */

$warnaUtama = !empty($data['warna'])
    ? $data['warna']
    : 'Silver';


/* =========================================================
   MAPPING NAMA WARNA

   Database / nama file
   menjadi nama yang ditampilkan
========================================================= */

$mappingWarna = [

    'hitam' => 'Black',
    'black' => 'Black',

    'merah' => 'Red',
    'red' => 'Red',

    'putih' => 'White',
    'white' => 'White',

    'silver' => 'Silver',

    'abu' => 'Grey',
    'abu-abu' => 'Grey',
    'grey' => 'Grey',
    'gray' => 'Grey',

    'biru' => 'Blue',
    'blue' => 'Blue',

    'hijau' => 'Green',
    'green' => 'Green',

    'kuning' => 'Yellow',
    'yellow' => 'Yellow',

    'coklat' => 'Brown',
    'brown' => 'Brown'

];


/* =========================================================
   MAPPING CLASS CSS WARNA

   Digunakan untuk icon / bulatan warna.
   
   Contoh:
   Putih   -> white
   Hitam   -> black
   Merah   -> red
========================================================= */

$mappingClassWarna = [

    'hitam' => 'black',
    'black' => 'black',

    'merah' => 'red',
    'red' => 'red',

    'putih' => 'white',
    'white' => 'white',

    'silver' => 'silver',

    'abu' => 'grey',
    'abu-abu' => 'grey',
    'grey' => 'grey',
    'gray' => 'grey',

    'biru' => 'blue',
    'blue' => 'blue',

    'hijau' => 'green',
    'green' => 'green',

    'kuning' => 'yellow',
    'yellow' => 'yellow',

    'coklat' => 'brown',
    'brown' => 'brown'

];


/* =========================================================
   FORMAT GAMBAR YANG DIDUKUNG
========================================================= */

$ekstensiGambar = [

    'jpg',
    'jpeg',
    'png',
    'webp'

];


/* =========================================================
   DAFTAR WARNA
========================================================= */

$warnaTersedia = [];


/* =========================================================
   WARNA UTAMA
========================================================= */

$warnaUtamaKey = strtolower(
    trim($warnaUtama)
);


/* =========================================================
   TENTUKAN CLASS CSS WARNA UTAMA
========================================================= */

$warnaUtamaClass = $mappingClassWarna[$warnaUtamaKey]
    ?? strtolower($warnaUtamaKey);


/* =========================================================
   MASUKKAN GAMBAR UTAMA
========================================================= */

$warnaTersedia[] = [

    'nama' => $mappingWarna[$warnaUtamaKey]
        ?? $warnaUtama,

    'file' => $fotoUtama,

    'class' => $warnaUtamaClass

];


/* =========================================================
   CARI GAMBAR VARIASI WARNA SECARA OTOMATIS

   Contoh:

   xpander.jpg
   xpander-hitam.png
   xpander-merah.png

   akan dibaca sebagai:

   White / Black / Red
========================================================= */

foreach ($ekstensiGambar as $ext) {


    $fileList = glob(

        $folderGambar .
        $namaFile .
        '-*.' .
        $ext

    );


    if (!$fileList) {

        continue;

    }


    foreach ($fileList as $pathFile) {


        $namaFileLengkap = basename($pathFile);


        /* =================================================
           AMBIL NAMA FILE TANPA EXTENSION

           xpander-hitam.png
           menjadi:
           xpander-hitam
        ================================================= */

        $namaTanpaExt = pathinfo(

            $namaFileLengkap,

            PATHINFO_FILENAME

        );


        /* =================================================
           CARI TANDA -

           xpander-hitam
           menjadi:
           hitam
        ================================================= */

        $posisiStrip = strrpos(

            $namaTanpaExt,

            '-'

        );


        if ($posisiStrip === false) {

            continue;

        }


        /* =================================================
           AMBIL NAMA WARNA DARI FILE
        ================================================= */

        $namaWarnaFile = substr(

            $namaTanpaExt,

            $posisiStrip + 1

        );


        $namaWarnaFile = strtolower(

            trim($namaWarnaFile)

        );


        /* =================================================
           UBAH NAMA WARNA UNTUK TAMPILAN
        ================================================= */

        if (isset($mappingWarna[$namaWarnaFile])) {

            $namaWarna =
                $mappingWarna[$namaWarnaFile];

        } else {

            $namaWarna =
                ucfirst($namaWarnaFile);

        }


        /* =================================================
           TENTUKAN CLASS CSS WARNA
        ================================================= */

        $warnaClass = $mappingClassWarna[$namaWarnaFile]
            ?? strtolower($namaWarnaFile);


        /* =================================================
           MASUKKAN GAMBAR KE DAFTAR WARNA
        ================================================= */

        $warnaTersedia[] = [

            'nama' => $namaWarna,

            'file' => $namaFileLengkap,

            'class' => $warnaClass

        ];

    }

}


/* =========================================================
   HILANGKAN DATA GAMBAR YANG DUPLIKAT
========================================================= */

$warnaUnik = [];


foreach ($warnaTersedia as $warna) {


    $sudahAda = false;


    foreach ($warnaUnik as $cek) {


        if ($cek['file'] === $warna['file']) {

            $sudahAda = true;

            break;

        }

    }


    if (!$sudahAda) {

        $warnaUnik[] = $warna;

    }

}


$warnaTersedia = $warnaUnik;

?>


<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Detail Kendaraan - AutoCar
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
====================================================== -->

<header class="site-header">

    <div class="container header-inner">

        <a
            class="brand"
            href="index.php"
        >

            <span class="brand-mark">
                3SM
            </span>

            <span>

                AutoCar

                <small>
                    SISTEM INFORMASI PENJUALAN
                </small>

            </span>

        </a>


        <nav class="public-nav">

            <a href="index.php">
                Home
            </a>

            <a href="kendaraan.php">
                Daftar Kendaraan
            </a>

            <a href="bengkel.php">
                Daftar Bengkel
            </a>

            <a
                class="btn btn-primary btn-small"
                href="login.php"
            >
                Login Sistem
            </a>

        </nav>

    </div>

</header>



<!-- =====================================================
     KONTEN
====================================================== -->

<main class="container">


    <div class="page-head">

        <p>

            Home /
            Daftar Kendaraan /
            Detail Kendaraan

        </p>

    </div>



    <div class="detail-grid">


        <!-- =================================================
             BAGIAN GAMBAR
        ================================================== -->

        <div class="detail-visual">


            <div class="detail-image-wrapper">

                <img

                    id="vehicleImage"

                    class="detail-image animated-car"

                    src="assets/images/<?= e($warnaTersedia[0]['file']) ?>"

                    alt="<?= e($data['nama_tipe']) ?>"

                >

            </div>



            <!-- =================================================
                 PILIH WARNA
            ================================================== -->

            <div class="color-selector">


                <div class="color-title">
                    Pilih Warna
                </div>


                <div class="color-options">


                    <?php foreach ($warnaTersedia as $index => $warna): ?>


                        <button

                            type="button"

                            class="color-option <?= $index === 0 ? 'active' : '' ?>"

                            data-index="<?= $index ?>"

                            data-image="assets/images/<?= e($warna['file']) ?>"

                            data-color="<?= e($warna['nama']) ?>"

                        >


                            <!--
                            CLASS WARNA SUDAH DIPERBAIKI

                            Sebelumnya:
                            color-<?= strtolower(e($warna['nama'])) ?>

                            Sekarang:
                            menggunakan $warna['class']
                            -->


                            <span
                                class="color-circle color-<?= e($warna['class']) ?>"
                            ></span>


                            <span>

                                <?= e($warna['nama']) ?>

                            </span>


                        </button>


                    <?php endforeach; ?>


                </div>


            </div>



            <div class="color-auto-info">

                <span class="auto-dot"></span>

                Warna kendaraan berubah otomatis

            </div>


        </div>



        <!-- =================================================
             INFORMASI KENDARAAN
        ================================================== -->

        <div class="detail-info">


            <span
                class="<?= status_kendaraan_class($data['status']) ?>"
            >

                <?= e($data['status']) ?>

            </span>



            <div class="vehicle-brand">

                <?= e($data['nama_merek']) ?>

            </div>



            <h1>

                <?= e($data['nama_tipe']) ?>

            </h1>



            <p>

                Kode Unit:

                <strong>

                    <?= e($data['kode_unit']) ?>

                </strong>

            </p>



            <div class="price">

                <?= rupiah($data['harga']) ?>

            </div>



            <!-- =================================================
                 INFORMASI DETAIL
            ================================================== -->

            <div class="info-list">


                <div class="info-item">

                    <small>
                        Merek
                    </small>

                    <strong>

                        <?= e($data['nama_merek']) ?>

                    </strong>

                </div>



                <div class="info-item">

                    <small>
                        Tipe
                    </small>

                    <strong>

                        <?= e($data['nama_tipe']) ?>

                    </strong>

                </div>



                <div class="info-item">

                    <small>
                        Tahun
                    </small>

                    <strong>

                        <?= e($data['tahun']) ?>

                    </strong>

                </div>



                <div class="info-item">

                    <small>
                        Warna
                    </small>

                    <strong id="vehicleColor">

                        <?= e($warnaTersedia[0]['nama']) ?>

                    </strong>

                </div>


            </div>



            <br>



            <div class="card">

                <h3>
                    Deskripsi Kendaraan
                </h3>

                <p>

                    <?= e(

                        $data['deskripsi']

                        ?:

                        'Informasi kendaraan tersedia melalui Sales dealer.'

                    ) ?>

                </p>

            </div>



            <br>



            <div class="notice">

                Tertarik dengan kendaraan ini?

                Silakan kunjungi dealer dan hubungi Sales kami
                untuk informasi lebih lanjut.

            </div>



            <br>



            <a

                class="btn btn-secondary"

                href="kendaraan.php"

            >

                Kembali ke Daftar Kendaraan

            </a>


        </div>


    </div>


</main>



<!-- =====================================================
     LOKASI DEALER
     DETAIL KENDARAAN
====================================================== -->

<section class="location-section">

    <div class="container">

        <div class="location-box">


            <!-- =========================================
                 INFORMASI DEALER
            ========================================== -->

            <div class="location-info">


                <div class="location-label">

                    📍 Lokasi Kami

                </div>


                <h2 class="location-title">

                    Tertarik dengan Kendaraan Ini?

                </h2>


                <p class="location-address">

                    Silakan datang langsung ke dealer kami
                    untuk melihat kendaraan secara langsung
                    dan mendapatkan informasi lebih lanjut
                    dari Sales.

                </p>


                <div class="location-detail">


                    <!-- ALAMAT DEALER -->

                    <div class="location-detail-item">

                        <div class="location-detail-icon">

                            📍

                        </div>


                        <div>

                            <small>
                                Alamat Dealer
                            </small>


                            <strong>

                                <?= htmlspecialchars($alamat_dealer) ?>

                            </strong>

                        </div>

                    </div>



                    <!-- JAM OPERASIONAL -->

                    <div class="location-detail-item">

                        <div class="location-detail-icon">

                            🕐

                        </div>


                        <div>

                            <small>
                                Jam Operasional
                            </small>


                            <strong>

                                <?= htmlspecialchars($jam_operasional) ?>

                            </strong>

                        </div>

                    </div>



                    <!-- WHATSAPP / SALES -->

                    <div class="location-detail-item">

                        <div class="location-detail-icon">

                            📱

                        </div>


                        <div>

                            <small>
                                WhatsApp / Sales
                            </small>


                            <strong>

                                <?= htmlspecialchars($whatsapp_dealer) ?>

                            </strong>

                        </div>

                    </div>


                </div>



                <!-- TOMBOL GOOGLE MAPS -->

                <a

                    href="<?= htmlspecialchars($maps_link) ?>"

                    target="_blank"

                    class="btn btn-primary location-button"

                >

                    Lihat Lokasi di Google Maps

                </a>


            </div>



            <!-- =========================================
                 GOOGLE MAPS
            ========================================== -->

            <div class="location-map">

                <iframe

                    src="<?= htmlspecialchars($maps_embed) ?>"

                    loading="lazy"

                    allowfullscreen=""

                    referrerpolicy="no-referrer-when-downgrade"

                >
                </iframe>

            </div>


        </div>

    </div>

</section>



<!-- =====================================================
     FOOTER
====================================================== -->

<footer class="site-footer">


    <div class="container">


        <p>

            © 2026 AutoCar —
            Sistem Informasi Penjualan Kendaraan.

        </p>


    </div>


</footer>



<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script src="assets/js/script.js"></script>

<script src="assets/js/detail-kendaraan.js"></script>


</body>

</html>