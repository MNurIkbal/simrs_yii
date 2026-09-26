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

<h1 style="text-align:center;">Riwayat Penyakit & Pemeriksaan Mata</h1>
<hr style="margin-bottom:25px;">
<div class="help-block"></div>
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

<hr>

<div class="row">
    <h5 style="margin-left:20px;">Anamnesis : </h5>
    <div class="row" style="margin-left:25px;">
        <span>Keluhan Utama</span>
        <?= $form->field($modelFisik, 'keluhanUtama', [
            'labelOptions' => ['class' => ''],
        ])->textInput(['class' => 'form-control', 'style' => 'width:1250px;'])->label(false); ?>
    </div>
    <div class="help-block"></div>
    <div class="row" style="margin-left:25px;">
        <span>Anamnesis Lanjutan</span>
        <?= $form->field($modelFisik, 'anamnesisLanjutan', [
            'labelOptions' => ['class' => ''],
        ])->textInput(['class' => 'form-control', 'style' => 'width:1250px;'])->label(false); ?>
    </div>
</div>

<hr>

<div class="row">
    <h5>Pemeriksaan Fisik</h5>
    <div class="table table-bordered table-mata">
        <table style="width:100%; border:0.5px solid black;">
            <thead style="background-color:#38444c;">
                <tr style="color:white;">
                    <th colspan="3" style="text-align:center; font-size:16px; width:68%; translate: 50px;">
                        OD
                    </th>
                    <th colspan="3" style="text-align:center; font-size:16px; width:33%; translate: -50px;">
                        OS
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6">
                        <?php
                        echo Html::img('@web/media/img/img-pemeriksaan/mata.png', [
                            'class' => 'img-responsive',
                            'style' => 'width:70%; height:auto; margin-left:auto; margin-right:auto;'
                        ]);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'visusOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;'],
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Visus
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'visusOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'siliaOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Silia
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'siliaOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'palpebraOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Palpebra
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'palpebraOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'konjungtivaOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Konjungtiva
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'konjungtivaOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'korneaOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Kornea
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'korneaOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'

                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'bmdOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'

                            ,
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Bmd
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'bmdOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'irisOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Iris
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'irisOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'pupilOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Pupil
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'pupilOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1">

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'lensaOD', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px;">
                        Lensa
                    </td>
                    <td colspan="2" style="text-align:center;">
                        <?= $form->field($modelFisik, 'lensaOS', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align: left; width: 25%;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?= $form->field($modelFisik, 'fisikMataTambahanOD[]', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}',
                            'options' => ['style' => 'margin-right:30px; float:right;']
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:10px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1"
                        style="text-align:center; font-weight:bold; translate:-15px; display:flex; justify-content:center;">
                        <?= $form->field($modelFisik, 'bagMataTambahan[]', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'width:75px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="2">
                        <?= $form->field($modelFisik, 'fisikMataTambahanOS[]', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control',
                                    'style' => 'margin-left:40px; margin-right:35px; width:500px;',
                                    'disabled' => $isDokter ? false : true,
                                ]); ?>
                    </td>
                    <td colspan="1" style="text-align: left; width: 25%;">
                        <div class="d-flex justify-content-center"
                            style="align-items: center; height: 100%; translate:-15px;">
                            <button type="button" class="btn btn-success addRowTambahan" name="addRowTambahan">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row" style="margin-left:25px; margin-top:30px;">
        <span>Catatan</span>
        <?= $form->field($modelFisik, 'catatanPemeriksaanFisik', [
            'labelOptions' => ['class' => ''],
        ])->textInput(['class' => 'form-control', 'style' => 'width:1250px;'])->label(false); ?>
    </div>
    <div class="help-block"></div>

    <hr>

    <div class="row">
        <h5 style="margin-left:25px;">Diagnostik Penunjang </h5>
        <div class="row" style="margin-left:25px;">
            <span>Vitreous Humor</span>
            <?= $form->field($modelFisik, 'vitreousHumor', [
                'labelOptions' => ['class' => ''],
            ])->textInput(['class' => 'form-control', 'style' => 'width:1250px;'])->label(false); ?>
        </div>
        <div class="help-block"></div>
        <div class="row" style="margin-left:25px;">
            <span>Funduskopi</span>
            <?= $form->field($modelFisik, 'funduskopi', [
                'labelOptions' => ['class' => ''],
            ])->textInput(['class' => 'form-control', 'style' => 'width:1250px;'])->label(false); ?>
        </div>
    </div>
</div>
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
    var is_draft = ' . $is_draft . ';
    var pendaftaranId = "' . $pendaftaran_id . '";
    var _model = "' . $modelName . '";
    var _modelName = _model.toLowerCase();
    var pemeriksaanfisik_id = "' . $modelFisik->pemeriksaanfisik_id . '";
    
    var _fisikMataTambahanOD = ' . json_encode($modelFisik->fisikMataTambahanOD) . ';
    var _bagMataTambahan = ' . json_encode($modelFisik->bagMataTambahan) . ';
    var _fisikMataTambahanOS = ' . json_encode($modelFisik->fisikMataTambahanOS) . ';
    var rowCountTambahan = 1;


    $(document).ready(function () {

        if (_bagMataTambahan) {
            $(".table-mata tbody tr:last").remove();
            let rowDataBagMataTambahan = "";
        
            $.each(_bagMataTambahan, function(index, value) {

                let bagMataTambahanValue = value;
                let fisikMataTambahanODValue = _fisikMataTambahanOD[index] || "";
                let fisikMataTambahanOSValue = _fisikMataTambahanOS[index] || "";
                
                if (rowCountTambahan === 1){
                    rowDataBagMataTambahan += `
                    <tr>
                        <td colspan="2">
                            <div class="form-group highlight-addon field-${_modelName}-fisikmatatambahanod" style="margin-right:30px; float:right;">
                                    <input type="text" id="${_modelName}-fisikmatatambahanod-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOD][]" 
                                    style="margin-left:10px; margin-right:35px; width:500px;" value="${fisikMataTambahanODValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px; display:flex; justify-content:center;">
                            <div class="form-group highlight-addon field-${_modelName}-bagmatatambahan">
                                    <input type="text" id="${_modelName}-bagmatatambahan-${rowCountTambahan}" class="form-control" name="${_model}[bagMataTambahan][]" 
                                    style="width:75px;" value="${bagMataTambahanValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group highlight-addon field-${_modelName}-fisikmatatambahanos">
                                    <input type="text" id="${_modelName}-fisikmatatambahanos-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOS][]" 
                                    style="margin-left:40px; margin-right:35px; width:500px;" value="${fisikMataTambahanOSValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="1" style="text-align: left; width:25%;">
                            <div class="d-flex justify-content-center" style="align-items: center; height: 100%;">
                                <button type="button" class="btn btn-success addRowTambahan" name="addRowTambahan" style="translate: -15px;">
                                    <i class="fa fa-plus"></i>
                            </div>
                        </td>
                    </tr>
                `;
                }
                else{
                rowDataBagMataTambahan += `
                    <tr>
                        <td colspan="2">
                            <div class="form-group highlight-addon field-${_modelName}-fisikmatatambahanod" style="margin-right:30px; float:right;">
                                    <input type="text" id="${_modelName}-fisikmatatambahanod-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOD][]" 
                                    style="margin-left:10px; margin-right:35px; width:500px;" value="${fisikMataTambahanODValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px; display:flex; justify-content:center;">
                            <div class="form-group highlight-addon field-${_modelName}-bagmatatambahan">
                                    <input type="text" id="${_modelName}-bagmatatambahan-${rowCountTambahan}" class="form-control" name="${_model}[bagMataTambahan][]" 
                                    style="width:75px;" value="${bagMataTambahanValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group highlight-addon field-${_modelName}-fisikmatatambahanos">
                                    <input type="text" id="${_modelName}-fisikmatatambahanos-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOS][]" 
                                    style="margin-left:40px; margin-right:35px; width:500px;" value="${fisikMataTambahanOSValue}">
                                    <div class="help-block"></div>
                            </div>
                        </td>
                        <td colspan="1" style="text-align: left; width:25%;">
                            <div class="d-flex justify-content-center" style="align-items: center; height: 100%;">
                                <button type="button" class="btn btn-success addRowTambahan" name="addRowTambahan" style="translate: -15px;">
                                    <i class="fa fa-plus"></i>
                                </button><button type="button" class="btn btn-danger deleteRowTambahan" name="deleteRowTambahan" style="translate: -5px;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                }
                rowCountTambahan++;
            });
        
            $(".table-mata tbody").append(rowDataBagMataTambahan);
        }        

        $("table tbody").on("click", ".addRowTambahan", function () {
            const $table = $(this).closest("table");

            const newRow = `
            <tr>
                <td colspan="2">
                    <div class="highlight-addon field-${_modelName}-fisikmatatambahanod" style="margin-right:30px; float:right;">
                            <input type="text" id="${_modelName}-fisikmatatambahanod-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOD][]" 
                            style="margin-left:10px; margin-right:35px; width:500px;">
                            <div class="help-block"></div>
                    </div>
                </td>
                <td colspan="1" style="text-align:center; font-weight:bold; translate:-15px; display:flex; justify-content:center;">
                    <div class="form-group highlight-addon field-${_modelName}-bagmatatambahan">
                            <input type="text" id="${_modelName}-bagmatatambahan-${rowCountTambahan}" class="form-control" name="${_model}[bagMataTambahan][]" 
                            style="width:75px;">
                            <div class="help-block"></div>
                    </div>
                </td>
                <td colspan="2">
                    <div class="form-group highlight-addon field-${_modelName}-fisikmatatambahanos">
                            <input type="text" id="${_modelName}-fisikmatatambahanos-${rowCountTambahan}" class="form-control" name="${_model}[fisikMataTambahanOS][]" 
                            style="margin-left:40px; margin-right:35px; width:500px;" >
                            <div class="help-block"></div>
                    </div>
                </td>
                <td colspan="1" style="text-align: left; width: 25%;">
                    <div class="d-flex justify-content-center" style="align-items: center; height: 100%;">
                        <button type="button" class="btn btn-success addRowTambahan" name="addRowTambahan" style="translate: -15px;">
                            <i class="fa fa-plus"></i>
                        </button><button type="button" class="btn btn-danger deleteRowTambahan" name="deleteRowTambahan" style="translate: -5px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            `;
            $table.find("tbody").append(newRow);
            rowCountTambahan++;
        });
    

        $("table tbody").on("click", ".deleteRowTambahan", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
            rowCountTambahan--;
        });
    });
', View::POS_END);
$this->registerJs($this->render('js/__fisik.js'), View::POS_END);
?>
