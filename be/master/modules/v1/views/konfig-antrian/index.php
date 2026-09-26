<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-05-02 11:43:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-05-02 13:08:23
 */
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Pengambilan antrian')?></td>
    </tr>
</table>
<br>
<table width="100%" cellpadding="10" class="tabel">
    <tbody>
        <tr>
            <td class="bold" width="80"><?= Yii::t('app', 'Jenis antrian') ?></td>
            <td width="1">:</td>
            <td><?=$header['jenis_antrian']?></td>
        </tr>
        <tr>
            <td class="bold"><?= Yii::t('app', 'Kode antrian') ?></td>
            <td>:</td>
            <td><?=$header['kode_antrian']?></td>
        </tr>
        <tr>
            <td class="bold"><?= Yii::t('app', 'Jenis pengambilan antrian') ?></td>
            <td>:</td>
            <td><?=$header['fungsi_antrian']?></td>
        </tr>
        <tr>
            <td class="bold"><?= Yii::t('app', 'Cara bayar') ?></td>
            <td>:</td>
            <td><?=$header['carabayar_nama']?></td>
        </tr>
        <tr>
            <td class="bold"><?= Yii::t('app', 'Status') ?></td>
            <td>:</td>
            <td><?=$header['status']?></td>
        </tr>
    </tbody>
</table>
<br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Jenis antrian') ?></th>
            <th><?= Yii::t('app', 'Kode antrian') ?></th>
            <th><?= Yii::t('app', 'Jenis pengambilan antrian') ?></th>
            <th><?= Yii::t('app', 'Cara bayar') ?></th>
            <th><?= Yii::t('app', 'Klasifikasi pasien') ?></th>
            <th><?= Yii::t('app', 'Status') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php 
        $no = 1;
        foreach($data as $value):
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['jenis_antrian']?></td>
            <td><?=$value['kode_antrian']?></td>
            <td><?=$value['fungsi_antrian']?></td>
            <td><?=$value['carabayar_nama']?></td>
            <td><?=$value['klasifikasipasien_nama']?></td>
            <td><?=$value['status']?></td>
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
    </tbody>
</table>
<!-- Header -->