<?php
include 'config1.php';
include 'authcheck.php';

// Jumlah Barang
$jumlahBarang = $dbconnect->query("SELECT SUM(jumlah) as jumlah FROM barang")->fetch_assoc()['jumlah'];

// Total Pendapatan
// Total Pendapatan Mingguan (7 hari terakhir)
$totalPendapatan = $dbconnect->query("SELECT SUM(total) as total FROM transaksi WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
$totalPendapatan = $totalPendapatan ?? 0; // untuk mencegah null

// Total Pengeluaran
$totalPengeluaran = $dbconnect->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE tanggal_keluar >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
$totalPengeluaran = $totalPengeluaran ?? 0;

// Kalender → misalnya tampilkan tanggal hari ini
$tanggalHariIni = date('d-m-Y');

// Tanggal rentang 7 hari terakhir
$tanggalAkhir = date('d-m-Y');
$tanggalAwal = date('d-m-Y', strtotime('-6 days')); // total 7 hari termasuk hari ini
?>


<!DOCTYPE html>
<html>
<head>
    <title>List Barang</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 20px;
            background: linear-gradient(to right,  #5a9bd5, #d895da, #5a9bd5);
            color: white;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .container {
            margin-top: 120px;
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

        .alert {
        z-index: 999;
        position: relative;
        }

    </style>
</head>
<body onload="getProdukTerlaris(); getPenjualanHarian();">
                <div class="header">
                    <h1 style="margin-top: 16px;"><strong>Dashboard</strong></h1>
                </div>

    <div class="container">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="card p-2 shadow">
                            <div class="d-flex align-items-center px-2">
                                <i class="fa-solid fa-boxes-stacked fa-3x py-auto" aria-hidden="true"></i>
                                <div class="card-body text-end">
                                    <h5 class="card-title"><?= $jumlahBarang ?></h5>
                                    <small class="text-muted">Items</small>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-start fw-bold">Jumlah Semua Barang</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="card p-2 shadow">
                            <div class="d-flex align-items-center px-2">
                                <i class="fa-regular fa-money-bill-1 fa-3x py-auto" aria-hidden="true"></i>
                                <div class="card-body text-end">
                                    <h5 class="card-title">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h5>
                                    <small class="text-muted"><?= $tanggalAwal ?> s.d. <?= $tanggalAkhir ?></small>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-start fw-bold">Pendapatan Perminggu</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <div class="card p-2 shadow">
                            <div class="d-flex align-items-center px-2">
                                <i class="fa-solid fa-money-bill-1 fa-3x py-auto" aria-hidden="true"></i>
                                <div class="card-body text-end">
                                    <h5 class="card-title">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h5>
                                    <small class="text-muted"><?= $tanggalAwal ?> s.d. <?= $tanggalAkhir ?></small>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-start fw-bold">Pengeluaran Perminggu</small>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-12 col-lg-6">
                        <div class="d-block rounded shadow bg-white p-3">
                            <canvas id="myChartOne"></canvas>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-block rounded shadow bg-white p-3">
                            <canvas id="myChartTwo"></canvas>
                        </div>
                    </div>
                </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function getProdukTerlaris() {
    fetch('file_backend.php?functionName=getProdukTerlaris')
        .then(response => response.json())
        .then(result => {
            console.log("DATA:", result);

            if (!Array.isArray(result) || result.length === 0) {
                alert("Data kosong");
                return;
            }

            const labels = result.map(item => item.nama);
            const dataQty = result.map(item => parseInt(item.total_qty));

            const ctx = document.getElementById('myChartOne').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Produk Terlaris',
                        data: dataQty,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error("Fetch error:", error);
        });
}

function getPenjualanHarian() {
    fetch('file_backend.php?functionName=getPenjualanHarian')
        .then(response => response.json())
        .then(result => {
            console.log("Penjualan Harian:", result);

            if (!Array.isArray(result) || result.length === 0) {
                alert("Data penjualan kosong");
                return;
            }

            const labels = result.map(item => item.label);
            const data = result.map(item => item.total_penjualan);

            const ctx = document.getElementById('myChartTwo').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pendapatan/Hari (Rp)',
                        data: data,
                        borderColor: 'rgba(218, 149, 218, 1)',
                        backgroundColor: 'rgba(218, 149, 218, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error("Fetch error (penjualan harian):", error);
        });
}
</script>

</body>
</html>
