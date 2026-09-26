<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use kartik\datetime\DateTimePicker;
?>
<nav class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'custom-save' => [
                        'title' => Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-save-asesmen-perawat',
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</nav>

<?php
$form = ActiveForm::begin([
    'id' => 'form-asesmen-keperawatan',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-2">
        <?= $form->field($model, 'tgl_asesmen')->textInput(['readonly' => true]); ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'perawat_nama')->textInput(['readonly' => true]); ?>
    </div>
    <?= Html::activeHiddenInput($model, 'perawat_id') ?>
    <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
    <?= Html::activeHiddenInput($model, 'ruangan_id') ?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" href="#collapse-triage">
                <h5 class="panel-title"><?= Yii::t('fe', 'Formulir 1 - Triage') ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse" data-toggle="collapse" href="#collapse-triage"></a></li>
                    </ul>
                </div>
            </div>
            <div id="collapse-triage" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?= $form->field($model, 'tgl_pendaftaran')->widget(DateTimePicker::className(), [
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd-MM-yyyy HH:mm:ss',
                                        'autoclose' => true,
                                        'todayBtn' => true
                                    ]
                                ]); ?>
                                <?= $form->field($model, 'tgl_datang')->widget(DateTimePicker::className(), [
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd-MM-yyyy HH:mm:ss',
                                        'autoclose' => true,
                                        'todayBtn' => true
                                    ]
                                ]); ?>
                                <?= $form->field($model, 'prioritas_triage'); ?>
                                <?= $form->field($model, 'pasien_datang')
                                    ->dropDownList(
                                        $data_pengantar,
                                        [
                                            'class' => 'select2',
                                            'prompt' => '--Pilih--'
                                        ]
                                    ); ?>
                                <?= $form->field($model, 'jenis_asmenperawat')
                                    ->dropDownList(
                                        $data_jenisasmen,
                                        [
                                            'class' => 'select2',
                                            'prompt' => '--Pilih--'
                                        ]
                                    ); ?>
                                <?= $form->field($model, 'alasan_kunjungan')->textArea(); ?>
                                <?= $form->field($model, 'is_alergi')->radioList($list_ada_tidak); ?>
                                <?= $form->field($model, 'is_alergiobat')->checkbox(); ?>
                                <?= $form->field($model, 'alergi_obat')->textArea(); ?>
                                <?= $form->field($model, 'is_alergilainnya')->checkbox(); ?>
                                <?= $form->field($model, 'alergi_lainnya')->textArea(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?= $form->field($model, 'keadaan_umum')->radioList($data_keadaanumum); ?>
                                <?= $form->field($model, 'is_nyeri')->radioList($list_ya_tidak); ?>
                                <?= $form->field($model, 'lokasi_nyeri'); ?>
                                <?= $form->field($model, 'skala_nyeri')->textInput(['class' => 'docoNumberOnly']); ?>
                                <?= $form->field($model, 'metode_nyeri')->radioList($data_asmennyeri); ?>
                                <?= $form->field($model, 'is_resikojatuh')->radioList($list_ya_tidak); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" href="#collapse-fisik">
                <h5 class="panel-title"><?= Yii::t('fe', 'Formulir 2 - Fisik') ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse" data-toggle="collapse" href="#collapse-fisik"></a></li>
                    </ul>
                </div>
            </div>
            <div id="collapse-fisik" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?= $form->field($model, 'keluhan')->textArea([
                                        'class' => 'form-control input-sm input-tags',
                                        'data-role' => 'tagsinput',
                                    ]); ?>
                                    <?= $form->field($model, 'lama_sakit', [
                                        'addon' => ['append' => ['content' => 'hari']]
                                    ])->textInput(['class' => 'docoNumberOnly']); ?>
                                    <?= $form->field($model, 'r_penyakitdahulu')->widget(Select2::classname(), [
                                        'showToggleAll' => false,
                                        'options' => [
                                            'multiple' => true,
                                            'placeholder' => '-- Pilih --',
                                            'class' => 'form-control input-sm select2'
                                        ],
                                        'pluginOptions' => [
                                            'tags' => true,
                                            'tokenSeparators' => [',', '_'],
                                            // 'maximumInputLength' => 50,
                                            // 'allowClear' => true,
                                            // 'minimumInputLength' => 3,
                                            'language' => [
                                                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                            ],
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('
                                                    function(params) {
                                                        return {
                                                            q: params.term,
                                                            type: "diagnosa_masuk",
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
                                            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                            'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                                        ],
                                    ]); ?>
                                    <?= $form->field($model, 'r_penyakitkeluarga'); ?>
                                    <?= $form->field($model, 'catatan_asesmen'); ?>

                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h6 class="panel-title"><?= Yii::t('fe', 'Glasgow coma scale') ?></h6>

                                        </div>
                                        <div class="panel-body">
                                            <div class="col-md-12">
                                                <?= $form->field($model, 'gcseye_id')->dropDownList(ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'nama_and_nilai'), ['class' => 'select2 gcs_eye', 'prompt' => '--Pilih--', 'options' => $gcsEyeOptions]) ?>
                                                <?= $form->field($model, 'gcsverbal_id')->dropDownList(ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'nama_and_nilai'), ['class' => 'select2 gcs_verbal', 'prompt' => '--Pilih--', 'options' => $gcsVerbalOptions]) ?>
                                                <?= $form->field($model, 'gcsmotorik_id')->dropDownList(ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'nama_and_nilai'), ['class' => 'select2 gcs_motorik', 'prompt' => '--Pilih--', 'options' => $gcsMotorikOptions]) ?>

                                                <?= $form->field($model, 'nilai_gcs')->textInput(['class' => 'nilai_gcs', 'readonly' => true]); ?>
                                                <?= $form->field($model, 'is_kapitis')->checkbox() ?>
                                                <?= $form->field($model, 'hasil_gcs')->textInput(['class' => 'hasil_gcs', 'readonly' => true]) ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Tanda Vital') ?></h6>

                                </div>
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <!-- <?= $form->field($model, 'tekanan_darah'); ?> -->
                                        <?= $form->field($model, 'td_systolic', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => 'MmHg']],
                                        ])->textInput(['class' => 'doco-decimal td_field']) ?>
                                        <?= $form->field($model, 'td_diastolic', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => 'MmHg']],
                                        ])->textInput(['class' => 'doco-decimal td_field']) ?>
                                        <?= $form->field($model, 'hasil_td')->textInput(['class' => 'hasil-td', 'readonly' => true]); ?>
                                        <?= $form->field($model, 'detak_nadi', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                                        ])->textInput(['class' => 'doco-decimal']) ?>
                                        <?= $form->field($model, 'pernapasan', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                                        ])->textInput(['class' => 'doco-decimal']) ?>
                                        <?= $form->field($model, 'suhu_tubuh', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => '&deg; Celcius']],
                                        ])->textInput(['class' => 'doco-decimal']) ?>
                                        <?= $form->field($model, 'tinggi_badan', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => Yii::t('fe', 'cm')]],
                                        ])->textInput(['class' => 'doco-decimal imt_field']) ?>
                                        <?= $form->field($model, 'berat_badan', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                                        ])->textInput(['class' => 'doco-decimal imt_field']) ?>
                                        <?= $form->field($model, 'bb_ideal', [
                                            'labelOptions' => ['class' => ''],
                                            'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                                        ])->textInput(['readonly' => true]); ?>
                                        <?= $form->field($model, 'imt')->textInput(['readonly' => true]); ?>
                                        <?= $form->field($model, 'ket_imt')->textInput(['readonly' => true]); ?>
                                        <?= $form->field($model, 'spo2'); ?>
                                        <?= $form->field($model, 'kelaianan_tubuh'); ?>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
<?php ActiveForm::end(); ?>


<?php

$this->registerJs("
        var dataPenyakit = " . json_encode($callbackDiagPenyerta) . "
        var dataGcs = " . json_encode($data_gcs) . "
        var data_bmi = " . json_encode($data_bmi) . "
        var jeniskelamin = " . $jeniskelamin . "
        var lakilaki = " . DocoConstants::LOOKUP_LAKI . "
    " . $this->render('_index.js'), View::POS_END);
?>
