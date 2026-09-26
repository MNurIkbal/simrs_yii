<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-28 10:45:40
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
        <th><?= Yii::t('app', 'No Retur') ?></th>
        <th><?= Yii::t('app', 'Tanggal Retur') ?></th>
        <th><?= Yii::t('app', 'Ruang') ?></th>
        <th><?= Yii::t('app', 'Status') ?></th>
        <th><?= Yii::t('app', 'User Retur') ?></th>
    </tr>
    <?php if (!empty($data)): ?>
        <?php $no = 1; ?>
        <?php foreach ($data as $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['no_permintaanretur'] ?></td>
                <td><?= date('j F Y h:i:s', strtotime($value['tgl_permintaanretur'])) ?></td>
                <td><?= $value['ruangan_nama'] ?></td>
                <td><?= $value['status'] ?></td>
                <td><?= $value['nama_pegawai'] ?></td>
            </tr>
        <?php $no++; ?>
        <?php endforeach ?>
    <?php endif ?>
</table>