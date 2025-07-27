<?php
include 'config1.php';
session_start(); // Memulai sesi

//Jika anda memiliki userid maka akan kembali ke dashboard, pengecekan autentikasi apakah memiliki userid/tidak
if (isset($_SESSION['userid'])) {
    header("Location: design.php");
    exit();
}

if (isset($_POST['masuk'])) 
{    
    // Periksa apakah username dan password sudah diisi
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username != '' && $password != '') {
        // Query untuk mendapatkan user berdasarkan username dan password
        $query = mysqli_query($dbconnect, "SELECT * FROM user WHERE username='$username' AND password='$password'");
        
        $data = mysqli_fetch_assoc($query); // Mengambil hasil query/Mendapatkan hasil dari data/Menampilkan data jd array (sekumpulan variabel)
        $count = mysqli_num_rows($query); // Mendapatkan nilai jumlah data/Baris utk mendapatkan nilai, misalnya nilai jumlahnya 0 atau 1, jika data bernilai 0 maka data tidak ada/salah, jika data bernilai 1 atau lebih dr 0 maka akan lanjut ke yang diinisiasikan seperti userid, nama, role_id dan diambil dari variabel data ($data).
        
        if ($count == 0) { 
            $_SESSION['error'] = 'Username & password salah';
        } else {
            $_SESSION['userid'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['role_id'] = $data['role_id'];

            // Redirect ke halaman utama
            header("location:design.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Username dan password wajib diisi!';
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <!--------------- Alert ----------------------->  
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']); // Menghapus pesan agar tidak tampil lagi
        }
        ?>

        <h1>Login</h1>
        <form method="post">
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
            <input type="submit" name="masuk" value="Masuk" class="btn btn-default">
        </form>
    </div>
</body>
</html>
