<?php
include 'config1.php';
session_start(); // Memulai sesi

include 'authcheck.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mengambil data berdasarkan ID
    $data = mysqli_query($dbconnect, "SELECT * FROM barang WHERE id_barang='$id'");
    $data = mysqli_fetch_assoc($data);

    // Jika data tidak ditemukan, kembali ke barang3.php
    if (!$data) {
        $_SESSION['error'] = '❌ Data barang tidak ditemukan!';
        header("location:barang3.php");
        exit();
    }
}

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $id = $_GET['id'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];

    // Menjalankan query update
    $query = "UPDATE barang SET tanggal_masuk ='$tanggal_masuk', nama='$nama', harga='$harga', jumlah='$jumlah' WHERE id_barang='$id'";
    if (mysqli_query($dbconnect, $query)) {
        $_SESSION['success'] = '✅ BERHASIL memperbarui data!';
    } else {
        $_SESSION['error'] = '❌ Gagal memperbarui data: ' . mysqli_error($dbconnect);
    }

    // Redirect ke barang3.php agar pesan muncul di halaman tersebut
    header("location:barang3.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perbarui Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <style>
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 8px 20px;
            background: #d895da;
            color: white;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .container {
            margin-top: 70px; /* Tambahkan margin agar tidak tertutup header */
        }

        .btn-custom1 {
        background-color: #5a9bd5;
        border-color: #5a9bd5;
        color: #fff; /* agar teksnya putih dan kontras */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>Edit Barang</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Tanggal Masuk</label>
                <input type="datetime-local" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_masuk'])) ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama barang" value="<?= $data['nama'] ?>" required> 
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" placeholder="Harga barang" value="<?= $data['harga'] ?>" required> 
            </div>
            <div class="form-group">
                <label>Jumlah Stok</label>
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah stok" value="<?= $data['jumlah'] ?>" required> 
            </div>
            <input type="submit" name="update" value="Perbarui" class="btn btn-custom1">
            <a href="barang3.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
