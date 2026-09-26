<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-12-05 16:54:04
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 17:52:02
 */

use Doco\components\DocoHelpers;
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
            <td style="font-weight: bold;">No</td>
            <td style="font-weight: bold;">Nama obat alkes</td>
            <td style="font-weight: bold;">Tanggal Kadaluarsa</td>
            <td style="font-weight: bold;">Stok Sistem</td>
            <td style="font-weight: bold;">Stok Fisik</td>
            <td style="font-weight: bold;">Selisih</td>
            <td style="font-weight: bold;">Kondisi</td>
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
                <td><?= $value['obatalkes_nama'] ?></td>
                <td><?= isset($value['tglkadaluarsa']) ? date('d-M-Y',strtotime($value['tglkadaluarsa'])) : '' ?></td>
                <td><?= DocoHelpers::formatNumber($value['volume_sistem'], 2) ?></td>
                <td><?= DocoHelpers::formatNumber($value['volume_fisik'], 2) ?></td>
                <td><?= DocoHelpers::formatNumber($value['volume_fisik'] - $value['volume_sistem'], 2) ?></td>
                <td><?= $value['kondisibarang_nama'] ?></td>
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
</table><br>