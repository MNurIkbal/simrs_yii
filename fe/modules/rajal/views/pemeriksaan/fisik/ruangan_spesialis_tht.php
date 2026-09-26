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

<h1 style="text-align:center;">Riwayat Penyakit & Pemeriksaan THT</h1>
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

<hr>

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

<hr>

<h2>Asesmen Medis</h2>

<hr>

<div class="row">
    <h5 style="margin-left:25px;">Kepala Leher</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/kepala_leher.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-leher" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Kepala Leher</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagKepalaLeher[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanKepalaLeher[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowKepalaLeher" name="addRowKepalaLeher"
                                    id="addRowKepalaLeher">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin:25px 0 15px 25px;">Telinga</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/telinga.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-telinga" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="3">Bagian Telinga</th>
                            <th colspan="5">Kanan</th>
                            <th colspan="6">Kiri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">1</td>
                            <td colspan="3">
                                <?= $form->field($modelFisik, 'bagTelinga[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:150px;'
                                        ]); ?>
                            </td>
                            <td colspan="5">
                                <?= $form->field($modelFisik, 'telingaKanan[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:200px;'
                                        ]); ?>
                            </td>
                            <td colspan="5">
                                <?= $form->field($modelFisik, 'telingaKiri[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:200px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-success addRowTelinga" name="addRowTelinga"
                                        id="addRowTelinga">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin-left:25px;">Hidung</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/hidung.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-hidung" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Hidung</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagHidung[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanHidung[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowHidung" name="addRowHidung"
                                    id="addRowHidung">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin-left:25px;">Mulut</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/mulut.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-mulut" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Mulut</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagMulut[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanMulut[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowMulut" name="addRowMulut"
                                    id="addRowMulut">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin-left:25px;">Pharynx</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/pharynx.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-pharynx" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Pharynx</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagPharynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanPharynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowPharynx" name="addRowPharynx"
                                    id="addRowPharynx">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin-left:25px;">Epipharynx</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/epipharynx.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-epipharynx" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Epipharynx</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagEpipharynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanEpipharynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowEpipharynx" name="addRowEpipharynx"
                                    id="addRowEpipharynx">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <h5 style="margin-left:25px;">Larynx</h5>
    <div class="row">
        <div class="col-md-4" style="left:2%;">
            <div class="image-frame">
                <?php
                echo Html::img('@web/media/img/img-pemeriksaan/larynx.png', ['class' => 'img-responsive', 'style' => 'width:350px; border:0.5px solid black;']);
                ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="table">
                <table class="table table-responsive table-larynx" style="border:0.5px solid black;">
                    <thead style="background-color:#38444c;">
                        <tr style="color:white;">
                            <th colspan="1" style="text-align:center;">No</th>
                            <th colspan="6">Bagian Larynx</th>
                            <th colspan="8">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="margin-bottom:5px;">
                            <td style="text-align: center;">
                                1
                            </td>
                            <td colspan="6">
                                <?= $form->field($modelFisik, 'bagLarynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:250px;'
                                        ]); ?>
                            </td>
                            <td colspan="7">
                                <?= $form->field($modelFisik, 'catatanLarynx[]', [
                                    'labelOptions' => ['class' => ''],
                                ])->label(false)->textInput([
                                            'class' => 'form-control',
                                            'disabled' => $isDokter ? false : true,
                                            'style' => 'width:350px;'
                                        ]); ?>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success addRowLarynx" name="addRowLarynx"
                                    id="addRowLarynx">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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

    var pemeriksaanfisik_id = "' . $modelFisik->pemeriksaanfisik_id . '";
    var is_draft = ' . $is_draft . ';
    var pendaftaranId = "' . $pendaftaran_id . '";
    var _bagKepalaLeher = ' . json_encode($modelFisik->bagKepalaLeher) . ';
    var _catatanKepalaLeher = ' . json_encode($modelFisik->catatanKepalaLeher) . ';
    var _bagTelinga = ' . json_encode($modelFisik->bagTelinga) . ';
    var _telingaKanan = ' . json_encode($modelFisik->telingaKanan) . ';
    var _telingaKiri = ' . json_encode($modelFisik->telingaKiri) . ';
    var _bagHidung  = ' . json_encode($modelFisik->bagHidung) . ';
    var _catatanHidung  = ' . json_encode($modelFisik->catatanHidung) . ';
    var _bagMulut = ' . json_encode($modelFisik->bagMulut) . ';
    var _catatanMulut = ' . json_encode($modelFisik->catatanMulut) . ';
    var _bagPharynx = ' . json_encode($modelFisik->bagPharynx) . ';
    var _catatanPharynx = ' . json_encode($modelFisik->catatanPharynx) . ';
    var _bagEpipharynx = ' . json_encode($modelFisik->bagEpipharynx) . ';
    var _catatanEpipharynx = ' . json_encode($modelFisik->catatanEpipharynx) . ';
    var _bagLarynx = ' . json_encode($modelFisik->bagLarynx) . ';
    var _catatanLarynx = ' . json_encode($modelFisik->catatanLarynx) . ';
    var _model = "' . $modelName . '"
    var _modelName = _model.toLowerCase()
    
    $(document).ready(function () {
        // Event delegation untuk addRow dan deleteRow di tabel Kepala Leher
        if (_bagKepalaLeher) {
            $(".table-leher tbody tr:first").remove();
            let rowDataBagKepalaLeher;
            let rowCountKepalaLeher = 0;
        
            $.each(_bagKepalaLeher, function(index, value) {
                rowCountKepalaLeher++;
        
                let bagKepalaLeherVal = value;
                let catatanKepalaLeherVal = _catatanKepalaLeher[index] || "";
        
                // Tentukan tombol add atau delete berdasarkan rowCount
                let _btnContent = rowCountKepalaLeher === 1
                    ? `<button type="button" class="btn btn-success addRowKepalaLeher" name="addRowKepalaLeher">
                            <i class="fa fa-plus"></i>
                       </button>`
                    : `<button type="button" class="btn btn-danger deleteRowKepalaLeher ms-1">
                            <i class="fa fa-trash"></i>
                       </button>`;
        
                // Buat baris tabel
                rowDataBagKepalaLeher = `
                    <tr>
                        <td style="text-align: center;">${rowCountKepalaLeher}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagkepalaleher-${rowCountKepalaLeher}" name="${_model}[bagKepalaLeher][]" class="form-control" style="width:250px;" value="${bagKepalaLeherVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatankepalaleher-${rowCountKepalaLeher}" name="${_model}[catatanKepalaLeher][]" class="form-control" style="width:350px;" value="${catatanKepalaLeherVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
        
                // Tambahkan baris ke tabel
                $(".table-leher tbody").append(rowDataBagKepalaLeher);
            });
        }
        
        $("table tbody").on("click", ".addRowKepalaLeher", function () {
            const $table = $(this).closest("table"); // Ambil tabel yang diklik
            const rowCountKepalaLeher = $table.find("tbody tr").length + 1;
            const newRowBagKepalaLeher = `
                <tr>
                    <td style="text-align: center;">${rowCountKepalaLeher}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagkepalaleher" name="${_model}[bagKepalaLeher][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatankepalaleher" name="${_model}[catatanKepalaLeher][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowKepalaLeher" name="addRowKepalaLeher">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowKepalaLeher ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRowBagKepalaLeher);
        });

        // Event delegation untuk deleteRow di tabel Kepala Leher
        $("table tbody").on("click", ".deleteRowKepalaLeher", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            // Update numbering hanya pada tabel yang diklik
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });

        // Event delegation untuk addRow dan deleteRow di tabel Telinga
        if (_bagTelinga) {
            $(".table-telinga tbody tr:first").remove();
            let rowDataBagTelinga;
            let rowCountTelinga = 0;
            
            $.each(_bagTelinga, function(index, value) {
                
                let bagTelingaVal = value;
                let telingaKananVal = _telingaKanan[index] || "";
                let telingaKiriVal = _telingaKiri[index] || "";
                
                rowCountTelinga++;

                let _btnContent = rowCountTelinga === 1 ? 
                    `<button type="button" class="btn btn-success addRowTelinga" name="addRowTelinga">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowTelinga ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagTelinga = `
                    <tr>
                        <td style="text-align: center;">${rowCountTelinga}</td>
                        <td colspan="3">
                            <input type="text" id="${_modelName}-bagtelinga-${rowCountTelinga}" name="${_model}[bagTelinga][]" class="form-control ${_modelName}-bagtelinga" style="width:150px;" value="${bagTelingaVal}">
                        </td>
                        <td colspan="5">
                            <input type="text" id="${_modelName}-telingaKanan-${rowCountTelinga}" name="${_model}[telingaKanan][]" class="form-control" style="width:200px;" value="${telingaKananVal}">
                        </td>
                        <td colspan="5">
                            <input type="text" id="${_modelName}-telingaKiri-${rowCountTelinga}" name="${_model}[telingaKiri][]" class="form-control" style="width:200px;" value="${telingaKiriVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-telinga tbody").append(rowDataBagTelinga);
            });
        }

        $("table tbody").on("click", ".addRowTelinga", function () {
            const $table = $(this).closest("table");
            const rowCountTelinga = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountTelinga}</td>
                    <td colspan="3">
                        <input type="text" id="${_modelName}-bagtelinga-${rowCountTelinga}" name="${_model}[bagTelinga][]" class="form-control" style="width:150px;">
                    </td>
                    <td colspan="5">
                        <input type="text" id="${_modelName}-telingaKanan-${rowCountTelinga}" name="${_model}[telingaKanan][]" class="form-control" style="width:200px;">
                    </td>
                    <td colspan="5">
                        <input type="text" id="${_modelName}-telingaKiri-${rowCountTelinga}" name="${_model}[telingaKiri][]" class="form-control" style="width:200px;">
                    </td>
                    <td style="text-align: center;">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-success addRowTelinga" name="addRowTelinga">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-danger deleteRowTelinga ms-1">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowTelinga", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });
        
        if (_bagHidung) {
            $(".table-hidung tbody tr:first").remove();
            let rowDataBagHidung;
            let rowCountHidung = 0;
            
            $.each(_bagHidung, function(index, value) {
                rowCountHidung++;

                let bagHidungVal = value;
                let catatanHidungVal = _catatanHidung[index] || "";

                let _btnContent = rowCountHidung === 1 ? 
                    `<button type="button" class="btn btn-success addRowHidung" name="addRowHidung">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowHidung ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagHidung = `
                    <tr>
                        <td style="text-align: center;">${rowCountHidung}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagHidung-${rowCountHidung}" name="${_model}[bagHidung][]" class="form-control" style="width:250px;" value="${bagHidungVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatanHidung-${rowCountHidung}" name="${_model}[catatanHidung][]" class="form-control" style="width:350px;" value="${catatanHidungVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-hidung tbody").append(rowDataBagHidung);
            });
        }

        $("table tbody").on("click", ".addRowHidung", function () {
            const $table = $(this).closest("table");
            const rowCountHidung = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountHidung}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagHidung-${rowCountHidung}" name="${_model}[bagHidung][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatanHidung-${rowCountHidung}" name="${_model}[catatanHidung][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowHidung" name="addRowHidung">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowHidung ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowHidung", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });

        // Event delegation untuk addRow dan deleteRow di tabel Mulut
        if (_bagMulut) {
            $(".table-mulut tbody tr:first").remove();
            let rowDataBagMulut;
            let rowCountMulut = 0;
            
            $.each(_bagMulut, function(index, value) {
                rowCountMulut++;
                
                let bagMulutVal = value;
                let catatanMulutVal = _catatanMulut[index] || "";

                let _btnContent = rowCountMulut === 1 ? 
                    `<button type="button" class="btn btn-success addRowMulut" name="addRowMulut">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowMulut ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagMulut = `
                    <tr>
                        <td style="text-align: center;">${rowCountMulut}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagMulut-${rowCountMulut}" name="${_model}[bagMulut][]" class="form-control" style="width:250px;" value="${bagMulutVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatanMulut-${rowCountMulut}" name="${_model}[catatanMulut][]" class="form-control" style="width:350px;" value="${catatanMulutVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-mulut tbody").append(rowDataBagMulut);
            });
        }
        $("table tbody").on("click", ".addRowMulut", function () {
            const $table = $(this).closest("table");
            const rowCountMulut = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountMulut}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagMulut-${rowCountMulut}" name="${_model}[bagMulut][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatanMulut-${rowCountMulut}" name="${_model}[catatanMulut][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowMulut" name="addRowMulut">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowMulut ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowMulut", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });

        // Event delegation untuk addRow dan deleteRow di tabel Pharynx
        if (_bagPharynx) {
            $(".table-pharynx tbody tr:first").remove();
            let rowDataBagPharynx;
            let rowCountPharynx = 0;
            
            $.each(_bagPharynx, function(index, value) {
                rowCountPharynx++;

                let bagPharynxVal = value;
                let catatanPharynxVal = _catatanPharynx[index] || "";

                let _btnContent = rowCountPharynx === 1 ? 
                    `<button type="button" class="btn btn-success addRowPharynx" name="addRowPharynx">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowPharynx ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagPharynx = `
                    <tr>
                        <td style="text-align: center;">${rowCountPharynx}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagPharynx-${rowCountPharynx}" name="${_model}[bagPharynx][]" class="form-control" style="width:250px;" value="${bagPharynxVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatanPharynx-${rowCountPharynx}" name="${_model}[catatanPharynx][]" class="form-control" style="width:350px;" value="${catatanPharynxVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-pharynx tbody").append(rowDataBagPharynx);
            });
        }
        $("table tbody").on("click", ".addRowPharynx", function () {
            const $table = $(this).closest("table");
            const rowCountPharynx = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountPharynx}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagPharynx-${rowCountPharynx}" name="${_model}[bagPharynx][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatanPharynx-${rowCountPharynx}" name="${_model}[catatanPharynx][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowPharynx" name="addRowPharynx">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowPharynx ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowPharynx", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });

        // Event delegation untuk addRow dan deleteRow di tabel Epipharynx
        if (_bagEpipharynx) {
            $(".table-epipharynx tbody tr:first").remove();
            let rowDataBagEpipharynx;
            let rowCountEpipharynx = 0;
            
            $.each(_bagEpipharynx, function(index, value) {
                rowCountEpipharynx++;

                let bagEpipharynxVal = value;
                let catatanEpipharynxVal = _catatanEpipharynx[index] || "";

                let _btnContent = rowCountEpipharynx === 1 ? 
                    `<button type="button" class="btn btn-success addRowEpipharynx" name="addRowEpipharynx">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowEpipharynx ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagEpipharynx = `
                    <tr>
                        <td style="text-align: center;">${rowCountEpipharynx}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagEpipharynx-${rowCountEpipharynx}" name="${_model}[bagEpipharynx][]" class="form-control" style="width:250px;" value="${bagEpipharynxVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatanEpipharynx-${rowCountEpipharynx}" name="${_model}[catatanEpipharynx][]" class="form-control" style="width:350px;" value="${catatanEpipharynxVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-epipharynx tbody").append(rowDataBagEpipharynx);
            });
        }
        $("table tbody").on("click", ".addRowEpipharynx", function () {
            const $table = $(this).closest("table");
            const rowCountEpipharynx = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountEpipharynx}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagEpipharynx-${rowCountEpipharynx}" name="${_model}[bagEpipharynx][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatanEpipharynx-${rowCountEpipharynx}" name="${_model}[catatanEpipharynx][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowEpipharynx" name="addRowEpipharynx">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowEpipharynx ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowEpipharynx", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });

        // Event delegation untuk addRow dan deleteRow di tabel Larynx
        if (_bagLarynx) {
            $(".table-larynx tbody tr:first").remove();
            let rowDataBagLarynx;
            let rowCountLarynx = 0;
            
            $.each(_bagLarynx, function(index, value) {
                rowCountLarynx++;

                let bagLarynxVal = value;
                let catatanLarynxVal = _catatanLarynx[index] || "";

                let _btnContent = rowCountLarynx === 1 ? 
                    `<button type="button" class="btn btn-success addRowLarynx" name="addRowLarynx">
                        <i class="fa fa-plus"></i>
                    </button>` : `<button type="button" class="btn btn-danger deleteRowLarynx ms-1">
                        <i class="fa fa-trash"></i>
                    </button>`;
        
                rowDataBagLarynx = `
                    <tr>
                        <td style="text-align: center;">${rowCountLarynx}</td>
                        <td colspan="6">
                            <input type="text" id="${_modelName}-bagLarynx-${rowCountLarynx}" name="${_model}[bagLarynx][]" class="form-control" style="width:250px;" value="${bagLarynxVal}">
                        </td>
                        <td colspan="7">
                            <input type="text" id="${_modelName}-catatanLarynx-${rowCountLarynx}" name="${_model}[catatanLarynx][]" class="form-control" style="width:350px;" value="${catatanLarynxVal}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-larynx tbody").append(rowDataBagLarynx);
            });
        }

        $("table tbody").on("click", ".addRowLarynx", function () {
            const $table = $(this).closest("table");
            const rowCountLarynx = $table.find("tbody tr").length + 1;
            const newRow = `
                <tr>
                    <td style="text-align: center;">${rowCountLarynx}</td>
                    <td colspan="6">
                        <input type="text" id="${_modelName}-bagLarynx-${rowCountLarynx}" name="${_model}[bagLarynx][]" class="form-control" style="width:250px;">
                    </td>
                    <td colspan="7">
                        <input type="text" id="${_modelName}-catatanLarynx-${rowCountLarynx}" name="${_model}[catatanLarynx][]" class="form-control" style="width:350px;">
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowLarynx" name="addRowLarynx">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteRowLarynx ms-1">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $table.find("tbody").append(newRow);
        });
        
        $("table tbody").on("click", ".deleteRowLarynx", function () {
            const $table = $(this).closest("table");
            $(this).closest("tr").remove();
        
            $table.find("tbody tr").each(function (index) {
                $(this).find("td:first-child").text(index + 1);
            });
        });
    });
', View::POS_END);
$this->registerJs($this->render('js/__fisik.js'), View::POS_END);
?>