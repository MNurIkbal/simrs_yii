<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-05 17:44:49
 */
?>

<table id="tabel-riwayat-visite-dokter" border="1" cellpadding="0" cellspacing="0" style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'Tanggal Admisi') ?></th>
            <th><?= Yii::t('app', 'Tanggal Visite') ?></th>
            <th><?= Yii::t('app', 'No Pendaftaran') ?></th>
            <th><?= Yii::t('app', 'No Rekam Medik') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Jenis Kelamin') ?></th>
            <th><?= Yii::t('app', 'Cara Bayar / Penjamin') ?></th>
            <th><?= Yii::t('app', 'Kasus Penyakit') ?></th>
            <th><?= Yii::t('app', 'Ruangan Kamar') ?></th>
            <th><?= Yii::t('app', 'Dokter Penanggung Jawab') ?></th>
            <th><?= Yii::t('app', 'Jenis Visite') ?></th>
            <th><?= Yii::t('app', 'Dokter Visite') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            foreach ($data_visite_dokter as $value) {
        ?>
            <tr>
                <td><?= $value['Tanggal Admisi'] ?></td>
                <td><?= $value['Tanggal Visite'] ?></td>
                <td><?= $value['No. Pendaftaran'] ?></td>
                <td><?= $value['No. Rekam Medik'] ?></td>
                <td><?= $value['Nama Pasien'] ?></td>
                <td><?= $value['Jenis Kelamin'] ?></td>
                <td><?= $value['Cara Bayar'].' / '.$value['Penjamin'] ?></td>
                <td><?= $value['Kasus Penyakit'] ?></td>
                <td><?= $value['Ruangan'].' - '.$value['Kamar'].' - '.$value['Bed'] ?></td>
                <td><?= $value['Dokter Penanggung Jawab'] ?></td>
                <td><?= $value['Jenis Visite'] ?></td>
                <td><?= $value['Dokter Visite'] ?></td>
            </tr>
        <?php 
            }
        ?>
    </tbody>
</table>