<?php
include 'config1.php';
session_start();
include 'authcheck.php';

// Ambil data berdasarkan ID dari GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($dbconnect, "SELECT * FROM role WHERE id_role='$id'");
    $data = mysqli_fetch_assoc($result);
}

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    
    if (mysqli_query($dbconnect, "UPDATE role SET nama='$nama' WHERE id_role='$id'")) {
        $_SESSION['success'] = 'BERHASIL memperbarui data';
    } else {
        $_SESSION['error'] = 'GAGAL memperbarui data';
    }

    // Redirect ke role.php
    header("location:role.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perbarui Peran</title>
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
            <h1><strong>Edit Peran</strong></h1>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Nama Peran</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama role" value="<?= $data['nama'] ?>" required>
            </div>
            <input type="submit" name="update" value="Perbarui" class="btn btn-custom1">
            <a href="role.php" class="btn btn-warning">Kembali</a>
        </form>
    </div>
</body>
</html>
