<?php

/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

?>
<style>
.picker_modal-one {
   bottom: 35px !important;
 }.picker__button--clear{
     display: none !important;
 }
</style>
<div class="text-center" id="loading-schedule"></div>
<table class="table table-hover" id="tbl-schedule" style="width: 100%">
    <thead>
        <tr class="bg-inverse">
            <th style='width: 1px'>No</th>
            <th width="20%">Hari</th>
            <th width="50%">Tanggal</th>
            <th>Estimasi Jam Mulai</th>
            <th>Estimasi Jam Selesai</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $fieldName = "schedule";
        for ($i = 0; $i < $frekuensi ; $i++) {
            $number = $i + 1;
            $hari = Html::textInput("hari-$i", Docohelpers::convertNamaHari($listHari[$i]),[
                'class' => 'form-control input-sm',
                'id' => "hari-$i",
                'data-ke' => "$i",
                'readonly' => true
            ]);
            $dateTimePicker = Html::textInput("tanggal-$i", $newDate[$i],[
                'class' => 'form-control input-sm pickadate-input',
                'id' => "schedule-$i",
                'data-ke' => "$i"
            ]);
            $jamMulai = Html::textInput("jam_mulai-$i", "08:00",[
                'class' => 'form-control input-sm jam_mulai',
                'id' => "jamMulai-$i",
                'data-ke' => "$i",
                'type' => 'time'
            ]);

            $jamSelesai = Html::textInput("jam_selesai-$i", "09:00",[
                'class' => 'form-control input-sm jam_selesai',
                'id' => "jamSelesai-$i",
                'data-ke' => "$i",
                'type' => 'time'
            ]);
            echo "<tr>";
            echo "<td>$number</td>";
            echo "<td><div class='input-group' style='width:100%'>$hari</div></td>";
            echo "<td><div class='input-group' style='width:100%'>$dateTimePicker</div></td>";
            echo "<td><div class='input-group' style='width:100%'>$jamMulai</div></td>";
            echo "<td><div class='input-group' style='width:100%'>$jamSelesai</div></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<?php
$phpVars = [
    'fieldName' => $fieldName
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJsVar('pegawaiId', $pegawaiId);
$this->registerJsVar('frekuensi', $frekuensi);
$this->registerJsVar('days', $days);
$this->registerJsVar('isDaily', $isDaily);
$this->registerJs($this->render('__schedule.js'), View::POS_END);
?>