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
    
    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }
</style>
<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan') ?></th>
            <th><?= Yii::t('app', 'Jumlah Tarif') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
                if (!empty($biayaAdmin)) : $total += $biayaAdmin; ?>
                <tr>
                    <td class="number text-center" style="width:5%"> <?= $no++ ?></td>
                    <td style="width:60%">
                        <?= $data_admin ?>
                    </td>
                    <td class="number text-right"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
                </tr>
                <?php
                endif;

        foreach ($data as $key => $value) : 
            $total_amount = isset($value['total_amount']) ? $value['total_amount'] : 0;
            $kelompoktindakan_nama = isset($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '';
        ?> 
            <tr>
                <td width="5%" class="text-center"><?= $no++ ?></td>
                <td><?= $kelompoktindakan_nama ?></td>
                <td width="25%" class="text-right"><?= DocoHelpers::formatNumber($total_amount) ?></td>
            </tr>
        <?php endforeach; ?>
       
    </tbody>

    <?php
    $subsidiAsuransi = !empty ($nominal_dijamin) ?  (int) $nominal_dijamin : 0;
    $uangMuka = !empty ($sisa_uangmuka) ? (int) $sisa_uangmuka  : 0;
    $akomodasiSementara = !empty($data_akomodasi['total_akomodasi']) ? (int)$data_akomodasi['total_akomodasi'] : 0;
    $totalSisaTagihan = $total - $subsidiAsuransi - $uangMuka + $akomodasiSementara;
    ?>
    <tfoot>
        <tr>
            <td colspan="2" class="text-right"><strong>Total Tagihan</strong></td>
            <td class="text-right"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
        <tr>
            <td colspan="2" class="text-right number" ><strong>Subsidi Asuransi</strong></td>
            <td class="text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber(0) ?></td>
        </tr>
        <tr>
            <td colspan="2" class=" text-right number"><strong>Sisa Uang Masuk</strong></td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
        </tr>
        <tr>
            <td colspan="2" class=" text-right number"><strong>Biaya Akomodasi Sementara</strong></td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber(($data_akomodasi['total_akomodasi'] != null)?$data_akomodasi['total_akomodasi'] : 0) ?></td>
        </tr>
        <tr>
            <td colspan="2" class=" text-right number"><strong>Total Sisa Tagihan</strong></td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber($totalSisaTagihan) ?></td>
        </tr>
    </tfoot>
</table>
<?php
    if (count($data) >= 10) :
?>
    <pagebreak />
    <?php 
    endif;
    ?>



<!-- <table width="100%" style="margin-top:20px" class="tbl-footer">
    <tbody>
        <tr>
            <td colspan="5" class="text-right number" >Subsidi Asuransi:</td>
            <td class="text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber(0) ?></td>
        </tr>
        <tr>
            <td colspan="5" class=" text-right number">Sisa Uang Masuk :</td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
        </tr>
        <tr>
            <td colspan="5" class=" text-right number">Biaya Akomodasi Sementara :</td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber(0) ?></td>
        </tr>
        <tr>
            <td colspan="5" class=" text-right number">Total Akomodasi :</td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber($data_akomodasi['total_akomodasi']) ?></td>
        </tr>
        <tr>
            <td colspan="5" class=" text-right number">Total Sisa Tagihan :</td>
            <td class=" text-right number border-bottom" style="width:25%"><?= DocoHelpers::formatNumber($totalSisaTagihan) ?></td>
        </tr>
    </tbody>
</table> -->