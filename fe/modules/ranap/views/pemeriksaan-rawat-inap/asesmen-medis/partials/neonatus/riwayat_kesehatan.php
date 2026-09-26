<?php

use yii\web\View;

$riwayatKesehatan = $model->riwayat_kesehatan;
$riwayatKesehatan1 = $riwayatKesehatan2 = $riwayatKesehatan3 = $riwayatKesehatan4 = $riwayatKesehatan5 = false;
$classFormControl = 'form-control input-sm';
$riwayatKesehatanName = 'riwayat_kesehatan[]';

if($riwayatKesehatan && is_array($riwayatKesehatan)) {
    if(in_array(1, $riwayatKesehatan)) {
        $riwayatKesehatan1 = true;
    }
    if(in_array(2, $riwayatKesehatan)) {
        $riwayatKesehatan2 = true;
    }
    if(in_array(3, $riwayatKesehatan)) {
        $riwayatKesehatan3 = true;
    }
    if(in_array(4, $riwayatKesehatan)) {
        $riwayatKesehatan4 = true;
    }
    if(in_array(5, $riwayatKesehatan)) {
        $riwayatKesehatan5 = true;
    }
}

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
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', 'Tidak Pernah Opname'),
                                'value' => 1,
                                'checked' => $riwayatKesehatan1,
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $riwayatKesehatanName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Pernah Operasi'),
                                    'value' => 2,
                                    'checked' => $riwayatKesehatan2,
                                ]
                            ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $riwayatKesehatanName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Tidak'),
                                    'value' => 3,
                                    'checked' => $riwayatKesehatan3,
                                ]
                            ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $riwayatKesehatanName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Obat Yang Dibawa ke RS/sementara di Konsumsi'),
                                    'value' => 4,
                                    'checked' => $riwayatKesehatan4,
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'obat_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$riwayatKesehatan4) ? true : false
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $riwayatKesehatanName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Pernah Opname Dengan Sakit'),
                                    'value' => 5,
                                    'checked' => $riwayatKesehatan5,
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'sakit_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$riwayatKesehatan5) ? true : false
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-md-6">
                        <?= $form->field($model, 'rs_opname')->label()->textInput(['class' => $classFormControl]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var obat_lainnya = "'.$model->obat_lainnya.'"
var sakit_lainnya = "'.$model->sakit_lainnya.'"

$(document).ready(function(){
    const checkboxObat = $("input[name=\'NeonatusForm[riwayat_kesehatan][]\'][value=\'4\']");
    const checkboxSakit = $("input[name=\'NeonatusForm[riwayat_kesehatan][]\'][value=\'5\']");
    const obatLainnya = $("#neonatusform-obat_lainnya");
    const sakitLainnya = $("#neonatusform-sakit_lainnya");

    obatLainnya.prop("readonly", true);
    sakitLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(obat_lainnya) {
            obatLainnya.prop("readonly", false);
        }
        if(sakit_lainnya) {
            sakitLainnya.prop("readonly", false);
        }
    }

    checkboxObat.change(function () {
        if(checkboxObat.is(":checked")) {
            obatLainnya.prop("readonly", false);
        }
        else {
            obatLainnya.val("").prop("readonly", true);
        }
    });

    checkboxSakit.change(function () {
        if(checkboxSakit.is(":checked")) {
            sakitLainnya.prop("readonly", false);
        }
        else {
            sakitLainnya.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);
?>
