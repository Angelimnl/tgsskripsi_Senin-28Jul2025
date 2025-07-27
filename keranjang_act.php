<?php
session_start();
include 'config1.php';
include 'authcheckkasir.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil data POST
$id_barang = $_POST['id_barang'];
$qty = $_POST['qty'];

// Validasi
if (empty($id_barang) || empty($qty) || $qty <= 0) {
    header("Location: kasir.php");
    exit();
}

// Ambil detail barang dari database
$result = mysqli_query($dbconnect, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    echo "Barang tidak ditemukan.";
    exit();
}

// Siapkan data untuk dimasukkan ke keranjang
$item = array(
    'id' => $barang['id_barang'],
    'nama' => $barang['nama'],
    'harga' => $barang['harga'],
    'qty' => $qty
);

// Tambahkan ke session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Cek apakah barang sudah ada di keranjang
$found = false;
foreach ($_SESSION['cart'] as $index => $value) {
    if ($value['id'] == $item['id']) {
        $_SESSION['cart'][$index]['qty'] += $qty;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = $item;
}

// Redirect kembali ke halaman kasir
header("Location: kasir.php");
exit();
?>