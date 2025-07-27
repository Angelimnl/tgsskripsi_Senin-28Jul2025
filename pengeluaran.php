<?php
include 'config1.php';
session_start();
include 'authcheck.php';

$view = $dbconnect->query("SELECT * FROM pengeluaran");
if (isset($_POST['tambah_jumlah'])) {
    $id_barang = $_POST['id_pengeluaran'];
    $jumlah_tambah = intval($_POST['jumlah']);

    $update = $dbconnect->query("UPDATE pengeluaran SET jumlah = jumlah + $jumlah_tambah WHERE id_pengeluaran = '$id_pengeluaran'");

    if ($update) {
        $_SESSION['success'] = "Jumlah pengeluaran berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Gagal menambahkan jumlah pengeluaran.";
    }   
        header("Location: pengeluaran.php");
        exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengeluaran</title>
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

        .btn-custom {
        background-color: #d895da;
        border-color: #d895da;
        color: #fff; /* agar teksnya putih dan kontras */
        }
        
        .btn-custom1 {
        background-color: #5a9bd5;
        border-color: #5a9bd5;
        color: #fff; /* agar teksnya putih dan kontras */
        }

        .alert {
        z-index: 999;
        position: relative;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>Pengeluaran</strong></h1>
        </div>

        <!-- Menampilkan pesan sukses atau error -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div class='alert alert-success' role='alert'>
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); // Hapus sesi setelah ditampilkan ?>
        <?php } ?>

        <?php if (isset($_SESSION['error'])) { ?>
            <div class='alert alert-danger' role='alert'>
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); // Hapus sesi setelah ditampilkan ?>
        <?php } ?>

        <div class="form-group">
        <a href="add_pengeluaran.php" class="btn btn-custom1">Tambah Data</a>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>ID Pengeluaran</th>
                <th>Tanggal Keluar</th> <!-- Tambahan kolom tanggal -->
                <th>Nama Pengeluaran</th>
                <th>Jumlah (Rp)</th>
                <th>Aksi</th>
            </tr>
            
            <?php while ($row = $view->fetch_array()) { ?>
            <tr>
                <td><?= $row['id_pengeluaran'] ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_keluar'])) ?? '-' ?></td>
                <td><?= $row['nama_pengeluaran'] ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td>
                    <a href="edit_pengeluaran.php?id=<?= $row['id_pengeluaran'] ?>" class="btn btn-custom btn-sm">Edit</a>
                    <a href="hapus_pengeluaran.php?id=<?= $row['id_pengeluaran'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
