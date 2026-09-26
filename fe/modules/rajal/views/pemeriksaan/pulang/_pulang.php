<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-26 11:06:18
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-14 14:45:03
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use kartik\widgets\Select2;
use app\components\DocoHelpers;
use yii\web\JsExpression;
use yii\web\View;
use app\components\DHtml;

?>
<style type="text/css">
    .select2-selection__clear::after {
        content: '';
    }
</style>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tindak Lanjut</h5>
</div>
<div class="modal-body">
    <?php
    if ($morbiditas === false) {
    ?>
        <br>
        <div class="alert alert-danger" role="alert"><b><?= $message ?></b></div>
    <?php
    }
    ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-pemulanganpasien',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
            ?>
            <?=Html::hiddenInput('PasienPulangForm[pasien_id]', $data_pasien["pasien_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[pendaftaran_id]', $data_pasien["pendaftaran_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[ruanganakhir_id]', $data_pasien["ruangan_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[tgl_pendaftaran]', $data_pasien["tgl_pendaftaran"]);?>
            <?=Html::hiddenInput('PasienPulangForm[tgl_konsulpoli]', $data_pasien["tgl_konsulpoli"]);?>
            <?=Html::hiddenInput('PasienPulangForm[nosep]', $data_pasien["nosep"]);?>
            <?=Html::activeHiddenInput($modelpulangpasien, 'is_meninggal', ['class' => 'is-meninggal'])?>

            <div class="form-group required">
                    <label for="Tanggal Pulang" class="col-lg-4 control-label">
                            <?= Yii::t('fe', 'Tanggal'); ?>
                    </label>
                    <div class="col-lg-8">
                    <!-- <b><span id="tglpasienpulang"></span></b>
                        <?= Html::hiddenInput('PasienPulangForm[tglpasienpulang]', $modelpulangpasien->tglpasienpulang);?>-->
                        <?=DateTimePicker::widget([
                                'model' => $modelpulangpasien,
                                'attribute' => 'tglpasienpulang',
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    // 'id' => 'tglpasienpulang',
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'startDate' => $tglpendaftaran,
                                    'endDate' => date('Y-m-d H:i:s'),
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    //'endDate' => date('Y-m-d H:i:s')
                                ]
                            ]);
                        ?>
                    </div>
                    <div class="form-jenazah hidden">
                        <label for="tempat_kematian" class="col-lg-4 control-label">
                            <?= Yii::t('fe', 'Tempat Kematian'); ?>
                        </label>
                        <div class="col-lg-8">
                            <?= Html::activeTextInput($modelpulangpasien, 'tempat_kematian',[
                                'class' => 'form-control'
                            ])?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            <div class="form-group required">
                <label for="carakeluar_id" class="col-lg-4 control-label">
                    <?= Yii::t('fe', 'Cara Pulang'); ?>
                </label>
                <div class="col-lg-8">
                    <select name="PasienPulangForm[carakeluar_id]" id="pasienpulangform-carakeluar_id" class="select2 autoJenisKasusPenyakit">
                        <option value="">-- Pilih --</option>
                        <?php foreach ($caraKeluar as $key => $value) : ?>
                            <option value="<?= $value['carakeluar_id'] ?>" data-freetext="<?= $value['is_freetext'] ?>"><?= $value['carakeluar_nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error_PelayananJenazahFormcarakeluar_id"></div>
                </div>
                <div class="form-jenazah hidden">
                    <label for="kualifikasi_pemeriksa" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Kualifikasi Pemeriksa'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'kualifikasi_pemeriksa',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="col-lg-12 <?= empty($data_pasien['bpjs_id']) || empty($data_pasien['rujukan_id']) ? 'hidden' : ''?>"> 
                    <?= Html::activeCheckbox($modelpulangpasien, 'is_prb', ['disabled' => true, 'data' => [
                        'bpjs-rujukan' => empty($data_pasien['bpjs_id']) || empty($data_pasien['rujukan_id']) ? 0 : 1
                    ]])?>
                </div>
            </div>
            <div class="form-jenazah hidden">
                <div class="form-group required">
                    <label for="no_surat_kematian" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Nomor Surat Kematian'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'no_surat_kematian',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="carakeluar_id" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Tanggal Meninggal'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= DateTimePicker::widget([
                            'model' => $modelpulangpasien,
                            'attribute' => 'tgl_meninggal',
                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                            'readonly' => true,
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                'autoclose' => true,
                                'todayBtn' => true,
                                'endDate' => date('Y-m-d H:i:s')
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="form-jenazah hidden">
                    <label for="waktu_pemeriksaan_jenazah" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Waktu Pemeriksaan Jenazah'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?=DateTimePicker::widget([
                                'model' => $modelpulangpasien,
                                'attribute' => 'waktu_pemeriksaan_jenazah',
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    'endDate' => date('Y-m-d H:i:s')
                                ]
                            ]);
                        ?>
                    </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-8 col-lg-offset-2 hidden">
                        <?= Html::activeCheckbox($modelpulangpasien, 'persetujuanpelayanan', ['class' => 'persetujuan-pelayanan']) ?>
                    </div>
                </div>
            </div>

            <div class="form-jenazah hidden">
                <div class="form-group">
                    <label for="status_jenazah" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Status Jenazah'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'status_jenazah',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="dasar_diagnosis" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Dasar Diagnosis'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'dasar_diagnosis',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tgl_kremasi" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Tanggal dimakamkan/dikremasi'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?=DateTimePicker::widget([
                                'model' => $modelpulangpasien,
                                'attribute' => 'tgl_kremasi',
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                    'endDate' => date('Y-m-d H:i:s')
                                ]
                            ]);
                        ?>
                    </div>
                    <label for="penyebab_dasar" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Dasar'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_dasar',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nama_pemeriksa_jenazah" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Nama Pemeriksa Jenazah'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'nama_pemeriksa_jenazah',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="kondisi_lain" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Kondisi Lain'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'kondisi_lain',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="kelompok_kematian" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Kelompok Kematian'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'kelompok_kematian',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="penyebab_lain_bayi" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Lain Bayi'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_lain_bayi',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="penyebab_langsung" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Langsung'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_langsung',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="penyebab_lain_ibu" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Lain Ibu'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_lain_ibu',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="penyebab_antara" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Antara'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_antara',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="hubungan_penerima" class="col-lg-4 control-label">
                            <?= Yii::t('fe', 'Hubungan Penerima'); ?>
                        </label>
                        <div class="col-lg-8">
                            <?= Html::activeTextInput($modelpulangpasien, 'hubungan_penerima',[
                                'class' => 'form-control'
                            ])?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-jenazah hidden">
                    <label for="penyebab_utama_bayi" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Utama Bayi'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_utama_bayi',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <label for="pihak_menerima" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Pihak Penerima'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'pihak_menerima',[
                            'class' => 'form-control'
                        ]);?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group form-jenazah hidden">
                    <label for="penyebab_utama_ibu" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Penyebab Utama Ibu'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?= Html::activeTextInput($modelpulangpasien, 'penyebab_utama_ibu',[
                            'class' => 'form-control'
                        ])?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-rujuk hidden">
                    <div class="form-group">
                        <?= $form->field($modelpulangpasien, 'kamarruangan_jenis', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList([], [
                            'class' => 'form-control',
                            'prompt' => 'Pilih Jenis Kamar'
                        ]) ?>
                        <div class="error_PelayananJenazahFormcarakeluar_id"></div>
                    </div>
                    <div class="form-group">
                        <?= $form->field($modelpulangpasien, 'tempattidurtujuan_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList([], [
                            'class' => 'form-control',
                            'prompt' => 'Pilih Kamar Ruangan'
                        ]) ?>
                        <div class="error_PelayananJenazahFormcarakeluar_id"></div>
                    </div>
                    <div class="form-group <?= $konfig_keramat_spri ? '' : "hidden" ?>">
                        <?= $form->field($modelpulangpasien, 'dokterspesialis_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList([], [
                            'class' => 'form-control',
                            'prompt' => 'Pilih Dokter Tujuan'
                        ]) ?>
                        <div class="error_PelayananJenazahFormcarakeluar_id"></div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="form-group form-catatan-tindakan hidden">
                  <label for="catatan_tindakan" class="col-lg-4 control-label">
                      <?= Yii::t('fe', 'Pemeriksaan / Pertolongan yang sudah / harus diberikan'); ?>
                  </label>
                  <div class="col-lg-8">
                      <?=
                      Html::activeTextarea($modelpulangpasien, 'catatan_tindakan', [
                          'class' => 'form-control',
                      ]);
                      ?>
                      <div class="help-block"></div>
                  </div>
              </div>
              <div class="form-group form-catatan hidden">
                  <label for="catatan_lain" class="col-lg-4 control-label">
                      <?= Yii::t('fe', 'Catatan Lain'); ?>
                  </label>
                  <div class="col-lg-8">
                      <?=
                      Html::activeTextarea($modelpulangpasien, 'catatan_lain', [
                          'class' => 'form-control',
                      ]);
                      ?>
                      <div class="help-block"></div>
                  </div>
              </div>

              <div class="form-group form-infeksi hidden">
                  <label class="has-star col-sm-4">Infeksi</label>
                      <?=
                      DHtml::multipleRadio([
                          'model' => $modelpulangpasien,
                          'fieldName' => 'infeksi',
                          'colSize' => 3,
                          'data' => [
                              'inf' => 'infeksi',
                          'non_inf' => 'Non infeksi',
                          ]
                      ])
                      ?>
              </div>
            </div>


            <?=Html::hiddenInput('PasienPulangForm[pasien_id]', $data_pasien["pasien_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[pendaftaran_id]', $data_pasien["pendaftaran_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[ruanganakhir_id]', $data_pasien["ruangan_id"]);?>
            <?=Html::hiddenInput('PasienPulangForm[tgl_pendaftaran]', $data_pasien["tgl_pendaftaran"]);?>
            <?=Html::hiddenInput('PasienPulangForm[tgl_konsulpoli]', $data_pasien["tgl_konsulpoli"]);?>
            <?=Html::hiddenInput('PasienPulangForm[nosep]', $data_pasien["nosep"]);?>
            <?=Html::activeHiddenInput($modelpulangpasien, 'is_meninggal', ['class' => 'is-meninggal'])?>

            <div class="hidden">
                <?= Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>

                <?= Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'), ['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?php
    $form = ActiveForm::begin([
        'id' => 'pelayanan-jenazah-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL, 'enableAjaxValidation' => false, 'enableClientValidation' => false],
    ]);
    ?>
    <div class="panel panel-white hidden" id="display_hidden_jenazah">
        <div class="panel-body">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                    <li class="active"><a href="#highlighted-justified-tab1" data-toggle="tab">Kondisi Pasien & Penanggung Jawab</a></li>
                    <li><a href="#highlighted-justified-tab2" data-toggle="tab">Obat & Tindakan</a></li>
                    <li><a href="#highlighted-justified-tab3" data-toggle="tab">Linen & Alat yang masih melekat</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="highlighted-justified-tab1">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?= \Yii::t('fe', 'Kondisi Pasien') ?></label>
                                    <div class="col-md-6">
                                        <?= Html::activeTextArea($modelJenazah, 'kondisi', ['class' => 'form-control', 'rows' => 3]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-md-5"><?= \Yii::t('fe', 'Nama Penanggung Jawab') ?></label>
                                    <div class="col-md-5">
                                        <?= Html::activeTextInput($modelJenazah, 'nama_pj', ['class' => 'form-control']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-md-3"><?= \Yii::t('fe', 'Jenis Kelamin') ?></label>
                                    <div class="col-md-5">
                                        <?= $form->field($modelJenazah, 'jeniskelamin_id', [
                                            'template' => '{input}'
                                        ])->radioList($jeniskelamin, ['inline' => true])->label(false) ?>
                                    </div>
                                </div>
                                <div class="col-md-offset-3" id="error_JenazahFormjeniskelamin_id"></div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-md-5"><?= \Yii::t('fe', 'Umur (Thn)') ?></label>
                                    <div class="col-md-5">
                                        <?= Html::activeTextInput($modelJenazah, 'umur', ['class' => 'docoNumberOnly form-control']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-md-3"><?= \Yii::t('fe', 'No Telp \ Hp') ?></label>
                                    <div class="col-md-5">
                                        <?= Html::activeTextInput($modelJenazah, 'no_kontak', ['class' => 'docoNumberOnly form-control']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label class="control-label col-md-5"><?= \Yii::t('fe', 'Hubungan Keluarga') ?></label>
                                    <div class="col-md-5">
                                        <?= Html::activeDropdownlist($modelJenazah, 'hubungan_keluarga', $hubkeluarga, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '-- Pilih --')]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?= \Yii::t('fe', 'Alamat') ?></label>
                                    <div class="col-md-6">
                                        <?= Html::activeTextArea($modelJenazah, 'alamat', ['class' => 'form-control', 'rows' => 3]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab2">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="form-tindakan">
                                        <div class="col-md-8">
                                            <div class="form-group required">
                                                <label class="control-label col-md-3"><?= \Yii::t('fe', 'Tindakan') ?></label>
                                                <div class="col-md-8">
                                                    <?php
                                                    echo Select2::widget([
                                                        'name' => 'tindakan_jenazah',
                                                        'options' => [
                                                            'id' => 'tindakan_jenazah',
                                                            'placeholder' => 'Cari Tindakan'
                                                        ],
                                                        'pluginOptions' => [
                                                            'minimumInputLength' => 0,
                                                            'ajax' => [
                                                                'url' => "cari-tindakan-jenazah",
                                                                'dataType' => 'json',
                                                                'delay' => 250,
                                                                'data' => new JsExpression('function (params) {
                                        var query = {
                                            term: params.term,
                                            page: params.page || 1,
                                            id:pendaftaranId
                                            }
                                            return query;
                                        }'),
                                                                'processResults' => new JsExpression('
                                        function (data, params) {
                                            params.page = params.page || 1;

                                            return {
                                            results: data.result,
                                                pagination: {
                                                    more: data.pagination
                                                            }
                                                    };
                                            }'),
                                                            ],
                                                        ],
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group required">
                                                <label class="control-label col-md-8"><?= \Yii::t('fe', 'Qty') ?></label>
                                                <div class="col-md-6">
                                                    <?= Html::textInput('tindakan_qty', '', ['id' => 'tindakan_qty', 'class' => 'form-control doco-number']) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class="btn btn-success btn-sm" id="btn-add-tindakan-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-tindakan">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('fe', 'No') ?></th>
                                                <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                                <th><?= Yii::t('fe', 'Qty') ?></th>
                                                <th><?= Yii::t('fe', 'Harga') ?></th>
                                                <th><?= Yii::t('fe', 'Aksi') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="form-obat">
                                        <div class="col-md-8">
                                            <div class="form-group required">
                                                <label class="control-label col-md-4"><?= \Yii::t('fe', 'Obat') ?></label>
                                                <div class="col-md-8">
                                                    <?php echo Select2::widget([
                                                        'name' => 'obat-jenazah',
                                                        'options' => ['id' => 'obat_jenazah', 'placeholder' => 'Cari Obat'],
                                                        'pluginOptions' => [
                                                            'minimumInputLength' => 0,
                                                            'ajax' => [
                                                                'url' => "cari-obat-jenazah",
                                                                'dataType' => 'json',
                                                                'delay' => 250,
                                                                'data' => new JsExpression('function (params) {
                                            var query = {
                                                term: params.term,
                                                page: params.page || 1,
                                                id:pendaftaranId
                                                }
                                                return query;
                                            }'),
                                                                'processResults' => new JsExpression('
                                            function (data, params) {
                                                dataobat = data.dataObat;
                                                params.page = params.page || 1;

                                            return {
                                            results: data.result,
                                                pagination: {
                                                    more: data.pagination
                                                            }
                                                    };
                                            }'),
                                                            ],
                                                        ],
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group required">
                                                <label class="control-label col-md-8"><?= \Yii::t('fe', 'Qty') ?></label>
                                                <div class="col-md-6">
                                                    <?= Html::textInput('qty_obat', '', [
                                                        'id' => 'qty_obat', 'class' => 'form-control doco-number'
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?= \Yii::t('fe', 'Satuan') ?></label>
                                                <div class="col-md-6">
                                                    <?php
                                                    echo Html::textInput('satuanobat_nama', '', ['class' => 'satuanobat-nama form-control']);
                                                    // echo DepDrop::widget([
                                                    //     'name' => 'satuan_obat',
                                                    //     'options' => ['id'=>'satuan_obat','class'=>'select2'],
                                                    //     'pluginOptions' => [
                                                    //         'depends' => ['obat_jenazah'],
                                                    //         'url'=>'set-satuan-obat-jenazah'
                                                    //     ]
                                                    // ]);
                                                    ?>
                                                    <?= Html::hiddenInput('satuan_obat', '', ['id' => 'satuan_obat']) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class="btn btn-success btn-sm" id="btn-add-obat-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('fe', 'No') ?></th>
                                                <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                                <th><?= Yii::t('fe', 'Qty') ?></th>
                                                <th><?= Yii::t('fe', 'Satuan') ?></th>
                                                <th><?= Yii::t('fe', 'Harga') ?></th>
                                                <th><?= Yii::t('fe', 'Aksi') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="highlighted-justified-tab3">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="form-linen">
                                        <div class="col-md-8">
                                            <div class="form-group required">
                                                <label class="control-label col-md-3"><?= \Yii::t('fe', 'Linen') ?></label>
                                                <div class="col-md-8">
                                                    <?php echo Select2::widget([
                                                        'name' => 'linen-jenazah',
                                                        'options' => [
                                                            'id' => 'linen_jenazah',
                                                            'placeholder' => 'Cari Linen'
                                                        ],
                                                        'pluginOptions' => [
                                                            'minimumInputLength' => 0,
                                                            'ajax' => [
                                                                'url' => "cari-linen-jenazah",
                                                                'dataType' => 'json',
                                                                'delay' => 250,
                                                                'data' => new JsExpression('function (params) {
                                                        var query = {
                                                        search: params,
                                                        }
                                                        return params;
                                                    }'),
                                                                'processResults' => new JsExpression('
                                            function (data, params) {
                                                params.page = params.page || 1;

                                                return {
                                                results: data.result,
                                                    pagination: {
                                                        more: data.pagination
                                                                }
                                                        };
                                                }'),
                                                            ],
                                                        ],
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group required">
                                                <label class="control-label col-md-8"><?= \Yii::t('fe', 'Qty') ?></label>
                                                <div class="col-md-6">
                                                    <?= Html::textInput('qty_linen', '', [
                                                        'id' => 'qty_linen',
                                                        'class' => 'form-control doco-number'
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class="btn btn-success btn-sm" id="btn-add-linen-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-linen">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('fe', 'No') ?></th>
                                                <th><?= Yii::t('fe', 'Nama Linen') ?></th>
                                                <th><?= Yii::t('fe', 'Qty') ?></th>
                                                <th><?= Yii::t('fe', 'Aksi') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div><br>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="form-alat">
                                        <div class="col-md-6">
                                            <div class="form-group required">
                                                <label class="control-label col-md-5"><?= \Yii::t('fe', 'Alat yang masih terpasang') ?></label>
                                                <div class="col-md-6">
                                                    <?php echo Select2::widget([
                                                        'name' => 'alat-jenazah',
                                                        'options' => [
                                                            'id' => 'alat_jenazah',
                                                            'placeholder' => 'Cari Alat Obat'
                                                        ],
                                                        'pluginOptions' => [
                                                            'minimumInputLength' => 0,
                                                            'ajax' => [
                                                                'url' => "cari-alat-jenazah",
                                                                'dataType' => 'json',
                                                                'delay' => 250,
                                                                'data' => new JsExpression('function (params) {
                                                        var query = {
                                                        search: params,
                                                        }
                                                        return params;
                                                    }'),

                                                                'processResults' => new JsExpression('
                                            function (data, params) {
                                                params.page = params.page || 1;

                                                return {
                                                results: data.result,
                                                    pagination: {
                                                        more: data.pagination
                                                                }
                                                        };
                                                }'),
                                                            ],
                                                        ],
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group required">
                                                <label class="control-label col-md-8"><?= \Yii::t('fe', 'Qty') ?></label>
                                                <div class="col-md-6">
                                                    <?= Html::textInput('qty_alat', '', [
                                                        'id' => 'qty_alat',
                                                        'class' => 'form-control doco-number'
                                                    ]) ?>
                                                </div>
                                                <div class="col-md-1">
                                                    <a class="btn btn-success btn-sm" id="btn-add-alat-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-alat">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('fe', 'No') ?></th>
                                                <th><?= Yii::t('fe', 'Nama Alat') ?></th>
                                                <th><?= Yii::t('fe', 'Qty') ?></th>
                                                <th><?= Yii::t('fe', 'Aksi') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div><br>
                        <hr>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>

    <div class="panel panel-default long-form" id="form_rujukan_pasien" hidden=true>
        <div class="panel-body">
            <div class="tabbable-rujukan">
            </div>
        </div>
    </div>

</div>
<div class="modal-footer text-right">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                // 'data-target' => 'form-pemulanganpasien',
                'id' => 'btn-save-pulang',
                'data-options' => 'click',
                'data-messages' => $stateMessage,
                // 'disabled' => ($morbiditas === false) ? 'disabled' : ($status_update) ? 'disabled' : false,
                'onClick' => false
            ]
        ]
    ]);
    ?>
</div>
<?php
$this->registerJs('
    if(pasienpulang_id != ""){
        $("#btn-save-pulang").prop("disabled", true);
    }
    var pendaftaranId = "' . $id . '";
    var konfig_keramat_spri = "' . $konfig_keramat_spri . '";
    var table_jenazah_tindakan;
    var table_jenazah_obat;
    var table_jenazah_linen;
    var table_jenazah_alat;
    var dataobat = [];
    var defaultDate = "'.date('d/m/Y H:i:s').'"
    ' . $this->render('js/_pulang.js'), View::POS_END, 'js-jenazah');
?>
