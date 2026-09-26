<?php

use yii\helpers\Html;

$nama = isset($bantaran['nama_pasien']) ? $bantaran['nama_pasien'] : '-';
$noTahanan = isset($bantaran['no_tahanan']) ? $bantaran['no_tahanan'] : '-';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wristband</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            width: 50mm;
            height: 100mm;
            margin: 0;
            padding: 0;
        }
        .wristband {
            width: 100%;
            height: 100%;
            padding: 5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5mm;
            width: 100%;
        }
        .qr-section {
            flex-shrink: 0;
            width: 40mm;
            height: 40mm;
        }
        .qr-section img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
        }
        .info-section {
            flex: 1;
            min-width: 0;
            margin-top: 2mm;
        }
        .info-row {
            margin-bottom: 2mm;
            word-wrap: break-word;
        }
        .label {
            font-size: 8pt;
            color: #666;
            font-weight: bold;
            display: block;
            margin-bottom: 0.5mm;
        }
        .value {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            display: block;
        }
        .nama .value {
            font-size: 13pt;
        }
    </style>
</head>
<body>
    <div class="wristband">
        <div class="container">
            <div class="qr-section">
                <?= $qrImage ?>
            </div>
            <div class="info-section">
                <div class="info-row nama">
                    <span class="label">NAMA</span><br />
                    <span class="value"><?= Html::encode($nama) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">NO. TAHANAN</span><br />
                    <span class="value"><?= Html::encode($noTahanan) ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
