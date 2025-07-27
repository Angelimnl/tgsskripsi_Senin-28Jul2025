<?php
include 'config1.php';
include 'authcheck.php';

$functionName = $_GET['functionName'] ?? ''; // jangan pakai htmlspecialchars di nama fungsi

switch ($functionName) {
    case 'getProdukTerlaris':
        getProdukTerlaris();
        break;
    case 'getPenjualanHarian':
        getPenjualanHarian();
        break;
    default:
        echo json_encode(['error' => 'Function not found']);
        break;
}


function getProdukTerlaris() {
    global $dbconnect;

    if (!$dbconnect) {
        echo json_encode(['error' => 'Koneksi DB gagal']);
        return;
    }

    $data = [];

    $query = mysqli_query($dbconnect, "
        SELECT nama, SUM(qty) as total_qty 
        FROM transaksi_detail
        GROUP BY nama
        ORDER BY total_qty DESC
        LIMIT 6
    ");

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode($data);
}

function getPenjualanHarian() {
    global $dbconnect;

    header('Content-Type: application/json');

    // Aktifkan format Bahasa Indonesia untuk nama hari
    mysqli_query($dbconnect, "SET lc_time_names = 'id_ID'");

    $result_data = [];

    // Buat rentang tanggal dari 6 hari lalu hingga hari ini
    for ($i = 6; $i >= 0; $i--) {
        $tanggal = date('Y-m-d', strtotime("-$i days"));
        $hariNama = strftime('%A', strtotime($tanggal)); // nama hari
        $label = strftime('%A, %e %B', strtotime($tanggal)); // contoh: Senin, 15 Juli
        $tanggalList[$tanggal] = $label;
    }

    // Ambil data penjualan sesuai range tanggal
    $query = mysqli_query($dbconnect, "
        SELECT 
            DATE(created_at) as tanggal,
            SUM(total) as total_penjualan
        FROM transaksi
        WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
        GROUP BY DATE(created_at)
    ");

    // Map hasil query ke array [tanggal => total]
    $penjualanMap = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $penjualanMap[$row['tanggal']] = (int)$row['total_penjualan'];
    }

    // Gabungkan semua tanggal dengan data penjualan (atau 0)
    foreach ($tanggalList as $tanggal => $label) {
        $result_data[] = [
            'label' => $label,
            'total_penjualan' => $penjualanMap[$tanggal] ?? 0
        ];
    }

    echo json_encode($result_data);
}

