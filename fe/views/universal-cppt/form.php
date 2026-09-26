<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-04 17:07:43
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use kartik\widgets\DateTimePicker;

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
        <h5 class="panel-title" id="header-form-soap"><?= Yii::t('fe', 'Tambah SOAP') ?></h5>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-soap',
					'enableAjaxValidation' => false,
					'enableClientValidation' => false,
                    'action' => '/ranap/pemeriksaan-rawat-inap/create-soap?id=' . $pendaftaran_id,
                    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>

                <div class="form-group field-cpptform-tgl_cppt" id="cpptDate-form-wrapper">
                    <label class="control-label" for="cpptform-tgl_cppt">Tanggal CPPT</label>
                    <?= DateTimePicker::widget([
                        'model' => $model,
                        'attribute' => 'tgl_cppt',
                        'options' => ['readonly'=>true],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd/mm/yyyy hh:ii:ss',
                            'startDate' => date('d/m/Y H:i:s', strtotime($tgl_pendaftaran)),
                            'endDate' => date('d/m/Y H:i:s', strtotime("+5 minute", strtotime("now"))),
                        ]
                    ]); ?>
                </div>
                <!-- Ruangan -->
                <?php
                    if(count($listRuangan) == 1) {
                        echo "<div class='form-group'>";
                        echo Html::label(Yii::t('fe', 'Ruangan'), 'nama_ruangan', ['class' => 'control-label required']);
                        echo "<div>";
                        echo Html::textInput('nama_ruangan', array_values($listRuangan)[0], ['class' => 'form-control input-sm', 'id' => 'nama_ruangan', 'disabled' => 'disabled']);
                        echo "</div>";
                        echo "<div class='help-block'></div>";
                        echo Html::hiddenInput('CpptForm[ruangan_id]', array_keys($listRuangan)[0]);
                        echo "</div>";
                    }else{
                        echo $form->field($model, 'ruangan_id')->widget(Select2::classname(), [
                            'data' => $listRuangan,
                            'options' => [
                                'class' => 'select2',
                                'placeholder' => '-- Pilih --'
                            ],
                        ]);
                    }
                ?>

                <!-- Subject -->
                <?= $form->field($model, 'subject')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '7',
                ])->label('Subjektif<span style="color:red; "> *</span>') ?>

                <!-- Object -->
                <?= $form->field($model, 'object')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '7',
                ])->label('Objektif<span style="color:red; "> *</span>') ?>

                <?php
                    $labelCheckbox = 'ICD X'; 
                    if ($is_perawat) $labelCheckbox = 'SDKI';
                    echo $form->field($model, 'is_icd_x')->checkbox([
                        'label' => $labelCheckbox,
                    ]);
                ?>
                <!-- Asesmen -->
                <div class="form-group">
              
                    <div class="">
                        <?= $form->field($model, 'a_diag_utama_text')->textarea([
                        'class' => 'form-control input-sm diag_utama_text',
                        'rows' => '7',
                        ])->label('Diagnosa Utama<span style="color:red; "> *</span>',['class'=>'label-class diag_utama_text_label']) ?>
     
                        <!-- Diagnosa utama -->
                        <?php echo $form->field($model, 'a_diag_utama')->widget(Select2::classname(), [
                            // 'initValueText' => isset($text_diagnosa) && $text_diagnosa != '' ? $text_diagnosa : null,
                            'options' => ['placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2'
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'minimumInputLength' => 3,
                                'allowClear' => true,
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
                                                page: params.page || 1,
                                                type: "diagnosa_utama",
                                                all_text: 0,
                                                id_with_text: 1,
                                                is_perawat: '.$is_perawat.'
                                            };
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                            ],
                        ])->label('Diagnosa Utama<span style="color:red; "> *</span>',['class'=>'label-class diag_utama_label'])  ?>

                        <!-- Diagnosa penyerta -->
                        <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(),[
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
                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            return {
                                                q: params.term,
                                                page: params.page || 1,
                                                type: "diagnosa_penyerta",
                                                all_text: 0,
                                                id_with_text: 1,
                                                is_perawat: '.$is_perawat.'
                                            };
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

                <!-- Planning -->
                <?= $form->field($model, 'planning')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '7',
                ])->label('Planning')->label('Planning<span style="color:red; "> *</span>')  ?>


                <?php if ($config_soap): ?>
                    <!-- Cek pegawai -->
                    <?php if ($pegawai['kelompokpegawai_namalainnya'] == 't_medis'): ?>
                        <!-- Catatan dokter -->
                        <?= $form->field($model, 'catatan_dokter')->textarea([
                            'class' => 'form-control input-sm',
                            'rows' => '7'
                        ]) ?>
                    <?php else: ?>
                        <!-- Catatan perawat -->
                        <?= $form->field($model, 'catatan_perawat')->textarea([
                            'class' => 'form-control input-sm',
                            'rows' => '7'
                        ]) ?>
                    <?php endif ?>
                    <?= $form->field($model, 'instruksi')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '7',
                    ])->label('Instruksi') ?>
                    <?= $form->field($model, 'is_instruksi_pulang')->checkbox() ?>
                    
                <?php else: ?>
                    <div class="panel panel-default">
                        <a id="info-heading" data-toggle="collapse" href="#catatan-instruksi" role="button" aria-expanded="false" aria-controls="catatan-instruksi">
                            <div class="panel-heading flex-container ">
                                <h6 class="panel-title text-bold">Catatan dan Instruksi</h6>
                                <ul class="icons-list">
                                    <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                </ul>
                            </div>
                        </a>

                        <div class="panel-body collapse multi-collapse lab" id="catatan-instruksi">
                            <?= $form->field($model, 'instruksi')->textarea([
                                'class' => 'form-control input-sm',
                                'rows' => '7',
                            ])->label('Instruksi') ?>
                            
                            <!-- Cek pegawai -->
                            <?php if ($pegawai['kelompokpegawai_namalainnya'] == 't_medis'): ?>
                                <!-- Catatan dokter -->
                                <?= $form->field($model, 'catatan_dokter')->textarea([
                                    'class' => 'form-control input-sm',
                                    'rows' => '7'
                                ]) ?>
                            <?php else: ?>
                                <!-- Catatan perawat -->
                                <?= $form->field($model, 'catatan_perawat')->textarea([
                                    'class' => 'form-control input-sm',
                                    'rows' => '7'
                                ]) ?>
                            <?php endif ?>

                            <?= $form->field($model, 'is_instruksi_pulang')->checkbox() ?>
                        </div>
                    </div>
                <?php endif ?>

                



                <!-- Hidden inputs -->
                <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
                <?= Html::activeHiddenInput($model, 'pegawai_id') ?>
                <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
                <?php ActiveForm::end() ?>
            </div>
            <div class="col-sm-12">
                <button type="button" id="btn-save-soap" style="width: 100%;" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
            </div>
        </div>
    </div>
    <!-- <div class="panel-footer">
        <div class="col-sm-12" id="button-wrapper">
            <button type="button" id="btn-add-terapi" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-plus"></i></b>Terapi</button>
            <button type="button" id="btn-add-instruksi-dpjp" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-plus"></i></b>Instruksi DPJP</button>
        </div>
    </div> -->
</div>
<?php
$this->registerJs('
    var autofill_diagnose = ' . json_encode($autofill_diagnose) . '
    var soapDate = "' . $model->tgl_cppt . '"
    var time_reset = "' . $time_reset . '"
    var id_ruangan = "' . $id_ruangan . '"
    var pegawai_id = "' . $pegawai['pegawai_id'] . '"
');
?>

<?php // File
$this->registerJs($this->render('js/form.js'), View::POS_END);
?>
