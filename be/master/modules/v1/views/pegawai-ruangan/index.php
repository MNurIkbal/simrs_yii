<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-28 11:22:55
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-20 10:33:16
 */
?>
<h3 style="text-align: center;"><center><?= Yii::t('app', 'PEGAWAI RUANGAN') ?></center></h3>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Ruangan</td>
            <td>Nama Pegawai</td>
            <td>Kelompok Pegawai</td>
            <td>Status</td>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $values) :
            foreach ($values as $key => $value) :?>
                <tr>
                    <td><?= $no ?></td>
                    <?php if($key == 0) :?>
                    <td rowspan="<?= count($values)?>"><?= !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                    <?php endif;?>
                    <td><?= !empty($value['nama_pegawai']) ? $value['nama_pegawai'] : '' ?></td>
                    <td><?= !empty($value['kelompokpegawai_nama']) ? $value['kelompokpegawai_nama'] : '' ?></td>
                    <td><?= $value['status'] == true ? 'Aktif' : 'Tidak Aktif' ?></td>
                </tr>
            <?php $no++; ?>
            <?php endforeach;
        endforeach; ?>
    </tbody>
</table>