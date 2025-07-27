
<?php
// Authority Check/Pemeriksaan otoritas pengguna
if (!isset($_SESSION['userid'])) {  
    $_SESSION['error'] = 'Anda harus login dahulu';
    header("Location: login.php");
    exit();
}

if ($_SESSION['role_id'] == 2) {
    //redirect/mengarahkan atau mengalihkan ke halaman dashboard.php
    header("Location: dashboard.php");
    exit();
}
?>