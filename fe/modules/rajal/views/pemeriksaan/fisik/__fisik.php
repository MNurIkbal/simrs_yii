<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 09:29:17
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 14:40:25
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->registerCss('
.radio,
.checkbox {
    display: block;
    min-height: @line-height-computed;
    margin-top: 70px;
    margin-bottom: 70px;
    padding-left: 20px;
    label {
        display: inline;
        font-weight: normal;
        cursor: pointer;
    }
}
.col-header {
    margin-top: 5px;
}
.col-glasgow {
    margin-bottom: 0px;
    padding-bottom: 50px;
}
.col-kesadaran {
    margin-top: 20px;
}
.col-thoraks {
    padding-bottom: 65px;
}
')
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Periksa fisik') ?></h5>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'save' => [
                'attributes' => [
                    'form_id' => 'form-fisik',
                    'id' => 'submit-fisik',
                    'disabled' => $isDokter ? false : true
                ]
            ],
            // 'reset',
            'custom-print' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Cetak'),
                'icon' => 'fa fa-print',
                'attributes' => [
                    'class' => 'print',
                    'method' => 'json',
                    'data-options' => 'link',
                    'disabled' => !empty($modelFisik->pemeriksaanfisik_id) ? false : true
                ],
            ],
        ]); ?>
        <span class='draft mr-3' style='color:red;display:none'>Draft : anda harus menyimpan terlebih dulu</span>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'id' => 'form-fisik',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],

        ]);
        ?>
        <div class="row">
            <div class="col-md-6 col-header">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Data Pemeriksaan') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($modelFisik, 'nama_dokter', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'value' => $data_nama_dokter,
                                'readonly' => 'readonly',
                            ]); ?>
                        <?php
                        if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) :
                        ?>
                            <?= $form->field($modelFisik, 'pegawaiperawat_id', ['labelOptions' => ['class' => '']])
                                ->dropDownList(ArrayHelper::map($list_perawat, 'pegawai_id', 'nama_pegawai'), [
                                    'class' => 'form-control input-sm select2',
                                    'prompt' => Yii::t('fe', '--Pilih perawat--')
                                ]); ?>
                        <?php else : ?>
                            <div class="form-group highlight-addon field-pemeriksaanfisikform-pegawaiperawat_id_view required">
                                <label class="control-label col-sm-5" for="pemeriksaanfisikform-pegawaiperawat_id_view">Perawat</label>
                                <div class="col-sm-7">
                                    <select id="pemeriksaanfisikform-pegawaiperawat_id_view" class="form-control input-sm" name="PemeriksaanFisikForm[pegawaiperawat_id_view]" disabled="" aria-required="true">
                                        <option value="<?= Yii::$app->docoVars->user("id_pegawai"); ?>" selected=""><?= Yii::$app->session->get('user_identity')['nama_pegawai'] ?></option>
                                    </select>

                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <?= Html::hiddenInput('PemeriksaanFisikForm[pegawaiperawat_id]', Yii::$app->docoVars->user("id_pegawai")); ?>
                            <?php /*$form->field($modelFisik, 'pegawaiperawat_id', ['labelOptions' => ['class' => '']])
                                    ->dropDownList(ArrayHelper::map($list_perawat, 'pegawai_id', 'nama_pegawai'), [
                                        'class' => 'form-control input-sm',
                                        // 'prompt' => Yii::t('fe', '--Pilih perawat--'),
                                        'type' => 'hidden',
                                ]); */
                            ?>
                        <?php endif; ?>

                        <div class="form-group highlight-addon has-size-sm field-pemeriksaanfisikform-tglperiksafisik required">
                            <label class="control-label col-sm-5" for="pemeriksaanfisikform-tglperiksafisik">
                                <?= Yii::t('fe', 'Tanggal periksa fisik') ?>
                            </label>
                            <div class="col-md-7">
                                <?= Html::activeTextInput($modelFisik, 'tglperiksafisik', ['value' => date('d/m/Y'), 'class' => 'form-control input-sm txt-timepicker', 'readonly' => true]) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <?= $form->field($modelFisik, 'keadaanumum', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm input-tags-keadaan',
                                'data-role' => 'tagsinput',
                                'disabled' => $isDokter ? false : true
                            ]); ?>
                    </div>
                </div>
                <div class="panel panel-default col-glasgow">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Glasgow Coma Scale') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($modelFisik, 'gcs_eye', ['labelOptions' => ['class' => '']])
                            ->dropDownList(ArrayHelper::map($gcs_list_eye, 'metodegcs_id', 'nama_and_nilai'), [
                                'class' => 'form-control input-sm select2 gcs_eye',
                                'id' => 'gcseye_select2',
                                'data-type' => 'eye',
                                'prompt' => Yii::t('fe', '-- Pilih gcs eye --'),
                                'options' => $gcsEyeOptions,
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'gcs_verbal', ['labelOptions' => ['class' => '']])
                            ->dropDownList(ArrayHelper::map($gcs_list_verbal, 'metodegcs_id', 'nama_and_nilai'), [
                                'class' => 'form-control input-sm select2 gcs_verbal',
                                'id' => 'gcsverbal_select2',
                                'data-type' => 'verbal',
                                'prompt' => Yii::t('fe', '-- Pilih gcs verbal --'),
                                'options' => $gcsVerbalOptions,
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'gcs_motorik', ['labelOptions' => ['class' => '']])
                            ->dropDownList(ArrayHelper::map($gcs_list_motorik, 'metodegcs_id', 'nama_and_nilai'), [
                                'class' => 'form-control input-sm select2 gcs_motorik',
                                'id' => 'gcsmotorik_select2',
                                'data-type' => 'motorik',
                                'prompt' => Yii::t('fe', '-- Pilih gcs motorik --'),
                                'options' => $gcsMotorikOptions,
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'gcs_hasil_metode', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'readonly' => 'readonly',
                            ])->label(Yii::t('fe', 'Hasil Metode GCS')); ?>
                        <div style="display:none">
                            <?= $form->field($modelFisik, 'gcs_is_kapitis', ['labelOptions' => ['class' => '']])
                                ->checkbox(); ?>
                            <?= $form->field($modelFisik, 'gcs_kategori', ['labelOptions' => ['class' => '']])
                                ->textInput([
                                    'class' => 'form-control input-sm hasil_gcs',
                                    'readonly' => 'readonly',
                                ])->label(Yii::t('fe', 'Kategori')); ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default col-kesadaran">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Kesadaran') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($modelFisik, 'kesadaran', [
                            'labelOptions' => ['class' => '']
                            ])->radioList($configVal['kesadaran'], [
                                'itemOptions' => [
                                    'disabled' => $isDokter ? false : true,
                                ]
                            ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-header">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Tanda Vital') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($modelFisik, 'td_systolic', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'mmHg']],
                        ])->textInput([
                            'class' => 'form-control input-sm td_field doco-number systolic',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'td_diastolic', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'mmHg']],
                        ])->textInput([
                            'class' => 'form-control input-sm td_field doco-number diastolic sysdia',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'tekanandarah_kategori', [
                            'labelOptions' => ['class' => ''],
                        ])->textInput([
                            'class' => 'form-control input-sm hasil-td',
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($modelFisik, 'meanarteripressure', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => '', 'options' => ['class' => 'kategori-map']]],
                        ])->textInput([
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($modelFisik, 'detaknadi', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                        ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'denyutjantung', [
                            'labelOptions' => ['class' => ''],
                        ])->textInput([
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ])->label("Kategori Nadi"); ?>
                        <?= $form->field($modelFisik, 'pernapasan', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                        ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'kategori_pernapasan', [
                            'labelOptions' => ['class' => '']
                        ])->radioList($configVal['kategori_pernapasan'], [
                            'itemOptions' => [
                                'disabled' => $isDokter ? false : true,
                            ]
                        ]); ?>
                        <?= $form->field($modelFisik, 'suhutubuh', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => '&deg; Celcius']],
                        ])->textInput([
                            'class' => 'form-control input-sm doco-decimal-wcomma',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'tinggibadan_cm', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => Yii::t('fe', 'cm')]],
                        ])->textInput([
                            'class' => 'form-control input-sm imt_field doco-decimal-wcomma tinggi-badan',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'beratbadan_kg', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                        ])->textInput([
                            'class' => 'form-control input-sm imt_field doco-decimal-wcomma',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'bb_ideal', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                        ])->textInput([
                            'class' => 'form-control input-sm berat-badan',
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($modelFisik, 'imt', [
                            'labelOptions' => ['class' => ''],
                        ])->textInput([
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ]); ?>
                        <?= $form->field($modelFisik, 'imt_kategori', [
                            'labelOptions' => ['class' => ''],
                        ])->textInput([
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ])->label("Klasifikasi Berat Badan <br> (WHO Western Pacific Region, 2000) "); ?>
                        <?= $form->field($modelFisik, 'kelainanpadabagtubuh', [
                            'labelOptions' => ['class' => '']
                        ])->textarea([
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                        <?= $form->field($modelFisik, 'bodymassindex_id', [
                            'labelOptions' => ['class' => ''],
                        ])->hiddenInput()->label(false); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default col-thoraks">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Thoraks') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($modelFisik, 'inspeksi', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'palpasi', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'perkusi', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                        <?= $form->field($modelFisik, 'auskultasi', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Jalan Nafas & Pernapasan') ?></h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($modelFisik, 'jn_paten', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'jn_obstruktifpartial', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'jn_obstruktifnormal', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'jn_stridor', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'jn_gargling', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($modelFisik, 'pgp_normal', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'pgp_kussmaul', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'pgp_takipnea', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'pgp_retraktif', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                                <?= $form->field($modelFisik, 'pgp_dangkal', ['labelOptions' => ['class' => '']])
                                    ->checkbox([
                                        'class' => 'input-sm checkbox',
                                        'disabled' => $isDokter ? false : true,
                                    ], false); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12" style="float: right;margin-top: 5px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Pernapasan Gerakan Dada') ?></h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <?= $form->field($modelFisik, 'pgd_simetri', ['labelOptions' => ['class' => '']])
                                        ->checkbox([
                                            'class' => 'input-sm',
                                            'disabled' => $isDokter ? false : true,
                                        ], false); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($modelFisik, 'pgd_asimetri', ['labelOptions' => ['class' => '']])
                                        ->checkbox([
                                            'class' => 'input-sm',
                                            'disabled' => $isDokter ? false : true,
                                        ], false); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
        </div>



        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Anggota Tubuh') ?></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="image-frame">
                            <?php
                            echo Html::img('@web/media/img/img-pemeriksaan/bagian_tubuh.jpg');
                            ?>
                        </div>
                        <!-- modal anatomi start -->
                        <div class="tag" style="display: none" data-show="1">
                            <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                            <div class="well well-sm" style="min-height:130px;">
                                <div class="form-group">
                                    <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <?php echo Html::dropDownList(
                                            'bagain_tubuh',
                                            null,
                                            ArrayHelper::map($optBagianTubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                            array(
                                                'class' => 'form-control bagian-tubuh',
                                                'style' => 'padding : 9px 12px !important;',
                                                'empty' => '-- Pilih --',
                                            )
                                        ); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                                    <div class="col-lg-9">
                                        <input type="text" placeholder="catatan.." class="form-control add-caption">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- modal anatomi end -->
                    </div>
                    <div class="col-md-6">
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh doco-wrap-table-text">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th><?= Yii::t('fe', 'Bagian tubuh') ?></th>
                                    <th><?= Yii::t('fe', 'Catatan') ?></th>
                                    <th><?= Yii::t('fe', 'Aksi') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- table data -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Sirkulasi') ?></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($modelFisik, 'sirkulasi_nadicarotis', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'x/ Menit']],
                        ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'cfr_kecil_2', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm checkbox',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'kulit_normal', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm checkbox',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'kulit_cyanosis', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm checkbox',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'kulit_berkeringat', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm checkbox',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($modelFisik, 'sirkulasi_nadiradialis', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'x/ Menit']],
                        ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'cfr_besar_2', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'kulit_jaundice', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelFisik, 'kulit_pucat', ['labelOptions' => ['class' => '']])
                            ->checkbox([
                                'class' => 'input-sm',
                                'disabled' => $isDokter ? false : true,
                            ], false); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($modelFisik, 'akral', ['labelOptions' => ['class' => '']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                'disabled' => $isDokter ? false : true,
                            ]); ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <?= Html::activeHiddenInput($modelFisik, 'gcs_id'); ?>
            <?= Html::activeHiddenInput($modelFisik, 'tekanandarah'); ?>
            <?= Html::activeHiddenInput($modelFisik, 'klasifikasitekanandarah_id', ['class' => 'disable-get-change']); ?>
            <?= Html::activeHiddenInput($modelFisik, 'pasien_id', ['value' => $pasien_id]); ?>
            <?= Html::activeHiddenInput($modelFisik, 'pendaftaran_id', ['value' => $pendaftaran_id]); ?>
            <?= Html::activeHiddenInput($modelFisik, 'pemeriksaanfisik_id', ['value' => isset($modelFisik->pemeriksaanfisik_id) ? $modelFisik->pemeriksaanfisik_id : null]); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$jsonBagianTubuh = json_encode(ArrayHelper::map($optBagianTubuh, 'bagiantubuh_id', 'namabagtubuh'));
$this->registerJs('
     $(document).ready(function(){
        $("#form-fisik :input").prop("disabled", ' . $status_update . ');
        $("#submit-fisik, .data-reset").prop("disabled", ' . $status_update . ');
    });
    var tabel_anggotatubuh = $(".tabel-anggotatubuh").DataTable({
        filter: false,
        bLengthChange: false,
        bInfo: false,
        processing: true,
        paging: false,
        ordering: false
    });

    // define data master bmi
    var data_bmi = ' . $data_bmi . ';

    // define data master tekanan darah & map
    var data_tekanandarah = ' . $data_tekanandarah . ';

    // define data pasien jeniskelamin
    var jeniskelamin = "' . $jeniskelamin . '";

    // define data pasien umur
    var umur = ' . $umur . ';

    // define data master tekanan darah & map
    var data_tekanandarah = ' . $data_tekanandarah . ';

    var bagianTubuh = ' . $jsonBagianTubuh . ';
    var tmpData = ' . $dataAnatomi . ';
    var counter = ' . $counter . ';

    // define data GCS
    var dataGcs = ' . json_encode($data_gcs) . ';

    // Define pemeriksaan fisik id
    var pemeriksaanfisik_id = $("#pemeriksaanfisikform-pemeriksaanfisik_id").val();

    var is_draft = ' . $is_draft . ';
    var pendaftaranId
', View::POS_END);
$this->registerJs($this->render('js/__fisik.js'), View::POS_END);
?>
