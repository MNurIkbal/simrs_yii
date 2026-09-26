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
$items["alasan_masuk_rs"] = [
    'KDRT' => 'KDRT',
    'Percobaan Bunuh Diri' => 'Percobaan Bunuh Diri',
    'Penganiayaan dari orang lain' => 'Penganiayaan dari orang lain',
    'Lainnya' => 'Lainnya'
];
$items["jalan_nafas"] = [
    'Bebas' => 'Bebas',
    'Obstruksi Parsial' => 'Obstruksi Parsial',
    'Obstruksi Total' => 'Obstruksi Total',
    'Lainnya' => 'Lainnya'
];
$items["pernafasan"] = [
    'Normal' => 'Normal',
    'Henti Nafas' => 'Henti Nafas',
    'Wheezing' => 'Wheezing',
    'Lainnya' => 'Lainnya'
];
$items["sirkulasi"] = [
    'Normal' => 'Normal',
    'Henti Nafas' => 'Henti Nafas',
    'Nadi tidak teraba' => 'Nadi tidak teraba',
    'Pucat' => 'Pucat',
    'Akral dingin' => 'Akral dingin',
    'Nadi lemah' => 'Nadi lemah',
    'Lainnya' => 'Lainnya'
];
$items["jalan_nafas"] = [
    'Bebas' => 'Bebas',
    'Obstruksi Parsial' => 'Obstruksi Parsial',
    'Obstruksi Total' => 'Obstruksi Total',
    'Lainnya' => 'Lainnya'
];
$items["pengkajian_psikologis"] = [
    'Pasrah' => 'Pasrah',
    'Tawakkal' => 'Tawakkal',
    'Menolak' => 'Menolak',
    'Marah' => 'Marah',
    'Stres' => 'Stres'
];
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
                    <div class="col-md-9" style="margin-left:15px;">
                        <?= $form->field($model, 'tempat_tanggal_kejadian', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                            ]); ?>
                    </div>
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-md-12">
                            <span>Alasan Masuk RS</span>
                        </div>
                        <div class="col-sm-9">
                            <?= $form->field($model, 'alasan_masuk_rs', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                                ->label(false)
                                ->checkboxList(
                                    $items['alasan_masuk_rs'],
                                    [
                                        'itemOptions' => [],
                                        'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                                    ]
                                );
                            ?>
                        </div>
                        <div id="riwayat-sekarang-textinput" class="col-sm-3">
                            <?= $form->field($model, 'alasan_masuk_rs_input', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'other-alasan_masuk_rs',
                                'class' => 'form-control',
                                'placeholder' => 'Lainnya',
                                'disabled' => true,
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="col-md-9" style="margin-left:15px;">
                        <?= $form->field($model, 'keluhan_utama', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                            ]); ?>
                    </div>
                    <div class="col-md-9" style="margin-left:15px;">
                        <?= $form->field($model, 'riwayat_keluhan_utama', ['labelOptions' => ['class' => '']])
                            ->textArea([
                                'class' => 'form-control input-sm',
                            ]); ?>
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
                <h5 class="panel-title">Pengkajian Psikologis</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'pengkajian_psikologis', ['labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['pengkajian_psikologis'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Diagnosa Keperawatan</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                        <?= $form->field($model, 'diagnosa_keperawatan', [
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
                <h5 class="panel-title">Rencana Keperawatan dan Tindakan</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
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
<div class="row" style="margin-right:5px;">
    <div class="col-md-6 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Keadaan Umum</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
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
                    <div class="col-md-5">
                        <?= $form->field(
                            $model,
                            'askep_gcs',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'askep_gcs_e',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'askep_gcs_v',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-12">
                        <?= $form->field(
                            $model,
                            'berat_badan',
                            ['addon' => ['append' => ['content' => 'kg']]]
                        )->textInput(['class' => 'form-control input-sm doco-decimal-wcomma']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
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
</div>
<hr style="margin-top:15px;">
<h2 style="margin-left:10px;">Asesmen Medis</h2>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemeriksaan Fisik</h5>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <span style="margin-left:15px;">a. Jalan Nafas (<i>Airway</i>)</span>
                    </div>
                    <div class="col-sm-9">
                        <?= $form->field($model, 'jalan_nafas', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                $items['jalan_nafas'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="riwayat-sekarang-textinput" class="col-sm-3">
                        <?= $form->field($model, 'jalan_nafas_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-jalan_nafas',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                        ])->label(false); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <span style="margin-left:15px;">b. Pernafasan (<i>Breathing</i>)</span>
                    </div>
                    <div class="col-sm-9">
                        <?= $form->field($model, 'pernafasan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                $items['pernafasan'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="riwayat-sekarang-textinput" class="col-sm-3">
                        <?= $form->field($model, 'pernafasan_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-pernafasan',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                        ])->label(false); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <span style="margin-left:15px;">c. Sirkulasi (<i>Circulation</i>)</span>
                    </div>
                    <div class="col-sm-9">
                        <?= $form->field($model, 'sirkulasi', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                $items['sirkulasi'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="sirkulasi-textinput" class="col-sm-3">
                        <?= $form->field($model, 'sirkulasi_input', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                            'id' => 'other-sirkulasi',
                            'class' => 'form-control',
                            'placeholder' => 'Lainnya',
                            'disabled' => true,
                            'style' => 'translate:-180px 40px;'
                        ])->label(false); ?>
                    </div>
                    <div class="col-sm-5    " style="margin-left:12px;margin-top:15px;">
                        <?= $form->field(
                            $model,
                            'sirkulasi_crt',
                            ['addon' => ['append' => ['content' => 'detik']]]
                        )->textInput(['class' => 'form-control input-sm doco-number']) ?>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;margin-left:5px;margin-bottom:10px;">
                    <div class="col-md-3">
                        <?= $form->field(
                            $model,
                            'asmed_kesadaran',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'asmed_gcs',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'asmed_gcs_e',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'asmed_gcs_v',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'asmed_gcs_m',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Tindakan/Pengobatan</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                        <?= $form->field($model, 'tindakan_pengobatan', [
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
                <h5 class="panel-title">Saran/Nasehat Dokter</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
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
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(
    '$(document).ready(function () {
        const checkboxAlasanMasukLainnya = $("input[name=\'KekerasanForm[alasan_masuk_rs][]\'][value=\'Lainnya\']");
        const inputAlasanMasukLainnya = $("#other-alasan_masuk_rs");

        const checkboxJalanNafasLainnya = $("input[name=\'KekerasanForm[jalan_nafas][]\'][value=\'Lainnya\']");
        const inputJalanNafasLainnya = $("#other-jalan_nafas");

        const checkboxPernafasanLainnya = $("input[name=\'KekerasanForm[pernafasan][]\'][value=\'Lainnya\']");
        const inputPernafasanLainnya = $("#other-pernafasan");

        const checkboxSirkulasiLainnya = $("input[name=\'KekerasanForm[sirkulasi][]\'][value=\'Lainnya\']");
        const inputSirkulasiLainnya = $("#other-sirkulasi");

        function toggleInput(checkbox, input) {
            if (checkbox.is(":checked")) {
                // container.show();
                input.prop("disabled", false);
            } else {
                // container.hide();
                input.val("").prop("disabled", true);
            }
        }

        function toggleRadio(radio, input) {
            if (radio.prop("value") == "Ada") {
                // container.show();
                input.prop("disabled", false);
            } else {
                // container.hide();
                input.val("").prop("disabled", true);
            }
        }
        
        toggleInput(checkboxAlasanMasukLainnya, inputAlasanMasukLainnya);
        toggleInput(checkboxJalanNafasLainnya, inputJalanNafasLainnya);
        toggleInput(checkboxPernafasanLainnya, inputPernafasanLainnya);
        toggleInput(checkboxSirkulasiLainnya, inputSirkulasiLainnya);

        checkboxAlasanMasukLainnya.on("change", function () {
            toggleInput($(this), inputAlasanMasukLainnya);
        });

        checkboxJalanNafasLainnya.change(function () {
            toggleInput($(this), inputJalanNafasLainnya);
        });

        checkboxPernafasanLainnya.change(function () {
            toggleInput($(this), inputPernafasanLainnya);
        });
        checkboxSirkulasiLainnya.change(function () {
            toggleInput($(this), inputSirkulasiLainnya);
        });
    });'
);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>