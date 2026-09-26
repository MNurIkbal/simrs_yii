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
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);

$items = [];
$items["riwayat_sakit_lama"] = [
    'Diabetes Melitus' => 'Diabetes Melitus',
    'Meningitis' => 'Meningitis',
    'Kanker' => 'Kanker',
    'AIDS' => 'AIDS',
    'Stroke multiple sklerosis' => 'Stroke multiple sklerosis',
    'Stroke' => 'Stroke',
    'Gangguan Darah' => 'Gangguan Darah',
    'CKD' => 'CKD',
    'Thyroid' => 'Thyroid',
    'Lainnya' => 'Lainnya'
];
$items["respon_penerimaan"] = [
    'Pasrah' => 'Pasrah',
    'Tawakkal' => 'Tawakkal',
    'Lainnya' => 'Lainnya',
    'Menolak' => 'Menolak',
    'Marah' => 'Marah',
];
$items["pengkajian_psikologis"] = [
    'Pasrah' => 'Pasrah',
    'Tawakkal' => 'Tawakkal',
    'Menolak' => 'Menolak',
    'Marah' => 'Marah',
    'Stres' => 'Stres',
    'Lainnya' => 'Lainnya'
];
$items["pemenuhan_adl"] = [
    'Bantuan minimal' => 'Bantuan minimal',
    'Bantuan total' => 'Bantuan total'
];
$items["pemeriksaan_fisik"] = [
    'Sianosis' => 'Sianosis',
    'Hipotensi' => 'Hipotensi',
    'Dispnea' => 'Dispnea',
    'Muntah' => 'Muntah',
    'Akral dingin' => 'Akral dingin',
    'Bradikardia' => 'Bradikardia',
    'Mual' => 'Mual',
    'Insomnia' => 'Insomnia',
    'Nyeri kepala' => 'Nyeri kepala',
    'Pusing' => 'Pusing',
    'Depresi' => 'Depresi',
    'Penurunan Kesadaran' => 'Penurunan Kesadaran',
    'Lainnya' => 'Lainnya'
];
$radio_template = "<div class='col-sm-5'>{label}</div><div class='col-sm-7'>{input}</div>";
$item_radio_template = "display:grid; grid-template-columns: repeat(2, 0.4fr); gap: 5px;";
?>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-top:15px;">
<h3 style="margin-left:10px;">Asesmen Keperawatan</h3>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <br>
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-9" style="margin-left:15px;">
                        <?= $form->field($model, 'keluhan_utama', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                            ]); ?>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-top:15px;margin-bottom:15px;">
                    <div class="col-sm-12" style="margin-bottom:10px;">
                        <span style="margin-left:15px;">Riwayat Sakit Lama</span>
                    </div>
                    <div class="col-sm-9">
                        <?= $form->field($model, 'riwayat_sakit_lama', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                $items['riwayat_sakit_lama'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'riwayat_sakit_lama_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-riwayat_sakit_lama',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:0px 40px;'
                        ])->label(false); ?>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-top:15px;margin-bottom:15px;">
                    <div class="col-sm-12" style="margin-bottom:10px;">
                        <span style="margin-left:15px;">Pengkajian Psikologis</span>
                    </div>
                    <div class="col-sm-9">
                        <?= $form->field($model, 'pengkajian_psikologis', ['template' => "<div class='col-sm-12'>{input}</div>", 'labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['pengkajian_psikologis'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 40px; margin-bottom: 10px;',
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'pengkajian_psikologis_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-pengkajian_psikologis',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:-120px 35px;'
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Respon Penerimaan</h5>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top:10px;margin-bottom:10px;">
                    <div class="col-sm-2" style="margin-top:10px;">
                        <span style="margin-left:15px;">1. Pasien</span>
                    </div>
                    <div class="col-sm-7">
                        <?= $form->field($model, 'respon_penerimaan_pasien', ['template' => "<div class='col-sm-12'>{input}</div>", 'labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['respon_penerimaan'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 40px; margin-bottom: 10px;',
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'respon_penerimaan_pasien_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-respon_penerimaan_pasien',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:-100px 0px;'
                        ])->label(false); ?>
                    </div>
                </div>
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-2" style="margin-top:10px;">
                        <span style="margin-left:15px;">2. Keluarga</span>
                    </div>
                    <div class="col-sm-7">
                        <?= $form->field($model, 'respon_penerimaan_keluarga', ['template' => "<div class='col-sm-12'>{input}</div>", 'labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['respon_penerimaan'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 40px; margin-bottom: 10px;',
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'respon_penerimaan_keluarga_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-respon_penerimaan_keluarga',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:-100px 0px;'
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-right:5px;">
    <div class="col-md-6 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Keadaan Umum</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field(
                            $model,
                            'askep_kesadaran',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <?= $form->field(
                            $model,
                            'askep_gcs',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field(
                            $model,
                            'askep_gcs_e',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field(
                            $model,
                            'askep_gcs_v',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field(
                            $model,
                            'askep_gcs_m',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field(
                            $model,
                            'berat_badan',
                            ['addon' => ['append' => ['content' => 'kg']]]
                        )->textInput(['class' => 'form-control input-sm doco-decimal-wcomma']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field(
                            $model,
                            'tinggi_badan',
                            ['addon' => ['append' => ['content' => 'cm']]]
                        )->textInput(['class' => 'form-control input-sm doco-decimal-wcomma']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Tanda-tanda Vital</h5>
            </div>
            <div class="panel-body">
                <?= $form->field(
                    $model,
                    'tekanan_darah',
                    ['addon' => ['append' => ['content' => 'mmHg']]]
                )->textInput(['class' => 'form-control input-sm']) ?>
                <?= $form->field(
                    $model,
                    'frekuensi_nadi',
                    ['addon' => ['append' => ['content' => 'x/Menit']]]
                )->textInput(['class' => 'form-control input-sm doco-number']) ?>
                <?= $form->field(
                    $model,
                    'frekuensi_nafas',
                    ['addon' => ['append' => ['content' => 'x/Menit']]]
                )->textInput(['class' => 'form-control input-sm doco-number']) ?>
                <?= $form->field(
                    $model,
                    'suhu_tubuh',
                    ['addon' => ['append' => ['content' => '°C']]]
                )->textInput(['class' => 'form-control input-sm doco-decimal-wcomma']) ?>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemeriksaan Fisik</h5>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top:10px;margin-bottom:15px;">
                    <div class="col-sm-9">
                        <?= $form->field($model, 'pemeriksaan_fisik', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                $items['pemeriksaan_fisik'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'pemeriksaan_fisik_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-pemeriksaan_fisik',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:-385px 80px;'
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemenuhan Kebutuhan Aktifitas Hidup Sehari-hari (ADL)</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-9" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_makan', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                    <div class="col-sm-9" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_mandi', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                    <div class="col-sm-9" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_buangair', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                    <div class="col-sm-9" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_berpakaian', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                    <div class="col-sm-9" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_istirahat', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                    <div class="col-sm-9" style="margin-top:15px;margin-bottom:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_obat', ['template' => $radio_template, 'labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true', 'style' => $item_radio_template]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-sm-9" style="margin-top:10px;">
                    <?= $form->field($model, 'diagnosa_keperawatan', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
                </div>
                <div class="col-sm-9" style="margin-top:10px;">
                    <?= $form->field($model, 'rencana_keperawatan', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<hr>
<h2 style="margin-left:10px;">Asesmen Medis</h2>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body" style="margin-left:10px;">
                <div class="row">
                    <div class="col-sm-9" style="margin-top:10px;">
                        <?= $form->field($model, 'pengkajian_sistem', [
                            'labelOptions' => ['class' => '']
                        ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9" style="margin-top:10px;">
                        <?= $form->field($model, 'diagnosa_medis', [
                            'labelOptions' => ['class' => '']
                        ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9" style="margin-top:10px;">
                        <?= $form->field($model, 'rencana_terapi', [
                            'labelOptions' => ['class' => '']
                        ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9" style="margin-top:10px;">
                        <?= $form->field($model, 'saran_dokter', [
                            'labelOptions' => ['class' => '']
                        ])->textarea([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Data Penunjang</h5>
            </div>
            <div class="panel-body">
                <div class="col-sm-9" style="margin-top:10px;">
                    <?= $form->field(
                        $model,
                        'data_lab',
                        ['labelOptions' => ['class' => '']]
                    )->textInput([
                        'class' => 'form-control input-sm',
                    ]); ?>
                    <?= $form->field(
                        $model,
                        'data_radiologi',
                        ['labelOptions' => ['class' => '']]
                    )->textInput([
                        'class' => 'form-control input-sm',
                    ]); ?>
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
    $(document).ready(function () {
        const checkboxRiwayatSakitLama = $("input[name=\'TerminalForm[riwayat_sakit_lama][]\'][value=\'Lainnya\']");
        const inputRiwayatSakitLama = $("#other-riwayat_sakit_lama");

        const checkboxPengkajianPsikologis = $("input[name=\'TerminalForm[pengkajian_psikologis][]\'][value=\'Lainnya\']");
        const inputPengkajianPsikologis = $("#other-pengkajian_psikologis");

        const checkboxResponPenerimaanPasien = $("input[name=\'TerminalForm[respon_penerimaan_pasien][]\'][value=\'Lainnya\']");
        const inputResponPenerimaanPasien = $("#other-respon_penerimaan_pasien");

        const checkboxResponPenerimaanKeluarga = $("input[name=\'TerminalForm[respon_penerimaan_keluarga][]\'][value=\'Lainnya\']");
        const inputResponPenerimaanKeluarga = $("#other-respon_penerimaan_keluarga");

        const checkboxPemeriksaanFisik = $("input[name=\'TerminalForm[pemeriksaan_fisik][]\'][value=\'Lainnya\']");
        const inputPemeriksaanFisik = $("#other-pemeriksaan_fisik");

        function toggleInput(checkbox, input) {
            if (checkbox.is(":checked")) {
                input.prop("disabled", false);
            } else {
                input.val("").prop("disabled", true);
            }
        }
        
        toggleInput(checkboxRiwayatSakitLama, inputRiwayatSakitLama);
        toggleInput(checkboxPengkajianPsikologis, inputPengkajianPsikologis);
        toggleInput(checkboxResponPenerimaanPasien, inputResponPenerimaanPasien);
        toggleInput(checkboxResponPenerimaanKeluarga, inputResponPenerimaanKeluarga);
        toggleInput(checkboxPemeriksaanFisik, inputPemeriksaanFisik);

        checkboxRiwayatSakitLama.on("change", function () {
            toggleInput($(this), inputRiwayatSakitLama);
        });

        checkboxPengkajianPsikologis.on("change", function () {
            toggleInput($(this), inputPengkajianPsikologis);
        });

        checkboxResponPenerimaanPasien.on("change", function () {
            toggleInput($(this), inputResponPenerimaanPasien);
        });

        checkboxResponPenerimaanKeluarga.on("change", function () {
            toggleInput($(this), inputResponPenerimaanKeluarga);
        });

        checkboxPemeriksaanFisik.on("change", function () {
            toggleInput($(this), inputPemeriksaanFisik);
        });
    });'
);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>