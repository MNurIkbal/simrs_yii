<?php

/**
 * 
 * @author : Maulana Muhammad Rizky
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cetak = 'hidden';
$offset = 'col-md-offset-4';
$final = '';
if ($state) {
    $cetak = '';
    $final = 'hidden';
}
$sep_valid = '';
$sep_valid_btn = '';
if (!$info['nosep']) {
    $sep_valid = 'hidden';
} else {
    $sep_valid_btn = '';
}
?>
<script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
<link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">
<style type="text/css">
    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    .marginbottom {
        margin-bottom: 5px;
    }

    .heightSelect {
        height: 33px !important;
        width: 100px !important;
    }

    .dropzone .dz-default.dz-message::before {
        padding: 30px !important;
    }

    .dz-default .dz-message {
        padding: 30px;
    }

    .button-select {
        cursor: pointer;
    }

    .dz-button {
        margin-top: 30px;
    }

    .margin-10 {
        margin-top: 10px !important;
    }

    .custom-image {
        height: 120px !important;
        width: 120px !important;
        padding: 10px;
    }

    .disabled-keluar {
        width: 150px;
        pointer-events: none;
    }

    .dz-image {
        margin-top: 75px !important;
    }

    .naik-turun-kelas-hide {
        display: none;
    }

    .fa-trash {
        font-size: 18px
    }

    .set-error-diagnosa-inacbgs {
        font-weight: bold;
    }

    .set-error-procedure-inacbgs {
        font-weight: bold;
    }

    .row-error {
        background-color: #f4cccc !important; /* merah muda lembut */
    }

    .btn-custom-delete {
        width: 60px !important;
        height: 25px !important;
        font-size: 11px !important;
        padding: 1px !important;
    }

    .btn-set-primary {
        width: 80px !important;
        height: 25px !important;
        font-size: 11px !important;
        padding: 1px !important;
    }

    .btn-custom-red {
        background-color: #e44f4f !important;
        border-color: #e44f4f !important;
    }

    .custom-size {
        font-size: 12px !important;
    }
</style>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'backBtn' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'id'    => 'btn-back',
                            'data-options' => 'click',
                        ]
                    ],
                    'log-activity' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Log Acivity'),
                        'icon'  => 'fa fa-list',
                        'attributes' => [
                            'id' => 'log-activity',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/log-activity?id=' . $id_dec,
                        ]
                    ],
                    'hapus-klaim' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Hapus Klaim'),
                        'icon'  => 'fa fa-trash custom-size',
                        'attributes' => [
                            'class' => 'btn-custom-red',
                            'id' => 'btn-hapus-klaim',
                            'data-options' => 'click',
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <center>
                    <h3><?= Yii::t('fe', 'E-Klaim INACBGS') ?></h3>
                </center>
                <hr>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-proses-klaim',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    // 'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
                ]);
                ?>
                <?= Html::activeHiddenInput($model, 'kunjungan_id', ['class' => 'pendaftaran-id-txt']) ?>
                <!-- <?= Html::activeHiddenInput($model, 'nama_pasien') ?> -->
                <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
                <?= Html::activeHiddenInput($model, 'no_rekam_medik') ?>
                <?= Html::activeHiddenInput($model, 'jeniskelamin') ?>
                <?= Html::activeHiddenInput($model, 'instalasi_id') ?>
                <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                <?= Html::activeHiddenInput($model, 'dokterdpjp_id') ?>
                <!-- <?= Html::activeHiddenInput($model, 'tgl_masuk') ?> -->
                <!-- <?= Html::activeHiddenInput($model, 'tgl_keluar') ?> -->
                <!-- <?= Html::activeHiddenInput($model, 'total_tarifrs') ?> -->
                <?= Html::activeHiddenInput($model, 'no_sep', ['class' => 'no-sep']) ?>
                <?= Html::activeHiddenInput($model, 'umur') ?>
                <!-- <?= Html::activeHiddenInput($model, 'berat_lahir') ?> -->
                <?= Html::activeHiddenInput($model, 'los') ?>
                <?= Html::activeHiddenInput($model, 'adl_subacute') ?>
                <?= Html::activeHiddenInput($model, 'adl_cronic') ?>
                <?= Html::activeHiddenInput($model, 'carapulang_id') ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_primer', ['id' => 'klaiminacbgranapform-diagnosa_primer']) ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_primer_ina', ['id' => 'klaiminacbgranapform-diagnosa_primer_ina']) ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_sekunder', ['id' => 'klaiminacbgranapform-diagnosa_sekunder']) ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_sekunder_ina', ['id' => 'klaiminacbgranapform-diagnosa_sekunder_ina']) ?>
                <?= Html::activeHiddenInput($model, 'no_kartu') ?>
                <?= Html::activeHiddenInput($model, 'tgl_lahir') ?>
                <?= Html::activeHiddenInput($model, 'nama_dokter') ?>
                <?= Html::activeHiddenInput($model, 'is_pasiensitb') ?>
                <!-- Informasi Pasien -->
                <div class="row">
                    <?=
                    $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_input_header', [
                        'info' => $info,
                        'noKartu' => $noKartu,
                        'opsi' => $opsi,
                        'model' => $model
                    ])
                    ?>
                    <?=
                    $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_detail_pasien', [
                        'info' => $info,
                        'jenistarif' => $jenistarif,
                        'opsi' => $opsi,
                        'expUmur' => $expUmur,
                        'sep_valid_btn' => $sep_valid_btn,
                        'dokterDpjp' => $dokterDpjp,
                        'model' => $model,
                        'form' => $form
                    ])
                    ?>
                </div>
                <hr>
                <!-- Start Tarif -->
                <?=
                $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_tarif', [
                    'model' => $model
                ])
                ?>
                <!-- End Tarif -->
                <div class="row">
                    <div id="modal-preview" class="modal">
                        <div class="modal-dialog modal-lg" style="width: 90%;">
                            <div class="modal-header bg-inverse" style="z-index: 1050">
                                <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title">Preview</h5>
                            </div>
                            <div class="modal-content">
                                <div class="preview-wrapper" style="margin-top: -50pekpx; position: relative;" id="preview-wrapper">
                                    <div class="overlay-preview"></div>
                                    <iframe frameborder="0" id="preview-content" style="width:100%;height:90vh"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="text-center">
                        <p><i><?= Html::checkbox('agree', true, ['disabled' => true]); ?> Menyatakan benar bahwa data tarif yang tersebut di atas adalah benar sesuai dengan kondisi yang sesungguhnya.</i></p>
                        <h3 class="covid-select hidden"><b>Unggah Berkas Pendukung Klaim</b>
                    </div>
                    </h3>
                </div>
            </div>
            <div class="row covid-berkas covid-select hidden">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <th class="label-grouper" width="170">
                                <?= Yii::t('fe', 'Resume Medis') ?>
                            </th>
                            <td>
                                <div id="upload1" class="fallback dropzone" enctype="multipart/form-data">
                                    <input type="file" class="input-file" id="files1" class="display" multiple />
                                </div>
                            </td>
                            <td class="text-right" width="170">
                                <span class="button-select">
                                    [ <span class="fileinput-1 fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="label-grouper">
                                <?= Yii::t('fe', 'Kartu Identitas') ?>
                            </th>
                            <td>
                                <div id="upload8" class="fallback dropzone" enctype="multipart/form-data">
                                    <input type="file" class="input-file" id="files8" class="display" multiple />
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="button-select">
                                    [ <span class="fileinput-8 fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                </span>
                            </td>
                        </tr>
                        <tr class="kipi-section hidden">
                            <th class="label-grouper">
                                <?= Yii::t('fe', 'Dokumen KIPI') ?>
                            </th>
                            <td>
                                <div id="upload-dokumen-kipi" class="fallback dropzone" enctype="multipart/form-data">
                                    <input type="file" class="input-file" id="files-dokumen-kipi" class="display" multiple />
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="button-select">
                                    [ <span class="fileinput-kipi fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="label-grouper">
                                <?= Yii::t('fe', 'Surat Bebas Biaya') ?>
                            </th>
                            <td>
                                <div id="upload-bebas-biaya" class="fallback dropzone" enctype="multipart/form-data">
                                    <input type="file" class="input-file" id="files-upload-biaya" class="display" multiple />
                                </div>
                                <div>
                                    <span id="source"></span>
                                </div>
                                <div>
                            <td class="text-right">
                                <span class="button-select">
                                    [ <span class="bebas-biaya-input fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                </span>
                            </td>
                        </tr>
                    </table>
                    <hr>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 text-center mb-3">
                    <b><i>Tekanan Darah (mmHg):</i></b>
                </div>
                <div class="col-md-4 col-md-offset-4 text-center" style="display: flex; justify-content: center;">
                    <div>
                        <?= Html::activeTextInput($model, 'sistole', ['class' => 'form-control input-sm validate-minus delete-on-edit free-txt mr-2', 'style' => 'width:60px; border-radius: 10px; text-align: center']) ?>
                        <label for="" class="mt-2"><b>Sistole</b></label>
                    </div>
                    <div>
                        <?= Html::activeTextInput($model, 'diastole', ['class' => 'form-control input-sm validate-minus delete-on-edit free-txt', 'style' => 'width:60px; border-radius: 10px; text-align: center']) ?>
                        <label for="" class="mt-2"><b>Diastole</b></label>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 col-md-offset-4 text-center">
                    <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-proses"><b><i class="fa fa-save"></i></b> <?= Yii::t('fe', 'Simpan Klaim') ?></button>
                </div>
            </div>
            <?=
            $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/partial/_informasi_koding_idrg', [
                'model' => $model
            ])
            ?>
            <?=
            $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/partial/_informasi_koding_inacbg', [
                'info' => $info,
                'model' => $model,
                'cetak' => $cetak,
                'final' => $final
            ])
            ?>
            <br>
            <?php ActiveForm::end(); ?>
            <div class="row">
                <div id="modal-konfirmasi" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-header bg-inverse" style="z-index: 1050">
                            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Konfirmasi Pasien TB</h5>
                        </div>
                        <div class="modal-content content-konfirmasi p-5">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
$this->registerCss($this->render('css/eklaim.css'));
$this->registerJs("
    var _detailDiagnosa = " . $detailDiagnosa . ";
    var _final = '" . $state . "';
    var _updated = '" . $update_dec . "'
    var _diajukan = '" . $isAjukan . "'
    var jenisRawat = '" . $jenisRawat . "';
    var jenisPerawatan = '" . $jenisPerawatan . "';
    var infoTxt = '" . $infoTxt . "';
    var infoInaGrouperTxt = '" . $infoInaGrouperTxt . "';
    var is_rawatintensif = '" . false . "';
    var jenisKelasRawatAwal = '" . $jenisKelasRawatAwal . "';
    var tambahanBiaya = '" . $tambahanBiaya . "';
    var instalasi_nama = '" . $instalasiNama . "';
    var instalasi_rajal = '" . $instalasiRajal . "';
    var instalasi_igd = '" . $instalasiIgd . "';
    var infoNoSep = '" . $info['nosep'] . "';
    var namaPasien = '" . addslashes(strtolower($info['nama_pasien'])) . "';
    var noRm = '" . strtolower($info['no_rekammedik']) . "';
    var kunjunganId = '" . $id . "';
    var isCheck = '" . $isChek . "';
    var stateEnc = '" . $stateEnc . "';
    var idEnc = '" . $idEnc . "';
    var COVID = '" . $payorCovid . "';
    var KIPI = '" . $payorKipi . "';
    var JAMPERSAL = '" . $payorJampersal . "';
    var COINSIDENSE = '" . $payorCoinsidense . "';
    var BAYIBARULAHIR = '" . $payorBayi . "';
    var PERPANJANGANMASARAWAT = '" . $payorPerpanjanganRawat . "';
    var JKN = '" . $payorJkn . "';
    var dateNow = '" . date('d-M-Y H:i', strtotime('now')) . "';
    var hakKelasBpjs = '" . $hakKelas . "';
    var jsonHakKelas = '" . $peserta_hakkelas . "';
    var hakKelas = jsonHakKelas.replace(/[^\d,]/g, '');
    var isVentilator = '" . $isVentilator . "';
    var numberPasientb = '" . $number_pasientb . "';
    var statusKunjungan = '" . $status_kunjungan . "';
    var statusBelumKoreksi = '" . DocoConstants::STATUS_VERIFIKASI_BPJS_BLM . "';
    var statusSudahKoreksi = '" . DocoConstants::STATUS_VERIFIKASI_BPJS_SDH . "';
    var statusFinalKlaim = '" . DocoConstants::STATUS_VERIFIKASI_BPJS_FNL . "';
    var is_prosesklaim = '" . $is_prosesklaim . "';
    var isDokterMultiple = '" . $isDokterMultiple . "';
    var spCodeDb = '" . $spesialProcedure . "';
    var spProsthesis = '" . $spesialProsthesis . "';
    var spInvestigate = '" . $spesialInvestigate . "';
    var spDrug = '" . $spesialDrug . "';
    var idrgDiagnosa = `" . json_encode($IdrgDiagnosa) . "`;
    var idrgProcedure = `" . json_encode($IdrgProcedure) . "`;
    var inacbgsDiagnosa = `" . json_encode($inacbgsDiagnosa) . "`;
    var inacbgsProcedure = `" . json_encode($inacbgsProcedure) . "`;

    " . $this->render('eklaim-new-idrg.js') . $this->render('./js/idrg.js'), View::POS_END, 'js');
?>