<?php

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Picqer\Barcode\BarcodeGeneratorPNG;

foreach ($data as $key => $value) {

    $barcodeGen = new BarcodeGeneratorPNG;
    $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($value['no_rekam_medik'], $barcodeGen::TYPE_CODE_128)) . '">';

    if (array_key_exists('namadepan', $value)) {
        $nama_depan = $value['namadepan'];
    }

    if (array_key_exists('nama_depan', $value)) {
        $nama_depan = $value['nama_depan'];
    }

    if ($ktp == null) {
        $ktp = '-';
    }
    ?>

    <div style="background-color: white; height:5px;"></div>
    <?php for ($i = 0; $i < $jumlah_data; $i++) { ?>
        <?php $displayName = $value['nama_pasien']; ?>
        <?php $name = $value['nama_pasien'];
        $displayName = DocoHelpers::cutSentence($name);

        $dpjp = $value['nama_pegawai'];
        $displayDpjp = DocoHelpers::cutSentence($dpjp, 50);

        $penjamin = $value['penjamin_nama'];
        $displayPenjamin = DocoHelpers::cutSentence($penjamin, 50);

        $kelas = $value['kelaspelayanan_nama'];
        $displayKelas = DocoHelpers::cutSentence($kelas);

        if ($value['jeniskelamin'] == $jenis_kelamin[0]['lookup_name']) {
            $value['jeniskelamin'] = $jenis_kelamin[0]['lookup_id'];
        }

        if ($value['jeniskelamin'] == $jenis_kelamin[1]['lookup_name']) {
            $value['jeniskelamin'] = $jenis_kelamin[1]['lookup_id'];
        }

        $lookJk = ($value['jeniskelamin'] == $jenis_kelamin[0]['lookup_id']) ? $jenis_kelamin[0]['lookup_kode'] : $jenis_kelamin[1]['lookup_kode'];

        $umur = "&nbsp;";

        ?>
        <?php if ($jenis == 'rajal') : ?>
            <?php if (($i % 3) == 0) : ?>
                <div style="background-color: white">
                    <div style="float:left;width:33%;background-color: white; margin-left: -2%; margin-top: 0.3%">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif (($i % 3) == 1) : ?>
                <div style="background-color: white">
                    <div style="float:center;width:33%;background-color: white; margin-left: 28%; margin-top: -7.5%">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div style="background-color: white; width: 100%">
                    <div style="width:33%;background-color: white; margin-left: 58%; margin-top: -7.5%;">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:-1%">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            <?php endif ?>
        <?php else : ?>
            <?php if (($i % 3) == 0) : ?>
                <div style="background-color: white;margin-top: 10px;">
                    <div style="float:left;width:33%;background-color: white; margin-left: -2%; margin-top: 0.8%">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif (($i % 3) == 1) : ?>
                <div style="background-color: white">
                    <div style="float:center;width:33%;background-color: white; margin-left: 28%; margin-top: -7.6%">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else : ?>
                <div style="background-color: white; width: 100%">
                    <div style="width:33%;background-color: white; margin-left: 58%; margin-top: -7.6%;">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:-3.7%">
                            <tbody>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;font-weight:bold;"><?= $displayName ?></span></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span style="font-size:10px">No.RM</span></td>
                                    <td><span style="font-size:10px">: <?= $value['no_rekam_medik'] ?></span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span style="font-size:10px"><?= $lookJk ?></span></td>
                                </tr>
                                <tr>
                                    <td><span style="font-size:10px">Tgl.Lhr</span></td>
                                    <td><span style="font-size:10px">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            <?php endif ?>
        <?php endif ?>
    <?php } 
}?>