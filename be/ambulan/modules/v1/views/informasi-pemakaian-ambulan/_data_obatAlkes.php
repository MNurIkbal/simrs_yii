<?php
use Doco\components\DocoHelpers;
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

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <th>Qty</th>
       </tr>
    </thead>
    <tbody>
        <?php if(count($dataObatAlkes) > 0):
            $no = 1; 
            foreach ($dataObatAlkes as $key => $value) : 
        ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= isset($value->obatalkes_nama) ? $value->obatalkes_nama : '-' ?></td>
                <td align="right"><?= isset($value->qty) ? $value->qty : 0?></td>
            </tr>
        <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="3"><?=\Yii::t("app", "Tidak ada data.");?></td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
