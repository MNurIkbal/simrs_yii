<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-29 10:04:01
 */
?>

<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Kode') ?></th>
            <th><?= Yii::t('app', 'Nama Makanan') ?></th>
            <th><?= Yii::t('app', 'Keterangan') ?></th>
            <th><?= Yii::t('app', 'Status') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($model)): ?>
            <?= $no = 1; ?>
            <?php foreach ($model as $key => $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value->makanandiet_kode ?></td>
                <td><?= $value->makanandiet_nama ?></td>
                <td><?= $value->makanandiet_keterangan ?></td>
                <td><?= ($value->is_active) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
            </tr>
            <?= $no++; ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>