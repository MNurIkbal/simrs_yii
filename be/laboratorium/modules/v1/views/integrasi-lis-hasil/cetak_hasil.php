<?php
// use Yii;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
    .bold {
        font-weight: bold;
    }
    .header-right {
        padding-right: 15px;
    }
</style>

<h4 align="right">ROCHE</h4>
<h3 align="center">LAPORAN HASIL LABORATORIUM </h3>

<h6 align="right" style="margin-bottom: 0px">Dokter Patologi Klinik : <?=$header['dokter_penunjang']?></h6>
<hr>
<table class="table borderless" style="font-size: 12">
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'No Lab')  ?></td>
        <td>:</td>
        <td><?= isset($header['no_masukpenunjang']) ? $header['no_masukpenunjang'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Dokter Pengirim')  ?></td>
        <td>:</td>
        <td><?= isset($header['dokter_penunjang']) ? $header['dokter_penunjang'] : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'No rekam medik')  ?></td>
        <td>:</td>
        <td><?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Nama')  ?></td>
        <td>:</td>
        <td><?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Tgl. Transaksi')  ?></td>
        <td>:</td>
        <td><?= isset($header['tglmasukpenunjang']) ? date('d-m-Y H:i', strtotime($header['tglmasukpenunjang'])) : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Tgl. Lahir / Umur')  ?></td>
        <td>:</td>
        <td><?= isset($header['tanggal_lahir']) ? date('d-m-Y', strtotime($header['tanggal_lahir'])) : '-' ?> / <?= isset($header['umur']) ? $header['umur'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Hasil Selesai')  ?></td>
        <td>:</td>
        <td><?= isset($header['result_time']) ? date('d-m-Y H:i', strtotime($header['result_time'])) : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Jenis Kelamin')  ?></td>
        <td>:</td>
        <td><?= isset($header['j_kelamin']) ? $header['j_kelamin'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Cetak Hasil')  ?></td>
        <td>:</td>
        <td><?= date('d-m-Y H:i:s') ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Alamat')  ?></td>
        <td>:</td>
        <td><?= isset($header['address']) ? $header['address'] : '-' ?></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Lokasi')  ?></td>
        <td>:</td>
        <td><?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-' ?></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
<br>

<table cellpadding="5" class="tbl-bordered" cellspacing="0" width="100%" style="font-size: 12">
    <thead>
        <tr>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'PEMERIKSAAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'HASIL') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'NILAI RUJUKAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'SATUAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'METODE') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    
        foreach ($data as $datum) :
    ?>
    <tr>
        <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $datum['obv_name'] ?></td>
        
        <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $datum['value']?></td>
        <?php
        $nilaiRujukan = $datum['ref_range_1'] ? : '';
        $nilaiRujukan = $datum['ref_range_2'] ? ($nilaiRujukan ? $nilaiRujukan . ' - ' . $datum['ref_range_2'] : $datum['ref_range_2']) : $nilaiRujukan;
        ?>
        <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $nilaiRujukan ?></td>
        <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $datum['unit_text']; ?></td>
        <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $datum['method'] ? $datum['method'] : '-' ; ?></td>
    </tr>
    <?php
        endforeach;
    ?>
    <tr>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
    </tr>
    </tbody>
</table>



<b><?= Yii::t('app', 'Expertise') ?> :</b>

<h6 align="right">Diotorisasi Oleh, </h6>
<br>
<h6 align="right"><?= @$nama_pegawai ?></h6>