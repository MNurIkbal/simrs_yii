<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

$classForm = 'form-control input-sm ews-input';
$classFormAngka = 'form-control input-sm doco-number doco-decimal-wcomma ews-input nullable';
?>

<style>
    .datepicker>div {
        display: block;
    }
    .badge-primary {
        background-color: #007bff;
        color: #fff;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }
    .score-label {
        float: right;
        /* color: #F54927; */
        font-weight: bold;
    }
    
    /* Fix for datepicker in modal */
    .modal .datepicker {
        z-index: 1060 !important;
    }
    
    .modal .datepicker.dropdown-menu {
        position: absolute !important;
        z-index: 1060 !important;
    }
    
    /* Ensure datepicker closes when clicking outside */
    .datepicker {
        pointer-events: auto;
    }
</style>

<?php
    if (!empty($ewsData)) {
        $model->setAttributes($ewsData, false);
        $model->tanggal_ews = date('d/m/Y H:i', strtotime($model->tanggal_ews));
        $ewsId = $ewsId != null ? $ewsId : ArrayHelper::getValue($ewsData, 'ews_id');
        $isTtv = ArrayHelper::getValue($ewsData, 'is_ttv');
    }
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-ews',
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<div class="modal-header bg-inverse" id="modal-ews">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Form EWS</h5>
</div>
<div class="modal-body">
    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;"><?= $title; ?></h5>
        <br/>
        <div class="col-sm-4">
            <?= $form->field($model, 'tanggal_ews')
            ->widget(DateTimePicker::classname(), [
                'options' => [
                    'readonly' => true,
                    'class' => 'tanggal_ews',
                ],
                'disabled' => !empty($ewsId),
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii:ss',
                    'startDate' => date('d/m/Y H:i:s', strtotime($tglPendaftaran)),
                    'endDate' => date('d/m/Y H:i:s'),
                    'orientation' => 'bottom',
                    'forceParse' => false,
                    'keyboardNavigation' => true,
                    'todayBtn' => true,
                    'clearBtn' => false,
                    'container' => '#modal_backdrop_ews',
                    'showOnFocus' => false,
                    'showOnClick' => true,
                    'keepOpen' => false,
                    'inline' => false,
                    'sideBySide' => false
                ]
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'jenis_ews_nama')->textInput(['class' => 'form-control input-sm', 'readonly' => true, 'disabled' => !empty($ewsId),]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'pegawai_nama')->textInput(['class' => 'form-control input-sm', 'readonly' => true, 'disabled' => !empty($ewsId),]); ?>
        </div>
    </div>
    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;">TTV Pasien</h5>
        <div class="col-sm-4">
            <?= $form->field($model, 'is_ttv')->checkbox(['label' => 'Tambahkan Data EWS ini ke Monitoring TTV', 'disabled' => !empty($ewsId)]); ?>
        </div>
    </div>
    <?= Yii::$app->controller->renderPartial('//observasi-ews/'.$partial,[
        'form' => $form,
        'model' => $model,
        'classForm' => $classForm,
        'classFormAngka' => $classFormAngka,
    ]); ?>
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-6">
            <p>Total Skor : <span id="total-skor" style="font-weight:bold;font-size:16px;"></span></p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= DocoHelpers::generateToolbar([
            'reset' => [
                'title' => 'Kosongkan Form',
                'attributes' => [
                    'disabled' => !empty($ewsId) ? !$isEditable : false,
                    'data-options' => 'click',
                    'id' => 'reset-ews',
                ]
            ],
            'save' => [
                'title' => !empty($ewsId) ? 'Update' : 'Simpan',
                'attributes' => [
                    'disabled' => !empty($ewsId) ? !$isEditable : false,
                    'data-options' => 'click',
                    'id' => 'btn-save-ews',
                ],
            ],
             'delete' => [
                'title' => 'Hapus',
                'attributes' => [
                    'disabled' => !empty($ewsId) ? !$isEditable : false,
                    'type' => 'button',
                    'id' => 'btn-delete-ews',
                    'class' => 'btn btn-danger btn-labeled btn-xs',
                    'style' => !empty($ewsId) ? 'display: inline-block;' : 'display: none;'
                ],
            ],
        ]); ?>
</div>

<?php ActiveForm::end(); ?>

<?php
    $this->registerJs("
        var pendaftaranId = '" . $pendaftaranId . "';
        var ewsId = '" . $ewsId . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var _jenisEwsId = '" . $model->jenis_ews . "';
        var _pegawaiId = '" . $model->pegawai_id . "';
        var ewsData = " . json_encode($ewsData) . ";
        var scoringRules = " . json_encode($scoringRules) . ";
        var isLastTtv = '" . $isLastTtv . "';
        var isEditable = '" . $isEditable . "';
    ", View::POS_END);
    $this->registerJs($this->render('form.js'), View::POS_END);
?>
