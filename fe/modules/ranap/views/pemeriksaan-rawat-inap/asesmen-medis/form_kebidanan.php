<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\date\DatePicker;

$dateFormat = "yyyy-mm-dd";
$classForm = 'form-control';
$classFormNumber = $classForm.' doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'margin-top:10px;margin-bottom:10px;';
?>
<style>
.datepicker>div {
    display: block;
}
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-asmed-ranap',
                'id' => 'submit-asmed-ranap',
            ]
        ],
    ]); ?>
</div>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-bottom:25px;">
<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<br>
<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Anamnesa</h5>
            </div>
            <div class="panel-body">
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keluhan_utama')
                            ->label(Yii::t('fe', '1. Keluhan Utama'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_keluhan')
                            ->label(Yii::t('fe', '2. Riwayat Keluhan Utama'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_dahulu')
                        ->label(Yii::t('fe', '3. Riwayat Penyakit Dahulu/Faktor Resiko :'))
                        ->checkboxList([
                            '1' => 'Hipertensi',
                            '2' => 'Tb Paru',
                            '3' => 'DM',
                            '4' => 'Pernah Dirawat',
                            '5' => 'PJK',
                            '6' => 'Asma',
                            '7' => 'Lain-Lain',
                        ],
                        [
                            'itemOptions' => [
                                'class' => 'riwayat_penyakit_dahulu'
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group highlight-addon field-kebidananform-pernah_dirawat" style="margin-top:80px;">
                                <div class="col-sm-3">
                                    <div id="kebidananform-pernah_dirawat">
                                        <input type="hidden" name="KebidananForm[pernah_dirawat]">
                                        <label class="radio-inline"><input type="radio" class="pernah_dirawat" id="pernah_dirawat_0" name="KebidananForm[pernah_dirawat]" value="0"> Tidak</label>
                                        <label class="radio-inline"><input type="radio" class="pernah_dirawat" id="pernah_dirawat_1" name="KebidananForm[pernah_dirawat]" value="1"> Ya</label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="KebidananForm[kapan_dirawat]" id="kapan_dirawat" class="<?= $classForm?> kapan_dirawat" placeholder="Kapan">
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="KebidananForm[dimana_dirawat]" id="dimana_dirawat" class="<?= $classForm?> dimana_dirawat" placeholder="Dimana">
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="KebidananForm[diagnosis_dirawat]" id="diagnosis_dirawat" class="<?= $classForm?> diagnosis_dirawat" placeholder="Diagnosis">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group highlight-addon field-kebidananform-riwayat_penyakit_dahulu_lainnya" style="margin-top:50px;">
                                <div class="col-sm-6">
                                    <input type="text" name="KebidananForm[riwayat_penyakit_dahulu_lainnya]" id="riwayat_penyakit_dahulu_lainnya" class="<?= $classForm?> riwayat_penyakit_dahulu_lainnya">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">4. Riwayat Pengobatan (termasuk obat yang sedang dikonsumsi) </p>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table style="width:100%" class="table-kebidanan">
                            <thead>
                                <tr>
                                    <th style="<?= $styleTable?>" id="header_kehamilan1">No</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan2">Nama Obat</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan3">Dosis</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan4">Waktu Penggunaan</th>
                                    <th style="<?= $styleTable?>" id="header_kehamilan10">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">#</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KebidananForm[nama_obat][]" class="<?=$classForm?> nama_obat" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KebidananForm[dosis][]" class="<?=$classForm?> dosis" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KebidananForm[waktu_penggunaan][]" class="<?=$classForm?> waktu_penggunaan" style="<?=$styleCells?>">
                                    </td>
                                    <td style="text-align: center;width:5%;">
                                        <button type="button" class="btn btn-success addRowKebidanan" name="addRowKebidanan"
                                            id="addRowKebidanan">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_keluarga')
                        ->label(Yii::t('fe', '5. Riwayat Penyakit Keluarga :'))
                        ->checkboxList([
                            '1' => 'Hipertensi',
                            '2' => 'Kencing Manis',
                            '3' => 'Jantung',
                            '4' => 'Asma',
                            '5' => 'Kanker',
                            '6' => 'Penyakit Jiwa',
                            '7' => 'Lainnya',
                        ],
                        [
                            'itemOptions' => [
                                'class' => 'riwayat_penyakit_keluarga'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_alergi')
                        ->label(Yii::t('fe', '6. Riwayat Alergi'))
                        ->radioList([
                            '0' => 'Tidak Ada',
                            '1' => 'Ada',
                        ],
                        [
                            'inline' => true,
                            'itemOptions' => [
                                'class' => 'riwayat_alergi'
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_alergi_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">7. Riwayat Obstetri </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_g')
                            ->label(Yii::t('fe', 'a. G'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_p')
                            ->label(Yii::t('fe', 'P'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_a')
                            ->label(Yii::t('fe', 'A'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_hpht')
                        ->label(Yii::t('fe', 'b. HPHT'))
                        ->widget(DatePicker::classname(), [
                            'options' => ['readonly' => true, 'class' => 'riwayat_obs_hpht'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat,
                                'orientation' => 'bottom'
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_tp')
                        ->label(Yii::t('fe', 'TP'))
                        ->widget(DatePicker::classname(), [
                            'options' => ['readonly' => true],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat,
                                'orientation' => 'bottom'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_obs_kehamilan')
                            ->label(Yii::t('fe', 'c. Riwayat Kehamilan, Persalinan dan Nifas'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_ginekologi')
                        ->label(Yii::t('fe', '8. Riwayat Ginekologi :'))
                        ->checkboxList([
                            '1' => 'Infertilitas',
                            '2' => 'Infeksi Virus',
                            '3' => 'PMS',
                            '4' => 'Endometriosis',
                            '5' => 'Myoma',
                            '6' => 'Kankers',
                            '7' => 'Riwayat Operasi',
                            '8' => 'Lainnya',
                        ],
                        [
                            'itemOptions' => [
                                'class' => 'riwayat_ginekologi'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_kb')
                        ->label(Yii::t('fe', '9. Riwayat KB :'))
                        ->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemeriksaan Fisis</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">1. Pemeriksaan Umum </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keadaan_umum')
                            ->label(Yii::t('fe', 'a. Keadaan Umum (KU)'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'kesadaran')
                            ->label(Yii::t('fe', 'b. Kesadaran'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">c. Tanda-Tanda Vital </p>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                        ->label(Yii::t('fe', 'TD'))
                        ->textInput(
                            [
                                'class' => $classForm,
                                'id' => 'tekanan_darah',
                                'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                'oninput' => "validateInput(this)",
                            ])->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'frekuensi_nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                        ->label(Yii::t('fe', 'Nadi'))
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'suhu_badan', ['addon' => ['append' => ['content' => '°C']]])
                        ->label(Yii::t('fe', 'Suhu'))
                        ->textInput(['class' => 'form-control doco-decimal-wcomma']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                        ->label(Yii::t('fe', 'Pernapasan'))
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">2. Pemeriksaan Payudara </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'puting_susu')
                        ->label(Yii::t('fe', 'a. Puting Susu :'))
                        ->radioList([
                            '1' => 'Menonjol',
                            '2' => 'Tenggelam',
                            '3' => 'Datar',
                        ],
                        [
                            'inline' => true,
                            'itemOptions' => [
                                'class' => 'puting_susu'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pengeluaran_asi')
                            ->label(Yii::t('fe', 'b. Pengeluaran ASI'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'colostrum')
                            ->label()->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">3. Pemeriksaan Perut </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tfu')
                            ->label(Yii::t('fe', 'a. TFU'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'kontraksi_uterus')
                            ->label(Yii::t('fe', 'b. Kontraksi Uterus'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'luka_operasi')
                            ->label(Yii::t('fe', 'c. Luka Operasi'))->radioList(['1' => 'Kering', '2' => 'Basah'], ['inline' => true]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'kandung_kemih')
                            ->label(Yii::t('fe', 'd. Kandung Kemih'))->radioList(['1' => 'Kosong', '2' => 'Penuh'], ['inline' => true]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">4. Pemeriksaan Vulva dan Perineum </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pengeluaran_lochia')
                            ->label(Yii::t('fe', 'a. Pengeluaran Lochia'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'luka_perineum')
                            ->label(Yii::t('fe', 'b. Luka Perineum'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Hasil Pemeriksaan Penunjang</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'lab')
                            ->label(Yii::t('fe', '1. Laboratorium'))->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'usg')
                            ->label(Yii::t('fe', '2. USG'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'xray')
                            ->label(Yii::t('fe', '3. X-Ray'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Diagnosa Kebidanan dan Masalah</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa_kebidanan')
                            ->label(false)->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <table style="width:100%" class="table-terapi">
                            <thead>
                                <tr>
                                    <th style="<?= $styleTable?>" id="header_terapi1">No</th>
                                    <th style="<?= $styleTable?>" id="header_terapi2">Tindakan</th>
                                    <th style="<?= $styleTable?>" id="header_terapi3">Dosis</th>
                                    <th style="<?= $styleTable?>" id="header_terapi4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="margin-bottom:5px;margin-top:30px;">
                                    <td class="<?=$classCenter?>">#</td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KebidananForm[tindakan][]" class="<?=$classForm?> tindakan" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KebidananForm[dosis_terapi][]" class="<?=$classForm?> dosis_terapi" style="<?=$styleCells?>">
                                    </td>
                                    <td style="text-align: center;width:5%;">
                                        <button type="button" class="btn btn-success addRowTerapi" name="addRowTerapi"
                                            id="addRowTerapi">
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
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
var isDokumenEklaim = "' . $isDokumenEklaim . '";
var _pernah_dirawat = "'.$model->pernah_dirawat.'"
var _kapan_dirawat = "'.$model->kapan_dirawat.'"
var _dimana_dirawat = "'.$model->dimana_dirawat.'"
var _diagnosis_dirawat = "'.$model->diagnosis_dirawat.'"
var riwayat_alergi_lainnya = "'.$model->riwayat_alergi_lainnya.'"
var riwayat_penyakit_dahulu_lainnya = "'.$model->riwayat_penyakit_dahulu_lainnya.'"

var namaObat = '.json_encode($model->nama_obat).'
var dosis = '.json_encode($model->dosis).'
var waktuPenggunaan = '.json_encode($model->waktu_penggunaan).'
var tindakan = '.json_encode($model->tindakan).'
var dosisTerapi = '.json_encode($model->dosis_terapi).'

var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"
var _modelForm = "KebidananForm"
var asesmenMedisId = "'.$asesmenMedisId.'"

function validateInput(input) {
    input.value = input.value.replace(/[^0-9/]/g, "");
    const parts = input.value.split("/");
    if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
        input.value = input.value.slice(0, -1);
    }
}
function resetFormKebidanan() {
    $(".nama_obat").val(null)
    $(".dosis").val(null)
    $(".waktu_penggunaan").val(null)
}
function resetFormTerapi() {
    $(".tindakan").val(null)
    $(".dosis_terapi").val(null)
}

$(document).ready(function(){
    $("#tekanan_darah").on("input", function () {
        validateInput(this);
    });
    
    const checkboxPD = $("input[name=\'KebidananForm[riwayat_penyakit_dahulu][]\'][value=\'4\']");
    const checkboxLain = $("input[name=\'KebidananForm[riwayat_penyakit_dahulu][]\'][value=\'7\']");
    const checkboxRA = $("input[name=\'KebidananForm[riwayat_alergi]\']");
    const checkboxPD2 = $("input[name=\'KebidananForm[pernah_dirawat]\']");

    const kapan = $(`#kapan_dirawat`);
    const dimana = $(`#dimana_dirawat`);
    const diagnosis = $(`#diagnosis_dirawat`);
    const otherRA = $(`#kebidananform-riwayat_alergi_lainnya`);
    const otherRPD = $(`#riwayat_penyakit_dahulu_lainnya`);

    $(".pernah_dirawat").prop("disabled", true)
    kapan.prop("readonly", true)
    dimana.prop("readonly", true)
    diagnosis.prop("readonly", true)
    otherRA.prop("readonly", true)
    otherRPD.prop("readonly", true)
    
    if(asesmenMedisId) {
        if(_pernah_dirawat) {
            if(_pernah_dirawat == "1") {
                $(".pernah_dirawat").prop("disabled", false)
                $("#pernah_dirawat_1").prop("checked", true)
                kapan.prop("readonly", false)
                dimana.prop("readonly", false)
                diagnosis.prop("readonly", false)
                kapan.val(_kapan_dirawat)
                dimana.val(_dimana_dirawat)
                diagnosis.val(_diagnosis_dirawat)
            }
            else if(_pernah_dirawat == "0") {
                $(".pernah_dirawat").prop("disabled", false)
                $("#pernah_dirawat_0").prop("checked", true)
                kapan.prop("readonly", true)
                dimana.prop("readonly", true)
                diagnosis.prop("readonly", true)
                kapan.val("")
                dimana.val("")
                diagnosis.val("")
            }
            else {
                $(".pernah_dirawat").prop("disabled", true)
                $("#pernah_dirawat_0").prop("checked", true)
                kapan.prop("readonly", true)
                dimana.prop("readonly", true)
                diagnosis.prop("readonly", true)
                kapan.val("")
                dimana.val("")
                diagnosis.val("")
            }
        }

        if(riwayat_alergi_lainnya) {
            otherRA.prop("readonly", false)
        }
        if(riwayat_penyakit_dahulu_lainnya) {
            otherRPD.val(riwayat_penyakit_dahulu_lainnya)
            otherRPD.prop("readonly", false)
        }
    }

    checkboxPD.change(function () {
        if(checkboxPD.is(":checked")) {
            $(".pernah_dirawat").prop("disabled", false)
        }
        else {
            $(".pernah_dirawat").prop("disabled", true)
            $("#pernah_dirawat_1").prop("checked", false)
            $("#pernah_dirawat_0").prop("checked", false)
            kapan.prop("readonly", true)
            dimana.prop("readonly", true)
            diagnosis.prop("readonly", true)
            kapan.val("")
            dimana.val("")
            diagnosis.val("")
        }
    });
    checkboxPD2.change(function () {
        if(checkboxPD2.is(":checked") && $(this).val() == "1") {
            kapan.prop("readonly", false)
            dimana.prop("readonly", false)
            diagnosis.prop("readonly", false)
        }
        else {
            kapan.val("").prop("readonly", true)
            dimana.val("").prop("readonly", true)
            diagnosis.val("").prop("readonly", true)
        }
    });
    checkboxRA.change(function () {
        if(checkboxRA.is(":checked")) {
            if($(this).val() == "1") {
                otherRA.prop("readonly", false);
            }
            else {
                otherRA.val("").prop("readonly", true);
            }
        }
        else {
            otherRA.val("").prop("readonly", true);
        }
    });
    checkboxLain.change(function () {
        if(checkboxLain.is(":checked")) {
            otherRPD.prop("readonly", false)
        }
        else {
            otherRPD.val("").prop("readonly", true)
        }
    });
    if (namaObat) {
        let rowDataKebidanan;
        let rowCountKebidanan = 0;
        $.each(namaObat, function(index, value) {
            if (value) {
                rowCountKebidanan++;
    
                let __obat = value;
                let __dosis = dosis[index] || "";
                let __waktu = waktuPenggunaan[index] || "";
        
                let _btnContent = rowCountKebidanan === "#"
                    ? `<button type="button" class="btn btn-success addRowKebidanan" name="addRowKebidanan">
                            <i class="fa fa-plus"></i>
                        </button>`
                    : `<button type="button" class="btn btn-danger deleteRowKebidanan ms-1">
                            <i class="fa fa-trash"></i>
                        </button>`;
                
                rowDataKebidanan = `
                    <tr>
                        <td class="${_classCenter}">${rowCountKebidanan}</td>
                        <td>
                            <input type="text" name="${_modelForm}[nama_obat][]" class="${_classForm}" style="${_styleCells}" value="${__obat}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[dosis][]" class="${_classForm}" style="${_styleCells}" value="${__dosis}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[waktu_penggunaan][]" class="${_classForm}" style="${_styleCells}" value="${__waktu}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
                $(".table-kebidanan tbody").append(rowDataKebidanan);
            }
        });
    }

    $("table tbody").on("click", ".addRowKebidanan", function () {
        const $table = $(this).closest("table");
        let counterKebidanan = $table.find("tbody tr").length;

        const _obat = $(".nama_obat").val()
        const _dosis = $(".dosis").val()
        const _waktu_penggunaan = $(".waktu_penggunaan").val()
        const newRowKebidanan = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td class="${_classCenter}">${counterKebidanan}</td>
                <td>
                    <input type="text" name="${_modelForm}[nama_obat][]" class="${_classForm}" style="${_styleCells}" value="${_obat}">
                </td>
                <td>
                    <input type="text" name="${_modelForm}[dosis][]" class="${_classForm}" style="${_styleCells}" value="${_dosis}">
                </td>
                <td>
                    <input type="text" name="${_modelForm}[waktu_penggunaan][]" class="${_classForm}" style="${_styleCells}" value="${_waktu_penggunaan}">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowKebidanan ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowKebidanan);
        resetFormKebidanan()
    });
    $("table tbody").on("click", ".deleteRowKebidanan", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    
        $table.find("tbody tr").each(function (index) {
            $(this).find("td:first-child").text(index);
            if($(this).find("td:first-child").text() == "0") {
                $(this).find("td:first-child").text("#")
            }
        });
    });
    
    if (tindakan) {
        let rowDataTerapi;
        let rowCountTerapi = 0;
        $.each(tindakan, function(index, value) {
            if (value) {
                rowCountTerapi++;
    
                let __tindakan = value;
                let __dosisTerapi = dosisTerapi[index] || "";
        
                let _btnContentTerapi = rowCountTerapi === "#"
                    ? `<button type="button" class="btn btn-success addRowTerapi" name="addRowTerapi">
                            <i class="fa fa-plus"></i>
                        </button>`
                    : `<button type="button" class="btn btn-danger deleteRowTerapi ms-1">
                            <i class="fa fa-trash"></i>
                        </button>`;
                
                rowDataTerapi = `
                    <tr>
                        <td class="${_classCenter}">${rowCountTerapi}</td>
                        <td>
                            <input type="text" name="${_modelForm}[tindakan][]" class="${_classForm}" style="${_styleCells}" value="${__tindakan}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[dosis_terapi][]" class="${_classForm}" style="${_styleCells}" value="${__dosisTerapi}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContentTerapi}
                        </td>
                    </tr>
                `;
                $(".table-terapi tbody").append(rowDataTerapi);
            }
        });
    }

    $("table tbody").on("click", ".addRowTerapi", function () {
        const $table = $(this).closest("table");
        let counterTerapi = $table.find("tbody tr").length;

        const _tindakan = $(".tindakan").val()
        const _dosisTerapi = $(".dosis_terapi").val()

        const newRowTerapi = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td class="${_classCenter}">${counterTerapi}</td>
                <td>
                    <input type="text" name="${_modelForm}[tindakan][]" class="${_classForm}" style="${_styleCells}" value="${_tindakan}">
                </td>
                <td>
                    <input type="text" name="${_modelForm}[dosis_terapi][]" class="${_classForm}" style="${_styleCells}" value="${_dosisTerapi}">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowTerapi ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowTerapi);
        resetFormTerapi()
    });
    $("table tbody").on("click", ".deleteRowTerapi", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    
        $table.find("tbody tr").each(function (index) {
            $(this).find("td:first-child").text(index);
            if($(this).find("td:first-child").text() == "0") {
                $(this).find("td:first-child").text("#")
            }
        });
    });
})
', View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
