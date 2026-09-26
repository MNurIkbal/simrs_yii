<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 17:00:47
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 17:02:04
 */

?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'Pemeriksaan Laboratorium') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Kode') ?></th>
            <th><?= Yii::t('app', 'Nama Pemeriksaan') ?></th>
            <th><?= Yii::t('app', 'Kelompok Pemeriksaan') ?></th>
            <th><?= Yii::t('app', 'Jenis Pemeriksaan') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($model)): ?>
            <?= $no = 1; ?>
            <?php foreach ($model as $index => $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= !empty($value->pemeriksaanlab_kode) ? $value->pemeriksaanlab_kode : ''; ?></td>
                <td><?= !empty($value->daftarTindakan->daftartindakan_nama) ? $value->daftarTindakan->daftartindakan_nama : ''; ?></td>
                <td><?= !empty($value->kelompokPemeriksaanLab->nama_kelompok) ? $value->kelompokPemeriksaanLab->nama_kelompok : ''; ?></td>
                <td><?= !empty($value->jenisPemeriksaanLab->jenispemeriksaanlab_nama) ? $value->jenisPemeriksaanLab->jenispemeriksaanlab_nama : ''; ?></td>
            </tr>
            <?= $no++; ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>