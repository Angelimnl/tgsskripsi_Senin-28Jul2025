<?php
include 'config1.php';
session_start(); // Mulai sesi

include 'authcheck.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID berupa angka untuk keamanan

    // Query DELETE
    $query = "DELETE FROM barang WHERE id_barang = $id";
    $result = mysqli_query($dbconnect, $query);

    // Periksa apakah query berhasil
    if ($result) {
        $_SESSION['success'] = 'BERHASIL menghapus data';
    } else {
        $_SESSION['error'] = 'GAGAL menghapus data: ' . mysqli_error($dbconnect);
    }

    // Redirect ke halaman sebelumnya atau halaman tertentu
    header("Location: barang3.php?success=1");
    exit();
}
?>