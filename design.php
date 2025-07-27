<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Authority Check/Pemeriksaan otoritas pengguna, pengecekan autentikasi kembali untuk mencegah penyusup atau peretas.
if (!isset($_SESSION['userid'])) {  
    $_SESSION['error'] = 'Anda harus login dahulu';
    header("Location: login.php");
    exit();
}

if ($_SESSION['role_id'] == 2) {
    header("Location: dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title> </title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
    <style>
        /* HEADER */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f5e9; /* Warna background luar */
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Agar teks berada di kiri */
            padding: 15px 20px; /* Padding kiri agar ada jarak */
            background: linear-gradient(to right, #d895da, #5a9bd5);
            color: white;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .icon {
        width: 40px; /* Sesuaikan ukuran icon */
        height: 40px;
        margin-right: 10px;
        }

        .text {
            text-align: left; /* Pastikan teks di dalam tetap rata kiri */
            margin-left: 10px; /* Jarak dari sisi kiri */
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            display: block;
        }

        .subtitle {
            font-size: 16px;
            font-weight: bold;
            display: block;
        }

        /* Layout utama */
        .main-container {
            display: flex;
            height: 100vh;
            padding-top: 60px; /* Agar konten tidak tertutup header */
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background:rgb(237, 237, 238);
            padding: 15px;
            flex-shrink: 0;
        }

        /* Styling untuk teks Dashboard agar tidak menempel ke atas */
        .sidebar a.dashboard-title {
            display: block;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: black;
            margin-top: 16px; /* Tambahkan margin atas agar ada jarak */
        }

        .nav-link:hover {
            background-color: grey;
        }

        /* Iframe agar mengisi sisa layar */
        .iframe-container {
            flex-grow: 1;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .hr-dashboard {
        border: none;
        border-top: 4px solid #000;
        margin: 10px 0;
        }

    </style>
</head>

<body>

<!-- Header -->
<div class="header">
    <img src="Logo Loddy's Pet Shop.png" alt="Icon" class="icon">
    <div class="text">
        <span class="title">SISTEM INFORMASI KEUANGAN</span>
        <span class="subtitle">LODDY'S PET SHOP  <i class="fa-solid fa-paw"></i> </span>
    </div>
</div>

<!-- Layout utama -->
<div class="main-container">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="#" class="dashboard-title" onclick="loadPage('dashboard.php')">Dashboard</a>
        <hr class="hr-dashboard">
        <ul class="fw-bold nav nav-pills flex-column mb-auto">
            <li><a href="#" class="text-black nav-link link-body-emphasis" onclick="loadPage('barang3.php')"><i class="fa-solid fa-basket-shopping me-2"></i>List Barang</a></li><hr>
            <li><a href="#" class="nav-link text-black link-body-emphasis" onclick="loadPage('role.php')"><i class="fa-solid fa-address-card me-2"></i>List Role</a></li><hr>
            <li><a href="#" class="nav-link text-black link-body-emphasis" onclick="loadPage('user.php')"><i class="fa-solid fa-users me-2"></i>List User</a></li><hr>
            <li><a href="#" class="nav-link text-black link-body-emphasis" onclick="loadPage('kasir.php')"><i class="fa-solid fa-cart-shopping me-2"></i>Kasir</a></li><hr>
            <li><a href="#" class="nav-link text-black link-body-emphasis" onclick="loadPage('pengeluaran.php')"><i class="fa-solid fa-money-bill-1 me-2"></i>Pengeluaran</a></li><hr>
            <li><a href="#" class="nav-link text-black link-body-emphasis" onclick="loadPage('hasil_transaksi.php')"><i class="fa-solid fa-file-lines me-2"></i>Hasil Transaksi</a></li><hr>
        </ul>
        <div class="dropdown ms-auto">
            <a href="#" class="d-flex align-items-center text-black text-decoration-none" data-bs-toggle="dropdown" class="fa-solid fa-right-from-bracket">
                <!-- <img src="Logo Loddy's Pet Shop.png" alt="" width="32" height="32" class="rounded-circle me-2"> -->
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="?logout=true">Sign out</a></li>
            </ul>
        </div>
    </div>

    <!-- Iframe Container -->
    <div class="iframe-container">
        <iframe id="content-frame" src="dashboard.php"></iframe>
    </div>
</div>

<script>
    // Fungsi untuk mengganti halaman dalam iframe
    function loadPage(page) {
        document.getElementById("content-frame").src = page;
    }
</script>

</body>
</html>
