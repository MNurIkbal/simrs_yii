<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">J. Kebutuhan Edukasi (Dikaji pada pasien dan atau keluarga)</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <p style="margin-left:20px;margin-bottom:10px;margin-top:10px;">Kebutuhan Pembelajaran Pasien (Pilih topik pembelajaran pada kotak yang tersedia) </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kebutuhan_pembelajaran')
                        ->label(false)
                        ->checkboxList(
                            [
                                '1' => 'Diagnosa dan Manajemen',
                                '2' => 'Rehabilitasi',
                                '3' => "Perawatan Luka",
                                '4' => 'Jenis Operasi',
                                '5' => 'Obat-Obatan',
                                '6' => 'Manajemen Nyeri',
                                '7' => 'Diet dan Nutrisi',
                                '8' => 'Penggunaan Alat Medis',
                                '9' => 'Lain-Lain',
                            ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kebutuhan_pembelajaran_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
$(document).ready(function(){
    const kebutuhanPembelajaran = $("input[name=\'GinekologiForm[kebutuhan_pembelajaran][]\'][value=\'9\']");
    const inputKP = $("input[name=\'GinekologiForm[kebutuhan_pembelajaran_lainnya]\']");

    inputKP.prop("readonly", true);

    if(kebutuhanPembelajaran.is(":checked")) {
        inputKP.prop("readonly", false);
    }
    else {
        inputKP.val("").prop("readonly", true);
    }
    
    kebutuhanPembelajaran.on("change", function(){
        if ($(this).is(":checked")) {
            inputKP.prop("readonly", false);
        }
        else {
            inputKP.val("").prop("readonly", true);
        }
    })
})

', View::POS_END);
?>
