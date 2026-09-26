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
$items["riwayat_penyakit_sekarang"] = [
    'Hipersensitivitas (Alergi)' => 'Hipersensitivitas (Alergi)',
    'Imunodefisiensi' => 'Imunodefisiensi',
    'Autoimun' => 'Autoimun',
    'Isoimunitas (Alloimunitas)' => 'Isoimunitas (Alloimunitas)',
    'Lainnya' => 'Lainnya'
];
$items["riwayat_penyakit_dahulu"] = [
    'Alergi' => 'Alergi',
    'Kebiasaan merokok' => 'Kebiasaan merokok',
    'Peningkatan stres' => 'Peningkatan stres',
    'Penyakit Auto imun' => 'Penyakit Auto imun',
    'Ketergantungan alkohol' => 'Ketergantungan alkohol',
    'Penggunaan obat-obatan' => 'Penggunaan obat-obatan'
];
$items["riwayat_keturunan"] = [
    'Tidak ada' => 'Tidak ada',
    'Ada' => 'Ada'
];
$items["pengkajian_psikologis"] = [
    'Cemas' => 'Cemas',
    'Sedih' => 'Sedih',
    'Takut' => 'Takut',
    'Marah' => 'Marah',
    'Gelisah' => 'Gelisah',
    'Tenang' => 'Tenang'
];
$items["respon_penerimaan"] = [
    'Pasrah' => 'Pasrah',
    'Tawakkal' => 'Tawakkal',
    'Menolak' => 'Menolak',
    'Marah' => 'Marah'
];
$items["pemeriksaan_fisik"] = [
    'Kelelahan' => 'Kelelahan',
    'Diuresis' => 'Diuresis',
    'Limfadenopati' => 'Limfadenopati',
    'Kelemahan muskuler' => 'Kelemahan muskuler',
    'Demam' => 'Demam',
    'Kemerahan' => 'Kemerahan',
    'Hepatomegali' => 'Hepatomegali',
    'Nyeri/pembengkakan sendi' => 'Nyeri/pembengkakan sendi',
    'Penurunan berat badan' => 'Penurunan berat badan',
    'Proses pemulihan buruk' => 'Proses pemulihan buruk'
];
$items["pemenuhan_adl"] = [
    'Bantuan minimal' => 'Bantuan minimal',
    'Bantuan total' => 'Bantuan total'
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
                <div class="row">
                    <div class="col-md-9" style="margin-left:15px;">
                        <?= $form->field($model, 'keluhan_utama', ['labelOptions' => ['class' => '']])
                            ->textInput([
                                'class' => 'form-control input-sm',
                            ]); ?>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <div class="col-md-10 form-group">
                        <div class="col-md-12">
                            <span style="margin-left:15px;">Riwayat Penyakit Sekarang :</span>
                        </div>
                        <div class="col-sm-9">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                                ->label(false)
                                ->checkboxList(
                                    $items['riwayat_penyakit_sekarang'],
                                    [
                                        'itemOptions' => [],
                                        'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                    ]
                                );
                            ?>
                        </div>
                        <div id="riwayat-sekarang-textinput" class="col-sm-3">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang_input', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'other-riwayat_penyakit_sekarang',
                                'class' => 'form-control',
                                'placeholder' => 'Lainnya',
                                'disabled' => true,
                                'style' => 'translate:-40px 0px;'
                            ])->label(false); ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;margin-bottom:15px;">
                    <div class="col-md-10 form-group" style="margin-left:15px;">
                        <span style="margin-bottom:10px;">Riwayat Penyakit Dahulu :</span>
                        <?= $form->field($model, 'riwayat_penyakit_dahulu', ['labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['riwayat_penyakit_dahulu'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
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
                <h5 class="panel-title">Riwayat Keturunan/Keluarga</h5>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top:15px;">
                    <label class="control-label col-md-3">a. Riwayat keluarga dengan penyakit kanker: </label>
                    <div class="col-md-9 form-group">
                        <div class="col-sm-5">
                            <?= $form->field($model, 'riwayat_keturunan_kanker', ['labelOptions' => ['class' => '']])
                                ->label(false)
                                ->radioList(
                                    $items['riwayat_keturunan'],
                                    [
                                        'itemOptions' => [],
                                        'inline' => 'true',
                                    ]
                                ); ?>
                        </div>
                        <div class="col-sm-7">
                            <?= $form->field($model, 'riwayat_keturunan_kanker_input', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'other-riwayat_keturunan_kanker',
                                'class' => 'form-control',
                                'placeholder' => 'Ada',
                                'disabled' => true,
                            ])->label(false); ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;">
                    <label class="control-label col-md-3">b. Riwayat keluarga dengan gangguan imun: </label>
                    <div class="col-md-9 form-group">
                        <div class="col-sm-5">
                            <?= $form->field($model, 'riwayat_keturunan_imun', ['labelOptions' => ['class' => '']])
                                ->label(false)
                                ->radioList(
                                    $items['riwayat_keturunan'],
                                    [
                                        'itemOptions' => [],
                                        'inline' => 'true',
                                    ]
                                ); ?>
                        </div>
                        <div class="col-sm-7">
                            <?= $form->field($model, 'riwayat_keturunan_imun_input', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'other-riwayat_keturunan_imun',
                                'class' => 'form-control',
                                'placeholder' => 'Ada',
                                'disabled' => true,
                            ])->label(false); ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15px;margin-bottom:15px;">
                    <label class="control-label col-md-3">c. Riwayat keluarga menderita alergi: </label>
                    <div class="col-md-9 form-group">
                        <div class="col-sm-5">
                            <?= $form->field($model, 'riwayat_keturunan_alergi', ['labelOptions' => ['class' => '']])
                                ->label(false)
                                ->radioList(
                                    $items['riwayat_keturunan'],
                                    [
                                        'itemOptions' => [],
                                        'inline' => 'true',
                                    ]
                                ); ?>
                        </div>
                        <div class="col-sm-7">
                            <?= $form->field($model, 'riwayat_keturunan_alergi_input', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'id' => 'other-riwayat_keturunan_alergi',
                                'class' => 'form-control',
                                'placeholder' => 'Ada',
                                'disabled' => true,
                            ])->label(false); ?>
                        </div>
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
    <div class="col-md-6 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Respon Penerimaan Pasien terhadap Penyakit</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($model, 'respon_penerimaan', ['labelOptions' => ['class' => '']])->label(false)
                        ->checkboxList(
                            $items['respon_penerimaan'],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                            ]
                        ); ?>
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
                            'kesadaran',
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
                            'gcs',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'gcs_e',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'gcs_v',
                            ['labelOptions' => ['class' => '']]
                        )->textInput([
                            'class' => 'form-control input-sm doco-number',
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field(
                            $model,
                            'gcs_m',
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
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pemeriksaan Fisik</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'pemeriksaan_fisik', ['labelOptions' => ['class' => '']])->label(false)
                            ->checkboxList(
                                $items['pemeriksaan_fisik'],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
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
                <h5 class="panel-title">Pemenuhan Kebutuhan Aktifitas Hidup Sehari-hari (ADL)</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_makan', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
                    </div>
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_mandi', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
                    </div>
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_buangair', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
                    </div>
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_berpakaian', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
                    </div>
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_istirahat', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
                    </div>
                    <div class="col-md-12" style="margin-top:15px;">
                        <?= $form->field($model, 'pemenuhan_adl_obat', ['labelOptions' => ['class' => '']])
                            ->radioList($items["pemenuhan_adl"], ['inline' => 'true']); ?>
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
                <div class="col-md-9" style="margin-top:10px;">
                    <?= $form->field($model, 'diagnosa_keperawatan', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
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
                <div class="col-md-9" style="margin-top:10px;">
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
<hr style="margin-top:15px;">
<h2 style="margin-left:10px;">Asesmen Medis</h2>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Pengkajian Sistem</h5>
            </div>
            <div class="panel-body">
                <div class="col-md-9" style="margin-top:10px;">
                    <?= $form->field($model, 'pengkajian_sistem', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Diagnosa Medis</h5>
            </div>
            <div class="panel-body">
                <div class="col-md-9" style="margin-top:10px;">
                    <?= $form->field($model, 'diagnosa_medis', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Rencana Terapi dan Tindakan</h5>
            </div>
            <div class="panel-body">
                <div class="col-md-9" style="margin-top:10px;">
                    <?= $form->field($model, 'rencana_terapi', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Saran/Nasihat Dokter</h5>
            </div>
            <div class="panel-body">
                <div class="col-md-9" style="margin-top:10px;">
                    <?= $form->field($model, 'saran_dokter', [
                        'labelOptions' => ['class' => '']
                    ])->textarea([
                        'class' => 'form-control input-sm',
                    ]); ?>
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
                <div class="col-md-9" style="margin-top:10px;">
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
    var _model = "' . $modelName . '";
    var _modelName = _model.toLowerCase();

    $(document).ready(function () {
        const checkboxSekarangLainnya = $("input[name=\'ImunologiForm[riwayat_penyakit_sekarang][]\'][value=\'Lainnya\']");
        const inputSekarangLainnya = $("#other-riwayat_penyakit_sekarang");

        const radioKeturunanKanker = $("input[name=\'ImunologiForm[riwayat_keturunan_kanker]\']");
        const inputKeturunanKanker = $("#other-riwayat_keturunan_kanker");
        
        const radioKeturunanImun = $("input[name=\'ImunologiForm[riwayat_keturunan_imun]\']");
        const inputKeturunanImun = $("#other-riwayat_keturunan_imun");
        
        const radioKeturunanAlergi = $("input[name=\'ImunologiForm[riwayat_keturunan_alergi]\']");
        const inputKeturunanAlergi = $("#other-riwayat_keturunan_alergi");
        
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

        toggleInput(checkboxSekarangLainnya, inputSekarangLainnya);
        toggleRadio(radioKeturunanKanker, inputKeturunanKanker);
        toggleRadio(radioKeturunanImun, inputKeturunanImun);
        toggleRadio(radioKeturunanAlergi, inputKeturunanAlergi);

        checkboxSekarangLainnya.change(function () {
            toggleInput($(this), inputSekarangLainnya);
        });

        radioKeturunanKanker.on(\'click change\', function(e) {
            toggleRadio($(this), inputKeturunanKanker);
        });

        radioKeturunanImun.on(\'click change\', function(e) {
            toggleRadio($(this), inputKeturunanImun);
        });

        radioKeturunanAlergi.on(\'click change\', function(e) {
            toggleRadio($(this), inputKeturunanAlergi);
        });
    });'
);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>