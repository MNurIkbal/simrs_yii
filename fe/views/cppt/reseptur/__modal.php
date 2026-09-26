<?php

use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .item-container-racikan {
        margin-bottom: 1% !important;
    }

    .required .has-star:not(.custom-control-label)::after,
    .is-required::after {
        content: "*";
        margin-left: 3px;
        font-weight: normal;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: tomato;
    }

    #text_stok_tersedia,
    #text_stok_tersedia_racikan,
    #text_konversi {
        font-size: 12pt;
    }

    .radio {
        margin-right: 5%;
    }

    .required-reseptur::after {
        color: red;
        content: " *";
    }

    .field-generalresepturnrdetailform-is_kronis {
        padding-top: 10%;
    }

    .is-kronis-racikan .form-group {
        padding-top: 5%;
        padding-left: 20%;
    }

    .required-type {
        color: red;
    }

    .mt-20 {
        margin-top: 20px;
    }
    .obatalkes__not-found {
        background-color: #F44336 !important;
        color: #fff;
    }

    .select2-results>.select2-results__options {
        max-height: 150px !important;
    }
</style>

<?= Html::hiddenInput('tmp_ruangan_id', null, [
    'id' => 'tmp-ruangan-id'
]) ?>

<?php $form = ActiveForm::begin([
    'id' => 'order-reseptur-form',
    'action' => $url['form-action'],
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]); ?>

<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b>Tambah Resep - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></b></h5>
</div>
<div class="modal-body">
    <!-- Form Reseptur Header -->
    <?= $this->render('_form_reseptur_header', [
        'form' => $form,
        'model' => $model,
        'is_ranap' => $is_ranap,
        'list_data_apotek' => $list_data_apotek,
        'penjamin_id' => isset($data_pasien['penjamin_id']) ? $data_pasien['penjamin_id'] : 1,
        'dokterList' => $dokterList,
        'dokter_url' => $url['dokter_url'],
        'history_resep_modal' => $url['urlModalHistoryResep']
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <hr>
        </div>
    </div>

    <?php ActiveForm::end() ?>

        <div class="row tabel-reseptur-container">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Tabel Reseptur') ?></h5>
                    <div class="heading-elements">
                    </div>
                </div>
                <div class="panel-body" id="div-tabel-reseptur" tabindex="-1">
                    <div class="row">
                        <div class="pull-left" style="margin-left:10px; margin-bottom:10px;">
                            <button type="button" id="btn-history-resep" class="btn btn-labeled btn-info btn-xs" data-width="80%" data-href="<?= $url['urlModalHistoryResep'] ?>">
                                <b><i class='fa fa-history'></i></b> Arsip Resep
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Template</label>
                            <div class="form-group">
                                <?= Html::dropDownList('template_list', null, [], [
                                    'id' => 'template_list'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top:12px;">
                            <button type="button" class="btn btn-md btn-info pilih-template">Pilih</button>
                        </div>

                        <?php
                        /* FORM CHECKLIST UDD RAWAT INAP */
                        if($is_ranap && isset($kategori_resep)): ?>
                            <div class="col-md-5">
                                <div class="row">
                                    <?php foreach ($kategori_resep as $key => $value): ?>
                                        <div class="col-sm-4" align="<?= ($key == 0) ? 'left' : 'right' ?>">
                                            <label>
                                                <?php if($key == 0): ?>
                                                    Kategori Resep : 
                                                <?php else : ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </label>
                                            <div class="form-group">
                                                <input type="checkbox" id="resep_<?= $value['lookup_id'] ?>" name="kategori_resep" class="kategori_resep" value="<?= $value['lookup_id'] ?>">
                                                <label for="resep_<?= $value['lookup_id'] ?>" style="vertical-align: bottom;"><?= $value['lookup_name']; ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12 table-container">
                            <table width="100%" id="tabel-reseptur" class="table table-condensed table-striped table-hover datatable-basic dataTable">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th></th>
                                        <th class="text-center" style="width: 9%;"><?= Yii::t('fe', 'Aksi') ?></th>
                                        <th>No</th>
                                        <th><?= Yii::t('fe', 'Racikan / non racikan') ?></th>
                                        <th><?= Yii::t('fe', 'R ke-') ?></th>
                                        <th><?= Yii::t('fe', 'Nama obat') ?></th>
                                        <th style="display:none"><?= Yii::t('fe', 'Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Signa') ?></th>
                                        <th><?= Yii::t('fe', 'Hari') ?></th>
                                        <th class="text-right"><?= Yii::t('fe', 'Qty') ?></th>
                                        <th class="text-right"><?= Yii::t('fe', 'Stok Tersedia') ?></th>
                                        <th class="text-right"><?= Yii::t('fe', 'Kebutuhan') ?></th>
                                        <th class="text-right" style="display:none"><?= Yii::t('fe', 'Harga satuan (Rp.)') ?></th>
                                        <th style="display:none" class="text-right"><?= Yii::t('fe', 'Jumlah harga (Rp.)') ?></th>
                                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                                        <th><?= Yii::t('fe', 'Kronis') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="list-temp-obat">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Non Racikan Input -->
    <?= $this->render('_form_non_racikan', [
        'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
        'pendaftaran_id' => $pendaftaran_id,
        'kelaspelayanan_id' => $kelaspelayanan_id,
    ]); ?>

    <!-- Racikan Input -->
    <?php if ($is_freetext) { ?>
        <?= $this->render('_form_racikan'); ?>
    <?php } else { ?>
        <?= $this->render('_form_racikan_detail'); ?>
    <?php } ?>
</div>
<div class="modal-footer text-right">
    <button type="button" style='margin-right: 5px' id="btn-hapus-template" class="btn btn-labeled btn-danger btn-xs disabled btn-template-group" disabled>
        <b><i class="fa fa-trash"></i></b> Hapus Template
    </button>
    <button type="button" style='margin-right: 5px' id="btn-update-template" class="btn btn-labeled btn-warning btn-xs disabled btn-template-group" data-toggle="modal" data-target="#modal-template-resep" disabled>
        <b><i class="fa fa-pencil"></i></b> Update Template
    </button>
    <button type="button" style='margin-right: 5px' id="btn-popup-template" class="btn btn-labeled btn-primary btn-xs disabled" action="<?= $url['actionTemplate'] ?>" data-toggle="modal" data-target="#modal-template-resep" disabled>
        <b><i class="fa fa-file"></i></b> Simpan Template
    </button>
    <button type='button' style='margin-right: 5px' id="btn-save-reseptur" class='btn btn-labeled btn-info btn-xs' onclick="simpanResepturValidasiKonfig(this)">
        <b><i class='fa fa-save'></i></b> Simpan Resep
    </button>
</div>

<?php
$groupJenisobat = isset($groupJenisobat) ? json_encode($groupJenisobat, JSON_FORCE_OBJECT) : null;
$allowZeroStock = isset($allowZeroStock) ? $allowZeroStock : false; 
$this->registerJs("
    var kategori_udd = ".DocoConstants::KATEGORI_RESEP_UDD.";
    var _tmpStok = [];
    var pendaftaran_id_origin = $('#order-reseptur-form').attr('action').includes('igd') ? '" . $pendaftaran_id . "' : '" . DocoHelpers::decrypt($pendaftaran_id) . "';
    var kelaspelayanan_id = '" . $kelaspelayanan_id ."';
    var kelastagihan_id = '" . ArrayHelper::getValue($data_pasien, 'kelas_ditagihkan_id') . "';
    var pegawai_id = '" . $pegawai_id . "';
    var is_others = '" . $is_others . "';
    var enable_split_kronis = '" . $enable_split_kronis . "';
    var hari_resep_kronis = '" . $hari_resep_kronis . "';
    var pasien_id = '" . $data_pasien['pasien_id'] . "';
    var ruanganreseptur_id = '" . $ruanganreseptur_id . "';
    var list_temp_obat = [];
    var is_ranap = '" . $is_ranap . "';
    var cek_kategori_resep = '" . isset($kategori_resep) . "';
    var racikanArray = [];
    var urlSimpanReseptur = '" . $url['urlSimpanReseptur'] . "';
    var urlDeleteTemplate = '" . $url['urlDeleteTemplate'] . "';
    var urlUpdateTemplate = '" . $url['urlUpdateTemplate'] . "';
    var konfigStokObatAlkes = '" . $konfigStokObatAlkes . "';
    var allowZeroStock = '". $allowZeroStock ."';
    var groupJenisobat = '". $groupJenisobat ."';
    var instalasiId = '".$instalasi_id."';
");
$this->registerJs($this->render('js/__modal.js'), View::POS_END);
$this->registerJs($this->render('js/_header.js'), View::POS_END);
?>
