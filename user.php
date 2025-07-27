<?php
include 'config1.php';
session_start();
include 'authcheck.php';

$view = $dbconnect->query("SELECT user.*, role.nama AS nama_role FROM user INNER JOIN role ON user.role_id = role.id_role");
?>

<!DOCTYPE html>
<html>
<head>
    <title>List User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
            <h1><strong>List User</strong></h1>
        </div>

        <?php if (isset($_SESSION['success']) && $_SESSION['success'] != '') { ?>
            <div class='alert alert-success' role="alert">
                <?= $_SESSION['success'] ?>
            </div>
        <?php 
            unset($_SESSION['success']); // Hapus session setelah ditampilkan
        } ?>

        <div class="form-group">
        <a href="user_add.php" class="btn btn-custom1">Tambah data</a>
        </div>
        
        <table class="table table-bordered">
            <tr>
                <th>ID User</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Password</th>
                <th>Role Akses</th>
                <th>Aksi</th>
            </tr>
            
            <?php while ($row = $view->fetch_array()) { ?>
            <tr>
                <td><?= $row['id_user'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['password'] ?></td>
                <td><?= $row['nama_role'] ?></td>
                <td>
                    <a href="user_edit.php?id=<?= $row['id_user'] ?>" class="btn btn-custom btn-sm">Edit</a>
                    <a href="user_hapus.php?id=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
