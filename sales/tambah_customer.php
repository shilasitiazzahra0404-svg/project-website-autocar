<?php
$page_title = "Tambah Customer";
$active = "customer";
include "../config/koneksi.php";
include "header.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp'] ?? ''));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));

    if ($nama === '' || $no_hp === '') {
        $error = "Nama dan nomor HP wajib diisi.";
    } else {
        $query = "INSERT INTO customer (nama,email,no_hp,alamat) VALUES ('$nama','$email','$no_hp','$alamat')";
        if (mysqli_query($conn, $query)) {
            header("Location: customer.php");
            exit;
        }
        $error = "Data customer gagal disimpan.";
    }
}
?>
<div class="page-head"><span class="eyebrow">DATA CUSTOMER</span><h1>Tambah Customer</h1><p>Masukkan data customer yang akan melakukan pembelian kendaraan.</p></div>
<div class="card form-card">
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-grid">
            <div class="form-group"><label>Nama Lengkap</label><input class="form-control" name="nama" required></div>
            <div class="form-group"><label>Nomor HP</label><input class="form-control" name="no_hp" required></div>
            <div class="form-group"><label>Email</label><input class="form-control" type="email" name="email"></div>
            <div class="form-group full"><label>Alamat</label><textarea class="form-control" name="alamat"></textarea></div>
        </div>
        <div class="action-group">
            <button class="btn btn-primary" type="submit">Simpan Customer</button>
            <a class="btn btn-secondary" href="customer.php">Batal</a>
        </div>
    </form>
</div>
<?php include "footer.php"; ?>
