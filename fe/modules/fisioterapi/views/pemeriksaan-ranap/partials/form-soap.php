<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;
use app\components\DocoConstants;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;

$pendaftaranIdEnc = ArrayHelper::getValue($dataView, 'pendaftaranIdEnc');
$programTerapiIdsEnc = ArrayHelper::getValue($dataView, 'programTerapiIdsEnc');
$model = ArrayHelper::getValue($dataView, 'model');
$listPegawai = ArrayHelper::getValue($dataView, 'listPegawai');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title" id="header-form-soap"><?= Yii::t('fe', 'Tambah SOAP') ?></h5>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'form-soap',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => "/fisioterapi/pemeriksaan-ranap/store-soap?pendaftaran_id=$pendaftaranIdEnc&program_terapi_ids=$programTerapiIdsEnc",
            'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
        ]) ?>
        <?= Html::hiddenInput('id', $pendaftaranIdEnc, ['id' => 'pendaftaran_id']); ?>
        <?= Html::hiddenInput('program_terapi_ids', $programTerapiIdsEnc, ['id' => 'program_terapi_ids']); ?>

        <div class="form-group field-soapform-tgl_soap" id="soapDate-form-wrapper">
            <label class="control-label" for="cpptform-tgl_cppt">Tanggal SOAP</label>
            <?= DateTimePicker::widget([
                'id' => 'tgl-soap',
                'disabled' => true,
                'model' => $model,
                'attribute' => 'tgl_soap',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii:ss'
                ]
            ]); ?>
        </div>
        <?= $form->field($model, 'terapis')->widget(Select2::classname(), [
            'data' => $listPegawai,
            'disabled' => true,
            'options' => [
                'id' => 'terapis',
                'class' => 'select2',
                'placeholder' => '-- Pilih --'
            ],
        ]); ?>
        <!-- Asesmen -->

        <!-- Subject -->
        <?= $form->field($model, 'subject')->textarea([
            'class' => 'form-control input-sm',
            'disabled' => true,
            'rows' => '3',
        ])->label('Subjektif') ?>

        <!-- Object -->
        <?= $form->field($model, 'object')->textarea([
            'class' => 'form-control input-sm',
            'disabled' => true,
            'rows' => '3',
        ])->label('Objektif') ?>

        <!-- Diagnosa utama -->
        <?php echo $form->field($model, 'a_diag_utama')->widget(Select2::classname(), [
            'disabled' => true,
            'options' => [
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 soaprj-form'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q: params.term,
                                                        page: params.page || 1,
                                                        type: "diagnosa_utama",
                                                        all_text: 0,
                                                        id_with_text: 1
                                                    };
                                                }
                                            ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]) ?>

        <!-- Diagnosa penyerta -->
        <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(), [
            'disabled' => true,
            'showToggleAll' => false,
            'class' => 'soaprj-form',
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q: params.term,
                                                        page: params.page || 1,
                                                        type: "diagnosa_penyerta",
                                                        all_text: 0,
                                                        id_with_text: 1
                                                    };
                                                }
                                            ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);
        ?>

        <?= $form->field($model, 'diagnosa_fungsi')->widget(Select2::classname(), [
            'disabled' => true,
            'showToggleAll' => false,
            'class' => 'soaprj-form',
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/fisioterapi/allow/get-diagnosa']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                type: "diagnosa_fungsi",
                                all_text: 0,
                                id_with_text: 1
                            };
                        }
                    ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);
        ?>

        <?= $form->field($model, 'prosedur')->widget(Select2::classname(), [
            'disabled' => true,
            'showToggleAll' => false,
            'class' => 'soaprj-form',
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/fisioterapi/allow/get-diagnosa']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                type: "prosedur_kerja",
                                all_text: 0,
                                id_with_text: 1
                            };
                        }
                    ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);
        ?>

        <!-- Planning -->
        <?= $form->field($model, 'planning')->textarea([
            'class' => 'form-control input-sm',
            'disabled' => true,
            'rows' => '3',
        ])->label('Planning') ?>

        <!-- Instruksi -->
        <?= $form->field($model, 'instruksi')->textarea([
            'class' => 'form-control input-sm',
            'disabled' => true,
            'rows' => '3',
        ])->label('Instruksi') ?>

        <?= $form->field($model, 'goal')->textarea([
                'disabled' => true,
                'class' => 'form-control input-sm',
                'rows' => '3'
            ]);
        ?>

        <?= $form->field($model, 'catatan_dokter')->textarea([
                'disabled' => true,
                'class' => 'form-control input-sm soaprj-form',
                'rows' => '3'
            ])->label('Catatan Penunjang');
        ?>

        <?php ActiveForm::end() ?>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" id="btn-save-soap" style="width: 100%;" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
        </div>
    </div>
</div>