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

<h4 align="right">ORIGINAL</h4>
<h3 align="center">LAPORAN HASIL LABORATORIUM </h3>

<h6 align="right" style="margin-bottom: 0px">Dokter Patologi Klinik : <?= isset($detail[0]['dokter_penunjang']) ? $detail[0]['dokter_penunjang'] : '-' ?></h6>
<hr>
<table class="table borderless" style="font-size: 12">
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'No Lab')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['no_masukpenunjang']) ? $detail[0]['no_masukpenunjang'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Dokter Pengirim')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['dokter_pengirim']) ? $detail[0]['dokter_pengirim'] : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'No rekam medik')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['no_rekam_medik']) ? $detail[0]['no_rekam_medik'] : '-' ?></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Nama')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['nama_pasien']) ? $detail[0]['nama_pasien'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Tgl. Transaksi')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['tgl_transaksi']) ? date('d-m-Y H:i', strtotime($detail[0]['tgl_transaksi'])) : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Tgl. Lahir / Umur')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['dateofbirth']) ? date('d-m-Y', strtotime($detail[0]['dateofbirth'])) : '-' ?> / <?= isset($detail[0]['umur']) ? $detail[0]['umur'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Hasil Selesai')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['tgl_hasil']) ? date('d-m-Y H:i', strtotime($detail[0]['tgl_hasil'])) : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Jenis Kelamin')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['jeniskelamin_nama']) ? $detail[0]['jeniskelamin_nama'] : '-' ?></td>
        <td> </td>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Cetak Hasil')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['tgl_cetak']) ? date('d-m-Y H:i', strtotime($detail[0]['tgl_cetak'])) : '-' ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Alamat')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['alamat_pasien']) ? $detail[0]['alamat_pasien'] : '-' ?></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><?= Yii::t('app', 'Lokasi')  ?></td>
        <td>:</td>
        <td><?= isset($detail[0]['lokasi_nama']) ? $detail[0]['lokasi_nama'] : '-' ?></td>
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
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px">&nbsp;</th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'HASIL') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'NILAI RUJUKAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'SATUAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'METODE') ?></th>
        </tr>
    </thead>
    <tbody>
   
        <?php
            foreach ($detail as $value){
        ?>
        <tr>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['test_nama_lis']; ?></td>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= !empty($value['test_flag_sign']) ? $value['test_flag_sign'] : ''; ?></td>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['hasil']; ?></td>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['nilai_rujukan']; ?></td>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['satuan']; ?></td>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['test_method']; ?></td>
        </tr>
        <?php
            }
        ?>
        <tr>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
            <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        </tr>
    </tbody>
</table>

<h6 align="right">Diotorisasi Oleh, </h6>
<br><br><br>
<h6 align="right">(<?= isset($detail[0]['authorization_user']) ? $detail[0]['authorization_user'] : '-' ?>)</h6>

