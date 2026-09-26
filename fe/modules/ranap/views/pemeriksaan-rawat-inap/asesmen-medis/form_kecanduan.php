<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

?>

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

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);

$formControl = 'form-control input-sm';
$classNumber = 'doco-number';
$labelTidakAda = 'Tidak Ada';
$labelAda = 'Ada';
$labelBantuanMinimal = 'Bantuan Minimal';
$labelBantuanTotal = 'Bantuan Total';
?>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-top:15px;">

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">I. Asesmen Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keluhan_utama')
                            ->textInput([
                                'class' => $formControl,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang')
                            ->checkboxList(
                                [
                                    'Hepatic Steatosis' => 'Hepatic Steatosis',
                                    'Hepatitis Alkoholic' => 'Hepatitis Alkoholic',
                                    'Sirosis Hepatic' => 'Sirosis Hepatic',
                                    'Gagal Jantung' => 'Gagal Jantung',
                                    'Hipertensi' => 'Hipertensi',
                                    'Pankreatitis' => 'Pankreatitis',
                                    'Hipoglikemia' => 'Hipoglikemia',
                                    'Anemia' => 'Anemia',
                                    'Kanker Hati' => 'Kanker Hati',
                                    'Pneumonia' => 'Pneumonia',
                                    'Stroke' => 'Stroke',
                                    'Impotensi' => 'Impotensi',
                                    'Osteoporosis' => 'Osteoporosis',
                                    'Demensia' => 'Demensia',
                                    'Sindrom Wernicke-Korsakoff' => 'Sindrom Wernicke-Korsakoff',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl, 'placeholder' => 'Riwayat Penyakit Sekarang Lainnya']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_penyakit_dahulu')
                            ->checkboxList(
                                [
                                    'Kebiasaan Merokok' => 'Kebiasaan Merokok',
                                    'Ketergantungan Alkohol' => 'Ketergantungan Alkohol',
                                    'Peningkatan Stres' => 'Peningkatan Stres',
                                    'Penggunaan Obat-Obatan' => 'Penggunaan Obat-Obatan',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_penyakit_dahulu_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl, 'placeholder' => 'Riwayat Penyakit Dahulu Lainnya']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Riwayat Keturunan/Keluarga </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_keturunan_obat')
                            ->label(Yii::t('fe', 'a. Riwayat keluarga dengan ketergantungan obat terlarang'))
                            ->radioList(
                                [
                                    $labelTidakAda => $labelTidakAda,
                                    $labelAda => $labelAda,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_keturunan_obat_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_keturunan_alkohol')
                            ->label(Yii::t('fe', 'b. Riwayat keluarga dengan ketergantungan alkohol'))
                            ->radioList(
                                [
                                    $labelTidakAda => $labelTidakAda,
                                    $labelAda => $labelAda,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_keturunan_alkohol_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_keturunan_kekerasan')
                            ->label(Yii::t('fe', 'c. Riwayat keluarga dengan korban kekerasan/penganiayaan'))
                            ->radioList(
                                [
                                    $labelTidakAda => $labelTidakAda,
                                    $labelAda => $labelAda,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_keturunan_kekerasan_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_keturunan_psikologis')
                            ->label(Yii::t('fe', 'd. Riwayat keluarga dengan masalah psikologis (stres)'))
                            ->radioList(
                                [
                                    $labelTidakAda => $labelTidakAda,
                                    $labelAda => $labelAda,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_keturunan_psikologis_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengkajian_psikologi')
                            ->checkboxList(
                                [
                                    'Cemas' => 'Cemas',
                                    'Marah' => 'Marah',
                                    'Sedih' => 'Sedih',
                                    'Gelisah' => 'Gelisah',
                                    'Takut' => 'Takut',
                                    'Tenang' => 'Tenang',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'respon_penerimaan')
                            ->label(Yii::t('fe', 'Respon Penerimaan Pasien Terhadap Penyakit'))
                            ->checkboxList(
                                [
                                    'Pasrah' => 'Pasrah',
                                    'Menolak' => 'Menolak',
                                    'Tawakkal' => 'Tawakkal',
                                    'Marah' => 'Marah',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Keadaan Umum </p>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'askep_kesadaran')
                                    ->label(Yii::t('fe', 'Kesadaran'))
                                    ->textInput(
                                        [
                                            'class' => $formControl,
                                        ]
                                    ); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'askep_gcs_e')
                                    ->label(Yii::t('fe', 'GCS Eye'))
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                            'placeholder' => 'GCS Eye'
                                        ]
                                    ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'askep_gcs_v')
                                    ->label(Yii::t('fe', 'GCS Verbal'))
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                            'placeholder' => 'GCS Verbal'
                                        ]
                                    ); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'askep_gcs_m')
                                    ->label(Yii::t('fe', 'GCS Motorik'))
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                            'placeholder' => 'GCS Motorik'
                                        ]
                                    ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Tanda-Tanda Vital </p>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                                ->textInput(
                                    [
                                        'class' => $formControl,
                                        'id' => 'tekanan_darah',
                                        'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                        'oninput' => "validateInput(this)",
                                    ]
                                )->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'frekuensi_nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                                ->textInput(
                                    [
                                        'class' => $formControl.' '.$classNumber,
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                        ]
                                    ); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                        ]
                                    ); ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                                    ->textInput(
                                        [
                                            'class' => $formControl.' '.$classNumber,
                                        ]
                                    ); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'suhu_tubuh', ['addon' => ['append' => ['content' => '°C']]])
                                    ->label(Yii::t('fe', 'Suhu Badan'))
                                    ->textInput(
                                        [
                                            'class' => $formControl.' doco-decimal-wcomma',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pemeriksaan_fisik')
                            ->checkboxList(
                                [
                                    'Insomnia' => 'Insomnia',
                                    'Tremor' => 'Tremor',
                                    'Amnesia' => 'Amnesia',
                                    'Gangguan Perilaku' => 'Gangguan Perilaku',
                                    'Gelisah' => 'Gelisah',
                                    'Mual' => 'Mual',
                                    'Nistagmus' => 'Nistagmus',
                                    'Diaforesis' => 'Diaforesis',
                                    'Jantung Berdebar' => 'Jantung Berdebar',
                                    'Gangguan Emosi' => 'Gangguan Emosi',
                                    'Halusinasi' => 'Halusinasi',
                                    'Kejang' => 'Kejang',
                                    'Gangguan Berpikir' => 'Gangguan Berpikir',
                                    'Lainnya' => 'Lainnya',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'pemeriksaan_fisik_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl, 'placeholder' => 'Pemeriksaan Fisik Lainnya']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Pemenuhan Kebutuhan Aktifitas Hidup Sehari-hari (ADL) </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_makan_minum')
                            ->label(Yii::t('fe', 'a. Makan/Minum'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_berpakaian')
                            ->label(Yii::t('fe', 'd. Berpakaian/Berhias'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'adl_mandi')
                            ->label(Yii::t('fe', 'b. Mandi'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'adl_istirahat')
                            ->label(Yii::t('fe', 'e. Istirahat dan Tidur'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'adl_bab')
                            ->label(Yii::t('fe', 'c. BAB/BAK'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_obat')
                            ->label(Yii::t('fe', 'f. Penggunaan Obat'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebutuhan_edukasi')
                            ->checkboxList(
                                [
                                    'Konseling' => 'Konseling',
                                    'Detoksifikasi' => 'Detoksifikasi',
                                    'Terapi Obat-Obatan' => 'Terapi Obat-Obatan',
                                    'Pemulihan di Rumah' => 'Pemulihan di Rumah',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'kebutuhan_edukasi_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosa_keperawatan')
                            ->textArea(
                                [
                                    'class' => $formControl,
                                    'rows' => 5,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencana_keperawatan')
                            ->label(Yii::t('fe', 'Rencana Keperawatan dan Tindakan'))
                            ->textArea(['class' => $formControl, 'rows' => 5,]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">II. Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengkajian_sistem')
                            ->textArea(
                                [
                                    'class' => $formControl,
                                    'rows' => 5,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosa_medis')
                            ->textArea(['class' => $formControl, 'rows' => 5,]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencana_terapi')
                            ->label(Yii::t('fe', 'Rencana Terapi dan Tindakan'))
                            ->textArea(
                                [
                                    'class' => $formControl,
                                    'rows' => 5,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'saran_dokter')
                            ->label(Yii::t('fe', 'Saran/Nasehat Dokter'))
                            ->textArea(['class' => $formControl, 'rows' => 5]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Data Penunjang </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'laboratorium')
                            ->label(Yii::t('fe', 'a. Laboratorium'))
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'radiologi')
                            ->label(Yii::t('fe', 'b. Radiologi'))
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs(
    '
    var isDokumenEklaim = "' . $isDokumenEklaim . '";
    function validateInput(input) {
        input.value = input.value.replace(/[^0-9/]/g, "");
        const parts = input.value.split("/");
        if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
            input.value = input.value.slice(0, -1);
        }
    }

    function toggleInput(checkbox, input) {
        if (checkbox.is(":checked")) {
            // container.show();
            input.prop("readonly", false);
        } else {
            // container.hide();
            input.val("").prop("readonly", true);
        }
    }

    $(document).ready(function () {
        $("#tekanan_darah").on("input", function () {
            validateInput(this);
        });
        
        const checkboxKebutuhanEdukasi = $("input[name=\'KecanduanForm[kebutuhan_edukasi][]\'][value=\'Lainnya\']");
        const checkboxRPS = $("input[name=\'KecanduanForm[riwayat_penyakit_sekarang][]\'][value=\'Lainnya\']");
        const checkboxRPD = $("input[name=\'KecanduanForm[riwayat_penyakit_dahulu][]\'][value=\'Lainnya\']");
        const inputKebutuhanEdukasi = $("#kecanduanform-kebutuhan_edukasi_lainnya");
        const inputRPS = $("#kecanduanform-riwayat_penyakit_sekarang_lainnya");
        const inputRPD = $("#kecanduanform-riwayat_penyakit_dahulu_lainnya");
        
        toggleInput(checkboxKebutuhanEdukasi, inputKebutuhanEdukasi);
        toggleInput(checkboxRPS, inputRPS);
        toggleInput(checkboxRPD, inputRPD);

        checkboxKebutuhanEdukasi.on("change", function () {
            toggleInput($(this), inputKebutuhanEdukasi);
        });
        checkboxRPS.on("change", function () {
            toggleInput($(this), inputRPS);
        });
        checkboxRPD.on("change", function () {
            toggleInput($(this), inputRPD);
        });

        // radio
        const checkboxKeturunanObat = $("input[name=\'KecanduanForm[riwayat_keturunan_obat]\']");
        const checkboxKeturunanAlkohol = $("input[name=\'KecanduanForm[riwayat_keturunan_alkohol]\']");
        const checkboxKeturunanKekerasan = $("input[name=\'KecanduanForm[riwayat_keturunan_kekerasan]\']");
        const checkboxKeturunanPsikologis = $("input[name=\'KecanduanForm[riwayat_keturunan_psikologis]\']");
        const inputKeturunanObat = $("#kecanduanform-riwayat_keturunan_obat_lainnya");
        const inputKeturunanAlkohol = $("#kecanduanform-riwayat_keturunan_alkohol_lainnya");
        const inputKeturunanKekerasan = $("#kecanduanform-riwayat_keturunan_kekerasan_lainnya");
        const inputKeturunanPsikologis = $("#kecanduanform-riwayat_keturunan_psikologis_lainnya");

        if(checkboxKeturunanObat.is(":checked")) {
            if($("input[name=\'KecanduanForm[riwayat_keturunan_obat]\']:checked").val() == "Ada") {
                inputKeturunanObat.prop("readonly", false);
            }
            else {
                inputKeturunanObat.val("").prop("readonly", true);
            }
        }
        if(checkboxKeturunanAlkohol.is(":checked")) {
            if($("input[name=\'KecanduanForm[riwayat_keturunan_alkohol]\']:checked").val() == "Ada") {
                inputKeturunanAlkohol.prop("readonly", false);
            }
            else {
                inputKeturunanAlkohol.val("").prop("readonly", true);
            }
        }
        if(checkboxKeturunanKekerasan.is(":checked")) {
            if($("input[name=\'KecanduanForm[riwayat_keturunan_kekerasan]\']:checked").val() == "Ada") {
                inputKeturunanKekerasan.prop("readonly", false);
            }
            else {
                inputKeturunanKekerasan.val("").prop("readonly", true);
            }
        }
        if(checkboxKeturunanPsikologis.is(":checked")) {
            if($("input[name=\'KecanduanForm[riwayat_keturunan_psikologis]\']:checked").val() == "Ada") {
                inputKeturunanPsikologis.prop("readonly", false);
            }
            else {
                inputKeturunanPsikologis.val("").prop("readonly", true);
            }
        }

        checkboxKeturunanObat.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == "Ada") {
                    inputKeturunanObat.prop("readonly", false);
                }
                else {
                    inputKeturunanObat.val("").prop("readonly", true);
                }
            }
        })
        checkboxKeturunanAlkohol.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == "Ada") {
                    inputKeturunanAlkohol.prop("readonly", false);
                }
                else {
                    inputKeturunanAlkohol.val("").prop("readonly", true);
                }
            }
        })
        checkboxKeturunanKekerasan.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == "Ada") {
                    inputKeturunanKekerasan.prop("readonly", false);
                }
                else {
                    inputKeturunanKekerasan.val("").prop("readonly", true);
                }
            }
        })
        checkboxKeturunanPsikologis.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == "Ada") {
                    inputKeturunanPsikologis.prop("readonly", false);
                }
                else {
                    inputKeturunanPsikologis.val("").prop("readonly", true);
                }
            }
        })
    });'
);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
