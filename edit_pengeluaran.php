<?php
include 'config1.php';
session_start(); // Memulai sesi

include 'authcheck.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mengambil data berdasarkan ID
    $data = mysqli_query($dbconnect, "SELECT * FROM pengeluaran WHERE id_pengeluaran='$id'");
    $data = mysqli_fetch_assoc($data);

    // Jika data tidak ditemukan, kembali ke barang3.php
    if (!$data) {
        $_SESSION['error'] = '❌ Data barang tidak ditemukan!';
        header("location:pengeluaran.php");
        exit();
    }
}

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $id = $_GET['id'];
    $tanggal_keluar = $_POST['tanggal_keluar'];
    $nama_pengeluaran = $_POST['nama_pengeluaran'];
    $jumlah = $_POST['jumlah'];

    // Menjalankan query update
    $query = "UPDATE pengeluaran SET tanggal_keluar ='$tanggal_keluar', nama_pengeluaran='$nama_pengeluaran', jumlah='$jumlah' WHERE id_pengeluaran='$id'";
    if (mysqli_query($dbconnect, $query)) {
        $_SESSION['success'] = '✅ BERHASIL memperbarui data pengeluaran!';
    } else {
        $_SESSION['error'] = '❌ Gagal memperbarui data pengeluaran: ' . mysqli_error($dbconnect);
    }

    // Redirect ke pengeluaran.php agar pesan muncul di halaman tersebut
    header("location:pengeluaran.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perbarui Pengeluaran</title>
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
            <h1><strong>Edit Pengeluaran</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Tanggal Keluar</label>
                <input type="datetime-local" name="tanggal_keluar" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_keluar'])) ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Pengeluaran</label>
                <input type="text" name="nama_pengeluaran" class="form-control" placeholder="Nama pengeluaran" value="<?= $data['nama_pengeluaran'] ?>" required> 
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah pengeluaran" value="<?= $data['jumlah'] ?>" required> 
            </div>
            <input type="submit" name="update" value="Perbarui" class="btn btn-custom1">
            <a href="pengeluaran.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
