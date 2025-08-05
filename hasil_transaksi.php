<?php
include 'config1.php';
include 'authcheck.php';
include 'authcheckrole.php';
allowRoles([6,1]);

// Ambil tanggal dari form GET (jika ada)
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Buat kondisi SQL jika tanggal diisi
$where = "";
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $tanggal_awal_db = date('Y-m-d', strtotime($tanggal_awal));
    $tanggal_akhir_db = date('Y-m-d', strtotime($tanggal_akhir));
    $where = "WHERE DATE(transaksi.created_at) BETWEEN '$tanggal_awal_db' AND '$tanggal_akhir_db'";
}

// Query transaksi
$query = $dbconnect->query("
    SELECT transaksi.* FROM transaksi 
    $where
    ORDER BY transaksi.created_at DESC
");

// Hitung total transaksi
$result_total = $dbconnect->query("SELECT SUM(total) AS total_transaksi FROM transaksi $where");
$total_transaksi = $result_total->fetch_assoc()['total_transaksi'] ?? 0;

// Hitung total pengeluaran
$result_pengeluaran = $dbconnect->query("SELECT SUM(jumlah) AS total_pengeluaran FROM pengeluaran $where");
$total_pengeluaran = $result_pengeluaran->fetch_assoc()['total_pengeluaran'] ?? 0;

// Hitung total pendapatan
$total_pendapatan = $total_transaksi - $total_pengeluaran;

// Tanggal cetak
$tanggal_cetak = date('d-m-Y H:i:s');
?>


<!DOCTYPE html>
<html>
<head>
    <title>Hasil Transaksi</title>
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

        .btn-custom1 {
        background-color: #5a9bd5;
        border-color: #5a9bd5;
        color: #fff; /* agar teksnya putih dan kontras */
        }

        @media print {
            body * {
                visibility: hidden;
            }

            table, table *, .panel, .panel * {
                visibility: visible;
            }

            table, .panel {
                position: relative;
                top: 0;
                left: 0;
            }

            .header, .form-group {
                display: none;
            }
        }


    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><strong>Hasil Transaksi</strong></h1>
        </div>

        <div class="form-group mt-3">
            <form method="GET" class="form-inline">
                
                <label for="tanggal_awal">Dari: </label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" value="<?= $tanggal_awal ?>" class="form-control mx-2" required>

                <label for="tanggal_akhir">Sampai: </label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="<?= $tanggal_akhir ?>" class="form-control mx-2" required>

                <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                <a href="hasil_transaksi.php" class="btn btn-default btn-sm">Reset</a>
                <button onclick="window.print()" class="btn btn-custom1" style="margin-left: 10px;">Cetak Hasil Transaksi</button>
            </form>
        </div>
    
        <!-- Struktur kondisional dlm PHP utk memeriksa apakah hasil query berisi data/tidak.
        Misalnya kamu punya 5 data transaksi, maka num_rows = 5.
        Jika hasil query memiliki lebih dari 0 baris (ada data) -->
            <?php if ($query->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Kembali</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>ID Order</th>

                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $query->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_transaksi'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= number_format($row['total'], 0, ',', '.') ?></td>
                            <td><?= number_format($row['bayar'], 0, ',', '.') ?></td>
                            <td><?= number_format($row['kembali'], 0, ',', '.') ?></td>
                            <td><?= $row['payment_method'] ?></td>
                            <td><?= $row['payment_status'] ?></td>
                            <td><?= $row['order_id'] ?></td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Ringkasan</strong></div>
                <div class="panel-body">
                    
                    <p><strong>Tanggal Cetak:</strong> <?= date('l, d F Y H:i:s') ?> </p>
                    <p><strong>Total Transaksi:</strong> Rp <?= number_format($total_transaksi, 0, ',', '.') ?></p>
                    <p><strong>Total Pengeluaran:</strong> Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></p>
                    <p><strong>Total Pendapatan (Keuntungan Bersih):</strong> <strong style="color: green;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></strong></p>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Belum ada transaksi.</div>
        <?php endif; ?>



        
</body>
</html>
