<?php
/**
 * @author Randy Vianda Putra
 * @copyright 8 Juni 2018 aweutist
 */
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Tahoma;
        font-size: 14px;
    }

    .tbl-bordered th {
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        padding: 5px;
    }

    .tbl-bordered tr.border-top td {
        border-top: 1px solid black;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }
</style>
<table class="tbl-bordered" width="100%" cellpadding="5">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th class="text-left"><?= Yii::t('app', 'Kode Obat Alkes') ?></th>
            <th class="text-left"><?= Yii::t('app', 'Nama Obat Alkes') ?></th>
            <th class="text-left"><?= Yii::t('app', 'Tanggal Kadaluarsa') ?></th>
            <th class="text-left"><?= Yii::t('app', 'Qty') ?></th>
            <th class="text-right"><?= Yii::t('app', 'Jumlah Harga Netto (Rp.)') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php
        $no = 1;
        $total = 0;
        foreach ($data_pemusnahan as $value) :
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$value['obatalkes_kode']?></td>
            <td><?=$value['obatalkes_nama']?></td>
            <td><?=date('d-M-Y', strtotime($value['tglkadaluarsa']));?></td>
            <td><?=DocoHelpers::formatNumber($value['stok']). ' ' .$value['satuan_kecil']?></td>
            <td class="text-right"><?=DocoHelpers::formatNumber($value['jumlah_harganetto']);?></td>
        </tr>
        <?php
        $no++;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr class="border-top">
            <td colspan="4"></td>
            <td><b><?= Yii::t('app', 'Total (Rp.)') ?></b></td>
            <td class="text-right"><?= DocoHelpers::formatNumber($header['total_harganetto']); ?></td>
        </tr>
    </tfoot>
</table>
