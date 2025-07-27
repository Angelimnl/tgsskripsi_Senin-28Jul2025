<?php
// ================= CONFIGURASI =================
$merchantId = 'G272811625';
$clientKey = 'SB-Mid-client-PJdOmGBmYLJqK8Xl';
$serverKey = 'SB-Mid-server-FR7_LdvokRoYnA7MEm5gjzZb';

$isProduction = false; // Sandbox mode
// ==============================================

// Generate random order ID
$orderId = 'ORDER-' . time() . '-' . rand(1000, 9999);

// Data transaksi
$transaction = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => 100000, // Total harga
    ],
    'item_details' => [
        [
            'id' => 'ITEM1',
            'price' => 50000,
            'quantity' => 2,
            'name' => 'Nama Produk'
        ]
    ],
    'customer_details' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '08123456789',
    ],
    'credit_card' => [
        'secure' => true // Enable 3DS
    ]
];

// Fungsi request Snap Token
function getSnapToken($serverKey, $transaction, $isProduction = false) {
    $url = $isProduction ? 
        'https://app.midtrans.com/snap/v1/transactions' : 
        'https://app.sandbox.midtrans.com/snap/v1/transactions';

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 201) {
        return ['error' => true, 'message' => $response];
    }

    return json_decode($response, true);
}

// Dapatkan Snap Token
$snapResponse = getSnapToken($serverKey, $transaction, $isProduction);

if (isset($snapResponse['error'])) {
    die('Error: ' . $snapResponse['message']);
}

$snapToken = $snapResponse['token'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Midtrans</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="<?= $clientKey ?>"></script>
</head>
<body>
    <button id="pay-button">Bayar Sekarang</button>

    <script>
        document.getElementById('pay-button').onclick = function(){
            snap.pay('<?= $snapToken ?>', {
                onSuccess: function(result){ 
                    alert("Pembayaran sukses!"); 
                    console.log(result);
                },
                onPending: function(result){ 
                    alert("Menunggu pembayaran..."); 
                    console.log(result);
                },
                onError: function(result){ 
                    alert("Pembayaran gagal!"); 
                    console.log(result);
                }
            });
        };
    </script>
</body>
</html>