<?php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka)
{
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

function status_kendaraan_class($status)
{
    switch ($status) {
        case 'TERSEDIA':
            return 'badge badge-success';

        case 'DIPROSES':
            return 'badge badge-warning';

        case 'TERJUAL':
            return 'badge badge-dark';

        default:
            return 'badge';
    }
}

function status_transaksi_class($status)
{
    switch ($status) {
        case 'Selesai':
            return 'badge badge-success';

        case 'Menunggu Verifikasi':
            return 'badge badge-warning';

        case 'Dibatalkan':
            return 'badge badge-danger';

        default:
            return 'badge';
    }
}

?>