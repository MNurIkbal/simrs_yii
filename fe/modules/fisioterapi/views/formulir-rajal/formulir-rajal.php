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
    <button type="button" id="btn-cetak-formulir-rawat-jalan"
        class="btn btn-info btn-labeled btn-xs data-pdf"
        style="margin-left:10px;margin-bottom:10px;" <?= !$hasData ? 'disabled' : '' ?>>
        <b><i class="fa fa-file-pdf-o"></i></b>Cetak PDF</button>

    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'formulir-rajal-form',
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
                <?= $form->field($model, 'anamnesa')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Input informasi hasil observasi ke pasien'
                ])->label('Anamnesa'); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'pemeriksaanfisik_dan_ujifungsi')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Inputan hasil pemeriksaan fisik dan uji fungsi'
                ])->label('Pemeriksaan Fisik dan Uji Fungsi'); ?>
            </div>
        </div>
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
                <?= $form->field($model, 'diag_kfr', [
                    'options' => [
                        'class' => 'form-group',
                    ],
                ])->widget(Select2::classname(), [
                    'showToggleAll' => false,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '-- Input tindakan ke pasien --'
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
                                        type: "diagnosa_kfr",
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
                ])->label('Tata Laksana KFR');
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'hasil_pemeriksaan_penunjang')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Inputan hasil pemeriksaan penunjang'
                ])->label('Hasil Pemeriksaan Penunjang'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'anjuran')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Input informasi anjuran'
                ])->label('Anjuran'); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'goal')->textArea([
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Input informasi goal'
                ])->label('Goal'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'evaluasi')->textInput([
                    'class' => 'form-control',
                    'placeholder' => 'Input informasi evaluasi'
                ])->label('Evaluasi'); ?>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Suspek Penyakit Akibat Kerja</label>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="suspek_penyakit_radio" id="suspek_tidak" value="Tidak" <?= !$model->is_suspek ? 'checked' : '' ?>>
                                <label class="form-check-label" for="suspek_tidak">
                                    Tidak
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check d-inline-block me-3">
                                <input class="form-check-input" type="radio" name="suspek_penyakit_radio" id="suspek_ya" value="Ya" <?= $model->is_suspek ? 'checked' : '' ?>>
                                <label class="form-check-label" for="suspek_ya">
                                    Ya
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'suspek_penyakit')->textInput([
                                'class' => 'form-control d-inline-block',
                                'placeholder' => 'Input suspek penyakit',
                                'value' => $model->is_suspek ? $model->suspek_penyakit : '',
                                'disabled' => !$model->is_suspek
                            ])->label(false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end() ?>
        <div class="row float-md-right" style="margin-top: 10px; margin-right: 10px;">
            <div class="col-sm-10"></div>
            <div class="col-sm-2">
                <button type="button" id="btn-save-formulir"
                    class="btn btn-info btn-labeled btn-xs data-save"
                    style="margin-left:10px;margin-bottom:10px; width: 100%;"><b><i class="fa fa-floppy-o"></i></b><?= $data ? 'Update' : 'Simpan' ?></button>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var pendaftaran_id = '" . $pendaftaranId . "';
" . $this->render('formulir-rajal.js'), View::POS_END, 'js');

?>



