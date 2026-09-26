<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-31 14:49:31
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-31 11:42:17
 */
?>

<style type="text/css">
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
</style>

<?php if (!empty($jenisObatAlkes)): ?>
<?php foreach ($jenisObatAlkes as $key => $value): ?>
<h4><?= 'PEMBERIAN '.$value ?></h4>
<table border="1" cellpadding="0" cellspacing="0" style="width:100%; font-size:10px">
    <thead>
        <tr class="bg-inverse">
            <th class="text-center"><?= Yii::t('app', 'No Resep') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Nama Obat') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Signa') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Jumlah') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Dokter') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Waktu Pembeian Obat') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Pemberi Obat 1') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Pemberi Obat 2') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Efek') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Keterangan') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($dataDetail)): ?>
            <?php foreach ($dataDetail as $index => $content): ?>
                <?php if ($value == 'OBAT LAIN'): ?>
                    <?php if ($key == $content['jenisobat_id'] || $content['jenisobat_id'] == ''): ?>
                    <tr>
                        <td><?= $content['no_res_rekon'] ?></td>
                        <td><?= $content['nama_obat'] ?></td>
                        <td><?= $content['signa_obat'] ?></td>
                        <td><?= $content['jumlah'] ?></td>
                        <td><?= $content['dokter'] ?></td>
                        <td><?= date('d-m-Y h:i:s', strtotime($content['wkt_pemberian'])) ?></td>
                        <td><?= $content['pemberi1'] ?></td>
                        <td><?= $content['pemberi2'] ?></td>
                        <td><?= isset($efek[$content['efek']]) ? $efek[$content['efek']] : '-' ?></td>
                        <td><?= isset($keterangan[$content['keterangan']]) ? $keterangan[$content['keterangan']] : '-' ?></td>
                    </tr>
                    <?php endif ?>
                <?php else: ?>
                    <?php if ($key == $content['jenisobat_id']): ?>
                    <tr>
                        <td><?= $content['no_res_rekon'] ?></td>
                        <td><?= $content['nama_obat'] ?></td>
                        <td><?= $content['signa_obat'] ?></td>
                        <td><?= $content['jumlah'] ?></td>
                        <td><?= $content['dokter'] ?></td>
                        <td><?= date('d-m-Y h:i:s', strtotime($content['wkt_pemberian'])) ?></td>
                        <td><?= $content['pemberi1'] ?></td>
                        <td><?= $content['pemberi2'] ?></td>
                        <td><?= isset($efek[$content['efek']]) ? $efek[$content['efek']] : '-' ?></td>
                        <td><?= isset($keterangan[$content['keterangan']]) ? $keterangan[$content['keterangan']] : '-' ?></td>
                    </tr>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>
<?php endforeach ?>
<?php endif ?>