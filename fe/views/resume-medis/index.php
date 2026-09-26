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
    #lab-order-table>tbody>tr>td {
        padding-bottom: 8px !important;
    }

    .text-label-size {
        font-size: 1.1em;
    }

    .datepicker>div {
        display: block;
    }

    textarea {
        resize: none;
    }
</style>
<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Ringkasan Pasien Pulang (Discharge Summary)') ?></h6>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'pdf' => [
                    'attributes' => [
                        'data-target' => 'cetak-resume?pendaftaran_id=' . $pendaftaran_id . '&pasien_id=' . ArrayHelper::getValue($patient_record, 'pasien_id', ''),
                        'data-options' => 'click',
                        'id' => 'btn-cetak-resume',
                        'disabled' => isset($model->resumemedisri_id) && $model->resumemedisri_id != null ? false : true,
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="panel-body">
            <div hidden="hidden" id="tglpasienpulang" data-tgl_pasien_pulang="<?php echo ArrayHelper::getValue($patient_record, 'tgl_pasien_pulang', ''); ?>" is_stopakomodasi="<?php echo ArrayHelper::getValue($patient_record,' is_stopakomodasi', FALSE); ?>"></div>
            <?php

            $form = ActiveForm::begin([
                'id' => 'resume-medis-ri-form',
                'type' => ActiveForm::TYPE_VERTICAL,
                // 'type' => ActiveForm::TYPE_HORIZONTAL,
                // 'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
            ?>
            <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
            <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                    <?= $form->field($model, 'tgl_masuk', [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-2 control-label text-bold',
                                'wrapper' => 'col-md-5'
                            ],
                        ])->widget(DatePicker::classname(), [
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'readonly' => true,
                            'value' => date('Y-m-d'),
                            'language' => 'en',
                            'pluginOptions' => [
                                'startDate' => !empty($patient_record['tgl_pendaftaran']) ? date('d-M-Y', strtotime($patient_record['tgl_pendaftaran'])) : date('d-M-Y'),
                                'endDate' => '0d',
                                'format' => 'dd-M-yyyy',
                                'autoclose' => true,
                                'todayBtn' => true
                            ],
                            'pluginEvents' => [
                                'changeDate' => 'function(e) {
                                    $("#resumemedisform-tgl_keluar-kvdate").datepicker("setStartDate", new Date(e.date));
                                }',
                            ]
                        ])->label($model->getAttributeLabel('tgl_masuk'), ['class' => 'text-bold']); ?>

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <?= $form->field($model, 'tgl_keluar', [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-2 control-label text-bold',
                                'wrapper' => 'col-md-5'
                            ],
                        ])->widget(DatePicker::classname(), [
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'readonly' => true,
                            'value' => date('Y-m-d'),
                            'language' => 'en',
                            'pluginOptions' => [
                                'startDate' => !empty($patient_record['tgl_pendaftaran']) ? date('d-M-Y', strtotime($patient_record['tgl_pendaftaran'])) : date('d-M-Y'),
                                'endDate' => '0d',
                                'format' => 'dd-M-yyyy',
                                'autoclose' => true,
                                'todayBtn' => true
                            ]
                        ])->label($model->getAttributeLabel('tgl_keluar'), ['class' => 'text-bold']); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?php
                    echo $form->field($model, 'diag_awal', [
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-2 control-label text-bold',
                            'wrapper' => 'col-md-5'
                        ],

                    ])->widget(Select2::classname(), [
                        'initValueText' => isset($model->diag_awal_text) && $model->diag_awal_text != '' ? $model->diag_awal_text : null,
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
                                            type: "diagnosa_utama",
                                            all_text: 0,
                                            id_with_text: 1,
                                            is_perawat: ' . $is_perawat . '
                                        };
                                    }
                                ')
                            ],
                            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                            'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                        ],
                    ])->label($model->getAttributeLabel('diag_awal'), ['class' => 'text-bold has-star'])  ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dokter_pengirim')->textInput()->label($model->getAttributeLabel('dokter_pengirim'), ['class' => 'text-bold']); ?>
                </div>
            </div>
            <div class="row">
                <!-- <label class="text-label-size control-label col-md-3 text-bold" for="">Diagnosa Keluar</label> -->
                <h5 style="margin-left: 10px;" class="text-bold">Diagnosa Keluar</h5>
                <div class="col-md-4">
                    <?= $form->field($model, 'diag_utama')->textArea(['rows' => 10]); ?>
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
                                                    type: "diagnosa_penyerta",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                    is_perawat: ' . $is_perawat . '
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
            </div>
            <?= Yii::$app->controller->renderPartial('//resume-medis/anamnesa', [
                'model' => $model,
                'form' => $form
            ]); ?>
            <hr>
            <?= Yii::$app->controller->renderPartial('//resume-medis/hasil_laboratorium', [
                'model' => $model,
                'form' => $form,
            ]); ?>
            <?= Yii::$app->controller->renderPartial('//resume-medis/hasil_radiologi', [
                'model' => $model,
                'form' => $form
            ]); ?>
            <?= Yii::$app->controller->renderPartial('//resume-medis/prosedur_terapi', [
                'model' => $model,
                'form' => $form
            ]); ?>
            <div class="form-group row">
                <div class="col-md-4">
                    <?= $form->field($model, 'obat_rs')->textArea(['rows' => 5])->label(Yii::t('fe', 'Obat RS'), ['class' => 'text-bold']); ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'is_alergi')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'is_alergi'])->label(Yii::t('fe', 'Alergi'), ['class' => 'text-bold']); ?>
                    <?= $form->field($model, 'nama_alergi')->textInput()->label($model->getAttributeLabel('nama_alergi'), ['class' => 'text-bold']); ?>
                </div>
            </div>
            <h5 style="margin-left: 3px;">Kondisi Saat Pulang</h5>
            <div class="form-group row">
                <div class="col-md-6">
                    <?=$form->field($model, 'keadaan_umum')->dropDownList([
                        'Baik' => 'Baik',
                        'Tampak Sakit Ringan' => 'Tampak Sakit Ringan',
                        'Tampak Sakit Berat' => 'Tampak Sakit Berat',
                        'Lain-Lain' => 'Lain-Lain',
                    ], ['prompt' => 'Pilih', 'options'=> []])->label($model->getAttributeLabel('keadaan_umum'), ['class' => 'text-bold'])?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'kesadaran')->dropDownList([
                        'Compos Mentis (GCS 14-15)' => 'Compos Mentis (GCS 14-15)',
                        'Apatis (GCS 12-13)' => 'Apatis (GCS 12-13)',
                        'Somnolen (GCS 10-11)' => 'Somnolen (GCS 10-11)',
                        'Delirium (GCS 9-7)' => 'Delirium (GCS 9-7)',
                        'Stupor (Soporos Coma) (GCS 4-6)' => 'Stupor (Soporos Coma) (GCS 4-6)',
                        'Koma (GCS 3)' => 'Koma (GCS 3)',
                    ], ['prompt' => 'Pilih', 'options'=> []])->label($model->getAttributeLabel('kesadaran'), ['class' => 'text-bold']); ?>
                </div>
            </div>

            <h5 style="margin-left: 3px;" class="text-bold">Tanda Vital</h5>
            <div class="form-group row">
                <div class="col-md-3">
                    <?= $form->field($model, 'td')->textInput(['class' => 'imt_field'])->label(Yii::t('fe', 'Tekanan Darah')); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'suhu')->textInput(['class' => 'doco-decimal-wcomma imt_field'])->label($model->getAttributeLabel('suhu')); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'nadi')->textInput(['class' => 'doco-decimal-wcomma imt_field'])->label($model->getAttributeLabel('nadi')); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'frekuensi_nafas')->textInput(['class' => 'doco-decimal-wcomma imt_field'])->label($model->getAttributeLabel('frekuensi_nafas')); ?>
                </div>
            </div>

            <h5 style="margin-left: 3px;" class="text-bold">Cara Keluar Rumah Sakit</h5>
            <div class="form-group row">
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'cara_keluar')->radioList($list_cara_keluar, ['inline' => true, 'class' => 'cara_keluar'])->label(Yii::t('fe', 'Cara Keluar Rumah Sakit'), ['class' => 'text-bold'])->label(false);
                    ?>
                </div>
            </div>
            <h5 style="margin-left: 3px;">Obat Yang Dibawa Pulang</h5>
            <div class="form-group row">
                <div class="col-md-12">
                    <?= $form->field($model, 'obat_dibawa_pulang_text')->textArea(['rows' => 10])->label(false); ?>
                </div>
            </div><br>
            <div class="form-group row">
                <div class="col-md-7">
                    <h5 style="margin-left: 3px;" class="text-bold">Instruksi Untuk Tindak Lanjut</h5>
                </div>
                <div class="col-md-4">
                    <h5 style="margin-left: 3px;" class="text-bold">Dalam Keadaan Darurat Dapat Menghubungi</h5>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <?= $form->field($model, 'instruksi')->textInput()->label(Yii::t('fe', 'Kontrol Ke'), ['class' => 'text-bold']); ?>
                </div>
                <div class="col-md-3" style="border-right: 1px solid #f0f0f0">
                    <?= $form->field($model, 'instruksi_tanggal')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'value' => date('Y-m-d'),
                        'readonly' => true,
                        'language' => 'en',
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                            'startDate' => empty($model->tgl_masuk) ? date('d-m-Y') : date('d-m-Y', strtotime($model->tgl_masuk)),
                        ]
                    ])->label(Yii::t('fe', 'Tanggal'), ['class' => 'text-bold']); ?>
                </div>

                <div class="col-md-1">
                    <br>
                    <?= $form->field($model, 'is_igd')->checkbox(['label' => 'IGD']); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'kontak_darurat')->textInput()->label(Yii::t('fe', 'Telepon'), ['class' => 'text-bold']); ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <?= $form->field($model, 'edukasi_rencana')->textArea(['rows' => 5])->label(Yii::t('fe', 'Edukasi Dan Rencana Tindak Lanjut (bila diperlukan)'), ['class' => 'text-bold']); ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <?= $form->field($model, 'konsultasi')->textArea(['rows' => 5]); ?>
                </div>
            </div>
            <div class="row" style="margin-top: 10px">
                <div class="col-md-2 col-md-offset-10 text-right">
                    <button type="submit" class="btn btn-info btn-labeled btn-xs btn-simpan-resume-medis" style="width: 100%" <?php echo $disabled; ?>>
                        <b><i class="fa fa-save"></i></b>
                        <?=($model->resumemedisri_id == '' || $model->resumemedisri_id == NULL) ? 'Simpan Resume Medis' : 'Update Resume Medis' ?>
                    </button>
                    <button type="button" id="btn-simpan-resume-medis-auto" class="btn btn-info btn-labeled btn-xs" style="width: 100%; display: none"></button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
            <br>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="lab-order-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Hasil Lab</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover" id="lab-order-table">
                            <thead>
                                <tr class="bg-inverse">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal Hasil</th>
                                    <th class="text-center">
                                        <div class="row">
                                            <div class="col-md-2" style="text-align: left;">
                                                <input type="checkbox" class="checkbox-lab" style="border-color: white !important; color: white !important; opacity: 1 !important">
                                            </div>
                                            <div class="col-md-9">
                                                Pemeriksaan
                                            </div>
                                        </div>
                                    </th>
                                    <th class="text-center">Hasil</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="lab-external-order-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Hasil Lab</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover" id="lab-external-order-table">
                            <thead>
                                <tr class="bg-inverse">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal Hasil</th>
                                    <th class="text-center">
                                        <div class="row">
                                            <div class="col-md-2" style="text-align: left;">
                                                <input type="checkbox" class="checkbox-lab-external" style="border-color: white !important; color: white !important; opacity: 1 !important">
                                            </div>
                                            <div class="col-md-9">
                                                Pemeriksaan
                                            </div>
                                        </div>
                                    </th>
                                    <th class="text-center">Hasil</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="rad-order-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Hasil Radiologi</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover" id="rad-order-table">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>&nbsp;</th>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal Hasil</th>
                                    <th class="text-center">Pemeriksaan</th>
                                    <th class="text-center">Kesan</th>
                                    <th class="text-center">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="choose-margin-resumemedis-modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Set Margin Cetak</h5>
            </div>
            <div class="modal-body">
                <div class="col-md-8">
                    <?php
                    echo Html::input('text', 'margin-top', 0, [
                        'class' => 'form-control docoNumberOnly',
                        'label' => 'Border Atas',
                        'id' => 'margin-top-resumemedis'
                    ]);
                    ?>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success btn-sm btn-print-resumemedis">Cetak</button>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="tindakan-order-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title"><?= Yii::t('fe', 'Tindakan Medis & Obat-Obatan/Terapi Selama di Rumah Sakit');?></h5>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJsFile(
    '/js/jquery.mask.min.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ]
    ]
);

$this->registerJs("
    var pendaftaran_id = '" . $pendaftaran_id . "';
    var pasien_id = '" . ArrayHelper::getValue($patient_record, 'pasien_id', '') . "';
    var pegawai_id = '".ArrayHelper::getValue(Yii::$app->session->get('user_identity'), 'id_pegawai')."';
    var diagnosaPenyerta = " . json_encode($model->diag_penyerta_json) . ";
    var status = '" . $status_disabled . "';
    var _orderData = {
        tindakan: '" .addslashes($tindakan). "',
        prosedur: ". json_encode($model->prosedur_list).",
    }
    var is_perawat = '" . json_encode($is_perawat) . "'
    $('#resumemedisform-keadaan_umum').select2()
    $('#resumemedisform-kesadaran').select2()
" . $this->render('index.js'), View::POS_END, 'js');

?>
