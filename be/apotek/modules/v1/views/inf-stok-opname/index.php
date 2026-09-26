<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-12-06 16:35:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 18:22:16
 */

use Doco\components\DocoHelpers;
?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 8px
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
            <td>Laci Obat</td>
            <td>Nama Obat</td>
            <td>Stok Saat Stok Opname</td>
            <td>Stok Fisik</td>
            <td>Selisih Stok Opname</td>
            <td>Stok Saat Ini</td>
            <td>Selisih Saat Ini</td>
            <?php 
                switch ($type) {
                    case '1':
                        $priceLabel = "HNA (RP)";
                        break;
                    
                    default:
                    $priceLabel = "Weighted Avg (Rp)";
                        break;
                }
            ?>
            <td><?= $priceLabel ?></td>
            <td>Total Selisih (Rp)</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (count($data)) :
            foreach ($data as $value) :
                switch ($type) {
                    case '1':
                        $priceVal = $value['base_price'];
                        $seleisihVal = $value['selisih_base_price'];
                        break;

                    default:
                        $priceVal = $value['weighted_avg'];
                        $seleisihVal = $value['selisih_weighted_avg'];
                        break;
                }
        ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $value['laci'] ?></td>
                    <td><?= $value['obatalkes_nama'] ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['volume_sistem'], true, false, 3) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['volume_fisik'], true, false, 3) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['selisih'], true, false, 3) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['stok_sistem'], true, false, 3) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['stok_selisih'], true, false, 3) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($priceVal, false, false) ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($seleisihVal, false, false) ?></td>
                </tr>
            <?php
                $no++;
            endforeach;
        else :
            ?>
            <tr>
                <td colspan="7" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
        endif;
        ?>
    </tbody>
</table>
<br>
