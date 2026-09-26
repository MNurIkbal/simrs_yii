<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-28 10:46:11
 */
?>

<style type="text/css">
    .col-print-1 {width:8%;  float:left;}
    .col-print-2 {width:16%; float:left;}
    .col-print-3 {width:25%; float:left;}
    .col-print-4 {width:33%; float:left;}
    .col-print-5 {width:42%; float:left;}
    .col-print-6 {width:50%; float:left;}
    .col-print-7 {width:58%; float:left;}
    .col-print-8 {width:66%; float:left;}
    .col-print-9 {width:75%; float:left;}
    .col-print-10{width:83%; float:left;}
    .col-print-11{width:92%; float:left;}
    .col-print-12{width:100%; float:left;}
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
</style>

<table border="1" cellpadding="0" cellspacing="0" style="width:100%;">
    <tr>
        <th><?= Yii::t('app', 'No') ?></th>
        <th><?= Yii::t('app', 'Nama Obat/Alkes') ?></th>
        <th><?= Yii::t('app', 'Satuan') ?></th>
        <th><?= Yii::t('app', 'Jumlah Retur') ?></th>
        <th><?= Yii::t('app', 'Alasan Retur Perawat') ?></th>
        <th><?= Yii::t('app', 'Jumlah Retur Acc') ?></th>
        <th><?= Yii::t('app', 'Alasan UF') ?></th>
        <th><?= Yii::t('app', 'Harga Satuan') ?></th>
        <th><?= Yii::t('app', 'Total') ?></th>
        <th><?= Yii::t('app', 'Tanggal ACC') ?></th>
    </tr>
    <?php if (!empty($data)): ?>
        <?php $no = 1; ?>
        <?php foreach ($data as $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['obatalkes_nama'] ?></td>
                <td><?= $value['signa_nama'] ?></td>
                <td><?= $value['qty_retur'] ?></td>
                <td><?= $value['alasan'] ?></td>
                <td><?= $value['qty_approve'] ?></td>
                <td><?= $value['alasan_retur'] ?></td>
                <td><?= $value['hargasatuan'] ?></td>
                <td><?= $value['total'] ?></td>
                <td><?= $value['tgl_approve'] ?></td>
            </tr>
        <?php $no++; ?>
        <?php endforeach ?>
    <?php endif ?>
</table>