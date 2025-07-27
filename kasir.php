<?php
include 'config1.php';
session_start();
include "authcheckkasir.php";
include 'authcheckrole.php';

allowRoles([1,5]); //1 = Admin, 5 = User

// Konfigurasi Midtrans
$midtrans_server_key = 'Mid-server-pTFOr9o0BiI6gG7x6yPGcvvd';
$midtrans_client_key = 'Mid-client-s9spq4afTtHsHd4N';
$midtrans_Merchant_ID = 'G390034457';
$midtrans_mode = 'sandbox'; // sandbox/production

// Fungsi untuk memanggil API Midtrans
function midtransApiCall($url, $serverKey, $data) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':')
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return array("status_code" => 500, "status_message" => $err);
    }

    return json_decode($response, true);
}


// Proses pembayaran QRIS jika ada request
if (isset($_POST['pay_with_qris'])) {
    $qris_total = $_POST['total'];
    $order_id = "QRIS-" . time() . "-" . rand(1000, 9999);
    
    // Data untuk API Midtrans
    // Siapkan item_details
    $item_details = [];
    foreach ($_SESSION['cart'] as $item) {
        $item_details[] = array(
            'id' => $item['id'],
            'price' => (int) $item['harga'],
            'quantity' => (int) $item['qty'],
            'name' => substr($item['nama'], 0, 50) // maksimal 50 karakter
        );
    }

    // Data untuk API Midtrans
    $transaction_data = array(
        'payment_type' => 'qris',
        'transaction_details' => array(
            'order_id' => $order_id,
            'gross_amount' => (int) $qris_total
        ),
        'item_details' => $item_details,
        'qris' => array(
            'acquirer' => 'gopay'
        )
    );

    
    // Panggil API Midtrans
    $api_url = $midtrans_mode == 'sandbox' ? 
        'https://api.sandbox.midtrans.com/v2/charge' : 
        'https://api.midtrans.com/v2/charge';
    
    $response = midtransApiCall($api_url, $midtrans_server_key, $transaction_data);
    
    if (isset($response['status_code']) && $response['status_code'] == '201') {
            
            $bayar = $qris_total;
            $kembali =  0;
        
        // Simpan transaksi ke database
        mysqli_query($dbconnect, "INSERT INTO transaksi (total, bayar, kembali, order_id, payment_method, payment_status) VALUES ('$qris_total', '$bayar', '$kembali', '$order_id', 'qris', 'pending')");
        $id_transaksi = mysqli_insert_id($dbconnect);
        
        foreach ($_SESSION['cart'] as $key => $value) {
            $id_barang = $value['id'];
            $nama = $value['nama']; // ambil nama barang dari session
            $harga = $value['harga'];
            $qty = $value['qty'];
            $total_item = $harga * $qty;

            mysqli_query($dbconnect, "INSERT INTO transaksi_detail (id_transaksi,id_barang, nama, harga,qty,total) VALUES ('$id_transaksi','$id_barang', '$nama', '$harga','$qty','$total_item')");
            mysqli_query($dbconnect, "UPDATE barang SET jumlah = jumlah - $qty WHERE id_barang = '$id_barang'");
        }
        
        // Kosongkan keranjang setelah data berhasil disimpan
        $_SESSION['cart'] = array();


        $_SESSION['qris_payment'] = array(
            'order_id' => $order_id,
            'qr_url' => $response['actions'][0]['url'],
            'status_url' => ($midtrans_mode == 'sandbox' ? 'https://api.sandbox.midtrans.com/v2/' : 'https://api.midtrans.com/v2/') . $order_id . '/status',
            'id_transaksi' => $id_transaksi
        );

        echo "<script>window.open('struk.php?id_transaksi=$id_transaksi', '_blank');</script>";
        
        header("Location: kasir.php?qris=1");
        exit();
    } else {
        die("Error processing QRIS payment: " . json_encode($response));
    }
}

// Cek status pembayaran QRIS
if (isset($_GET['check_qris_status']) && isset($_SESSION['qris_payment'])) {
    $order_id = $_SESSION['qris_payment']['order_id'];
    $api_url = $_SESSION['qris_payment']['status_url'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($midtrans_server_key . ':')
    ));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $status_data = json_decode($response, true);
    
    if ($status_data['transaction_status'] == 'settlement') {
        // Update status pembayaran di database
        mysqli_query($dbconnect, "UPDATE transaksi SET payment_status = 'paid' WHERE order_id = '$order_id'");
        
        // Siapkan data untuk struk
        $id_transaksi = $_SESSION['qris_payment']['id_transaksi'];
        $transaksi = mysqli_query($dbconnect, "SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
        $transaksi = mysqli_fetch_assoc($transaksi);
        
        $transaksi_detail = mysqli_query($dbconnect, "SELECT barang.nama, transaksi_detail.* FROM transaksi_detail LEFT JOIN barang ON transaksi_detail.id_barang=barang.id_barang WHERE transaksi_detail.id_transaksi='$id_transaksi'");
    
        // Kembalikan data struk dalam format JSON
        $struk_html = generateStrukHtml($transaksi, $transaksi_detail);
     
        unset($_SESSION['qris_payment']);
        unset($_SESSION['cart']);

        echo json_encode(array(
            'status' => 'success',
            'struk_html' => $struk_html
        ));
    } else {
        echo json_encode(array('status' => $status_data['transaction_status']));
    }
    exit();
}

// Fungsi untuk generate HTML struk
function generateStrukHtml($transaksi, $transaksi_detail) {
    ob_start();
    ?>
    <div class="struk-container" style="width: 300px; margin: 0 auto; padding: 15px; border: 1px solid #ddd; font-family: Arial, sans-serif;">
        <h3 style="text-align: center; margin-bottom: 10px;">Struk Pembayaran</h3>
        <p style="text-align: center; margin-bottom: 5px;"><?=date('d/m/Y H:i:s', strtotime($transaksi['created_at']))?></p>
        <?php if (!empty($transaksi['order_id'])): ?>
            <p style="text-align: center; margin-bottom: 15px;">ID Transaksi: <?=$transaksi['order_id']?></p>
        <?php endif; ?>

        
        <hr style="border-top: 1px dashed #ddd; margin: 10px 0;">
        
        <table style="width: 100%; margin-bottom: 15px;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 3px 0;">Nama Barang</th>
                    <th style="text-align: right; padding: 3px 0;">Harga</th>
                    <th style="text-align: center; padding: 3px 0;">Qty</th>
                    <th style="text-align: right; padding: 3px 0;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($transaksi_detail)): ?>
                <tr>
                    <td style="text-align: left; padding: 3px 0;"><?=$row['nama']?></td>
                    <td style="text-align: right; padding: 3px 0;"><?=number_format($row['harga'])?></td>
                    <td style="text-align: center; padding: 3px 0;"><?=$row['qty']?></td>
                    <td style="text-align: right; padding: 3px 0;"><?=number_format($row['total'])?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <hr style="border-top: 1px dashed #ddd; margin: 10px 0;">
        
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td style="text-align: right; font-weight: bold; padding: 5px 0;">Total:</td>
                <td style="text-align: right; padding: 5px 0; width: 100px;">Rp <?=number_format($transaksi['total'])?></td>
            </tr>
            </tr>
            <tr>
                <td style="text-align: right; font-weight: bold; padding: 5px 0;">Metode:</td>
                <td style="text-align: right; padding: 5px 0;">
                    <?= strtoupper($transaksi['payment_method']) ?>
                </td>
            </tr>
            
                <?php if (strtolower($transaksi['payment_method']) == 'cash'): ?>
            <tr>
                    <td style="text-align: right; font-weight: bold; padding: 5px 0;">Bayar:</td>
                    <td style="text-align: right; padding: 5px 0;">Rp <?= number_format($transaksi['bayar']) ?></td>
            </tr>
                <?php endif; ?>

            <tr>
                <td style="text-align: right; font-weight: bold; padding: 5px 0;">Kembali:</td>
                <td style="text-align: right; padding: 5px 0;"> Rp <?= number_format($transaksi['kembali']) ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; font-weight: bold; padding: 5px 0;">Status:</td>
                <td style="text-align: right; padding: 5px 0; color: green;">Berhasil</td>
            </tr>
        </table>
        
        <hr style="border-top: 1px dashed #ddd; margin: 10px 0;">
        
        <p style="text-align: center; font-style: italic; margin-top: 15px;">Terima kasih telah berbelanja</p>
    </div>
    <?php
    return ob_get_clean();
}

// Proses transaksi tunai biasa
// Proses transaksi tunai biasa
if (isset($_POST['pay_with_cash'])) {
    // Konversi ke float untuk memastikan operasi matematika berjalan
    $cash_total = (float) str_replace(['Rp', '.', ','], '', $_POST['total']);
    $bayar = (float) str_replace(['Rp', '.', ','], '', $_POST['bayar']);
    $kembali = $bayar - $cash_total;
    
    // Simpan ke database dengan format angka
    $order_id = "CASH-" . time() . "-" . rand(1000, 9999); // tambahkan order_id manual
    mysqli_query($dbconnect, "INSERT INTO transaksi (total, bayar, kembali, order_id, payment_method, payment_status) VALUES ('$cash_total','$bayar','$kembali','$order_id','cash','paid')");

    $id_transaksi = mysqli_insert_id($dbconnect);
    
    foreach ($_SESSION['cart'] as $key => $value) {
        $id_barang = $value['id'];
        $nama = $value['nama'];
        $harga = $value['harga'];
        $qty = $value['qty'];
        $total_item = $harga * $qty;
        
        mysqli_query($dbconnect, "INSERT INTO transaksi_detail (id_transaksi, id_barang, nama, harga, qty, total) VALUES ('$id_transaksi','$id_barang','$nama','$harga','$qty','$total_item')");
        mysqli_query($dbconnect, "UPDATE barang SET jumlah = jumlah - $qty WHERE id_barang = '$id_barang'");
    }
    
    $_SESSION['cash_payment'] = [
    'id_transaksi' => $id_transaksi
    ];
    $_SESSION['cart'] = array();
    header("Location: kasir.php?cash_struk=1");
    exit();

}

// Hitung total belanja
$sum = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {           
    foreach ($_SESSION['cart'] as $key => $value) {
        if (!empty($value['harga']) && !empty($value['qty'])) {
            $sum += $value['harga'] * $value['qty'];
        }
    }
}

// Reset keranjang jika diminta
if (isset($_GET['reset_cart'])) {
    $_SESSION['cart'] = array();
    header("Location: kasir.php");
    exit();
}

// Reset pembayaran QRIS jika diminta
if (isset($_GET['reset_qris'])) {
    unset($_SESSION['qris_payment']);
    header("Location: kasir.php");
    exit();
}

$barang = mysqli_query($dbconnect,"SELECT * FROM barang");
if (isset($_GET['cash_struk'])) {
    if (isset($_SESSION['cash_payment']) && !empty($_SESSION['cash_payment']['id_transaksi'])) {
        $id_transaksi = $_SESSION['cash_payment']['id_transaksi'];

        if (empty($id_transaksi)) {
            echo "<script>alert('ID transaksi kosong.');</script>";
        } else {
            echo "<script>console.log('ID transaksi: $id_transaksi');</script>";
        }
        
        $transaksi = mysqli_query($dbconnect, "SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
        $transaksi = mysqli_fetch_assoc($transaksi);

        $transaksi_detail = mysqli_query($dbconnect, "
            SELECT barang.nama, transaksi_detail.* 
            FROM transaksi_detail 
            LEFT JOIN barang ON transaksi_detail.id_barang = barang.id_barang 
            WHERE transaksi_detail.id_transaksi = '$id_transaksi'
        ");

        // ✅ DEBUG 2: Cek apakah query transaksi_detail berhasil dan tidak kosong
        if (!$transaksi_detail) {
            echo "<script>alert('Gagal mengambil transaksi_detail: " . mysqli_error($dbconnect) . "');</script>";
        } elseif (mysqli_num_rows($transaksi_detail) === 0) {
            echo "<script>alert('transaksi_detail kosong! Tidak ada barang yang dibeli.');</script>";
        }

        if ($transaksi && $transaksi_detail) {
            $struk_html = generateStrukHtml($transaksi, $transaksi_detail);
            echo "<script>
                window.onload = function() {
                    document.getElementById('struk-content').innerHTML = " . json_encode($struk_html) . ";
                    $('#struk-modal').modal('show');
                };
            </script>";
        } else {
            echo "<script>alert('Gagal mengambil data transaksi.');</script>";
        }

        // Jangan hapus session sebelum selesai
        unset($_SESSION['cash_payment']);
    } else {
        echo "<script>alert('ID transaksi tidak ditemukan di sesi.');</script>";
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            color: #fff;
        }
        .qr-container {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
            background: white;
        }
        .payment-method {
            margin-bottom: 15px;
        }
        .payment-option {
            display: none;
        }
        .payment-option.active {
            display: block;
        }
        #struk-modal .modal-dialog {
            width: 350px;
        }
        #struk-modal .modal-content {
            border-radius: 0;
        }


        //HIDDEN DESAIN TIDAK PENTING
        @media print {
            body * {
                display: none !important;
            }

            #struk-modal,
            #struk-modal * {
                display: block !important;
            }

            #struk-modal {
                position: absolute !important;
                top: 0;
                left: 0;
                width: 100% !important;
                background: white;
                padding: 20px;
                z-index: 9999;
            }

            #struk-modal .modal-footer,
            #struk-modal .modal-header {
                display: none !important;
            }

            #struk-content {
                margin: 0 auto;
                width: 300px;
            }
        }

    </style>

</head>
<body>
    <div class="header">
        <h1><strong>Kasir</strong></h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <form method="post" action="keranjang_act.php" class="form-inline">
                    <div class="input-group">
                        <select class="form-control" name="id_barang">
                            <option value="">Pilih Barang</option>
                            <?php while ($row = mysqli_fetch_array($barang)) { ?>
                                <option value="<?=$row['id_barang']?>"><?=$row['nama']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="number" name="qty" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-custom1" type="submit">Tambah</button>
                        </span> 
                    </div>
                </form>
                <br>
                
                <form method="post" action="keranjang_update.php">
                    <button type="submit" class="btn btn-success">Perbarui</button>
                    <a href="?reset_cart=1" class="btn btn-danger">Reset Keranjang</a>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                        <?php if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <?php foreach ($_SESSION['cart'] as $key => $value): ?>
                                <tr>
                                    <td><?= $value['nama'] ?></td>
                                    <td><?= number_format($value['harga']) ?></td>
                                    <td class="col-md-2">
                                        <input type="number" name="qty[]" class="form-control" value="<?= $value['qty'] ?>">
                                    </td>
                                    <td align="right"><?= number_format($value['qty'] * $value['harga']) ?></td>
                                    <td>
                                        <a href="keranjang_hapus.php?id=<?= $value['id'] ?>" class="btn btn-danger">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Keranjang kosong</td>
                            </tr>
                        <?php endif; ?>

                    </table>
                </form>
            </div>
            
            <div class="col-md-4">
                <h3>Total Rp. <?=number_format($sum)?></h3>
                
                <?php if (isset($_SESSION['qris_payment'])): ?>
                    <div class="qr-container">
                        <h4>Pembayaran QRIS</h4>
                        <img src="<?=$_SESSION['qris_payment']['qr_url']?>" alt="QR Code" style="max-width: 100%;">
                        <p>Order ID: <?=$_SESSION['qris_payment']['order_id']?></p>
                        <p>Scan QR code di atas menggunakan aplikasi e-wallet atau mobile banking Anda</p>
                        <button id="check-status" class="btn btn-primary">Cek Status Pembayaran</button>
                        <a href="?reset_qris=1" class="btn btn-danger">Batalkan</a>
                    </div>    
                    
                    <script>
                        $(document).ready(function() {
                            // Cek status pembayaran setiap 5 detik
                            var checkInterval = setInterval(checkPaymentStatus, 5000);
                            
                            function checkPaymentStatus() {
                            $.get('kasir.php?check_qris_status=1', function(response) {
                                try {
                                    var data = JSON.parse(response);
                                    if (data.status === 'success' && data.struk_html) {
                                        clearInterval(checkInterval);
                                        $('#struk-content').html(data.struk_html);
                                        $('#struk-modal').modal('show');
                                    } else if (data.status !== 'pending') {
                                        console.log('Status:', data.status);
                                    }
                                } catch (e) {
                                    console.error("Gagal parse JSON:", response);
                                }
                            });
                        }
                            
                            // Juga bisa dicek manual
                            $('#check-status').click(function() {
                                $.get('kasir.php?check_qris_status=1', function(response) {
                                    var data = JSON.parse(response);
                                    if (data.status == 'success') {
                                        clearInterval(checkInterval);
                                        $('#struk-content').html(data.struk_html);
                                        $('#struk-modal').modal('show');
                                    } else {
                                        alert('Status pembayaran: ' + data.status);
                                    }
                                });
                            });
                        });
                    </script>
                <?php else: ?>
                    <div class="payment-method">
                        <label>Metode Pembayaran:</label>
                        <select class="form-control" id="payment-method-select">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    
                    <div id="cash-payment" class="payment-option active">
                        <form action="" method="POST">
                            <input type="hidden" name="total" value="<?=$sum?>">
                            <input type="hidden" id="bayar_asli" name="bayar">
                            <div class="form-group">
                                <label for="bayar">Bayar</label>
                                <input type="text" id="bayar" name="bayar" class="form-control">
                            </div>
                            <button type="submit" name="pay_with_cash" class="btn btn-custom1">Selesai</button>
                        </form>
                    </div>
                    
                    <div id="qris-payment" class="payment-option">
                        <form action="" method="POST">
                            <input type="hidden" name="total" value="<?=$sum?>">
                            <button type="submit" name="pay_with_qris" class="btn btn-custom1">Bayar dengan QRIS</button>
                        </form>
                    </div>
                    
                    <script>
                        $(document).ready(function() {
                            $('#payment-method-select').change(function() {
                                $('.payment-option').removeClass('active');
                                $('#' + $(this).val() + '-payment').addClass('active');
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Format input bayar
        var bayar = document.getElementById('bayar');
        var bayarAsli = document.getElementById('bayar_asli');

        if (bayar) {
            bayar.addEventListener('keyup', function (e) {
                var clean = this.value.replace(/[^,\d]/g, '').replace(',', '');
                bayarAsli.value = clean;
                this.value = formatRupiah(this.value, 'Rp. ');
            });
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? prefix + rupiah : '');
        }
    </script>

                    <!-- Modal untuk menampilkan struk -->
                    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="struk-Modal-Label" id="struk-modal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="struk-Modal-Label">Struk Pembayaran</h4>
                                </div>
                                <div class="modal-body" id="struk-content">
                                    <!-- Konten struk akan dimuat di sini -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-primary" onclick="window.print()">Cetak</button>
                                </div>
                            </div>
                        </div>
                    </div>

</body>
</html>