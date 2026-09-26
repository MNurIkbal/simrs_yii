<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
    use Picqer\Barcode\BarcodeGeneratorPNG;
    
    $barcodeGen = new BarcodeGeneratorPNG;
    $i = 0;
    $jenis = NULL;

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

<style>
    .fz-9 {
        font-size: 9px;
    }
    .fz-10 {
        font-size: 10px;
    }
    .fz-11 {
        font-size: 11px;
    }
    .fw-b {
        font-weight: bold;
    }

    /* .div-border-black {
        border: solid #000; border-width: 1px 1px; border:solid;
    }

    .div-border-red {
        border: solid #000; border-width: 1px 1px; border:solid red;
    }

    .div-border-green {
        border: solid #000; border-width: 1px 1px; border:solid green; 
    }

    .div-border-blue {
        border: solid #000; border-width: 1px 1px; border:solid blue; 
    } */
    
    .label-size-half {
        width: 50%;
        height: 16.5%;
    }
</style>

<?php $displayName = $nama_depan.$data['nama_pasien']; ?>
<?php $name = $nama_depan.$data['nama_pasien'];
    // $displayName = DocoHelpers::cutSentence($name);
    $length = 25;
    if (strlen($name) > $length) {
        $displayName = substr($name, 0, $length);
    } else {
        $displayName = $name;
    }
    
    $dpjp = $data['nama_pegawai'];
    $displayDpjp = DocoHelpers::cutSentence($dpjp, 50);

    $penjamin = $data['penjamin_nama'];
    $displayPenjamin = DocoHelpers::cutSentence($penjamin, 50);
    
    $kelas = $data['kelaspelayanan_nama'];
    $displayKelas = DocoHelpers::cutSentence($kelas);
    $lookJk = '';
    
    foreach($jenis_kelamin as $k => $v) {
        if($data['jeniskelamin'] == $v['lookup_id']) {
            $lookJk = $v['lookup_kode'];
            break;
        }
    }
?>
<?php if (($i % 2) == 0): ?>
    <div style="background-color: white;" class="div-border-red">
        <div style="float:left;background-color: white; margin-left: 2.5%;" class="div-border-green label-size-half">
            <table border="0" cellpadding="1" cellspacing="1.5" style="width:100%;">
                <tbody>
                    <tr>
                        <td colspan="2"><span class="fz-11 fw-b"><?= $displayName ?></span></td>
                        <td><?= $barcode ?></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span class="fz-9">No. RM</span></td>
                        <td style="width:125px;"><span class="fz-9">: <?= $data['no_rekam_medik'] ?></span></td>
                        <td><span class="fz-9"><?= $lookJk ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Tgl. Lahir</span></td>
                        <td><span class="fz-9">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                        <td style="width:150px"><span class="fz-9"><?= $data['umur'] ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Tgl. Daftar</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= ($jenis == 'ranap') ? DocoHelpers::convDateTime($data['tgl_admisi'], true, false) : DocoHelpers::convDateTime($data['tgl_pendaftaran'], true, false) ?> / <?= $data['no_pendaftaran'] ?> / <?= date('H:i:s', strtotime(($jenis == 'ranap') ? $data['tgl_admisi'] : $data['tgl_pendaftaran'])) ?></span></td>
                        <!-- <td></td> -->
                    </tr>
                    <tr>
                        <td><span class="fz-9">Kelas</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayKelas ?>
                        <?php if ($data['instalasi_id'] == DocoConstants::INST_ID_RI || $data['instalasi_id'] == DocoConstants::INST_ID_RD): ?>
                            / <?= (isset($data['kamarruangan_nokamar']) ? $data['kamarruangan_nokamar'] : "").' - '.(isset($data['no_tempattidur']) ? $data['no_tempattidur'] : ""); ?>
                        <?php endif ?>
                        </span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">DPJP</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayDpjp ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Penjamin</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayPenjamin ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="div-border-black" style="margin-top: -6%">
        <div style="background-color: white; margin-left: 51.5%; margin-top: -20%; margin-right: 0px; " class="div-border-blue label-size-half">
            <table border="0" cellpadding="1" cellspacing="1.5" style="width:100%; margin-top: -14.5%">
                <tbody>
                    <tr>
                        <td colspan="2"><span class="fz-11 fw-b"><?= $displayName ?></span></td>
                        <td style="width: 45% !important;"><?= $barcode ?></td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><span class="fz-9">No. RM</span></td>
                        <td><span class="fz-9">: <?= $data['no_rekam_medik'] ?></span></td>
                        <td><span class="fz-9"><?= $lookJk ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Tgl. Lahir</span></td>
                        <td><span class="fz-9">: <?= DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($data['tanggal_lahir'])), true, false) ?></span></td>
                        <td style="width:150px"><span class="fz-9"><?= $data['umur'] ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Tgl. Daftar</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= ($jenis == 'ranap') ? DocoHelpers::convDateTime($data['tgl_admisi'], true, false) : DocoHelpers::convDateTime($data['tgl_pendaftaran'], true, false) ?> / <?= $data['no_pendaftaran'] ?> / <?= date('H:i:s', strtotime(($jenis == 'ranap') ? $data['tgl_admisi'] : $data['tgl_pendaftaran'])) ?></span></td>
                        <!--  -->
                    </tr>
                    <tr>
                    <td><span class="fz-9">Kelas</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayKelas ?>
                        <?php if ($data['instalasi_id'] == DocoConstants::INST_ID_RI || $data['instalasi_id'] == DocoConstants::INST_ID_RD): ?>
                            / <?= (isset($data['kamarruangan_nokamar']) ? $data['kamarruangan_nokamar'] : "").' - '.(isset($data['no_tempattidur']) ? $data['no_tempattidur'] : ""); ?>
                        <?php endif ?>
                        </span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">DPJP</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayDpjp ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="fz-9">Penjamin</span></td>
                        <td colspan="2" style="white-space:nowrap;"><span class="fz-9">: <?= $displayPenjamin ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
<?php endif ?>
<?php $i++; endforeach; ?>