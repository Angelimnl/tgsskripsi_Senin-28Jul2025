<?php
include 'config1.php';
session_start();
include 'authcheck.php';

$view = $dbconnect->query("SELECT * FROM role");
?>

<!DOCTYPE html>
<html>
<head>
    <title>List Role</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <style>
        .header {
            display: flex;
            align-items: center;
            padding: 8px 20px;
            background: #d895da;
            color: white;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .container { margin-top: 70px; }

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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>List Role</strong></h1>
        </div>

        <!-- Menampilkan pesan sukses hanya sekali, lalu menghapusnya -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div class='alert alert-success' role="alert">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); // Hapus pesan setelah ditampilkan ?>
        <?php } ?>

        <div class="form-group">
            <a href="role_add.php" class="btn btn-custom1">Tambah Data</a>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>ID Role</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
            
            <?php while ($row = $view->fetch_array()) { ?>
            <tr>
                <td><?= $row['id_role'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td>
                    <a href="role_edit.php?id=<?= $row['id_role'] ?>" class="btn btn-custom btn-sm">Edit</a>
                    <a href="role_hapus.php?id=<?= $row['id_role'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
