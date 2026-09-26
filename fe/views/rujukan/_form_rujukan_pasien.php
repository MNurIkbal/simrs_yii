<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;

?>
<style>
    .textareaStyle {
        width: 130%;
        height: 90%;
    }

    .datepicker>div {
        display: block;
    }

    .required-field::after {
        color: red;
        content: " *";
    }

    .dataTables_scroll {
        height: auto !important;
    }
    .tabel-reseptur td,th {
        border: 1px solid #ddd;
    }
</style>
<div class="col-lg-12">

    <?php
    $form = ActiveForm::begin([
        'id' => 'pasien-rujuk-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'showErrors' => true,
            'labelSpan' => 4,
            'deviceSize' => ActiveForm::SIZE_SMALL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false
        ],
    ]);
    ?>

    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">FORMULIR RUJUKAN PASIEN ANTAR RUMAH SAKIT</h5>
            </div>
            <div class="panel-body">
                <div class="row form-row">
                    <div class="col-sm-8">
                        <?= $form->field($rujukanPulangForm, 'pendaftaran_id')->hiddenInput()->label(false); ?>
                        <?= $form->field($rujukanPulangForm, 'pasienadmisi_id')->hiddenInput()->label(false); ?>
                        <?= $form->field($rujukanPulangForm, 'rujukan_dituju', [
                            'labelOptions' => ['class' => 'required-field']
                        ]) ?>
                        <?= $form->field($rujukanPulangForm, 'pic_rujukan_dituju', [
                            'labelOptions' => ['class' => 'required-field']
                        ]) ?>
                        <?= $form->field($rujukanPulangForm, 'diagnosa_masuk', [
                            'labelOptions' => ['class' => 'required-field']
                        ]) ?>
                        <?= $form->field($rujukanPulangForm, 'diagnosa_keluar', [
                            'labelOptions' => ['class' => 'required-field']
                        ]) ?>
                        <?= $form->field($rujukanPulangForm, 'diagnosa_prb', [
                            'labelOptions' => ['class' => 'required-field']
                        ])->dropDownList($listDiagnosa, [
                            'prompt' => '-- Pilih --',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">I. RINGKASAN RIWAYAT PASIEN</h5>
            </div>
            <div class="panel-body">
                <h6 class="panel-title"><?= Yii::t('fe', 'Anamnesia') ?></h6>
                <div class="row form-row">
                    <div class="col-sm-8">
                        <?= $form->field($rujukanPulangForm, 'anamnesis_keluhan_utama') ?>
                        <?= $form->field($rujukanPulangForm, 'riwayat_penyakit_sekarang') ?>
                        <?= $form->field($rujukanPulangForm, 'riwayat_penyakit_dahulu') ?>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group highlight-addon field-tanda-vital1">
                            <label class="control-label col-sm-2"><?= Yii::t('fe', 'Tanda Vital') ?></label>
                            <div class="col-sm-10">
                                <!-- <div class="col-sm-3"> -->
                                <?php //$form->field($rujukanPulangForm, 'anamnesis_keluhan_utama')->textInput(['id' => 'rujukanpulangform-anamnesis_keluhan_utama_tanda-vital'])->label('KU') 
                                ?>
                                <!-- </div> -->
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_kesadaran')->textInput() ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_saturasi_o2')->textInput() ?>
                                </div>
                            </div>
                        </div>
                        <div class="help-block"></div>
                        <div class="form-group highlight-addon field-tanda-vital2">
                            <label class="control-label col-sm-2"></label>
                            <div class="col-sm-10">
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_tensi', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput() ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput() ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_nadi', ['addon' => ['append' => ['content' => 'x/mnt']]])->textInput() ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($rujukanPulangForm, 'anamnesis_pernafasan', ['addon' => ['append' => ['content' => 'x/mnt']]])->textInput()->label('RR') ?>
                                </div>
                            </div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($rujukanPulangForm, 'alasan_dirujuk')->textarea(['rows' => '3'], ['class' => 'form-control']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">II. PEMERIKSAAN PENUNJANG YANG SUDAH DI LAKUKAN</h5>
            </div>
            <div class="panel-body">
                <div class="row form-row">
                    <div class="col-sm-12">
                        <?= $form->field($rujukanPulangForm, 'pemeriksaan_penunjang')->textarea(['rows' => '3', 'cols' => 5, 'value' => $penunjangValue], ['class' => 'form-control textareaStyle'])->label(false); ?>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">III. TINDAKAN MEDIS YANG SUDAH DI LAKUKAN</h5>
            </div>
            <div class="panel-body">
                <div class="row form-row">
                    <div class="col-sm-12">
                        <?= $form->field($rujukanPulangForm, 'tindakan_medis')->textarea(['rows' => '3', 'cols' => 5, 'value' => $tindakanValue], ['class' => 'form-control textareaStyle'])->label(false); ?>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">IV. PEMBERIAN TERAPI</h5>
            </div>
            <div class="panel-body">
                <div class="row form-row">
                    <div class="col-sm-12">
                        <?= $form->field($rujukanPulangForm, 'tindakan_terapi')->textarea(['rows' => '3', 'cols' => 5, 'value' => $terapiValue], ['class' => 'form-control textareaStyle'])->label(false); ?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="row form-row reseptur hidden">

                    <div class="col-md-6">
                        <div class="legend-index">
                            <div class="legend-header">Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: #ffc0cb"></div>
                                    <div class="legend-information__text">Obat BPJS</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 tabel-reseptur">
                        <table class="table datatable-basic table-striped table-hover dataTable" id="tabel-reseptur" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No.</th>
                                    <th>R Ke-</th>
                                    <th>Obat</th>
                                    <th>Signa</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data Belum Tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-8" style="padding-right: 20px;">
        <div class="row form-row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">V. LAIN-LAIN</h5>
                </div>
                <div class="panel-body">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= $form->field($rujukanPulangForm, 'tindakan_lainnya')->textarea(['rows' => '3', 'cols' => 5], ['class' => 'form-control textareaStyle'])->label(false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="row form-row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">Dokter Pengirim / DPJP</h5>
                </div>
                <div class="panel-body">
                    <div class="row form-row">
                        <div class="col-sm-12" style="margin-top: 9%;">
                            <?= $form->field($rujukanPulangForm, 'pegawai_nama')->textInput(['disabled' => true])->label(false) ?>
                            <?= $form->field($rujukanPulangForm, 'pegawai_kode_bpjs', [
                                'labelOptions' => ['style' => 'margin-right: -199px;']
                            ])->dropDownList($listDpjp, [
                                'prompt' => '-- Pilih --',
                                'disabled' => true
                            ])->label(false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6" style="padding-right: 20px;">
        <div class="row form-row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">VI. KATEGORI & PENDAMPING PASIEN TRANSFER</h5>
                </div>
                <div class="panel-body">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <h6 class="panel-title col-sm-4"><?= Yii::t('fe', 'Derajat Pasien') ?></h6>
                            <h6 class="panel-title col-sm-5"><?= Yii::t('fe', 'Nama Petugas Pendamping') ?></h6>
                            <?= $form->field($rujukanPulangForm, 'derajat_0') ?>
                            <?= $form->field($rujukanPulangForm, 'derajat_1') ?>
                            <?= $form->field($rujukanPulangForm, 'derajat_2') ?>
                            <?= $form->field($rujukanPulangForm, 'derajat_3') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="row form-row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">VII. KONDISI PASIEN</h5>
                </div>
                <div class="panel-body">
                    <h6 class="panel-title"><?= Yii::t('fe', 'Sebelum Dirujuk') ?></h6>
                    <div class="row form-row">
                        <div class="col-sm-6">
                            <?= $form->field($rujukanPulangForm, 'tanggal_rujukan')
                                ->widget(DatePicker::className(), [
                                    // 'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'name' => 'tanggal_rujukan',
                                    'readonly' => true,
                                    'language' => 'en',
                                    // 'convertFormat' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-mm-yyyy',
                                    ]
                                ])->label('Tanggal');
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon field-tanda-vital1">
                                <label class="control-label col-sm-2"><?= Yii::t('fe', 'Jam') ?></label>
                                <div class="col-sm-3">
                                    <input name="jam_rujukan" type="input" class="form-control jam_rujukan" value="<?= $jam ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= $form->field($rujukanPulangForm, 'keadaan_umum') ?>
                            <?= $form->field($rujukanPulangForm, 'kesadaran') ?>
                        </div>
                    </div>
                    <h6 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Tanda-tanda vital') ?></h6>
                    <div class="row form-row">
                        <div class="col-sm-6">
                            <?= $form->field($rujukanPulangForm, 'tensi', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput() ?>
                            <?= $form->field($rujukanPulangForm, 'suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput() ?>
                            <?= $form->field($rujukanPulangForm, 'saturasi_o2', ['addon' => ['append' => ['content' => 'SpO2']]])->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($rujukanPulangForm, 'nadi', ['addon' => ['append' => ['content' => 'x/mnt']]])->textInput() ?>
                            <?= $form->field($rujukanPulangForm, 'pernafasan', ['addon' => ['append' => ['content' => 'x/mnt']]])->textInput()->label('RR') ?>
                        </div>
                    </div>
                    <h6 class="panel-title"><?= Yii::t('fe', 'Catatan Penting') ?></h6>
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= $form->field($rujukanPulangForm, 'catatan_penting')->textarea(['rows' => '3', 'cols' => 5], ['class' => 'form-control textareaStyle'])->label(false); ?>
                        </div>
                    </div>
                    <h6 class="panel-title"><?= Yii::t('fe', 'Petugas Yang Menyerahkan Pasien') ?></h6>
                    <div class="row form-row">
                        <?= $form->field($rujukanPulangForm, 'pegawai_id', [
                            'labelOptions' => ['style' => 'margin-right: -199px;']
                        ])->dropDownList($pegawai, [
                            'prompt' => '-- Pilih --',
                            'class' => 'form-control select2',
                            'style' => 'padding:9px!important;',
                            'id' => 'pegawai_id'
                        ])->label(false . Html::tag('span', '', ['class' => 'required'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?php $this->registerJs('
    var dpjp = "'.$rujukanPulangForm->pegawai_nama.'"
' . $this->render('_form_rujukan_pasien.js'), View::POS_END) ?>
