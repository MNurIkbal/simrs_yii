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
        font-size: 12px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size: 12px;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>
<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal Tindakan') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Tarif Satuan') ?></th>
            <th><?= Yii::t('app', 'Cyto') ?></th>
            <th><?= Yii::t('app', 'Tarif Cyto') ?></th>
            <th><?= Yii::t('app', 'Jumlah Tarif') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; $total = 0;
        foreach ($data as $key => $value) : $total = $total + $value['sub_total'] ?> 
            <tr>
                <td width="5%" class="text-center"><?= $no++ ?></td>
                <td width="15%"><?= date('d M Y', strtotime($value['tgl_pelayanan'])).'<br/>'.date('H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                <td><?= $value['tindakan_obat_nama'] ?></td>
                <td width="5%" class="text-right"><?= $value['qty'] ?></td>
                <td width="10%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                <td width="8%"><?= ($value['is_cyto']) ? 'Ya' : 'Tidak' ?></td>
                <td width="10%" class="text-right"><?= DocoHelpers::formatNumber($value['tarif_cyto']) ?></td>
                <td width="15%" class="text-right"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" class="text-right"><strong>Total</strong></td>
            <td class="text-right"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
    </tfoot>
</table>
