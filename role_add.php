<?php
include 'config1.php';
session_start();
include 'authcheck.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];

    if (mysqli_query($dbconnect, "INSERT INTO role VALUES ('','$nama')")) {
        $_SESSION['success'] = 'BERHASIL menambahkan data';
    }

    header("location:role.php");
    exit(); // Hentikan eksekusi setelah redirect
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Role</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>Tambah Role</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Nama Role</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama role" required>
            </div>
            <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
            <a href="role.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
