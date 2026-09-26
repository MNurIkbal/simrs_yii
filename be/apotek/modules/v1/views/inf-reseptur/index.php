<?php

use Doco\components\DocoHelpers;

?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Tahoma;
        font-size: 12px;
    }

    .tbl-bordered th {
        border-bottom: 1px solid black;
        padding: 5px;
    }

    .tbl-bordered tr.border-top td {
        border-top: 1px solid black;
    }

    .bold {
        font-weight: bold;
    }

    .header-right {
        padding-right: 15px;
    }
</style>

<?php

$no = 0;
$prev_rke = null;
$total_racikan = 0.0;
$prev_sub = 0;
$firstkey = null;
foreach($data_obat as $key => $value){
    if($value['jenis_racikan']=="Racikan"){
        $rke = $value['rke'];
        if($rke != $prev_rke){
            $no++;
            $firstkey = $key;
            $data_obat[$key]['no'] = $no;
            $data_obat[$key]['show'] = true;
            $total_racikan = $value['sub_total'];
        }else{
            $data_obat[$key]['no'] = "";
            $total_racikan = $total_racikan + $value['sub_total'];
            $data_obat[$key]['show'] = false;
            $data_obat[$firstkey]['sub_total_racikan'] = $total_racikan;
        }
        $prev_rke = $rke;
    } else {
        $no++;
        $data_obat[$key]['no'] = $no;
        $data_obat[$key]['show'] = true;
    }
}

?>

<table width="100%" class="tbl-bordered">
    <thead  style="font-size: 12px;break-inside: avoid;">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th style="text-align: left"><strong><?= Yii::t('app', 'Obat') ?></strong></th>
            <th style="text-align: left"><strong><?= Yii::t('app', 'Qty') ?></strong></th>
            <th style="text-align: right;white-space: nowrap;"><strong><?= Yii::t('app', 'Sub Total (Rp.)') ?></strong></th>
        </tr>
    </thead>
    <tbody style="font-size: 13px">
        <?php
        $no = 1;
        $no_racikan = 0;
        $temp_rke = $data_obat[0]['rke'];
        $first = true;
        foreach($data_obat as $value):
        ?>
        <tr>
            <td>
            <?php
                echo $value['no'];
            ?>
            </td>
            <td><?=$value['obatalkes_nama']?></td>
            <td><?=$value['qty']." ".$value['satuan_input']?></td>
            <td style="text-align: right">
                <?php
                    if($value['show'] && isset($value['sub_total_racikan'])){
                        echo DocoHelpers::formatNumber($value['sub_total_racikan']);
                    } else if($value['show']) {
                        echo DocoHelpers::formatNumber($value['sub_total']);
                    }
                ?>
            </td>
        </tr>
        <?php
        endforeach;
        ?>
        <tr class="border-top">
            <td style="text-align: right" class="bold" colspan="3">Sub Total (Rp.)</td>
            <td style="text-align: right"><?=$subtotal?></td>
        </tr>
        <tr>
            <td style="text-align: right" class="bold" colspan="3">Biaya Admin (Rp.)</td>
            <td style="text-align: right">
                <?php
                    echo $biayaadmin;
                ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: right" class="bold" colspan="3">Total (Rp.)</td>
            <td style="text-align: right"><?=$total?></td>
        </tr>
    </tbody>
</table>
