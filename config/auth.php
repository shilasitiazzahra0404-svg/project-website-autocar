<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function wajib_login($role = null)
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../login.php");
        exit;
    }

    if ($role !== null && $_SESSION['role'] !== $role) {
        header("Location: ../index.php");
        exit;
    }
}
?>
