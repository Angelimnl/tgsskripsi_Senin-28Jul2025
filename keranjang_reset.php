<?php
session_start();

unset($_SESSION['cart']); // Menghapus isi keranjang
header('Location: kasir.php'); // Kembali ke halaman kasir
?>