<?php

use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 14px
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
<table border="1" class="tbl-bordered" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Stok Saat Stok Opname</td>
            <td>Stok Fisik</td>
            <td>Selisih Stok Opname</td>
            <td>Stok Saat Ini</td>
            <td>Selisih Saat Ini</td>
            <td>Harga Netto (Rp)</td>
            <td>Total Harga Netto (Rp)</td>
            <td>Total Selisih (Rp)</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($data)) :
                foreach ($data as $value) :
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= ArrayHelper::getValue($value, 'barang_nama', ''); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'volume_sistem', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'volume_fisik', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'selisih_so', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_sistem', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok_selisih', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harganetto', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harga_netto_fisik', 0)); ?></td>
                        <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_selisih', 0)); ?></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="9" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>
<br>
