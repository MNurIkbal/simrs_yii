<?php

use yii\web\View;

$riwayatKesehatan = $model->riwayat_kesehatan;
$classFormControl = 'form-control input-sm';
$riwayatKesehatanName = 'riwayat_kesehatan';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">B. Riwayat Kesehatan</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $riwayatKesehatanName)
                            ->label(false)
                            ->radioList(
                            [
                                '1' => Yii::t('fe', 'Tidak Pernah Opname'),
                                '2' => Yii::t('fe', 'Pernah Operasi'),
                                '3' => Yii::t('fe', 'Tidak'),
                                '4' => Yii::t('fe', 'Obat Yang Dibawa ke RS/Sementara di Konsumsi'),
                                '5' => Yii::t('fe', 'Pernah Opname Dengan Sakit'),
                            ],
                            ['itemOptions' => [
                                'class' => $riwayatKesehatanName,
                                'id' => $riwayatKesehatanName
                            ]]
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'obat_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'placeholder' => 'Obat Yang Dibawa ke RS/Sementara di Konsumsi'
                            ]
                        ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'sakit_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'placeholder' => 'Pernah Opname Dengan Sakit'
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'rs_opname')->label(Yii::t('fe', 'Di RS'))->textInput(
                            [
                                'class' => $classFormControl,
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var riwayat_kesehatan = "'.$riwayatKesehatan.'"
var obat_lainnya = "'.$model->obat_lainnya.'"
var sakit_lainnya = "'.$model->sakit_lainnya.'"

$(document).ready(function(){
    const checkbox = $("input[name=\'AnakForm[riwayat_kesehatan]\']");
    const obatLainnya = $("#anakform-obat_lainnya");
    const sakitLainnya = $("#anakform-sakit_lainnya");

    obatLainnya.prop("readonly", true);
    sakitLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(checkbox.is(":checked")) {
            if(riwayat_kesehatan == "4") {
                obatLainnya.prop("readonly", false);
                sakitLainnya.prop("readonly", true);
            }
            else if(riwayat_kesehatan == "5") {
                obatLainnya.prop("readonly", true);
                sakitLainnya.prop("readonly", false);
            }
            else {
                obatLainnya.val("").prop("readonly", true);
                sakitLainnya.val("").prop("readonly", true);
            }
        }
    }
    
    checkbox.change(function () {
        if(checkbox.is(":checked")) {
            var _rk = $(this).val()
            if(_rk == "4") {
                obatLainnya.prop("readonly", false);
                sakitLainnya.val("").prop("readonly", true);
            }
            else if(_rk == "5") {
                obatLainnya.val("").prop("readonly", true);
                sakitLainnya.prop("readonly", false);
            }
            else {
                obatLainnya.val("").prop("readonly", true);
                sakitLainnya.val("").prop("readonly", true);
            }
        }
    });
})

', View::POS_END);
?>
