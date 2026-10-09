<?php

/* =========================================================
   PENGATURAN LOKASI DEALER
========================================================= */

$nama_dealer = "AutoCar Dealer";

#bisa di ubah sesuka hati 
$alamat_dealer = " 83-21, Wangsimni-ro, Seongdong-gu, Seoul, Korea Selatan";

$jam_operasional = "Senin - Sabtu, 08.00 - 17.00";

$whatsapp_dealer = "08XX-XXXX-XXXX";


/* =========================================================
   GOOGLE MAPS
   Dibuat otomatis berdasarkan alamat dealer
========================================================= */

$alamat_maps = urlencode($alamat_dealer);

$maps_link =
    "https://www.google.com/maps/search/?api=1&query="
    . $alamat_maps;

$maps_embed =
    "https://www.google.com/maps?q="
    . $alamat_maps
    . "&output=embed";

?>