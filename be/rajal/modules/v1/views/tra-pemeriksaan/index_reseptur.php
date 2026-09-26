<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-16 16:28:22
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-25 12:44:52
 * @Description: 
 */
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
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
<div>
    <center>
        <h3><?=$title?></h3>
    </center>
    <br />
    <table width="100%" class="tbl-bordered">
        <thead  style="font-size: 11px">
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?= Yii::t('app', 'Racikan / Non racikan') ?></th>
                <th><?= Yii::t('app', 'R ke-') ?></th>
                <th><?= Yii::t('app', 'Nama obat') ?></th>
                <th><?= Yii::t('app', 'Satuan kecil') ?></th>
                <th><?= Yii::t('app', 'Signa') ?></th>
                <th><?= Yii::t('app', 'Qty') ?></th>
                <th><?= Yii::t('app', 'Catatan') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $no = 1;
            $string = '';
            foreach ($data as $key => $value) {
                $signa = (isset($value['signa']) && !empty($value['signa']) ? json_decode($value['signa'], true) : @$value['signa_nama']);
                $string .= 
                    "<tr>"
                        ."<td>". $no ."</td>"
                        ."<td>". @$value['racikan_nama'] ."</td>"
                        ."<td>". @$value['rke'] ."</td>"
                        ."<td>". @$value['obatalkes_nama'] ."</td>"
                        ."<td>". @$value['satuan_kecil'] ."</td>"
                        ."<td>". (isset($signa['text']) ? $signa['text'] : $signa) ."</td>"
                        ."<td>". @$value['qty_reseptur'] ."</td>"
                        ."<td>". @$value['etiket'] ."</td>"
                    ."</tr>"
                ;
                $no++;
            }

            echo $string;
        ?>
        </tbody>
    </table>
</div>