<?php

$page_title = "Report Prestasi & Insentif";
$active = "insentif";

include "../config/koneksi.php";
include "../config/akses_laporan.php";


/* =========================================================
   PROSES PIN
========================================================= */

$pesan_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pin_input = $_POST['pin_laporan'] ?? '';

    if ($pin_input === $pin_laporan) {

        $_SESSION['akses_laporan'] = true;

        header("Location: laporan_insentif.php");
        exit;

    } else {

        $pesan_error = "PIN yang dimasukkan salah.";

    }
}


/* =========================================================
   HEADER ADMIN
========================================================= */

include "header.php";


/* =========================================================
   JIKA BELUM MEMILIKI AKSES
   TAMPILKAN FORM PIN DI DALAM HALAMAN ADMIN
========================================================= */

if (!isset($_SESSION['akses_laporan']) || $_SESSION['akses_laporan'] !== true) {
?>

    <div class="page-head">
        <div class="page-head-row">
            <div>
                <span class="eyebrow">LAPORAN</span>
                <h1>Report Prestasi & Insentif</h1>
                <p>Akses khusus untuk melihat laporan prestasi dan insentif Sales.</p>
            </div>
        </div>
    </div>


    <div class="card pin-access-card">

        <div class="pin-access-icon">
            🔐
        </div>

        <h2>Akses Laporan</h2>

        <p>
            Report Prestasi & Insentif merupakan laporan khusus.
            Silakan masukkan PIN untuk melanjutkan.
        </p>

        <?php if ($pesan_error !== ""): ?>

            <div class="pin-error">
                <?= e($pesan_error) ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="pin-form-group">

                <label for="pin_laporan">
                    Masukkan PIN
                </label>

                <input
                    type="password"
                    name="pin_laporan"
                    id="pin_laporan"
                    maxlength="10"
                    placeholder="Masukkan PIN"
                    autocomplete="off"
                    required
                    autofocus
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary pin-button"
            >
                Buka Laporan
            </button>

        </form>

    </div>


    <style>

        /* =====================================================
           CARD AKSES PIN
        ===================================================== */

        .pin-access-card {
            max-width: 400px;
            margin: 25px auto;
            padding: 3px 10px;
            text-align: center;
        }


        .pin-access-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 0px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #eef5fb;
            border-radius: 50%;

            font-size: 22px;
        }

        
        /* JUDUL */

        .pin-access-card h2 {
            margin: 0 0 7px;

            color: #09243b;
            font-size: 22px;
            font-weight: 800;
        }


        .pin-access-card > p {
            max-width: 400px;
            margin: 0 auto 18px;

            color: #61758a;
            font-size: 12px;
            line-height: 1.5;
        }


        /* =====================================================
           PESAN ERROR
        ===================================================== */

        .pin-error {
            margin-bottom: 12px;
            padding: 8px 12px;

            background: #fff1f1;
            border: 1px solid #f0caca;
            border-radius: 7px;

            color: #c62828;
            font-size: 12px;
            font-weight: 600;
        }


        /* =====================================================
           FORM PIN
        ===================================================== */

        .pin-form-group {
            text-align: left;
            margin-bottom: 13px;
        }


        .pin-form-group label {
            display: block;
            margin-bottom: 5px;

            color: #09243b;
            font-size: 12px;
            font-weight: 700;
        }

        /* INPUT */

        .pin-form-group input {
            width: 100%;
            box-sizing: border-box;

            padding: 10px 14px;

            border: 1px solid #d8e1e9;
            border-radius: 8px;

            background: #fff;

            color: #09243b;
            font-size: 16px;

            text-align: center;
            letter-spacing: 4px;

            outline: none;

            transition: 0.2s;
        }


        .pin-form-group input:focus {
            border-color: #1261a0;

            box-shadow:
                0 0 0 3px rgba(18, 97, 160, 0.10);
        }


        .pin-form-group input::placeholder {
            color: #9aa9b8;
            letter-spacing: 2px;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .pin-button {
            width: 100%;
            padding: 13px 16px;

            font-size: 13px;
            font-weight: 700;

            cursor: pointer;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 700px) {

            .pin-access-card {
                margin: 20px 0;
                padding: 22px 20px;
            }

        }

    </style>


<?php

    include "footer.php";
    exit;
}


/* =========================================================
   JIKA PIN BENAR
   TAMPILKAN LAPORAN
========================================================= */

$result = mysqli_query(
    $conn,
    "SELECT 
        s.nama,
        COALESCE(
            SUM(
                CASE 
                    WHEN p.status='Selesai' 
                    THEN 1 
                    ELSE 0 
                END
            ),
            0
        ) AS total_penjualan,

        COALESCE(
            SUM(i.jumlah_insentif),
            0
        ) AS total_insentif

    FROM sales s

    LEFT JOIN penjualan p 
        ON s.id_sales = p.id_sales

    LEFT JOIN insentif i 
        ON s.id_sales = i.id_sales

    GROUP BY s.id_sales

    ORDER BY total_penjualan DESC"
);

?>


<div class="page-head">

    <div class="page-head-row">

        <div>

            <span class="eyebrow">
                LAPORAN
            </span>

            <h1>
                Report Prestasi & Insentif
            </h1>

            <p>
                Ringkasan pencapaian Sales dan insentif.
            </p>

        </div>


        <button
            class="btn btn-primary btn-small"
            onclick="window.print()"
        >
            Cetak Laporan
        </button>

    </div>

</div>


<div class="card">

    <div class="table-wrap">

        <table class="table">

            <tr>

                <th>No</th>

                <th>Nama Sales</th>

                <th>Total Penjualan</th>

                <th>Target</th>

                <th>Pencapaian</th>

                <th>Total Insentif</th>

            </tr>


            <?php

            $no = 1;

            while ($row = mysqli_fetch_assoc($result)):

                $persen = min(
                    100,
                    ($row['total_penjualan'] / 10) * 100
                );

            ?>

                <tr>

                    <td>
                        <?= $no++ ?>
                    </td>

                    <td>
                        <?= e($row['nama']) ?>
                    </td>

                    <td>
                        <?= $row['total_penjualan'] ?> Unit
                    </td>

                    <td>
                        10 Unit
                    </td>

                    <td>
                        <?= number_format($persen, 0) ?>%
                    </td>

                    <td>
                        <?= rupiah($row['total_insentif']) ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>


<?php include "footer.php"; ?>