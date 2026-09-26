<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 10:41:34
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
?>

<style>
    .select2-selection__clear{
        position: absolute;
        top: 0;
        left: -10px;
        height: 100%;
        padding: 0 12px;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Tambah Asesmen') ?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'custom-back' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-back-soap',
                ],
            ],
            'custom-backs' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-back-soap-update',
                ],
            ],
            'custom-save' => [
                'type' => 'submit',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'id' => 'btn-save-soap',
                ],
            ],
        ]) ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-soap',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>

                <?= $form->field($model, 'subject')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3',
                ])->label('S') ?>

                <?= $form->field($model, 'object')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3',
                ])->label('O') ?>

                <div class="form-group">
                    <?= Html::label('A', null, ['class' => 'control-label col-sm-2 required']) ?>
                    <div class="col-sm-10">
                        <?php
                         echo $form->field($model, 'a_diag_utama')->widget(Select2::classname(), [
                                'initValueText' => isset($text_diagnosa) && $text_diagnosa != '' ? $text_diagnosa : null,
                                'data' => $optDiagUtama,
                                'options' => [
                                    'placeholder' => '-- Pilih --',
                                    'class' => 'form-control input-sm select2'
                                ],
                                'pluginOptions' => [
                                    // 'allowClear' => true,
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    // 'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/igd/end-point/get-new-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                return {
                                                    q: params.term,
                                                    type: "diagnosa_utama",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                    page:params.page || 1
                                                }; 
                                            }
                                        '),
                                        'processResults' => new JsExpression('
                                            function (data, params) {
                                                            params.page = params.page || 1;
                                                            return {
                                                            results: data.result,
                                                                pagination: {
                                                                    more: data.pagination.more
                                                                }
                                                            }
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                ],
                            ]);
                        ?>

                       <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(),[
                            'showToggleAll' => false,
                            'data' => $optDiagPnyrt,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2'
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'maximumInputLength' => 50,
                                // 'allowClear' => true,
                                // 'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/igd/end-point/get-new-diagnosa']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            return {
                                                q: params.term,
                                                type: "diagnosa_penyerta",
                                                all_text: 0,
                                                id_with_text: 1,
                                                page:params.page || 1
                                            }; 
                                        }
                                    '),
                                    'processResults' => new JsExpression('
                                        function (data, params) {
                                                        params.page = params.page || 1;
                                                        return {
                                                        results: data.result,
                                                            pagination: {
                                                                more: data.pagination.more
                                                            }
                                                        }
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                            ],
                        ]);
                    ?>
                    </div>
                </div>

                <?= $form->field($model, 'planning')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3',
                ])->label('P') ?>
                <?= $form->field($model, 'catatan_dokter')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3'
                ])->label(Yii::t('fe', 'Catatan')) ?>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
                <?= Html::activeHiddenInput($model, 'pegawai_id') ?>
                <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                <?= Html::activeHiddenInput($model, 'tgl_cppt') ?>
                <?= Html::activeHiddenInput($model, 'ruangan_id') ?>
                <?= Html::activeHiddenInput($model, 'cppt_id') ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
    var cppt_id = "'.DocoHelpers::decrypt($cppt_id).'";
', View::POS_END);
$this->registerJs($this->render('js/form_soap.js'), View::POS_END) 
?>