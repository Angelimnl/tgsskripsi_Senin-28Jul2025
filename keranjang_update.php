<?php
include 'config1.php';
session_start();
include "authcheckkasir.php";

// Mengambil input qty dari form. Dimasukkan ke variabel quantity
$qty = $_POST['qty'];
// print_r($_SESSION['cart'][1]);

// Perulangan untuk memperbarui qty dalam sesi cart keranjang
// $key → Menyimpan indeks (id)/kunci array (bisa numerik/string jika asosiatif).
// $value → Menyimpan nilai elemen array pada kunci tsb.
foreach ($_SESSION['cart'] as $key => $value) 
{
    // Mengecek apakah (id, nama, harga) dari item keranjang tidak kosong. Jika semuanya terisi, item dianggap valid.
    // $qty[$key] Mengambil nilai jumlah barang yang baru dari data form POST yang dikirim dari input dengan nama qty[].
    // = Menetapkan nilai baru dari $qty[$key] ke dalam session keranjang belanja.
    if (!empty($value['id']) && !empty($value['nama']) && !empty($value['harga'])) {
        $_SESSION['cart'][$key]['qty'] = !empty($qty[$key]) ? $qty[$key] : 1; // Default ke 1 jika kosong
    } else {
        unset($_SESSION['cart'][$key]); // Hapus item kosong
    }    
}
header('location:kasir.php');
?>