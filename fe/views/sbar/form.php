<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

$classForm = 'form-control input-sm doco-number doco-decimal-wcomma';
$classFormAngka = 'form-control input-sm doco-number doco-decimal-wcomma sbar-input nullable';
?>

<style>
    #modal_backdrop_sbar {
        z-index: 1050 !important;
    }
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
        font-weight: bold;
    }
    
    .modal .datepicker {
        z-index: 1060 !important;
    }
    
    .modal .datepicker.dropdown-menu {
        position: absolute !important;
        z-index: 1060 !important;
    }
    
    .datepicker {
        pointer-events: auto;
    }

    textarea {
        resize: none;
    }
</style>

<?php
    if (!empty($sbarData)) {
        $model->setAttributes($sbarData, false);
        $model->tgl_sbar = date('d/m/Y H:i', strtotime($model->tgl_sbar));
        $sbarId = $sbarId != null ? $sbarId : ArrayHelper::getValue($sbarData, 'sbar_id');
        $isTtv = ArrayHelper::getValue($sbarData, 'is_ttv');
    }
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-sbar',
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<div class="modal-header bg-inverse" id="modal-sbar">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <h5 style="font-weight:bold;margin-left:10px;margin-bottom:10px;">SBAR Pasien</h5>
        <br/>
        <div class="col-sm-4">
            <?php
            $pluginOptions = [
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
                'container' => '#modal_backdrop_sbar',
                'showOnFocus' => false,
                'showOnClick' => true,
                'keepOpen' => false,
                'inline' => false,
                'sideBySide' => false,
            ];
            ?>
            <?= $form->field($model, 'tgl_sbar')
            ->widget(DateTimePicker::classname(), [
                'options' => [
                    'readonly' => true,
                    'class' => 'tgl_sbar',
                ],
                'disabled' => !empty($sbarId),
                'pluginOptions' => $pluginOptions
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form
                ->field($model, 'dokter_tujuan_id')
                ->dropdownList(
                    ['' => '-- Pilih --'],
                    ['class' => 'form-control input-sm select2 selectDokter']
                );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'situasi')->textArea(['rows' => 6]); ?>
        </div>
    </div>
    <div class="row">
        <h5 style="margin-left:10px;margin-bottom:10px;">Background</h5>
        <div class="col-sm-1">
            <button type="button"
                class="btn btn-info btn-sm btn-copy-ttv"
                data-width="75%"
                data-href=<?= '/'. $modul.'/'.$url.'/copy-ttv?pendaftaran_id='.$pendaftaranId ?>
                >Copy TTV</button>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'is_ttv')->checkbox(['label' => 'Tambahkan Data SBAR ini ke Monitoring TTV', 'disabled' => !empty($sbarId)]); ?>
        </div>
    </div>
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-4">
            <?= $form->field($model, 'sistol', [
                'addon' => ['append' => ['content' => 'mmHg']]
            ])
            ->textInput([
                'class' => $classForm,
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'diastol', ['addon' => ['append' => ['content' => 'mmHg']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-sm-4">
            <?= $form->field($model, 'respirasi', [
                'addon' => ['append' => ['content' => 'x/Menit']]
            ])
            ->textInput([
                'class' => $classForm,
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'Cm']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'Kg']]])
            ->textInput(['class' => 'form-control input-sm berat_badan_sbar']); ?>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-sm-4">
            <?= $form->field($model, 'spo2', ['addon' => ['append' => ['content' => '%']]])
            ->textInput(['class' => $classForm]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])
            ->textInput(['class' => 'form-control input-sm suhu-sbar']); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'lingkar_kepala', ['addon' => ['append' => ['content' => 'Cm']]])
            ->textInput(['class' => 'form-control input-sm lingkar_kepala']); ?>
        </div>
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-sm-4">
            <?= $form->field($model, 'asesmen')->textArea(['rows' => 6]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'rekomendasi')->textArea(['rows' => 6]); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= DocoHelpers::generateToolbar([
            'save' => [
                'title' => !empty($sbarId) ? 'Update' : 'Simpan',
                'attributes' => [
                    // 'disabled' => !empty($sbarId) ? !$isEditable : false,
                    'data-options' => 'click',
                    'id' => 'btn-save-sbar',
                ],
            ],
        ]); ?>
</div>

<?php ActiveForm::end(); ?>
<div id="modal_backdrop_copy_sbar" class="modal fade" style="z-index: 1041 !important; overflow-y: auto;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
    $this->registerJs("
        var pendaftaranId = '" . $pendaftaranId . "';
        var sbarId = '" . $sbarId . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var _pegawaiId = '" . $model->dokter_tujuan_id . "';
        var sbarData = " . json_encode($sbarData) . ";
        var instalasiId = '".$instalasiId."';
        var _pegawaiNama = '" . $dokterNama . "';
        var _tglPendaftaran = '" . $model->tgl_pendaftaran . "';
        var _tglPulang = '" . $model->tglpasienpulang . "';
    ", View::POS_END);
    $this->registerJs($this->render('form.js'), View::POS_END, 'sbar-form-js-v2');
?>
