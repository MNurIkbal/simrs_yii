<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
    use Picqer\Barcode\BarcodeGeneratorPNG;
    
    $barcodeGen = new BarcodeGeneratorPNG;

    $i = 0;
    $jenis = NULL;

    foreach ($data as $key => $value):
    $data = $value;
    
    $barcode = '<img style="height: 15px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($data['no_rekam_medik'], $barcodeGen::TYPE_CODE_128, 4)) . '">';

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
        font-family: Arial, Helvetica, sans-serif;
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

    /* .label-size-half {
        width: 50%;
        height: 16.5%;
    } */
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
    $displayDpjp = DocoHelpers::cutSentence($dpjp, 20);

    $penjamin = $data['penjamin_nama'];
    $displayPenjamin = DocoHelpers::cutSentence($penjamin, 20);
    
    $kelas = $data['kelaspelayanan_nama'];
    $displayKelas = DocoHelpers::cutSentence($kelas);
    $lookJk = '';
    
    foreach($jenis_kelamin as $k => $v) {
        if($data['jeniskelamin'] == $v['lookup_id']) {
            $lookJk = $v['lookup_kode'];
            break;
        }
    }
    
    if($i == 0) {
        $pos = '-3%';
    } else {
        $pos = '10%';
    }
?>
<div style="background-color: white;" class="div-border-red">
    <div style="background-color: white; margin-left: 25%; margin-top: <?= $pos ?>" class="div-border-green label-size-half">
        <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
            <tbody>
                <tr>
                        <td colspan="3"><span class="fz-10 fw-b"><?= $displayName ?></span></td>
                </tr>
                <tr>
                        <td colspan="2"><span class="fz-9 fw-b">No.Registrasi</span></td>
                        <td class="fz-9 fw-b">: <?= $data['no_pendaftaran'] ?></td>
                </tr>
                <tr>
                        <td colspan="2"><span class="fz-9 fw-b">Penjamin</span></td>
                        <td class="fz-9 fw-b">: <?= $displayPenjamin ?></td>
                </tr>
                <tr>
                        <td colspan="3"><?= $barcode ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $i++; endforeach; ?>