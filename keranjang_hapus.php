<?php
include 'config.php';
session_start();
include "authcheckkasir.php";

// Ambil ID Produk yang Akan Dihapus, Kode ini mengambil nilai dari parameter id yg dikirim melalui metode GET (biasanya dari URL) dan menyimpannya ke dalam variabel $id.
$id = $_GET['id'];

// $_SESSION['cart'] adalah variabel sesi yang digunakan untuk menyimpan keranjang belanja (shopping cart) dalam sesi pengguna.
// Ambil Data Keranjang Belanja dari Session
$cart = $_SESSION['cart']; 
// print_r($cart);

// Temukan Item yang Akan Dihapus
// array_filter() untuk mencari produk dalam keranjang ($cart) yang memiliki id sama dengan $id yang diterima dari URL.
// Fungsi anonim (function($var)) adalah callback/cocok utk sekali pakai yang dijalankan untuk setiap elemen dalam $cart.
// use ($id) Mengizinkan fungsi anonim menggunakan variabel $id dari luar fungsi.
// FUNGSI BIASA MEMPUNYAI NAMA: function salam($nama), FUNGSI ANONIM TIDAK MEMPUNYAI NAMA: $salam = function($nama) 
$k = array_filter($cart, function ($var) use ($id) { 
    return ($var['id']==$id);
});
// print_r($k);

// Hapus Item dari Session, kode ini menghapus item yang cocok ($k) dari $_SESSION['cart'] berdasarkan kunci ($key) yang ditemukan.
// Berfungsi utk mengambil data secara spesifik
// Menggunakan unset untuk menghapus datanya
foreach ($k as $key => $value) {
    unset($_SESSION['cart'][$key]);
}

// Fungsinya mengembalikan urutan indeks array/data agar berurutan kembali setelah salah satu elemen dihapus dengan unset(). Jadinya 0-1-2-3
// array_values Mengembalikan array baru dari nilai array yg lama, tapi indeks numerik (urutan angkanya) berurutan mulai dari 0.
$_SESSION['cart'] = array_values($_SESSION['cart']);

header('location:kasir.php');

?>