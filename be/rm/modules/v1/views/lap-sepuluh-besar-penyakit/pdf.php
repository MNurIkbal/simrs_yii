<?php
/**
 * @Author: Sigit
 * @Date:   2019-03-14 16:10:14
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
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Kode Diagnosa') ?></th>
            <th><?= Yii::t('app', 'Nama Diagnosa') ?></th>
            <th><?= Yii::t('app', 'Jumlah') ?></th>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
           <tr>
                <td><?= $counter; ?></td>
                <td><?= $value['diagnosa_kode']; ?></td>
                <td><?= $value['diagnosa_nama']; ?></td>
                <td><?= $value['jumlah']; ?></td>
                <?php $counter++; ?>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>