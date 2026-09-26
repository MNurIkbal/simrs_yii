<?php

use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Picqer\Barcode\BarcodeGeneratorPNG;

$barcodeGen = new BarcodeGeneratorPNG;

$ktp = '-';
$i = 0;

foreach ($data as $key => $value):
$data = $value;
$barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($data['no_rekam_medik'], $barcodeGen::TYPE_CODE_128)) . '">';

if (array_key_exists('namadepan', $data)) {
    $nama_depan = $data['namadepan'];
}

if (array_key_exists('nama_depan', $data)) {
    $nama_depan = $data['nama_depan'];
}

if (array_key_exists('additional_pasien', $value) && !empty($value['additional_pasien'])) {
    $additionalPasien = json_decode($value['additional_pasien']);

    if (!empty($additionalPasien)) {
        foreach ($additionalPasien as $key => $value) {
            if (isset($value->jenisidentitas) && $value->jenisidentitas == $self_ktp) {
                $ktp = $value->no_identitas_pasien;
            }
        }
    }
} else {
    if (ArrayHelper::getValue($value, 'jenisidentitas', null) == $self_ktp) {
        $ktp = ArrayHelper::getValue($value, 'no_identitas_pasien', null);
    }
}

?>

<div style="background-color: white; height:5px;"></div>

    <?php $displayName = $data['nama_pasien']; ?>
    <?php $name = $data['nama_pasien'];
    $displayName = DocoHelpers::cutSentence($name, 25);

    $dpjp = $data['nama_pegawai'];
    $displayDpjp = DocoHelpers::cutSentence($dpjp, 50);

    $penjamin = ArrayHelper::getValue($data, 'penjamin_nama', '-');
    $displayPenjamin = DocoHelpers::cutSentence($penjamin, 50);

    $kelas = ArrayHelper::getValue($data, 'kelaspelayanan_nama', '-');
    $displayKelas = DocoHelpers::cutSentence($kelas);

    if ($data['jeniskelamin'] == $jenis_kelamin[0]['lookup_name']) {
        $data['jeniskelamin'] = $jenis_kelamin[0]['lookup_id'];
    }

    if ($data['jeniskelamin'] == $jenis_kelamin[1]['lookup_name']) {
        $data['jeniskelamin'] = $jenis_kelamin[1]['lookup_id'];
    }

    $lookJk = ($data['jeniskelamin'] == $jenis_kelamin[0]['lookup_id']) ? $jenis_kelamin[0]['lookup_kode'] : $jenis_kelamin[1]['lookup_kode'];

    $umur = "&nbsp;";

    if (isset($data['namadepan']) && isset($data['nama_pasien'])) {
        $displayName = $data['namadepan'] . ' ' . $data['nama_pasien'];
        $displayName = DocoHelpers::cutSentence($displayName, 25);
    }
    ?>
    <?php if (($i % 3) == 0) : ?>
        <div style="background-color: white">
            <div style="float:left;width:33%;background-color: white; margin-left: 13%; margin-top: 0.3%">
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <tbody>
                        <tr>
                            <td colspan="2"><span style="font-size:10px;font-weight:bold;"><?= $displayName ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $ktp ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $data['no_rekam_medik'] ?> / <?= $lookJk ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $umur ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (($i % 3) == 1) : ?>
        <div style="background-color: white">
            <div style="float:center;width:33%;background-color: white; margin-left: 43%; margin-top: -8.8%">
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <tbody>
                        <tr>
                            <td colspan="2"><span style="font-size:10px;font-weight:bold;"><?= $displayName ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $ktp ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $data['no_rekam_medik'] ?> / <?= $lookJk ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $umur ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <div style="background-color: white; width: 100%">
            <div style="width:33%;background-color: white; margin-left: 72%; margin-top: -8.9%;">
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <tbody>
                        <tr>
                            <td colspan="2"><span style="font-size:10px;font-weight:bold;"><?= $displayName ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $ktp ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $data['no_rekam_medik'] ?> / <?= $lookJk ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size:9px"><?= $umur ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    <?php endif ?>

<?php $i++; endforeach; ?>
