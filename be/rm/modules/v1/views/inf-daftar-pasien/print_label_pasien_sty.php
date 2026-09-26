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
<style>
    body {
        font-family: tahoma !important;
    }

</style>
<div style="background-color: white; height:5px;"></div>
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

    $umur = '-';
    if (array_key_exists('umur', $data)) {
        $umur = $data['umur'];
    }

    if (isset($data['namadepan']) && isset($data['nama_pasien'])) {
        $displayName = $data['namadepan'].' '.$data['nama_pasien'];
        $displayName = DocoHelpers::cutSentence($displayName, 30);
    }
?>
    <div style="background-color: white">
        <div style="float:left;width:33%;background-color: white; margin-left: -3%; margin-top: 0.3%">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:75px;"><span style="font-size:12px;">No RM</span>
                        <td><b><span style="font-size:12px;"><?= $data['no_rekam_medik'] ?></span></b></td>
                    </tr>   
                    <tr>
                        <td colspan="2"><span style="font-size:14px;font-weight:bold;"><?= $displayName ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size:12px;">TGL.LAHIR</span>
                        <td><span style="font-size:12px;"><?= date("d-m-Y", strtotime($data['tanggal_lahir'])) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
    <br>
<?php $i++; endforeach; ?>