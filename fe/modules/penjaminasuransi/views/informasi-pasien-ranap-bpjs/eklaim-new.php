<?php

/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DepDrop;
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
    .marginbottom{
        margin-bottom: 5px;
    }

    .heightSelect {
        height: 33px !important;
        width: 100px !important;
    }

    .dropzone .dz-default.dz-message::before  {
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
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'backBtn' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id'    => 'btn-back',
                                'data-options' => 'click',
                            ]
                        ],
                        'edit-koreksi' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Edit Koreksi'),
                        'icon'  => 'fa fa-edit',
                        'attributes' => [
                            'id' => 'edit-koreksi',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/edit-koreksi?id='.$id_dec.'&no_pendaftaran='.  $model->no_pendaftaran . '',
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
                            'action' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/log-activity?id='.$id_dec,
                        ]
                    ],
                        'reset-grouping' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Update Grouping'),
                        'icon'  => 'fa fa-refresh',
                        'attributes' => [
                            'id' => 'reset-grouping',
                            'data-options' => 'click',
                            'disabled' => $is_prosesklaim,
                            'data-url' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/reset-grouping?id='.$id_dec,
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <center><h3><?=Yii::t('fe', 'E-Klaim INACBGS')?></h3></center><hr>
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
                        </div></h3>
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
                <?=
                    $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/partial/_informasi_koding', [
                        'model' => $model,
                        'inaGrouper' => $inaGrouper,
                        'unuGrouper' => $unuGrouper,
                    ])
                ?>
                <br>
                <div class="row mb-4">
                    <div class="col-md-12 text-center mb-3">
                        <b><i>Tekanan Darah (mmHg):</i></b>
                    </div>
                    <div class="col-md-4 col-md-offset-4 text-center" style="display: flex; justify-content: center;">
                        <div>
                            <?= Html::activeTextInput($model, 'sistole', ['class' => 'form-control input-sm validate-minus delete-on-edit free-txt mr-2', 'style' => 'width:60px; border-radius: 10px; text-align: center']) ?>
                            <label for="" class="mt-2"><b>Sitole</b></label>
                        </div>
                        <div>
                            <?= Html::activeTextInput($model, 'diastole', ['class' => 'form-control input-sm validate-minus delete-on-edit free-txt', 'style' => 'width:60px; border-radius: 10px; text-align: center']) ?>
                            <label for="" class="mt-2"><b>Diastole</b></label>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-proses"><b><i class="fa fa-save"></i></b> <?=Yii::t('fe', 'Proses')?></button>
                        <button disabled="disabled" type="button" class="btn btn-danger btn-xs btn-labeled" id="btn-hapus-klaim"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Hapus Klaim')?></button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                <div id="proses-final-klaim" class="hidden">
                    <hr>
                    <div class="col-md-10 col-md-offset-2">
                        <div class="row">
                            <div class="col-md-10">
                                <center><h4><?=Yii::t('fe', 'Hasil Grouper')?></h4></center>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <table class="table">
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Info')?></th>
                                        <td colspan="4" class="info-txt"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Jenis Rawat')?></th>
                                        <td colspan="4" class="jenisrawat-txt"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Group')?></th>
                                        <td class="text-center penyakit-nama"></td>
                                        <td class="text-center kode-penyakit"></td>
                                        <td class="text-center kolom-nosep"></td>
                                        <td class="text-right harga-klaim"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Sub Acute')?></th>
                                        <td class="text-center subacute-detail">-</td>
                                        <td class="text-center subacute-kode">-</td>
                                        <td class="text-center"></td>
                                        <td class="text-right subacute-harga">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Chronic')?></th>
                                        <td class="text-center cronic-detail">-</td>
                                        <td class="text-center cronic-kode">-</td>
                                        <td class="text-center"></td>
                                        <td class="text-right cronic-harga">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Procedure')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('sproc_combo', '', [], ['class'=>'form-control sproc-combo spesial-prosedur', 'id' => 'sproc-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="sproc-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="sproc-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Prosthesis')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('spros_combo', '', [], ['class'=>'form-control spros-combo spesial-prosedur', 'id' => 'spros-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="spros-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="spros-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Investigation')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('inv_combo', '', [], ['class'=>'form-control inv-combo spesial-prosedur', 'id' => 'inv-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="inv-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="inv-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Drug')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('drug_combo', '', [], ['class'=>'form-control drug-combo spesial-prosedur', 'id' => 'drug-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="drug-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="drug-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr class="kategoriHemodialysis" >
                                        <th style="width: 150px"><?=Yii::t('fe', 'Penggunaan Dializer')?></th>
                                        <td colspan="4">
                                             <?= Html::activeRadioList($model, 'dializer', [
                                                    '0' => 'Multiple Use (reuse)', 
                                                    '1' => 'Single Use', 
                                                ], [
                                                'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                                                    $check = "";
                                                    if ($model->dializer == $value) {
                                                        $check = 'checked="checked"';
                                                    }
                                                    $return = '<label class="radio-' . $value . '">';
                                                    $return .= '<input class="klaiminacbgranapform-dializer" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' id="dializer-' . $value . '">';
                                                    $return .= ' <i></i>';
                                                    $return .= '<span>' . ucwords($label) . '</span>';
                                                    $return .= '</label>';
                                                    return $return;
                                                }
                                            ]);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr class="kategoriHemodialysis" >
                                        <th style="width: 150px"><?=Yii::t('fe', 'Transfusi Darah')?></th>
                                        <td colspan="4"><p> Jumlah Kantong darah <?= Html::activeTextInput($model, 'transfusi_darah', ['class' => 'input-sm doco-number', 'style' => 'width:60px; border-radius: 10px;']) ?> Kantong</p></td>
                                    </tr>
                                     <tr class="kemenkes_status_klaim">
                                        <th style="width: 150px"><?=Yii::t('fe', 'Status Data Klaim')?></th>
                                        <td colspan="4" class="text-left text-danger kemenkes_status">
                                        </td>
                                    </tr>
                                    <tr class="kemenkes_status_klaim">
                                        <th style="width: 150px"><?=Yii::t('fe', 'Status Klaim')?></th>
                                        <td colspan="4" class="text-left">
                                            <?=Yii::t('fe', '')?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Total')?></th>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td id="total-harga" data="0" class="text-right total-harga"></td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" class="tbl-tambahan-biaya naik-turun-kelas-hide" style="padding-left: 0; padding-right: 0;">
                                            <table style="width: 100%; background-color: #fff0; margin-bottom: 30px;" class="table">
                                                <tr>
                                                    <th colspan="5" class="text-center keterangan-naik-kelas" style="border-top: none;"></th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?=Yii::t('fe', 'Tambahan Biaya')?></th>
                                                    <td colspan="3" class="str-tambahan" style="font-size: 11pt;"></td>
                                                    <td class="text-right" style="font-size: 11pt;">= <span class="tambahanbiaya-txt"></span></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px; border-bottom: 2px solid #ddd; padding-bottom: 15px !important;"><?=Yii::t('fe', 'Pembayar Selisih Biaya')?></th>
                                                    <td colspan="4" style="border-bottom: 2px solid #ddd; padding-bottom: 15px !important;">
                                                        <?= Html::activeRadioList($model, 'pembayar_selisih_biaya', [
                                                                'peserta' => 'Peserta', 
                                                                'pemberi_kerja' => 'Pemberi Kerja', 
                                                                'asuransi_tambahan' => 'Asuransi Tambahan'
                                                            ], [
                                                            'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                                                                $check = "";
                                                                if ($model->naik_kelas == $value) {
                                                                    $check = 'checked="checked"';
                                                                }
                                                                $return = '<label class="radio-' . $value . '">';
                                                                $return .= '<input class="" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' id="pembayarselisih-' . $value . '">';
                                                                $return .= ' <i></i>';
                                                                $return .= '<span>' . ucwords($label) . '</span>';
                                                                $return .= '</label><br>';
                                                                return $return;
                                                            }
                                                        ]);
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row inagrouper">
                            <div class="col-md-10">
                                <center><h4><?=Yii::t('fe', 'Hasil Grouper Eklaim v6')?></h4></center>
                            </div>
                        </div>
                        <div class="row inagrouper">
                            <div class="col-md-10">
                                <table class="table">
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Info')?></th>
                                        <td colspan="4" class="info-ina-txt"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Jenis Rawat')?></th>
                                        <td colspan="4" class="jenisrawat-ina-txt"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'MDC')?></th>
                                        <td class="text-left mdc-ina-txt"></td>
                                        <td class="text-center mdcnumber-ina-txt"></td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'DRG')?></th>
                                        <td class="text-left drg-ina-txt"></td>
                                        <td class="text-center drgnumber-ina-txt"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="button-proc col-sm-4">
                                <button type="button" id="formfinal-btn-cetak-klaim" class="btn btn-primary btn-cetak-klaim btn-xs btn-labeled <?=$cetak?>"><b><i class="fa fa-print"></i></b> <?=Yii::t('fe', 'Cetak Klaim')?></button>
                                <button type="button" id="formfinal-btn-kirim-klaim" class="btn btn-primary btn-xs btn-labeled btn-kirim-klaim <?=$cetak?>"><b><i class="fa fa-print"></i></b> <?=Yii::t('fe', 'Kirim Klaim Online')?></button>
                            </div>
                            <div class="button-proc col-sm-6 text-right">
                                <button type="button" id="formfinal-btn-final-klaim" class="btn btn-success btn-final-klaim btn-xs btn-labeled <?=$final?>"><b><i class="fa fa-send"></i></b> <?=Yii::t('fe', 'Final Klaim')?></button>
                                <button type="button" id="formfinal-btn-edit-klaim" class="<?=$cetak?> btn btn-warning btn-edit-klaim btn-xs btn-labeled"><b><i class="fa fa-pencil"></i></b> <?=Yii::t('fe', 'Edit Klaim')?></button>
                            </div>
                        </div><br>
                    </div>
                </div>
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
    var stateEnc = '".$stateEnc."';
    var idEnc = '".$idEnc."';
    var COVID = '".$payorCovid."';
    var KIPI = '".$payorKipi."';
    var JAMPERSAL = '".$payorJampersal."';
    var COINSIDENSE = '".$payorCoinsidense."';
    var BAYIBARULAHIR = '".$payorBayi."';
    var PERPANJANGANMASARAWAT = '".$payorPerpanjanganRawat."';
    var JKN = '".$payorJkn."';
    var dateNow = '" . date('d-M-Y H:i', strtotime('now')) . "';
    var hakKelasBpjs = '".$hakKelas."';
    var jsonHakKelas = '".$peserta_hakkelas."';
    var hakKelas = jsonHakKelas.replace(/[^\d,]/g, '');
    var isVentilator = '".$isVentilator."';
    var numberPasientb = '".$number_pasientb."';
    var statusKunjungan = '".$status_kunjungan."';
    var statusBelumKoreksi = '".DocoConstants::STATUS_VERIFIKASI_BPJS_BLM."';
    var statusSudahKoreksi = '".DocoConstants::STATUS_VERIFIKASI_BPJS_SDH."';
    var isDokterMultiple = '".$isDokterMultiple."';

    " . $this->render('eklaim-new.js'), View::POS_END, 'js');

?>