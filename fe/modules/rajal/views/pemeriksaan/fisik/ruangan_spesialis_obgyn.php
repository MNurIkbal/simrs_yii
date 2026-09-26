<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>

<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-fisik',
                'id' => 'submit-fisik',
            ]
        ],
        'custom-print' => [
            'type' => 'button',
            'title' => Yii::t('fe', 'Cetak'),
            'icon' => 'fa fa-print',
            'attributes' => [
                'class' => 'print',
                'method' => 'json',
                'data-options' => 'link',
            ],
        ],
    ]); ?>
    <span class="draft mr-3" id="draft" style="color:red;display:none">Draft : anda harus menyimpan terlebih dulu</span>
</div>

<h1 style="text-align:center;">Riwayat Penyakit & Pemeriksaan Kandungan</h1>
<hr>
<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-fisik',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],

]);
?>

<input type="hidden" name="pemeriksaanfisik_id">

<?= Yii::$app->controller->renderPartial('/pemeriksaan/fisik/__askep', [
    'modelFisik' => $modelFisik,
    'modelAskep' => $modelAskep,
    'form' => $form,
    'data_nama_dokter' => $data_nama_dokter,
    'gcs_list_eye' => $gcs_list_eye,
    'gcs_list_verbal' => $gcs_list_verbal,
    'gcs_list_motorik' => $gcs_list_motorik,
    'gcsEyeOptions' => $gcsEyeOptions,
    'gcsVerbalOptions' => $gcsVerbalOptions,
    'gcsMotorikOptions' => $gcsMotorikOptions,
    'list_perawat' => $list_perawat,
    'isDokter' => $isDokter,
    'configVal' => $configVal
]); ?>

<hr style="margin-top:15px;">

<h2>Asesmen Medis</h2>

<div class="row">
    <div class="col-md-6 col-header">
        <h3>Status Obstetric</h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Luar') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'obstetric_luar_tfu', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_tfu',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_situs', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_situs',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_punggung', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_punggung',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_bagterendah', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_bagterendah',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_perlimaan', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_perlimaan',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_his', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_his',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_denyutjanin', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_denyutjanin',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_luar_beratjanin', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_luar_beratjanin',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Inspekulo') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'obstetric_inspekulo', [
                    'labelOptions' => ['class' => '']
                ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Dalam Vagina') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'obstetric_dalam_vulva', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_vulva',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_portio', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_portio',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_pembukaan', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_pembukaan',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_ketuban', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_ketuban',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_bagterendah', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_bagterendah',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_ubunkecil', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_ubunkecil',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_penurunan', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_penurunan',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_kesanpanggul', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_kesanpanggul',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'obstetric_dalam_pelepasan', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'obstetric_dalam_pelepasan',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-header">
        <h3>Status Gynekology</h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Luar') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'gynekology_luar_tfu', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_luar_tfu',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_luar_mtnt', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_luar_mtnt',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_luar_fluksus', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_luar_fluksus',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Inspekulo') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'gynekology_inspekulo', [
                    'labelOptions' => ['class' => '']
                ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Dalam Vagina') ?></h5>
            </div>
            <div class="panel-body" style="margin-top:10px">
                <?= $form->field($modelFisik, 'gynekology_dalam_vulva', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_vulva',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_dalam_portio', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_portio',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_dalam_oue', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_oue',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_dalam_uterus', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_uterus',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_dalam_adnexa', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_adnexa',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
                <?= $form->field($modelFisik, 'gynekology_dalam_pelepasan', [
                    'labelOptions' => ['class' => ''],
                ])->textInput([
                            'id' => 'gynekology_dalam_pelepasan',
                            'class' => 'form-control input-sm',
                            'disabled' => $isDokter ? false : true,
                        ]); ?>
            </div>
        </div>
    </div>
</div>

<hr>

<?php ActiveForm::end(); ?>
<?php
$jsonBagianTubuh = json_encode(ArrayHelper::map($optBagianTubuh, 'bagiantubuh_id', 'namabagtubuh'));
$this->registerJs('
    var data_bmi = ' . $data_bmi . ';

    var data_tekanandarah = ' . $data_tekanandarah . ';

    var jeniskelamin = "' . $jeniskelamin . '";

    var umur = ' . $umur . ';

    var data_tekanandarah = ' . $data_tekanandarah . ';

    var bagianTubuh = ' . $jsonBagianTubuh . ';
    var tmpData = ' . $dataAnatomi . ';
    var counter = ' . $counter . ';

    var dataGcs = ' . json_encode($data_gcs) . ';

    var pendaftaranId = "' . $pendaftaran_id . '";
    var is_draft = ' . $is_draft . ';
    var pemeriksaanfisik_id = "' . $modelFisik->pemeriksaanfisik_id . '";

    var _model = "' . $modelName . '";
    var _modelName = _model.toLowerCase();
', View::POS_END);
$this->registerJs($this->render('js/__fisik.js'), View::POS_END);
?>