<?php
include 'config1.php';
session_start();
include 'authcheck.php';
include 'authcheckrole.php';

allowRoles([1]); //1 = Admin

$view = $dbconnect->query("SELECT * FROM barang");
if (isset($_POST['tambah_qty'])) {
    $id_barang = $_POST['id_barang'];
    $jumlah_tambah = intval($_POST['jumlah']);

    $update = $dbconnect->query("UPDATE barang SET jumlah = jumlah + $jumlah_tambah WHERE id_barang = '$id_barang'");

    if ($update) {
        $_SESSION['success'] = "Stok berhasil ditambahkan.";
        header("Location: barang3.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan stok.";
        header("Location: barang3.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>List Barang</title>
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
            <h1><strong>List Barang</strong></h1>
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

        <a href="add_barang4.php" class="btn btn-custom1">Tambah data</a>
                <form method="POST" class="form-inline" style="margin: 15px 0;">
                    <div class="form-group">
                        <label for="id_barang">Pilih Barang:</label>
                        <select name="id_barang" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $result_barang = $dbconnect->query("SELECT * FROM barang");
                            while ($barang = $result_barang->fetch_assoc()) {
                                echo "<option value='{$barang['id_barang']}'>{$barang['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah:</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                    <button type="submit" name="tambah_qty" class="btn btn-success">OK</button>
                </form>

        <table class="table table-bordered">
            <tr>
                <th>ID Barang</th>
                <th>Tanggal Masuk</th> <!-- Tambahan kolom tanggal -->
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah Stok</th>
                <th>Aksi</th>
            </tr>
            
            <?php while ($row = $view->fetch_array()) { ?>
            <tr>
                <td><?= $row['id_barang'] ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_masuk'])) ?? '-' ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['harga'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td>
                    <a href="edit_barang5.php?id=<?= $row['id_barang'] ?>" class="btn btn-custom btn-sm">Edit</a>
                    <a href="hapus_barang6.php?id=<?= $row['id_barang'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
