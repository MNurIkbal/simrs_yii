<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 16:33:18
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
            <th><?= Yii::t('app', 'Tanggal') ?></th>
            <th><?= Yii::t('app', 'Ruangan') ?></th>
            <th><?= Yii::t('app', 'Kamar') ?></th>
            <th><?= Yii::t('app', 'No Tempat Tidur') ?></th>
            <th><?= Yii::t('app', 'Keterangan') ?></th>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
           <tr>
                <td><?= $counter++; ?></td>
                <td><?= $value['tgl_tthistory']; ?></td>
                <td><?= $value['ruangan_nama']; ?></td>
                <td><?= $value['kamarruangan_nokamar']; ?></td>
                <td><?= $value['no_tempattidur']; ?></td>
                <td><?= $value['keterangan']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>