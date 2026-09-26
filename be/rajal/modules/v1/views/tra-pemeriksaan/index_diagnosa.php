<?php
/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-02 16:51:52
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-01 11:36:25
 * @Description: 
 */
use Doco\components\DocoHelpers;
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
<table class="tbl-bordered" width="100%">
    <thead>
        <tr>
            <th><?=Yii::t('app', 'No');?></th>
            <th><?=Yii::t('app', 'Tanggal diagnosa');?></th>
            <th><?=Yii::t('app', 'Kelompok diagnosa');?></th>
            <th><?=Yii::t('app', 'Klasifikasi diagnosa');?></th>
            <th><?=Yii::t('app', 'Kode');?></th>
            <th><?=Yii::t('app', 'Nama diagnosa');?></th>
            <th><?=Yii::t('app', 'Nama lainnya');?></th>
            <th><?=Yii::t('app', 'Kata kunci');?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $no = 1;
        $string = '';
        foreach ($data as $key => $value) {
            $string .= 
                "<tr>"
                    ."<td>". $no ."</td>"
                    ."<td>". DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tglmorbiditas'])), false, true) ."</td>"
                    ."<td>". $value['kelompokdiagnosa_nama'] ."</td>"
                    ."<td>". $value['klasifikasidiagnosa_nama'] ."</td>"
                    ."<td>". $value['diagnosa_kode'] ."</td>"
                    ."<td>". $value['diagnosa_nama'] ."</td>"
                    ."<td>". $value['diagnosa_namalainnya'] ."</td>"
                    ."<td>". $value['diagnosa_katakunci'] ."</td>"
                ."</tr>"
            ;
            $no++;
        }

        echo $string;
    ?>
    </tbody>
</table>