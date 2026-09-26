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
    <button type="button" id="btn-cetak-resume"
        class="btn btn-info btn-labeled btn-xs data-pdf"
        <?= $disabledCetakan ?>
        style="margin-left:10px;margin-bottom:10px;"><b><i class="fa fa-file-pdf-o"></i></b>Cetak PDF</button>

    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'resume-medis-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        ]);
        ?>
        <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
        <?= Html::activeHiddenInput($model, 'pasien_id') ?>
        <?= Html::activeHiddenInput($model, 'program_terapi_ids') ?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'tgl_masuk', [
                        'addon' => [
                            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                        ],
                    ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'tgl_keluar', [
                        'addon' => [
                            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                        ],
                    ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?php
                echo $form->field($model, 'diag_utama')->widget(Select2::classname(), [
                    'initValueText' => isset($model->diag_utama) && $model->diag_utama != '' ? $model->diag_utama : null,
                    'options' => [
                        'placeholder' => '-- Pilih --',
                        'class' => 'form-control input-sm'
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', '_'],
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
                                        page: params.page || 1,
                                    };
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                        'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                    ],
                ])->label($model->getAttributeLabel('diag_utama'), ['class' => 'text-bold has-star'])  ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'diag_penyerta', [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-2 control-label text-bold',
                        'wrapper' => 'col-md-5'
                    ],
                ])->widget(Select2::classname(), [
                    'showToggleAll' => false,
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
                            'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                            'dataType' => 'json',
                            'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q: params.term,
                                        page: params.page || 1,
                                        type: "diagnosa_penyerta",
                                        all_text: 0,
                                        id_with_text: 1,
                                        page: params.page || 1,
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
            </div>
            <div class="col-md-4">
                <?php echo $form->field($model, 'prosedur_diag')->widget(Select2::classname(), [
                    'showToggleAll' => false,
                    'options' => [
                        'placeholder' => '-- Pilih --',
                        'class' => 'form-control input-sm select2',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', '_'],
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
                                        type: "prosedur_kerja",
                                        all_text: 0,
                                        id_with_text: 1,
                                        page: params.page || 1,
                                    };
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                        'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                    ],
                ])->label() ?>
            </div>
        </div>
        <div class="row">
            <h5 class="panel-title" style="margin-left:10px;margin-bottom:20px;margin-top:10px;">Tanda-Tanda Vital</h5>
            <div class="col-md-4">
                <?= $form->field($model, 'berat_badan', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                ])->textInput([
                    'class' => 'form-control input-sm imt_field doco-decimal-wcomma',
                ]); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'tinggi_badan', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => Yii::t('fe', 'cm')]],
                ])->textInput([
                    'class' => 'form-control input-sm imt_field doco-decimal-wcomma tinggi-badan',
                ]); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'tekanan_darah', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'mm/Hg']],
                ])
                    ->textInput([
                        'id' => 'tekanan_darah',
                        'class' => 'form-control',
                        'placeholder' => 'Tekanan Darah',
                        'pattern' => '^\\d{1,3}/\\d{1,3}$',
                        'oninput' => "validateInput(this)",
                    ])
                    ->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'nadi', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'x/Menit']],
                ])
                    ->textInput(
                        [
                            'id' => 'nadi',
                            'class' => 'form-control doco-number doco-decimal-wcomma',
                            'placeholder' => 'Nadi',
                        ]
                    );
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'suhu', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => '&deg; Celcius']],
                ])
                    ->textInput(
                        [
                            'id' => 'suhu',
                            'class' => 'form-control doco-decimal-wcomma',
                            'placeholder' => 'Suhu',
                        ]
                    );
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'respirasi', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'x/Menit']],
                ])
                    ->textInput(
                        [
                            'id' => 'respirasi',
                            'class' => 'form-control doco-number doco-decimal-wcomma',
                            'placeholder' => 'Nafas',
                        ]
                    );
                ?>
            </div>
        </div>
        <div class="row">
            <h5 class="panel-title" style="margin-left:10px;margin-bottom:20px;margin-top:10px;">Asesmen Pasien</h5>
            <div class="col-md-4">
                <?= $form->field($model, 'subjective')->textArea(['class' => 'form-control']); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'objective')->textArea(['class' => 'form-control']); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'assesment')->textArea(['class' => 'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'planning')->textArea(['class' => 'form-control']); ?>
            </div>
        </div>
        <div class="row" style="text-align:right;">
            <button type="button" id="btn-save"
            class="btn btn-info btn-labeled btn-xs data-save"
            style="margin-left:10px;margin-bottom:10px;"><b><i class="fa fa-floppy-o"></i></b><?= $data ? 'Update' : 'Simpan' ?></button>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<?php

$this->registerJs("
    var pendaftaran_id = '" . $pendaftaranId . "';
    $('.selectDokter').select2()
" . $this->render('resume-medis.js'), View::POS_END, 'js');

?>



