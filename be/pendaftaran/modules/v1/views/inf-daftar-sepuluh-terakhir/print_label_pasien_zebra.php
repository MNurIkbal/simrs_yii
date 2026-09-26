<?php

use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Picqer\Barcode\BarcodeGeneratorPNG;

foreach ($data as $key => $value) {
    $barcodeGen = new BarcodeGeneratorPNG;
    $barcode = '<img style="height: 20px; margin-left: 1px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($value['no_rekam_medik'], $barcodeGen::TYPE_CODE_128)) . '">';

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
    <style>
        .font-custom {
            font-family: Monospace;
        }
    </style>

    <!-- <div style="background-color: white; height:5px;"></div> -->
    <?php for ($i = 0; $i < $jumlah_data; $i++) { ?>
        <?php $displayName = $value['nama_pasien']; ?>
        <?php $name = $value['nama_pasien'];
        $displayName = DocoHelpers::cutSentence($name, 25);

        $dpjp = $value['nama_pegawai'];
        $displayDpjp = DocoHelpers::cutSentence($dpjp, 50);

        $penjamin = ArrayHelper::getValue($value, 'penjamin_nama', '-');
        $displayPenjamin = DocoHelpers::cutSentence($penjamin, 50);

        $kelas = ArrayHelper::getValue($value, 'kelaspelayanan_nama', '-');
        $displayKelas = DocoHelpers::cutSentence($kelas);

        $id_jeniskelamin = ArrayHelper::map($jenis_kelamin,'lookup_id','lookup_kode');
        $name_jeniskelamin = ArrayHelper::map($jenis_kelamin,'lookup_name','lookup_kode');
        if(in_array($value['jeniskelamin'],array_keys($id_jeniskelamin))){
            $lookJk = $id_jeniskelamin[$value['jeniskelamin']];
        }elseif(in_array($value['jeniskelamin'],array_keys($name_jeniskelamin))){
            $lookJk = $name_jeniskelamin[$value['jeniskelamin']];
        }else{
            $lookJk = '-';
        }
        
        $umur = "&nbsp;";

        if (isset($value['namadepan']) && isset($value['nama_pasien'])) {
            $displayName = $value['namadepan'] . ' ' . $value['nama_pasien'];
            $displayName = DocoHelpers::cutSentence($displayName, 25);
        }
        ?>
            <div style="background-color: white;">
                <div style="float:left;width:100%;background-color: white; padding-top: 4%">
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                        <tbody>
                            <tr>
                                <td colspan="2"><span style="font-size:11px;font-weight:bold;" class="font-custom"><?= $displayName ?></span></td>
                            </tr>
                            <tr>
                                <td style="width:50px;"><span style="font-size:11px;font-weight:bold;" class="font-custom">MR. No</span></td>
                                <td><span style="font-size:11px;font-weight:bold;" class="font-custom">: <?= $value['no_rekam_medik'] ?></span></td>
                            </tr>
                            <tr>
                                <td style="width:50px;"><span style="font-size:11px;font-weight:bold;" class="font-custom">REG. No</span></td>
                                <td><span style="font-size:11px;font-weight:bold;" class="font-custom">: <?= $value['no_pendaftaran'] ?></span></td>
                            </tr>
                            <tr>
                                <td style="width:50px;"><span style="font-size:11px;font-weight:bold;" class="font-custom">DOB/SEX</span></td>
                                <td><span style="font-size:11px;font-weight:bold;" class="font-custom">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tanggal_lahir'])), true, false) ?> / <?= $lookJk ?></span></td>
                            </tr>
                            <tr>
                                <td style="width:50px;"><span style="font-size:11px;font-weight:bold;" class="font-custom">ROOM NAME</span></td>
                                <td style="width:60%;"><span style="font-size:11px;font-weight:bold;" class="font-custom">: <?= DocoHelpers::cutSentence($value['ruangan_nama'], 15); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                        <tbody>
                            <tr>
                                <td style="width: 50px; !important; padding-top: 3%;"><?= $barcode ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php } ?>
<?php } ?>