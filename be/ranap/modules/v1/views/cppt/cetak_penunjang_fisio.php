<?php

use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    width: 100%;
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
<table border="1" class="tbl-bordered">
    <thead>
        <tr class="bg-inverse">
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Jenis Pemeriksaan"); ?></th>
            <th><?= \Yii::t("app", "Nama Pemeriksaan"); ?></th>
            <th><?= \Yii::t("app", "CITO"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $no = $total = 0; ?>
        <?php foreach ($model as $each) : ?>
        <tr>
            <td><?=@++$no; ?></td>
            <td><?=@$each['jenis_periksa']; ?></td>
            <td><?=@$each['pemeriksaan_nama']; ?></td>
            <td align="center"> x </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
