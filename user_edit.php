<?php
include 'config1.php';
session_start(); // Memulai sesi

include 'authcheck.php';

$role = mysqli_query($dbconnect, "SELECT * FROM role");


if (isset($_GET['id'])) {
    $id = $_GET['id'];  // Ambil ID Produk yang Akan Diedit, kode ini mengambil nilai id dari parameter URL, yang merupakan id produk yang ingin diedit dari user.

    // Mengambil data berdasarkan ID
    $data = mysqli_query($dbconnect, "SELECT * FROM user WHERE id_user='$id'");
    $data = mysqli_fetch_assoc($data);
}

// Proses update data
if (isset($_POST['update'])) {
    $id = $_GET['id'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Simpan ke database
    $update = mysqli_query($dbconnect, "UPDATE user SET nama='$nama', username='$username', password='$password', role_id='$role_id' WHERE id_user='$id'");

    if ($update) {
        $_SESSION['success'] = 'BERHASIL memperbarui data';
    } else {
        $_SESSION['error'] = 'Gagal memperbarui data';
    }

    // Redirect ke user.php menggunakan PRG pattern
    header("Location: user.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perbarui Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 10px 20px;
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
            <h1><strong>Edit Pengguna</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Nama User</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama user" value="<?= htmlspecialchars($data['nama']) ?>"> 
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username" value="<?= htmlspecialchars($data['username']) ?>"> 
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="text" name="password" class="form-control" placeholder="Password" value="<?= htmlspecialchars($data['password']) ?>"> 
            </div>
            <div class="form-group">
                <label>Role Akses</label>
                <select name="role_id" class="form-control">
                    <option value="">Pilih Peran Pengguna</option>
                    <?php while ($row = mysqli_fetch_array($role)) { ?>
                        <option value="<?= $row['id_role'] ?>" <?= ($row['id_role'] == $data['role_id']) ? 'selected' : '' ?>>
                            <?= $row['nama'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <input type="submit" name="update" value="Perbarui" class="btn btn-custom1">
            <a href="user.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
