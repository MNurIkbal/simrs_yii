<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">P. Kebutuhan Edukasi (Dikaji pada pasien dan atau keluarga)</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hambatan_pembelajaran')->checkboxList(
                            [
                                '1' => 'Tidak Ada',
                                '2' => 'Penglihatan',
                                '3' => "Pendengaran",
                                '4' => 'Bahasa',
                                '5' => 'Kognitif',
                                '6' => 'Emosi',
                                '7' => 'Budaya/Kepercayaan',
                                '8' => 'Lain-Lain',
                            ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hambatan_pembelajaran_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'edukasi')->checkboxList(
                            [
                                '1' => 'Stimulasi Tumbuh Kembang',
                                '2' => 'Rehabilitasi',
                                '3' => "Perawatan Luka",
                                '4' => 'Obat-Obatan',
                                '5' => 'Perawatan Stoma',
                                '6' => 'Manajemen Nyeri',
                                '7' => 'Diet dan Nutrisi',
                                '8' => 'Jaminan Finansial',
                                '9' => 'Lain-Lain',
                            ])->label(Yii::t('fe', 'Edukasi Yang Diperlukan')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'edukasi_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
$(document).ready(function(){
    const hambatanPembelajaran = $("input[name=\'AnakForm[hambatan_pembelajaran][]\'][value=\'8\']");
    const inputHP = $("input[name=\'AnakForm[hambatan_pembelajaran_lainnya]\']");
    const edukasi = $("input[name=\'AnakForm[edukasi][]\'][value=\'9\']");
    const inputEdukasi = $("input[name=\'AnakForm[edukasi_lainnya]\']");

    inputHP.prop("readonly", true);
    inputEdukasi.prop("readonly", true);

    if(hambatanPembelajaran.is(":checked")) {
        inputHP.prop("readonly", false);
    }
    else {
        inputHP.val("").prop("readonly", true);
    }
    
    if(edukasi.is(":checked")) {
        inputEdukasi.prop("readonly", false);
    }
    else {
        inputEdukasi.val("").prop("readonly", true);
    }

    hambatanPembelajaran.on("change", function(){
        if ($(this).is(":checked")) {
            inputHP.prop("readonly", false);
        }
        else {
            inputHP.val("").prop("readonly", true);
        }
    })
    edukasi.on("change", function(){
        if ($(this).is(":checked")) {
            inputEdukasi.prop("readonly", false);
        }
        else {
            inputEdukasi.val("").prop("readonly", true);
        }
    })
})

', View::POS_END);
?>
