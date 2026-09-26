<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-27 13:41:11
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 10:32:46
 */

use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    font-size: 12px;
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
<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama obat alkes</td>
            <td>Tanggal Expired</td>
            <td>Qty</td>
            <td style="text-align: right;">Jumlah Harga Netto (Rp.)</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td>
                            <?= $no ?>
                        </td>
                        <td>
                            <?= $value['obatalkes_nama'] ?>
                        </td>
                        <td>
                            <?= date('d-M-Y',strtotime($value['tglkadaluarsa'])) ?>
                        </td>
                        <td>
                            <?= DocoHelpers::formatNumber($value['stok']). ' ' . $value['satuan_kecil']?>
                        </td>
                        <td style="text-align: right;">
                            <?= DocoHelpers::formatNumber($value['jumlah_harganetto']) ?>
                        </td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="6" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>