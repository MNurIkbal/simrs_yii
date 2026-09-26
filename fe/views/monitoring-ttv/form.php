<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

$classForm = 'form-control input-sm doco-number doco-decimal-wcomma';
?>

<style>
    .datepicker>div {
        display: block;
    }
</style>

<?php
    $vitalSignId = null;
    if (!empty($vitalSignData)) {
        $model->setAttributes($vitalSignData, false);
        $model->tanggal_ttv = date('d/m/Y H:i', strtotime($model->tanggal_ttv));
        $vitalSignId = ArrayHelper::getValue($vitalSignData, 'vitalsign_id');
    }
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-monitoring-ttv',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<div class="modal-header bg-inverse" id="modal-monitoring-ttv">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Form Monitoring TTV</h5>
</div>
<div class="modal-body">
    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;"><?= $title; ?></h5>
        <br/>
        <div class="col-sm-6">
            <?= $form
                ->field($model, 'jenisttv_id')
                ->dropdownList(
                    ['' => '-- Pilih --'] + $jenis,
                    [
                        'class' => 'form-control input-sm select2 selectJenis',
                    ]
                ); 
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'tanggal_ttv')
            ->widget(DateTimePicker::classname(), [
                'options' => ['readonly' => true, 'class' => 'tanggal_ttv'],
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii:ss',
                    'startDate' => date('d/m/Y H:i:s', strtotime($tglPendaftaran)),
                    'orientation' => 'bottom'
                ]
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form
                ->field($model, 'tingkatkesadaran_id')
                ->dropdownList(
                    ['' => '-- Pilih --'] + $kesadaran,
                    ['class' => 'form-control input-sm select2 selectKesadaran']
                ); 
            ?>
        </div>
    </div>

    <br/>

    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;">TTV</h5>
        <br/>
        <div class="col-sm-6">
            <?= $form->field($model, 'sistol', ['addon' => ['append' => ['content' => 'mmHg']]])
            ->textInput(['class' => $classForm]); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'Cm']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'diastol', ['addon' => ['append' => ['content' => 'mmHg']]])
            ->textInput(['class' => $classForm]); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'Kg']]])
            ->textInput(['class' => 'form-control input-sm berat_badan']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
            ->textInput(['class' => $classForm]); ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'spo2', ['addon' => ['append' => ['content' => '%']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'respirasi', ['addon' => ['append' => ['content' => 'x/Menit']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
        
        <div class="col-sm-6">
            <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])
            ->textInput(['class' => 'form-control input-sm suhu']); ?>
        </div>
    </div>

    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;">GCS</h5>
        <div class="col-sm-4">
            <?= $form->field($model, 'gcs_e', ['addon' => ['prepend' => ['content' => 'E']]])
            ->label(false)
            ->textInput(['class' => $classForm]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'gcs_m', ['addon' => ['prepend' => ['content' => 'M']]])
            ->label(false)
            ->textInput(['class' => $classForm]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'gcs_v', ['addon' => ['prepend' => ['content' => 'V']]])
            ->label(false)
            ->textInput(['class' => $classForm]); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php if (!empty($vitalSignData)) {
        echo DocoHelpers::generateToolbar([
            'save' => [
                'title' => 'Update',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'btn-edit-mon-ttv',
                ],
            ],
        ]);
    } else {
        echo DocoHelpers::generateToolbar([
            'save' => [
                'title' => 'Simpan',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'btn-save-mon-ttv',
                ],
            ],
        ]);
    } ?>
</div>

<?php ActiveForm::end(); ?>

<?php
    $this->registerJs("
        var pendaftaran_id = '" . $pendaftaranId . "';
        var vitalsign_id = '" . $vitalSignId . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var sumberttv_id = '" . $sumberTtvId . "';
    ", View::POS_END);
    $this->registerJs($this->render('form.js'), View::POS_END);
?>