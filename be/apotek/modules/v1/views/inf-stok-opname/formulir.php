<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-23 15:47:48
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-12-10 14:33:02
 * @Description: 
 */

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
            <td>Nama obat alkes</td>
            <td>Tanggal Kadaluarsa</td>
            <td>Stok Sistem</td>
            <td>Stok Fisik</td>
            <td>Kondisi</td>
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
                        <td><?= $value['stok_sistem'] ?></td>
                        <td></td>
                        <td></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="4" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>