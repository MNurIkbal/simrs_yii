
<style media="print">
    .text p { 
        border: 1px solid black;
        height: 50pt;
        overflow: hidden;
        float: left;
        padding: 5px;
    }
</style>

<h3 align="center">Hasil Pemeriksaan Radiologi</h3>
<?php
dump($query); die();
    foreach ($query as $key => $data) {
        if ($key == 'data_hasil') {
?>
            <h5 align="center"><?= $data['nama_pemeriksaan'] ?> - <?= $data['nama_kelompok'] ?></h5>
<?php
        }
    }
?>

<?php
    foreach ($query as $key => $data) {
        if ($key == 'data_hasil') {
?>
            <table width="100%" align="center" cellpadding="5">
                <tr>
                    <td style="font-weight:bold;font-size:13px;"><?= Yii::t('app', 'No hasil')  ?></td>
                    <td><?= !empty($data['no_hasilrad']) ? $data['no_hasilrad'] : '-' ?></td>
                    <td style="font-weight:bold;font-size:13px;"><?= Yii::t('app', 'Tanggal hasil radiologi')  ?></td>
                    <td><?= !empty($data['tanggal_pemeriksaan']) ? date('d M Y', strtotime($data['tanggal_pemeriksaan'])) : '-' ?></td>
                    <td style="font-weight:bold;font-size:13px;"><?= Yii::t('app', 'Penanggung jawab radiologi')  ?></td>
                    <td><?= !empty($data['nama_pegawai']) ? $data['nama_pegawai'] : '-' ?></td>
                </tr>
            </table>
<?php
        }
    }
?>

<br>
<fieldset style="border:1px solid black; padding:5px;">
    <legend style="font-weight:bold;"><?= Yii::t('app', 'Data Pasien') ?></legend>
    <table class="table borderless">
        <tr>
            <td style="font-weight:bold;"><?= Yii::t('app', 'No pendaftaran')  ?></td>
            <td>:</td>
            <td><?= isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'No Radiologi')  ?></td>
            <td></td>
            <td><?= isset($header['no_masukpenunjang']) ? $header['no_masukpenunjang'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Jenis kelamin')  ?></td>
            <td>:</td>
            <td><?= isset($header['j_kelamin']) ? $header['j_kelamin'] : '-' ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold;"><?= Yii::t('app', 'No rekam medik')  ?></td>
            <td>:</td>
            <td><?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Nama pasien')  ?></td>
            <td>:</td>
            <td><?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Umur')  ?></td>
            <td>:</td>
            <td><?= isset($header['umur']) ? $header['umur'] : '-' ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Dokter')  ?></td>
            <td>:</td>
            <td><?= isset($header['dokter_penunjang']) ? $header['dokter_penunjang'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Ruangan asal')  ?></td>
            <td>:</td>
            <td><?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-' ?></td>
            <td style="font-weight:bold;"><?= Yii::t('app', 'Tanggal lahir')  ?></td>
            <td>:</td>
            <td><?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?></td>
        </tr>
    </table>
</fieldset>

<br>

<?php
    foreach ($query as $key => $data) {
        if ($key == 'data_hasil') {
?>
            <b><?= Yii::t('app', 'Hasil Expertise') ?></b>
            <br>
            <div class="text"><?= $data['hasil_expertise'] ?></div>
<?php
        }
    }
?>
<?php
    foreach ($query as $key => $data) {
        if ($key == 'data_hasil') {
?>
            <b><?= Yii::t('app', 'Kesan') ?></b>
            <br>
            <div class="text"><?= $data['kesan'] ?></div>
<?php
        }
    }
?>
<?php
    foreach ($query as $key => $data) {
        if ($key == 'data_hasil') {
?>
            <b><?= Yii::t('app', 'Kesimpulan') ?></b>
            <div class="text"><?= $data['kesimpulan'] ?></div>
<?php
        }
    }
?>