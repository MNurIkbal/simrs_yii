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
<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td><b>No</b></td>
            <td><b>Nama Barang</b></td>
            <td><b>Kelompok Barang</b></td>
            <td><b>Sub Kelompok Barang</b></td>
            <td><b>Stok Sistem</b></td>
            <td><b>Stok Fisik</b></td>
            <td><b>Satuan</b></td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= ArrayHelper::getValue($value, 'barang_nama') ?></td>
                <td><?= ArrayHelper::getValue($value, 'kelompok_barang') ?></td>
                <td><?= ArrayHelper::getValue($value, 'subkelompok_barang') ?></td>
                <td><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'stok')) ?></td>
                <td></td>
                <td><?= ArrayHelper::getValue($value, 'satuankecil_nama') ?></td>
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