<?php
include 'config1.php';
session_start(); // Mulai sesi

include 'authcheck.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID berupa angka untuk keamanan

    // Query DELETE
    $query = "DELETE FROM pengeluaran WHERE id_pengeluaran = $id";
    $result = mysqli_query($dbconnect, $query);

    // Periksa apakah query berhasil
    if ($result) {
        $_SESSION['success'] = 'BERHASIL menghapus data pengeluaran';
    } else {
        $_SESSION['error'] = 'GAGAL menghapus data pengeluaran: ' . mysqli_error($dbconnect);
    }

    // Redirect ke halaman sebelumnya atau halaman tertentu
    header("Location: pengeluaran.php?success=1");
    exit();
}
?>