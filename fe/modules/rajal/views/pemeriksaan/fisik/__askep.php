<?php 

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
?>

<h2>Asesmen Keperawatan</h2>
<div class="row">
    <div class="col-md-6 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Data Pemeriksaan') ?></h5>
            </div>
            <div class="panel-body">
                <?= $form->field($modelAskep, 'nama_dokter', ['labelOptions' => ['class' => '']])
                    ->textInput([
                        'class' => 'form-control input-sm',
                        'value' => $data_nama_dokter,
                        'readonly' => 'readonly',
                    ]); ?>
                <?php
                if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS):
                    ?>
                    <?= $form->field($modelAskep, 'pegawaiperawat_id', ['labelOptions' => ['class' => '']])
                        ->dropDownList(ArrayHelper::map($list_perawat, 'pegawai_id', 'nama_pegawai'), [
                            'class' => 'form-control input-sm select2',
                            'prompt' => Yii::t('fe', '--Pilih perawat--')
                        ]); ?>
                <?php else: ?>
                    <div class="form-group highlight-addon field-pemeriksaanaskepform-pegawaiperawat_id_view required">
                        <label class="control-label col-sm-5"
                            for="pemeriksaanaskepform-pegawaiperawat_id_view">Perawat</label>
                        <div class="col-sm-7">
                            <select id="pemeriksaanaskepform-pegawaiperawat_id_view" class="form-control input-sm"
                                name="PemeriksaanAskepForm[pegawaiperawat_id_view]" disabled="" aria-required="true">
                                <option value="<?= Yii::$app->docoVars->user("id_pegawai"); ?>" selected="">
                                    <?= Yii::$app->session->get('user_identity')['nama_pegawai'] ?>
                                </option>
                            </select>

                            <div class="help-block"></div>
                        </div>
                    </div>
                    <?= Html::hiddenInput('PemeriksaanAskepForm[pegawaiperawat_id]', Yii::$app->docoVars->user("id_pegawai")); ?>
                <?php endif; ?>
                <div class="form-group highlight-addon has-size-sm field-pemeriksaanaskepform-tglperiksafisik required">
                    <label class="control-label col-sm-5" for="pemeriksaanaskepform-tglperiksafisik">
                        <?= Yii::t('fe', 'Tanggal periksa fisik') ?>
                    </label>
                    <div class="col-md-7">
                        <?= Html::activeTextInput($modelAskep, 'tglperiksafisik', ['value' => date('d/m/Y'), 'class' => 'form-control input-sm txt-timepicker', 'readonly' => true]) ?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <?= $form->field($modelAskep, 'keadaanumum', ['labelOptions' => ['class' => '']])
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
                <?= $form->field($modelAskep, 'gcs_eye', ['labelOptions' => ['class' => '']])
                    ->dropDownList(ArrayHelper::map($gcs_list_eye, 'metodegcs_id', 'nama_and_nilai'), [
                        'class' => 'form-control input-sm select2 gcs_eye',
                        'id' => 'gcseye_select2',
                        'data-type' => 'eye',
                        'prompt' => Yii::t('fe', '-- Pilih gcs eye --'),
                        'options' => $gcsEyeOptions,
                        'disabled' => $isDokter ? false : true,
                    ]); ?>
                <?= $form->field($modelAskep, 'gcs_verbal', ['labelOptions' => ['class' => '']])
                    ->dropDownList(ArrayHelper::map($gcs_list_verbal, 'metodegcs_id', 'nama_and_nilai'), [
                        'class' => 'form-control input-sm select2 gcs_verbal',
                        'id' => 'gcsverbal_select2',
                        'data-type' => 'verbal',
                        'prompt' => Yii::t('fe', '-- Pilih gcs verbal --'),
                        'options' => $gcsVerbalOptions,
                        'disabled' => $isDokter ? false : true,
                    ]); ?>
                <?= $form->field($modelAskep, 'gcs_motorik', ['labelOptions' => ['class' => '']])
                    ->dropDownList(ArrayHelper::map($gcs_list_motorik, 'metodegcs_id', 'nama_and_nilai'), [
                        'class' => 'form-control input-sm select2 gcs_motorik',
                        'id' => 'gcsmotorik_select2',
                        'data-type' => 'motorik',
                        'prompt' => Yii::t('fe', '-- Pilih gcs motorik --'),
                        'options' => $gcsMotorikOptions,
                        'disabled' => $isDokter ? false : true,
                    ]); ?>
                <?= $form->field($modelAskep, 'gcs_hasil_metode', ['labelOptions' => ['class' => '']])
                    ->textInput([
                        'id' => 'gcs_hasil_metode',
                        'class' => 'form-control input-sm',
                        'readonly' => 'readonly',
                    ])->label(Yii::t('fe', 'Hasil Metode GCS')); ?>
                <div style="display:none">
                    <?= $form->field($modelAskep, 'gcs_is_kapitis', ['labelOptions' => ['class' => '']])
                        ->checkbox(); ?>
                    <?= $form->field($modelAskep, 'gcs_kategori', ['labelOptions' => ['class' => '']])
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
                <?= $form->field($modelAskep, 'kesadaran', [
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
                <?= $form->field($modelAskep, 'td_systolic', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'mmHg']],
                ])->textInput([
                        'id' => 'td_systolic',
                        'class' => 'form-control input-sm td_field doco-number systolic',
                        'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'td_diastolic', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'mmHg']],
                ])->textInput([
                            'id' => 'td_diastolic',
                            'class' => 'form-control input-sm td_field doco-number diastolic sysdia',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'tekanandarah_kategori', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'tekanandarah_kategori',
                            'class' => 'form-control input-sm hasil-td',
                            'readonly' => 'readonly',
                        ]); ?>
                <?= $form->field($modelAskep, 'meanarteripressure', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => 'Berbahaya', 'options' => ['class' => 'kategori-map']]],
                ])->textInput([
                            'id' => 'meanarteripressure',
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ]); ?>
                <?= $form->field($modelAskep, 'detaknadi', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                ])->textInput([
                            'id' => 'detaknadi',
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'denyutjantung', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'denyutjantung',
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly',
                        ])->label("Kategori Nadi"); ?>
                <?= $form->field($modelAskep, 'pernapasan', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => '/' . Yii::t('fe', 'Menit')]],
                ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'kategori_pernapasan', [
                    'labelOptions' => ['class' => '']
                ])->radioList($configVal['kategori_pernapasan'], [
                            'itemOptions' => [
                                'disabled' => $isDokter ? false : true,
                            ]
                        ]); ?>
                <?= $form->field($modelAskep, 'suhutubuh', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => '&deg; Celcius']],
                ])->textInput([
                            'class' => 'form-control input-sm doco-decimal-wcomma',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'tinggibadan_cm', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => Yii::t('fe', 'cm')]],
                ])->textInput([
                            'id' => 'tinggibadan_cm',
                            'class' => 'form-control input-sm imt_field doco-decimal-wcomma tinggi-badan',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'beratbadan_kg', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                ])->textInput([
                            'id' => 'beratbadan_kg',
                            'class' => 'form-control input-sm imt_field doco-decimal-wcomma',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'bb_ideal', [
                    'labelOptions' => ['class' => ''],
                    'addon' => ['append' => ['content' => Yii::t('fe', 'kg')]],
                ])->textInput([
                            'id' => 'bb_ideal',
                            'class' => 'form-control input-sm berat-badan',
                            'readonly' => 'readonly',
                        ]); ?>
                <?= $form->field($modelAskep, 'imt', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'imt',
                            'class' => 'form-control input-sm imt',
                            'readonly' => 'readonly',
                        ]); ?>
                <?= $form->field($modelAskep, 'imt_kategori', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'imt_kategori',
                            'class' => 'form-control input-sm imt_kategori',
                            'readonly' => 'readonly',
                        ])->label("Berat Badan <br> (WHO Western Pacific Region, 2000) "); ?>
                <?= $form->field($modelAskep, 'kelainanpadabagtubuh', [
                    'labelOptions' => ['class' => '']
                ])->textarea([
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelAskep, 'bodymassindex_id', [
                    'labelOptions' => ['class' => 'bodymassindex_id', 'id' => 'bodymassindex_id'],
                ])->hiddenInput()->label(false); ?>
            </div>
        </div>
    </div>
</div>