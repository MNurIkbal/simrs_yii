<?php

use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:800px;margin-top:10px';
$labelTidakAda = 'Tidak Ada';
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

    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }

    .input-margin {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .table-vaksinasi {
        width: 100%;
        border-collapse: collapse;
    }

    .table-vaksinasi th,
    .table-vaksinasi td {
        border: 1px solid #000;
        padding: 8px;
    }

    .table-vaksinasi th {
        background-color: #f2f2f2;
    }
    .table-vaksinasi tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .table-vaksinasi tr:hover {
        background-color: #f1f1f1;
    }
    .table-vaksinasi input[type="checkbox"] {
        cursor: pointer;
    }

    .modal-open .modal {
        overflow-y: hidden !important;
    }
    .modal-body {
        height: 100%;
        max-height: 600px;
        overflow-y: auto;
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
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isRujukan')->radioList([
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], [
                                'itemOptions' => ['class' => 'rujukan-radio'],
                            ])->label(Yii::t('fe', 'Rujukan')); ?>
                        </div>
                        <div class="col-sm-6">
                            <div id="rujukan-ya" style="display:none; margin-left: 20px;">
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'rujukanDari', [
                                            'value' => '1',
                                            'label' => Yii::t('fe', 'RS'),
                                            'uncheck' => null,
                                            'id' => 'rujukanDari-rs',
                                            'class' => 'rujukanDari'])
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganRs')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'rujukanDari', [
                                            'value' => '2',
                                            'label' => Yii::t('fe', 'Puskesmas'),
                                            'uncheck' => null,
                                            'id' => 'rujukanDari-puskesmas',
                                            'class' => 'rujukanDari'])
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganPuskesmas')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'rujukanDari', [
                                            'value' => '3',
                                            'label' => Yii::t('fe', 'Dr'),
                                            'uncheck' => null,
                                            'id' => 'rujukanDari-dokter',
                                            'class' => 'rujukanDari'])
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganDokter')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'rujukanDari', [
                                            'value' => '4',
                                            'label' => Yii::t('fe', 'Lainnya'),
                                            'uncheck' => null,
                                            'id' => 'rujukanDari-lainnya',
                                            'class' => 'rujukanDari'])
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganLainnya')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                            </div>

                            <div id="rujukan-tidak" style="display:none; margin-left: 20px;">
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'datangTanpaRujukan', [
                                                'value' => '1',
                                                'label' => Yii::t('fe', 'Datang Sendiri'),
                                                'uncheck' => null,
                                                'id' => 'datangTanpaRujukan-sendiri',
                                                'class' => 'datangTanpaRujukan'
                                            ])
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganDatangSendiri')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= Html::activeRadio($model, 'datangTanpaRujukan', [
                                            'value' => '2',
                                            'label' => Yii::t('fe', 'Diantar'),
                                            'uncheck' => null,
                                            'id' => 'datangTanpaRujukan-diantar',
                                            'class' => 'datangTanpaRujukan'
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'keteranganDiantar')->textInput(['class' => $classForm])->label(false) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosaRujukan')->textArea() ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isRiwayatAlergi')->radioList([
                                '0' => $labelTidakAda,
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'riwayat-alergi-radio'],
                            ])->label(Yii::t('fe', 'Riwayat Alergi')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keteranganRiwayatAlergi')->textInput(['class' => $classForm.' keteranganRiwayatAlergi'])->label(false) ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'dokterPemeriksa')->dropdownList($dataDokter, [
                                'class' => 'form-control select2 selectDokter',
                                'prompt' => 'Pilih Dokter',
                            ]) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Anamnesa</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keluhanUtama')->textInput(['class' => $classForm]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayatPenyakitSekarang')->textArea(['class' => $classForm]) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Riwayat Penyakit Dahulu</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayatPenyakitDahulu')->checkboxList([
                                '1' => 'Hipertensi', '2' => 'DM',
                                3 => 'PJK', 4 => 'Asma', 5 => 'Stroke',
                                6 => 'Liver', 7 => 'Ginjal', 8 => 'TB Paru', 9 => 'Rokok',
                                10 => 'Minuman Alkohol', 11 => 'Lain-Lain'
                            ], [
                                // 'inline' => true,
                                'itemOptions' => ['class' => 'riwayatPenyakitDahulu-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6" style="margin-top:280px;">
                            <?= $form->field($model, 'riwayatPenyakitDahuluLainnya')->textInput(['class' => $classForm, 'id' => 'riwayatPenyakitDahuluLainnya'])->label(false) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isPernahDirawat')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ya, Pernah',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'isPernahDirawat-radio'],
                            ])->label(Yii::t('fe', 'Pernah Dirawat')); ?>
                        </div>
                        <div class="col-sm-6">
                            <div id="isPernahDirawat-ya" style="margin-left: 20px;">
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'isPernahDirawatKapan')->textInput(['class' => $classForm, 'id' => 'isPernahDirawatKapan'])->label(Yii::t('fe', 'Kapan')) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'isPernahDirawatDimana')->textInput(['class' => $classForm, 'id' => 'isPernahDirawatDimana'])->label(Yii::t('fe', 'Dimana')) ?>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-2">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'isPernahDirawatDiagnosa')->textInput(['class' => $classForm, 'id' => 'isPernahDirawatDiagnosa'])->label(Yii::t('fe', 'Diagnosa')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Riwayat Pengobatan (Termaksud obat yang sedang dikonsumsi)
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%" class="table-obat">
                                <thead>
                                    <tr>
                                        <th class="<?= $classCenter ?>" id="obatHeader">Nama Obat</th>
                                        <th class="<?= $classCenter ?>" id="dosisHeader">Dosis</th>
                                        <th class="<?= $classCenter ?>" id="waktuHeader">Waktu Penggunaan</th>
                                        <th class="<?= $classCenter ?>" id="aksiObat">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="margin-bottom:5px;margin-top:30px;">
                                        <td class="<?= $classCenter ?>">
                                            <input type="text" name="PenyakitDalamForm[namaObat][]"
                                            class="form-control input-sm namaObat input-margin">
                                        </td>
                                        <td class="<?= $classCenter ?>">
                                            <input type="text" name="PenyakitDalamForm[dosis][]"
                                                class="form-control input-sm dosis input-margin">
                                        </td>
                                        <td class="<?= $classCenter ?>">
                                            <input type="text" name="PenyakitDalamForm[waktuPenggunaan][]"
                                                class="form-control input-sm waktuPenggunaan input-margin">
                                        </td>
                                        <td style="text-align: center;width:15%;">
                                            <button type="button" class="btn btn-success addRowObat" name="addRowObat"
                                                id="addRowObat">
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
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Riwayat Penyakit Keluarga</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-md-6">
                            <?= $form->field($model, 'riwayatPenyakitKeluarga')->checkboxList([
                                '1' => 'Hipertensi',
                                '2' => 'Kencing Manis',
                                '3' => 'Jantung',
                                '4' => 'Asma',
                                '5' => 'Lain-Lain',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'riwayatPenyakitKeluarga-radio'],
                            ])->label(false); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'riwayatPenyakitKeluargaLainnya')->textInput(['class' => $classForm, 'id' => 'riwayatPenyakitKeluargaLainnya'])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Riwayat Sosial</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-md-6">
                            <?= $form->field($model, 'riwayatPenyakitSosial')->checkboxList([
                                '1' => 'Merokok',
                                '2' => 'Minum Alkohol',
                                '3' => 'Lain-Lain',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'riwayatPenyakitSosial-radio'],
                            ])->label(false); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'riwayatPenyakitSosialLainnya')->textInput(['class' => $classForm, 'id' => 'riwayatPenyakitSosialLainnya'])->label(false) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Penilaian Nyeri</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isNyeri')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ya',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'isNyeri-radio'],
                            ])->label(Yii::t('fe', 'Nyeri')); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lokasiNyeri')->textInput(['class' => $classForm.' lokasiNyeri'])->label(Yii::t('fe', 'Lokasi')) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'intensitasNyeri')->textInput(['class' => $classFormNumber.' intensitasNyeri'])->label(Yii::t('fe', 'Intesitas (0-10)')) ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'jenisNyeri')->radioList([
                                '1' => 'Akut',
                                '2' => 'Kronis',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'jenisNyeri-radio'],
                            ])->label(Yii::t('fe', 'Jenis')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'metodeNyeri')
                                ->dropdownList([1 => 'VAS', 2 => 'BPS', 3 => 'NIPS/FLACC'], ['class' => $classForm.' select2 selectMetode', 'prompt' => 'Pilih Metode'])
                                ->label(Yii::t('fe', 'Metode')) ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'skorNyeri')->textInput(['class' => $classForm])->label(Yii::t('fe', 'Skor')) ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Tanda-Tanda Vital</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-md-6">
                            <?= $form->field($model, 'keadaanUmum')->radioList([
                                '1' => 'Baik',
                                '2' => 'Sedang',
                                '3' => 'Lemah',
                                '4' => 'Jelek',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'keadaanUmum-radio'],
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'gizi')->radioList([
                                '1' => 'Baik',
                                '2' => 'Kurang',
                                '3' => 'Buruk',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'gizi-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <label class="control-label" for="">GCS:</label>
                        <div class="col-md-12">
                            <div class="form-inline">
                                <?= $form->field($model, 'gcsE', [
                                    'addon' => ['append' => ['content' => 'E']],
                                ])->textInput(['class' => $classFormNumber])->label(false); ?>
                                <?= $form->field($model, 'gcsM', [
                                    'addon' => ['append' => ['content' => 'M']],
                                ])->textInput(['class' => $classFormNumber])->label(false); ?>
                                <?= $form->field($model, 'gcsV', [
                                    'addon' => ['append' => ['content' => 'V']],
                                ])->textInput(['class' => $classFormNumber])->label(false); ?>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isTindakanResusitasi')->radioList([
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'isTindakanResusitasi-radio'],
                            ])->label(Yii::t('fe', 'Tindakan Resusitasi')); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'beratBadan', [
                                    'addon' => ['append' => ['content' => 'Kg']],
                            ])->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tinggiBadan', [
                                    'addon' => ['append' => ['content' => 'cm']],
                            ])->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tensi', ['addon' => ['append' => ['content' => 'mmHg']]])
                            ->textInput(
                                [
                                    'class' => 'form-control input-sm',
                                    'id' => 'tensi',
                                    'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                    'oninput' => "validateInput(this)",
                                ])->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nadi', [
                                    'addon' => ['append' => ['content' => 'x/menit']],
                            ])->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'respirasi', [
                                    'addon' => ['append' => ['content' => 'x/menit']],
                            ])->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suhu', [
                                    'addon' => ['append' => ['content' => '°C']],
                            ])->textInput(['class' => 'form-control doco-decimal-wcomma']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rektal', [
                                    'addon' => ['append' => ['content' => '°C']],
                            ])->textInput(['class' => 'form-control doco-decimal-wcomma']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Pemeriksaan Fisik</p>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Mata</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'anemis')->radioList([
                                '1' => 'Ada',
                                '0' => $labelTidakAda,
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'anemis-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pupil')->radioList([
                                '1' => 'Aniskor',
                                '2' => 'Sokor',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'anemis-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ikterus')->radioList([
                                '1' => 'Ada',
                                '0' => $labelTidakAda,
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'ikterus-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diameter', [
                                    'addon' => ['append' => ['content' => 'mm']],
                            ])->textInput(); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'udemPalpabrae')->radioList([
                                '1' => 'Ada',
                                '0' => $labelTidakAda,
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'ikterus-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">THT</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tonsil')->textInput(['class' => $classForm]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lidah')->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'faring')->textInput(['class' => $classForm]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bibir')->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Leher</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'jvp')->textInput(['class' => $classForm])->label(Yii::t('fe', 'JVP')); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembesaranKelenjar')->radioList([
                                '0' => $labelTidakAda,
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'pembesaranKelenjar-radio'],
                            ])->label(Yii::t('fe', 'Pembesaran Kelenjar Limfe')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembesaranKelenjarLainnya')->textInput(['class' => $classForm.' pembesaranKelenjarLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kakuDuduk')->radioList([
                                '0' => $labelTidakAda,
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'kakuDuduk-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kakuDudukLainnya')->textInput(['class' => $classForm.' kakuDudukLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'thorax')->radioList([
                                '1' => 'Simestris',
                                '2' => 'Asimestris',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'thorax-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'thoraxLainnya')->textInput(['class' => $classForm.' thoraxLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Cor</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'corS1S2')->textInput(['class' => $classForm.' corS1S2'])->label(Yii::t('fe', 'S1/S2')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'corIsReguler')->radioList([
                                '1' => 'Reguler',
                                '2' => 'Ireguler',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'corIsReguler-radio'],
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'corMurmur')->textInput(['class' => $classForm.' corMurmur'])->label(Yii::t('fe', 'Murmur')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'corLainLain')->textInput(['class' => $classForm.' corLainLain'])->label(Yii::t('fe', 'Lain-Lain')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Pulmo</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suaraNafas')->textInput(['class' => $classForm.' suaraNafas']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rinchi')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'rinchi-radio'],
                            ])->label(Yii::t('fe', 'Ronchi')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rinchiLainnya')->textInput(['class' => $classForm.' rinchiLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'wheezing')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'wheezing-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'wheezingLainnya')->textInput(['class' => $classForm.' wheezingLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Abdomen</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'distended')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'distended-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'meteorismus')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'meteorismus-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'peristaltik')->radioList([
                                '1' => 'Normal',
                                '2' => 'Meningkat',
                                '3' => 'Menurun',
                                '4' => $labelTidakAda,
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'peristaltik-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'asites')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'asites-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeriTekanan')->radioList([
                                '0' => $labelTidakAda,
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'nyeriTekanan-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeriTekananLokasi')->textInput(['class' => $classForm.' nyeriTekananLokasi'])->label(Yii::t('fe', 'Lokasi')); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hepar')->textInput(['class' => $classForm.' hepar']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lien')->textInput(['class' => $classForm.' lien']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'extermitas')->radioList([
                                '1' => 'Hangat',
                                '2' => 'Dingin',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'extermitas-radio'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'udem')->radioList([
                                '0' => 'Tidak',
                                '1' => 'Ada',
                            ], [
                                'inline' => true,
                                'itemOptions' => ['class' => 'udem-radio'],
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'udemLainnya')->textInput(['class' => $classForm.' udemLainnya'])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lainLain')->textArea(['class' => $classForm.' lainLain']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Hasil Pemeriksaan Penunjang</p>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosaKerja')->textArea(['class' => $classForm.' diagnosaKerja']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'laboratorium')->textArea(['class' => $classForm.' laboratorium']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ekg')->textArea(['class' => $classForm.' ekg']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'xray')->textArea(['class' => $classForm.' xray']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;margin-right:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'terapiTindakan')->textArea(['class' => $classForm.' terapiTindakan']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencanaKerja')->textArea(['class' => $classForm.' rencanaKerja']); ?>
                        </div>
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
var asesmenMedisId = "' . $asesmenMedisId . '";
var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"
var isRujukan = "' . $model->isRujukan . '";
var isPernahDirawat = "' . $model->isPernahDirawat . '";
var isPernahDirawatKapan = "' . $model->isPernahDirawatKapan . '";
var isPernahDirawatDimana = "' . $model->isPernahDirawatDimana . '";
var isPernahDirawatDiagnosa = "' . $model->isPernahDirawatDiagnosa . '";
var namaObat = ' . json_encode($model->namaObat) . ';
var dosis = ' . json_encode($model->dosis) . ';
var waktuPenggunaan = ' . json_encode($model->waktuPenggunaan) . ';

', View::POS_END);

$this->registerJs($this->render('js/penyakit-dalam.js'), View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
