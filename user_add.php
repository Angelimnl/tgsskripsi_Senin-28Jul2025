<?php
include 'config1.php';
session_start(); // Memulai sesi

include 'authcheck.php';

$role = mysqli_query($dbconnect, "SELECT * FROM role");

// Cek apakah tombol simpan diklik
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Simpan ke database
    mysqli_query($dbconnect, "INSERT INTO user VALUES ('', '$nama','$username','$password','$role_id')");

    // Set session sukses
    $_SESSION['success'] = 'BERHASIL menambahkan data';

    // Redirect ke user.php untuk menghindari pengisian ulang data (PRG Pattern)
    session_write_close(); // Pastikan session disimpan
    header("Location: user.php");
    exit(); // Stop eksekusi kode lebih lanjut
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>Tambah User</strong></h1>
        </div>

        <form method="post" autocomplete="off">
            <div class="form-group">
                <label>Nama User</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama user" autocomplete="off"> 
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" autocomplete="off"> 
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="new-password"> 
            </div>
            <div class="form-group">
                <label>Role Akses</label>
                <select name="role_id" class="form-control">
                    <option value="">Pilih role akses</option>
                    <?php while ($row = mysqli_fetch_array($role)) { ?>
                        <option value="<?= $row['id_role'] ?>"><?= $row['nama'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
            <a href="user.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
