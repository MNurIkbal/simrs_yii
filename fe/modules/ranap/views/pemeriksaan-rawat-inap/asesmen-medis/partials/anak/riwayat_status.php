<?php

use yii\web\View;

$statusMental = $model->status_mental;
$statusMental1 = $statusMental2 = $statusMental3 = false;
$classFormControl = 'form-control input-sm';
$statusName = 'status_mental[]';

if($statusMental && is_array($statusMental)) {
    if(in_array(1, $statusMental)) {
        $statusMental1 = true;
    }
    if(in_array(2, $statusMental)) {
        $statusMental2 = true;
    }
    if(in_array(3, $statusMental)) {
        $statusMental3 = true;
    }
}

?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">G. Riwayat Status Psikologi</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_psikologi')
                            ->label(Yii::t('fe', 'Status Psikologis'))
                            ->checkboxList(
                            [
                                1 => 'Cemas',
                                2 => 'Sedih',
                                3 => 'Tenang',
                                4 => 'Marah',
                                5 => 'Acuh Tak Acuh',
                                6 => 'Takut',
                                7 => 'Gelisah',
                                8 => 'Lain-Lain',
                            ]
                        );
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_psikologi_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-bottom:20px;">Status Mental </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(Yii::t('fe', 'Status Mental'))
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', 'Sadar & Orientasi Baik'),
                                'value' => 1,
                                'checked' => $statusMental1,
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Ada Masalah Perilaku'),
                                    'value' => 2,
                                    'checked' => $statusMental2,
                                    'id' => 'sm2'
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'masalah_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$statusMental2) ? true : false
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Perilaku Kekerasan yang di Alami Pasien Sebelumnya'),
                                    'value' => 3,
                                    'checked' => $statusMental3,
                                    'id' => 'sm3'
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'perilaku_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$statusMental3) ? true : false
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
var masalah_lainnya = "'.$model->masalah_lainnya.'"
var perilaku_lainnya = "'.$model->perilaku_lainnya.'"
var status_psikologi_lainnya = "'.$model->status_psikologi_lainnya.'"

$(document).ready(function(){
    const checkboxPsikologi = $("input[name=\'AnakForm[status_psikologi][]\'][value=\'8\']");
    const checkboxMasalah = $("input[name=\'AnakForm[status_mental][]\'][value=\'2\']");
    const checkboxPerilaku = $("input[name=\'AnakForm[status_mental][]\'][value=\'3\']");
    const masalahLainnya = $("#anakform-masalah_lainnya");
    const perilakuLainnya = $("#anakform-perilaku_lainnya");
    const psikologiLainnya = $("#anakform-status_psikologi_lainnya");

    masalahLainnya.prop("readonly", true);
    perilakuLainnya.prop("readonly", true);
    psikologiLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(masalah_lainnya) {
            masalahLainnya.prop("readonly", false);
        }
        if(perilaku_lainnya) {
            perilakuLainnya.prop("readonly", false);
        }
        if(status_psikologi_lainnya) {
            psikologiLainnya.prop("readonly", false);
        }
    }

    checkboxMasalah.change(function () {
        if(checkboxMasalah.is(":checked")) {
            masalahLainnya.prop("readonly", false);
        }
        else {
            masalahLainnya.val("").prop("readonly", true);
        }
    });

    checkboxPerilaku.change(function () {
        if(checkboxPerilaku.is(":checked")) {
            perilakuLainnya.prop("readonly", false);
        }
        else {
            perilakuLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPsikologi.change(function () {
        if(checkboxPsikologi.is(":checked")) {
            psikologiLainnya.prop("readonly", false);
        }
        else {
            psikologiLainnya.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);
?>
