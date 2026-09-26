<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
    use Picqer\Barcode\BarcodeGeneratorPNG;
    
    $barcodeGen = new BarcodeGeneratorPNG;
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
?>
<div>&nbsp;</div>
<?php $displayName = $data['nama_pasien']; ?>
<?php $name = $data['nama_pasien'];
    $displayName = DocoHelpers::cutSentence($name);
    
    $dpjp = $data['nama_pegawai'];
    $displayDpjp = DocoHelpers::cutSentence($dpjp, 50);

    $penjamin = $data['penjamin_nama'];
    $displayPenjamin = DocoHelpers::cutSentence($penjamin, 50);
    
    $kelas = $data['kelaspelayanan_nama'];
    $displayKelas = DocoHelpers::cutSentence($kelas);
    
    if ($data['jeniskelamin'] == $jenis_kelamin[0]['lookup_name']) {
        $data['jeniskelamin'] = $jenis_kelamin[0]['lookup_id'];
    }

    if ($data['jeniskelamin'] == $jenis_kelamin[1]['lookup_name']) {
        $data['jeniskelamin'] = $jenis_kelamin[1]['lookup_id'];
    }

    $lookJk = ($data['jeniskelamin'] == $jenis_kelamin[0]['lookup_id']) ? $jenis_kelamin[0]['lookup_kode'] : $jenis_kelamin[1]['lookup_kode'];
?>
<?php if (($i % 3) == 0): ?>
    <div style="background-color: white">
        <div style="float:left;width:33%;background-color: white; margin-left: 47%; margin-top: -0.6%">
            <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                        <td><span style="font-size:10px">: <?= $data['no_rekam_medik'] ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:10px">Tgl.Lhr</span></td>
                        <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif (($i % 3) == 1): ?>
    <div style="background-color: white">
        <div style="float:center;width:33%;background-color: white; margin-left: 67%; margin-top: -6%">
            <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                        <td><span style="font-size:10px">: <?= $data['no_rekam_medik'] ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-size:9px"><?= $lookJk ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:10px">Tgl.Lhr</span></td>
                        <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div style="background-color: white; width: 100%">
        <div style="width:40%;background-color: white; margin-left: 87%; margin-top: -6.2%; margin-right: -100px">
            <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                        <td><span style="font-size:10px">: <?= $data['no_rekam_medik'] ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:10px">Tgl.Lhr</span></td>
                        <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
<?php endif ?>
<?php $i++; endforeach; ?>