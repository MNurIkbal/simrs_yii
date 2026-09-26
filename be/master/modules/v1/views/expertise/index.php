<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 13:57:32
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-25 14:54:51
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
<br>
<table class="tbl-bordered">
    <tr>
        <td>No</td>
        <td><?=Yii::t('app', 'Nama expertise')?></td>
        <td><?=Yii::t('app', 'Nama pemeriksaan')?></td>
        <td><?=Yii::t('app', 'Hasil expertise')?></td>
        <td><?=Yii::t('app', 'Kesan')?></td>
        <td><?=Yii::t('app', 'Kesimpulan')?></td>
    </tr>
    <?php 
    $no = 0;
    foreach ($data as $key => $value) {
        $no++;
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['nama_expertise']?></td>
            <td><?=$value['pemeriksaanrad_nama']?></td>
            <td><?=$value['hasil_expertise']?></td>
            <td><?=$value['kesan']?></td>
            <td><?=$value['kesimpulan']?></td>
        </tr>
        <?php
    }
    ?>
</table>