<?php
include 'config1.php';
include 'authcheck.php';

// AMBIL DATA TRANSAKSI + TRANSAKSI DETAIL
$query = $dbconnect->query("
    SELECT transaksi.* FROM transaksi ORDER BY transaksi.created_at
");

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
            margin-top: 110px;
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

            table, table * {
                visibility: visible;
            }

            table {
                position: absolute;
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

        <div class="form-group">
            <button onclick="window.print()" class="btn btn-custom1">Cetak Hasil Transaksi</button>
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
                        <th>Metode Pembayaran</th>
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
                            <td><?= $row['order_id'] ?></td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">Belum ada transaksi.</div>
        <?php endif; ?>



        
</body>
</html>
