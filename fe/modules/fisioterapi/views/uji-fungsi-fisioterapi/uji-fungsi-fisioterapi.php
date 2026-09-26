<?php

use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;

?>
<style>
    .datepicker>div {
        display: block;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><?= Yii::t('fe', $title) ?></h6>
    </div>
    <br>
    <button type="button" id="btn-cetak-uji-fungsi"
        class="btn btn-info btn-labeled btn-xs data-pdf"
        style="margin-left:10px;margin-bottom:10px;" <?= !$hasData ? 'disabled' : '' ?>>
        <b><i class="fa fa-file-pdf-o"></i></b>Cetak PDF</button>

    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'uji-fungsi-fisioterapi-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        ]);
        ?>
        <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
        <?= Html::activeHiddenInput($model, 'pasien_id') ?>
        <?= Html::activeHiddenInput($model, 'program_terapi_ids') ?>
        <?= Html::activeHiddenInput($model, 'programterapi_id') ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'diag_medis', [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-2 control-label text-bold',
                        'wrapper' => 'col-md-5'
                    ],
                ])->widget(Select2::classname(), [
                    'showToggleAll' => false,
                    'options' => [
                        'multiple' => false,
                        'placeholder' => '-- Pilih Diagnosa Medis --'
                    ],
                    'pluginOptions' => [
                        'maximumInputLength' => 50,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                        ],
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                            'dataType' => 'json',
                            'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q: params.term,
                                        page: params.page || 1,
                                        type: "diagnosa_medis",
                                        all_text: 0,
                                        id_with_text: 1,
                                    };
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                        'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                        'tags' => false,
                    ],
                ])->label('Diagnosa Medis');
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'diag_fungsi', [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-2 control-label text-bold',
                        'wrapper' => 'col-md-5'
                    ],
                ])->widget(Select2::classname(), [
                    'showToggleAll' => false,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '-- Pilih Diagnosa Fungsional --'
                    ],
                    'pluginOptions' => [
                        'maximumInputLength' => 50,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                        ],
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                            'dataType' => 'json',
                            'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q: params.term,
                                        page: params.page || 1,
                                        type: "diagnosa_fungsional",
                                        all_text: 0,
                                        id_with_text: 1,
                                    };
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                        'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                        'tags' => true, // Memungkinkan input freetext
                    ],
                ])->label('Diagnosa Fungsional');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'hasil_yang_didapat')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Input hasil yang didapat'
                ])->label('Hasil Yang Didapat'); ?>
            </div>
            <div class="col-md-6">
                <?php
                    echo $form->field($model, 'diag_utama')->widget(Select2::classname(), [
                        'initValueText' => isset($model->diag_utama) && $model->diag_utama != '' ? $model->diag_utama : null,
                        'options' => [
                            'placeholder' => '-- Pilih Kesimpulan Diagnosa --',
                            'class' => 'form-control input-sm'
                        ],
                        'pluginOptions' => [
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                            ],
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                                'dataType' => 'json',
                                'data' => new JsExpression('
                                    function(params) {
                                        return {
                                            q: params.term,
                                            page: params.page || 1,
                                            type: "diagnosa_utama",
                                            all_text: 0,
                                            id_with_text: 1,
                                        };
                                    }
                                ')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                            'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                            'tags' => false,
                        ],
                    ])->label('Kesimpulan');
                ?>
            </div>
        </div>
        <div class="row">
        </div>
        <div class="row">
            <h5 class="panel-title" style="margin-left:10px;margin-bottom:20px;margin-top:10px;">Rekomendasi</h5>
            <div class="col-md-6">
                <?= $form->field($model, 'tindakan_prosedur', [
                    'options' => [
                        'class' => 'form-group',
                    ],
                ])->widget(Select2::classname(), [
                    'showToggleAll' => false,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '-- Pilih Tindakan/Prosedur --'
                    ],
                    'pluginOptions' => [
                        'maximumInputLength' => 50,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                        ],
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                            'dataType' => 'json',
                            'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q: params.term,
                                        page: params.page || 1,
                                        type: "tindakan_dan_prosedur",
                                        all_text: 0,
                                        id_with_text: 1,
                                    };
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                        'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                        'tags' => true, // Memungkinkan input freetext
                    ],
                ])->label('Tindakan/Prosedur');
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'anjuran_dan_goal')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Input Anjuran dan Goal'
                ])->label('Anjuran dan Goal'); ?>
            </div>
        </div>
        <div class="row">
        </div>
        <?php ActiveForm::end() ?>
        <div class="row float-md-right" style="margin-top: 10px; margin-right: 10px;">
            <div class="col-sm-10"></div>
            <div class="col-sm-2">
                <button type="button" id="btn-save-uji"
                    class="btn btn-info btn-labeled btn-xs data-save"
                    style="margin-left:10px;margin-bottom:10px; width: 100%;"><b><i class="fa fa-floppy-o"></i></b><?= $data ? 'Update' : 'Simpan' ?></button>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var pendaftaran_id = '" . $pendaftaranId . "';
" . $this->render('uji-fungsi-fisioterapi.js'), View::POS_END, 'js');

?>



