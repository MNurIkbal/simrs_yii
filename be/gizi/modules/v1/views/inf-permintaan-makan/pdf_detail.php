<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-17 13:28:32
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
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <tr class="bg-inverse">
                <th><?=Yii::t('app', 'No')?></th>
                <th><?=Yii::t('app', 'Jenis Diet')?></th>
                <th><?=Yii::t('app', 'Menu')?></th>
                <th><?=Yii::t('app', 'Waktu Diet')?></th>
                <th><?=Yii::t('app', 'Keterangan')?></th>
                <th><?=Yii::t('app', 'Jumlah')?></th>
            </tr>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
			<tr>
                <td><?= $counter++ ?></td>
                <td><?= $value['jenisdiet_nama'] ?></td>
                <td><?= $value['makanandiet_nama'] ?></td>
                <td><?= $value['waktu'] ?></td>
                <td><?= $value['keterangan'] ?></td>
                <td><?= $value['jumlah'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>