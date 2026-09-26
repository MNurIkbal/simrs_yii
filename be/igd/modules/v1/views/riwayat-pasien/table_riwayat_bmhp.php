<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-06 14:50:53
 */

use Doco\components\DocoHelpers;
?>

<table id="tabel-bmhp" border="1" cellpadding="0" cellspacing="0" style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal Tindakan') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan / Paket') ?></th>
            <th><?= Yii::t('app', 'Obat / Alkes') ?></th>
            <th><?= Yii::t('app', 'Perawat 1') ?></th>
            <th><?= Yii::t('app', 'Perawat 2') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Ditagihkan') ?></th>
            <th><?= Yii::t('app', 'Implementasi') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $no = 1;
        foreach ($data_bmhp as $value) {
    ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_tindakan'])), false, true) ?></td>
            <td><?= $value['tindakan_obat'] ?></td>
            <td><?= $value['tindakan'] ?></td>
            <td><?= $value['perawat_1'] ?></td>
            <td><?= $value['perawat_2'] ?></td>
            <td><?= $value['qty'] ?></td>
            <td><?= $value['ditagihkan'] > 0 ? 'Ya' : 'Tidak' ?></td>
            <td><?= $value['stat_implementasi'] ?></td>
        </tr>
    <?php 
            $no++;
        }
    ?>
    </tbody>
</table>