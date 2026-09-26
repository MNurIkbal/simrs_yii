<?php

/**
 * @Author: rizal
 * @Date: 2018-04-03 13:47:49
 * @Last Modified by: 
 * @Last Modified time: 
 */
?>
<table border="0" style="width: 100%">
    <tr>
        <?php foreach ($header as $key=>$each) : ?>
        <td> <?= $key; ?> : <?= $each; ?></td>
        <?php endforeach; ?>
    </tr>
</table>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Tanggal pembayaran'); ?></th>
            <th><?= Yii::t('app', 'No pendaftaran'); ?></th>
            <th><?= Yii::t('app', 'Nama pasien'); ?></th>
            <th><?= Yii::t('app', 'Total pembayaran'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $total = 0;
        ?>
        <?php foreach ($detail as $value): ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= !empty($value['tanggal_pembayaran']) ? $value['tanggal_pembayaran'] : '' ?></td>
                <td><?= !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= !empty($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= !empty($value['total_pembayaran']) ? $value['total_pembayaran'] : '' ?></td>
            </tr>
            <?php 
            $no++; 
            $total += $value['total_pembayaran'];
            ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>Total Closing</td>
            <td><?= $total; ?></td>
        </tr>
    </tfoot>
</table>

<br>
<br>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th><?= Yii::t('app', 'Uang pecahan'); ?></th>
            <th><?= Yii::t('app', 'Qty'); ?></th>
            <th><?= Yii::t('app', 'Jumlah'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $totalPecahan = 0;
        ?>
        <?php foreach ($detailPecahan as $value): ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= !empty($value['uang_pecahan']) ? $value['uang_pecahan'] : '' ?></td>
                <td><?= !empty($value['qty']) ? $value['qty'] : '' ?></td>
                <td><?= !empty($value['jumlah']) ? $value['jumlah'] : '' ?></td>
            </tr>
            <?php 
            $no++; 
            $totalPecahan += $value['jumlah'];
            ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td><?= Yii::t('app', 'Total Pecahan'); ?></td>
            <td><?= $totalPecahan; ?></td>
        </tr>
    </tfoot>
</table>
