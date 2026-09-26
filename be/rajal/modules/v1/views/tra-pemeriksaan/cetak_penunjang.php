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
            <!-- <th><?php // \Yii::t("app", "Tarif Satuan"); ?></th> -->
            <th><?= \Yii::t("app", "CITO"); ?></th>
            <!-- <th><?php // \Yii::t("app", "Tarif Satuan CITO"); ?></th> -->
            <!-- <th><?php // \Yii::t("app", "Sub Total"); ?></th> -->
        </tr>
    </thead>
    <tbody>
        <?php $no = $total = 0; ?>
        <?php foreach ($model as $each) : ?>
        <tr>
            <td><?=@++$no; ?></td>
            <td><?=@$each['jenis_periksa']; ?></td>
            <td><?=@$each['pemeriksaan_nama']; ?></td>
            <!-- <td align="right"><?php // DocoHelpers::rupiahDisplay($each['tarif_pelayanan']); ?></td> -->
            <td align="center"><?=$each['is_cyto'] ? 'v' : 'x'; ?></td>
            <!-- <td align="right"><?php // DocoHelpers::rupiahDisplay($each['tarif_cytotindakan']); ?></td> -->
            <!-- <td align="right"><?php //DocoHelpers::rupiahDisplay($each['tarif_pelayanan'] + $each['tarif_cytotindakan']); ?></td> -->
        </tr>
        <?php $total += ($each['tarif_pelayanan'] + $each['tarif_cytotindakan']); ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <!-- <tr>
            <td colspan=6><strong><i>Total</i></strong></td>
            <td align="right"><?php // DocoHelpers::rupiahDisplay($total); ?></td>
        </tr> -->
    </tfoot>
</table>
