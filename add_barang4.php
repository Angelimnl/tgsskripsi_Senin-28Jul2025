<?php
include 'config1.php';
session_start();
include 'authcheck.php';
include 'authcheckrole.php';

allowRoles([1]); //1 = Admin

date_default_timezone_set('Asia/Jakarta'); // Atur zona waktu lokal
$default_datetime = date('Y-m-d\TH:i');     // Format yang cocok untuk input datetime-local

// Isset() adalah mengecek apakah variabel SIMPAN sudah ada/didefinisikan dan tidak bernilai NULL, jika ada, maka if dijalankan/dieksekusi. Untuk mencegah error jika form belum dikirim.
// $_POST['simpan'] mengacu pada tombol submit dalam form yang memiliki atribut name="simpan".
// Jika tombol submit dengan name "simpan" ditekan, maka kondisi isset($_POST['simpan']) akan bernilai true, dan blok kode di dalamnya akan dieksekusi.
if (isset($_POST['simpan'])) {
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $jumlah = $_POST['jumlah'];

    // Cek apakah nama barang sudah ada
    $cek = mysqli_query($dbconnect, "SELECT * FROM barang WHERE nama = '$nama'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = '❌ Nama barang sudah ada, tidak boleh duplikat!';
        header("location:barang3.php");
        exit();
    }

    // Simpan ke database jika belum ada
    $query = "INSERT INTO barang (tanggal_masuk, nama, harga, jumlah) VALUES ('$tanggal_masuk', '$nama', '$harga', '$jumlah')";
    if (mysqli_query($dbconnect, $query)) {
        $_SESSION['success'] = '✅ BERHASIL menambahkan data!';
    } else {
        $_SESSION['error'] = '❌ Gagal menambahkan data: ' . mysqli_error($dbconnect);
    }

    header("location:barang3.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
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
            margin-top: 70px;
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
            <h1><strong>Tambah Barang</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Tanggal Masuk</label>
                <input type="datetime-local" name="tanggal_masuk" class="form-control" value="<?= $default_datetime ?>" required>
            </div>

            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama barang" required> 
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" class="form-control" placeholder="Harga barang" required> 
            </div>
            <div class="form-group">
                <label>Jumlah Stok</label>
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah stok" required> 
            </div>
            <input type="submit" name="simpan" value="Simpan" class="btn btn-custom1">
            <a href="barang3.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
