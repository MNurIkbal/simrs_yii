<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-05-21 15:15:24
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-05-21 15:20:14
 */
?>
<br><br>
<h3><center><?= Yii::t('app', 'KELAS RUANGAN') ?></center></h3>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Ruangan</td>
            <td>Kelas Pelayanan</td>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $value) : ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                    <td><?= !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '' ?></td>
                </tr>
            <?php $no++; ?>
            <?php endforeach; ?>
    </tbody>
</table>