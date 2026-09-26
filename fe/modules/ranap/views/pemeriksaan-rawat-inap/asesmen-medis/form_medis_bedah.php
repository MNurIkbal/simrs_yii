<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;

$classForm = 'form-control';
$classFormNumber = $classForm.' doco-number';
$classFormComa = $classForm.' doco-decimal-wcomma';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'margin-top:10px;margin-bottom:10px;';
?>
<style>
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
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keluhan_utama')
                            ->label(Yii::t('fe', '1. Keluhan Utama'))->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_sekarang')
                            ->label(Yii::t('fe', '2. Riwayat Penyakit Sekarang'))->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_dahulu')
                            ->label(Yii::t('fe', '3. Penyakit Dahulu/Faktor Resiko'))
                            ->checkboxList([
                                '1' => 'Hipertensi',
                                '2' => 'DM',
                                '3' => 'PJK',
                                '4' => 'Asma',
                                '5' => 'Stroke',
                                '6' => 'Liver',
                                '7' => 'Ginjal',
                                '8' => 'TB Paru',
                                '9' => 'Rokok',
                                '10' => 'Minum Alkohol',
                                '11' => 'Lain-Lain',
                            ]); ?>
                    </div>
                    <div class="col-md-6" style="margin-top:280px;">
                        <?= $form->field($model, 'riwayat_penyakit_dahulu_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <p style="margin-left:10px;margin-top:20px;margin-bottom:20px;">4. Riwayat Pengobatan (termasuk obat yang sedang dikonsumsi) </p>
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
                                        <input type="text" name="KulitForm[nama_obat][]" class="<?=$classForm?> nama_obat" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KulitForm[dosis][]" class="<?=$classForm?> dosis" style="<?=$styleCells?>">
                                    </td>
                                    <td class="<?=$classCenter?>">
                                        <input type="text" name="KulitForm[waktu_penggunaan][]" class="<?=$classForm?> waktu_penggunaan" style="<?=$styleCells?>">
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
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_keluarga')
                        ->label(Yii::t('fe', '5. Riwayat Penyakit Keluarga'))
                        ->checkboxList([
                            '1' => 'Hipertensi',
                            '2' => 'Kencing Manis',
                            '3' => 'Jantung',
                            '4' => 'Asma',
                            '5' => 'Lainnya',
                        ],
                        [
                            'itemOptions' => [
                                'class' => 'riwayat_penyakit_keluarga'
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'riwayat_penyakit_keluarga_lainnya')->label(false)
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:10px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Penilaian Nyeri </p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'nyeri')
                            ->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ya',
                            ],['inline' => true]); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group highlight-addon field-medisbedahform-pernah_dirawat">
                                <div class="col-sm-6">
                                    <input type="text" name="MedisBedahForm[lokasi_nyeri]" id="lokasi_nyeri" class="<?= $classForm?> lokasi_nyeri" placeholder="Lokasi">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="MedisBedahForm[intensitas_nyeri]" id="intensitas_nyeri" class="<?= $classForm?> intensitas_nyeri" placeholder="Intensitas (0-10)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'jenis_nyeri')->label(Yii::t('fe', 'Jenis'))
                        ->radioList(['1' => 'Akut', '2' => 'Kronis'], ['inline' => true]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'skor_nyeri')->label(Yii::t('fe', 'Skor'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:10px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Tanda-Tanda Vital</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keadaan_umum')->radioList([
                            '1' => 'Baik', '2' => 'Sedang',
                            '3' => 'Lemah', '4' => 'Jelek',
                        ], ['inline' => true]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'gizi')->radioList([
                            '1' => 'Baik', '2' => 'Kurang', '3' => 'Buruk'
                        ], ['inline' => true]); ?>
                    </div>
                </div>
                <div class="row">
                    <p style="margin-left:10px;margin-top:20px;margin-bottom:20px;font-weight:bold;">GCS</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'gcs_e')->label(Yii::t('fe', 'Eye'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'gcs_m')->label(Yii::t('fe', 'Motorik'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'gcs_v')->label(Yii::t('fe', 'Verbal'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tindakan_resusitasi')->radioList([
                            '1' => 'Ya', '0' => 'Tidak',
                        ], ['inline' => true]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'Kg']]])
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'Cm']]])
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                        ->label(Yii::t('fe', 'Tensi'))
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
                        <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                        ->label(Yii::t('fe', 'Respirasi'))
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'suhu_badan', ['addon' => ['append' => ['content' => '°C']]])
                        ->label(Yii::t('fe', 'Suhu Axilia/Rektal'))
                        ->textInput(['class' => $classFormComa]); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <p style="margin-left:20px;margin-bottom:20px;text-align:center;font-weight:bold;">Secondary Survey </p>
                </div>
                <div class="row" style="text-align:center;">
                    <div class="image-frame">
                        <?php
                            echo Html::img( '@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
                                'width'=> 500,
                                'height'=> 520,
                            ]);
                        ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'hasil_penunjang')
                            ->label(Yii::t('fe', 'Hasil Pemeriksaan Penunjang'))->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa')->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'terapi')->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'hasil_pembedahan')->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa_akhir')->textArea(['class' => $classForm]); ?>
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
var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"
var _modelForm = "MedisBedahForm"
var asesmenMedisId = "'.$asesmenMedisId.'"

var namaObat = '.json_encode($model->nama_obat).'
var dosis = '.json_encode($model->dosis).'
var waktuPenggunaan = '.json_encode($model->waktu_penggunaan).'

var nyeri = "' . $model->nyeri . '";
var lokasi_nyeri = "' . $model->lokasi_nyeri . '";
var intensitas_nyeri = "' . $model->intensitas_nyeri . '";

var riwayat_penyakit_dahulu_lainnya = "' . $model->riwayat_penyakit_dahulu_lainnya . '";
var riwayat_penyakit_keluarga_lainnya = "' . $model->riwayat_penyakit_keluarga_lainnya . '";

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
$(document).ready(function(){
    $("#tekanan_darah").on("input", function () {
        validateInput(this);
    });
    
    const checkboxPenyakitDahulu = $("input[name=\'MedisBedahForm[riwayat_penyakit_dahulu][]\'][value=\'11\']");
    const checkboxRPK = $("input[name=\'MedisBedahForm[riwayat_penyakit_keluarga][]\'][value=\'5\']");
    const checkboxNyeri = $("input[name=\'MedisBedahForm[nyeri]\']");

    const otherRPD = $(`#medisbedahform-riwayat_penyakit_dahulu_lainnya`);
    const otherRPK = $(`#medisbedahform-riwayat_penyakit_keluarga_lainnya`);
    const lokasi = $(`#lokasi_nyeri`);
    const intensitas = $(`#intensitas_nyeri`);
    
    otherRPD.prop("readonly", true)
    otherRPK.prop("readonly", true)
    lokasi.prop("readonly", true)
    intensitas.prop("readonly", true)
    
    if(asesmenMedisId) {
        if(riwayat_penyakit_dahulu_lainnya) {
            otherRPD.prop("readonly", false)
        }
        if(riwayat_penyakit_keluarga_lainnya) {
            otherRPK.prop("readonly", false)
        }
        if(nyeri && nyeri == "1") {
            lokasi.val(lokasi_nyeri)
            intensitas.val(intensitas_nyeri)

            lokasi.prop("readonly", false)
            intensitas.prop("readonly", false)
        }
    }

    checkboxPenyakitDahulu.change(function () {
        if(checkboxPenyakitDahulu.is(":checked")) {
            otherRPD.prop("readonly", false)
        }
        else {
            otherRPD.val("").prop("readonly", true)
        }
    });
    checkboxRPK.change(function () {
        if(checkboxRPK.is(":checked")) {
            otherRPK.prop("readonly", false)
        }
        else {
            otherRPK.val("").prop("readonly", true)
        }
    });
    checkboxNyeri.change(function () {
        if(checkboxNyeri.is(":checked") && $(this).val() == "1") {
            lokasi.prop("readonly", false)
            intensitas.prop("readonly", false)
        }
        else {
            lokasi.val("").prop("readonly", true)
            intensitas.val("").prop("readonly", true)
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
})
', View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
