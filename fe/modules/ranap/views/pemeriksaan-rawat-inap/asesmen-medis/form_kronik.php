<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;

?>

<style>
    .datepicker>div {
        display: block;
    }

    .form-row {
        margin-bottom: 4px;
    }

    .panel-heading {
        padding: 3px 20px !important;
    }

    hr {
        margin-top: 3px;
        margin-bottom: 3px;
    }

    .panel {
        margin-bottom: 10px;
    }

    .box-scale {
        margin-top: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    p {
        margin-bottom: 3px;
    }

    td {
        padding-top: 0px !important;
    }

    .form-horizontal .control-label {
        padding-bottom: 3px;
        padding-top: 3px !important;
    }

    .box-scale-header {
        margin-bottom: 3px !important;
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <br>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'keluhan_utama')->label()->textInput(['class' => 'form-control input-sm']); ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'riwayat_keluhan')->label()->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
            <hr>
            <h3>Pengkajian Nyeri</h3>
            <p style="font-weight:bold;">1. Provokatif/Paliatif</p>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'pengkajian_nyeri_provokatif')->label(Yii::t('fe', 'Penyebab Timbulnya Nyeri :'))->checkboxList([
                        'Ruda Paksa' => 'Ruda Paksa',
                        'Benturan' => 'Benturan',
                        'Penyayatan' => 'Penyayatan',
                    ], 
                    [
                        'itemOptions' => [],
                        'inline' => true,
                    ]); ?>
                </div>
            </div>
            <br>
            <p style="font-weight:bold;">2. Kuantitas</p>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'pengkajian_nyeri_kuantitas')->label(Yii::t('fe', 'Bagaimana Rasanya :'))->checkboxList([
                        'Tertusuk' => 'Tertusuk',
                        'Tertekan' => 'Tertekan',
                        'Diiris-iris' => 'Diiris-iris',
                        'Tertimpa Benda Berat' => 'Tertimpa Benda Berat',
                        'Lainnya' => 'Lainnya',
                    ], 
                    [
                        'itemOptions' => [],
                        'inline' => true,
                    ]); ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'pengkajian_nyeri_kuantitas_lainnya')->label(false)->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
            <br>
            <p style="font-weight:bold;">3. Region/Area</p>
            <div class="row">
                <div class="col-md-8">
                <?= $form->field($model, 'pengkajian_nyeri_region')->label(Yii::t('fe', 'Lokasi Dimana Keluhan Nyeri Dirasakan :'))
                    ->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
            <br>
            <p style="font-weight:bold;">4. Skala</p>
            <p>a. Orang Dewasa</p>
            <div class="row">
                <div class="col-md-8">
                <?= $form->field($model, 'skala_dewasa_intensitas_nyeri')->label(Yii::t('fe', 'Berdasarkan Skala Intensitas Nyeri :'))->radioList([
                        '0' => 'Nilai 0 : Tidak Ada Nyeri',
                        '1' => 'Nilai 1 : Nyeri Seperti Gatal, Tersetrum dan Nyut-nyutan',
                        '2' => 'Nilai 2 : Nyeri Seperti Melilit atau Terpukul',
                        '3' => 'Nilai 3 : Nyeri Seperti Perih atau Mules',
                        '4' => 'Nilai 4 : Nyeri Seperti Kram atau Kaku',
                        '5' => 'Nilai 5 : Nyeri Seperti Tertekan/Bergerak',
                        '6' => 'Nilai 6 : Nyeri Seperti Terbakar, Tertusuk-tusuk',
                        '789' => 'Nilai 7 8 9 : Sangat Nyeri Tetapi Masih Dapat Dikontrol oleh Pasien dan Aktifitas Yang Bisa Dilakukan',
                        '10' => 'Nilai 10 : Sangat Nyeri dan Tidak Dapat Dikontrol oleh Pasien',
                    ], 
                    [
                        'itemOptions' => [],
                    ]); ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8">
                <?= $form->field($model, 'skala_dewasa_tipe_nyeri')->label(Yii::t('fe', 'Berdasarkan Tipe Nyeri :'))->radioList([
                        '1_3' => 'Nilai 1 - 3 : Tipe Nyeri Ringan',
                        '4_6' => 'Nilai 4 - 6 : Tipe Nyeri Sedang',
                        '7_9' => 'Nilai 7 - 9 : Tipe Nyeri Berat',
                        '10' => 'Nilai 10 : Tipe Nyeri Sangat Berat',
                    ], 
                    [
                        'itemOptions' => [],
                    ]); ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8">
                <?= $form->field($model, 'skala_anak')->label(Yii::t('fe', 'b. Klasifikasi Tingkat Nyeri Untuk Usia 3 - 4 Tahun/Lebih :'))->radioList([
                        '1' => 'Nilai 1 : Tidak ada nyeri',
                        '2_4' => 'Nilai 2 - 4 : Nyeri ringan, dimana anak belum mengeluh nyeri atau masih dapat ditolelir karena masih dibawah ambang rangsang',
                        '5_6' => 'Nilai 5 - 6 : Nyeri sedang, dimana anak mulai merintih dan mengeluh, ada yang sambil menekan bagian yang nyeri',
                        '7_9' => 'Nilai 7 - 9 : Termasuk nyeri berat, anak mungkin mengeluh sakit sekali dan pasien tidak mampu melakukan kegiatan biasa',
                        '10' => 'Nilai 10 : Termasuk nyeri yang sangat berat, pada tingkat ini anak tidak dapat mengenal dirinya',
                    ], 
                    [
                        'itemOptions' => [],
                    ]); ?>
                </div>
            </div>
            <br>
            <p style="font-weight:bold;">4. Skala</p>
            <div class="row">
                <div class="col-md-12">
                    <p style="font-weight:bold;">c. Faces Rating Scale dan Wong Baker untuk anak pra sekolah</p>
                    <div class="box-scale">
                        <div class="box-scale-header">
                            <img src="/media/img/all-emote.svg" alt="">
                        </div>
                        <div class="box-scale-line box-scale-line__separator">
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div class="box-scale-line__hidePercentage"></div>
                        </div>
                        <div class="box-scale-line">
                            <div data-percentage="0" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__0">0</span>
                            </div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div data-percentage="10" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__1">1</span>
                            </div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div data-percentage="20" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__2">2</span>
                            </div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div data-percentage="30" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__3">3</span>
                            </div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div data-percentage="40" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__4">4</span>
                            </div>
                            <div class="box-scale-line__point">&nbsp;</div>
                            <div data-percentage="50" class="box-scale-line__point">
                                <span class="box-scale-line__btn box-scale-line__5">5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'skala_faces')->label(false)->radioList([
                        '0' => 'Nilai 0 : Nyeri tidak dirasakan oleh anak',
                        '1' => 'Nilai 1 : Nyeri dirasakan sedikit saja',
                        '2' => 'Nilai 2 : Nyeri dirasakan hilang timbul',
                        '3' => 'Nilai 3 : Nyeri yang dirasakan anak lebih banyak',
                        '4' => 'Nilai 4 : Nyeri yang dirasakan anak secara keseluruhan',
                        '5' => 'Nilai 5 : Nyeri sekali dan anak menjadi menangis',
                    ], 
                    [
                        'itemOptions' => [],
                    ]); ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'pengkajian_sistem')->label()->textArea(['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'diagnosa_medis')->label()->textArea(['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'rencana_terapi_dan_tindakan')->label()->textArea(['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'saran_nasehat_dokter')->label()->textArea(['class' => 'form-control']); ?>
                </div>
            </div>
            <p class="header-form">Data Penunjang</p>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'laboratorium')->label()->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'radiologi')->label()->textInput(['class' => 'form-control input-sm']); ?>
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
var pengkajian_nyeri_kuantitas_lainnya = "'.$model->pengkajian_nyeri_kuantitas_lainnya.'"

$(document).ready(function(){
    const checkboxKuantitasLainnya = $("input[name=\'NyeriKronikForm[pengkajian_nyeri_kuantitas][]\'][value=\'Lainnya\']");
    const inputKuantitasLainnya = $("#nyerikronikform-pengkajian_nyeri_kuantitas_lainnya");
    inputKuantitasLainnya.prop("readonly", true);
    if(asesmenMedisId && pengkajian_nyeri_kuantitas_lainnya) {
        inputKuantitasLainnya.prop("readonly", false);
    }

    checkboxKuantitasLainnya.change(function () {
        if(checkboxKuantitasLainnya.is(":checked")) {
            inputKuantitasLainnya.prop("readonly", false);
        }
        else {
            inputKuantitasLainnya.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);

$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>