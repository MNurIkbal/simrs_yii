<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-18 10:10:46
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-15 10:46:59
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
use kartik\widgets\DepDrop;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cetak = 'hidden';
$offset = 'col-md-offset-4';
$final = '';
if($state){
    $cetak = '';
    $final = 'hidden';
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
    .dz-image {
        margin-top: 75px !important;
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
                    'back' => [
                        'attributes' => [
                            'href' => '/penjamin-asuransi/informasi-pasien-rajal-bpjs/proses?id='. $id_dec .'&no_pendaftaran='.  $model->no_pendaftaran . ''
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
                            'action' => '/penjamin-asuransi/informasi-pasien-rajal-bpjs/edit-koreksi?id='.$id_dec.'&no_pendaftaran='.  $model->no_pendaftaran . '',
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
                <?=Html::activeHiddenInput($model, 'kunjungan_id', ['class'=>'pendaftaran-id-txt'])?>
                <?=Html::activeHiddenInput($model, 'nama_pasien')?>
                <?=Html::activeHiddenInput($model, 'no_rekam_medik')?>
                <?=Html::activeHiddenInput($model, 'jeniskelamin')?>
                <?=Html::activeHiddenInput($model, 'instalasi_kode')?>
                <?=Html::activeHiddenInput($model, 'pasien_id')?>
                <?=Html::activeHiddenInput($model, 'dokter_kode')?>
                <?=Html::activeHiddenInput($model, 'tgl_masuk')?>
                <?=Html::activeHiddenInput($model, 'tgl_keluar')?>
                <?=Html::activeHiddenInput($model, 'total_tarifrs')?>
                <?=Html::activeHiddenInput($model, 'no_sep', ['class'=>'no-sep'])?>
                <?=Html::activeHiddenInput($model, 'umur')?>
                <?=Html::activeHiddenInput($model, 'berat_lahir')?>
                <?=Html::activeHiddenInput($model, 'los')?>
                <?=Html::activeHiddenInput($model, 'adl_subacute')?>
                <?=Html::activeHiddenInput($model, 'adl_cronic')?>
                <?=Html::activeHiddenInput($model, 'carapulang_id', ['id' => 'carapulang_id-txt'])?>
                <?=Html::activeHiddenInput($model, 'diagnosa_primer')?>
                <?=Html::activeHiddenInput($model, 'diagnosa_sekunder')?>
                <?=Html::activeHiddenInput($model, 'no_kartu')?>
                <?=Html::activeHiddenInput($model, 'tgl_lahir')?>
                <?=Html::activeHiddenInput($model, 'nama_dokter')?>
                <?=Html::activeHiddenInput($model, 'kelas_bpjs')?>
                <?=Html::activeHiddenInput($model, 'no_pendaftaran')?>

                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: center;">
                            <div class="mr-3">
                                <i for="">Jaminan / Cara Bayar : </i><br>
                                <!-- <span style="font-weight: bold;"><?= ArrayHelper::getValue($info, 'penjamin_nama', '-')?></span> -->
                                <div class="dep-jaminan" style="min-width: 250px;">
                                    <?= Html::activeDropDownList($model, 'klaim_penjamin', ArrayHelper::map($opsiPenjamin, 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 delete-on-edit']) ?>
                                 </div>
                            </div>
                            <div class="jkn-select mr-3">
                                <i for="">No. Peserta </i><br>
                                <input type="text" class="form-control jkn-select" id="nomer_peserta" readonly  value="<?= $noKartu ?>">
                            </div>
                            <div class="mr-4 jkn-select">
                                <i for="">No. SEP </i><br>
                                <input type="text" id="no_sep" class="form-control jkn-select" readonly value="<?= ArrayHelper::getValue($info, 'nosep') ?>">
                            </div>
                            <div class="mr-3">
                                <label class="text-left control-label covid-select hidden" style="padding: 0 10px"><b><?= Yii::t("fe", "No Identitas Pasien") ?></b></label>
                                <div class="covid-select hidden" style="display: flex;">
                                    <div style="width:250px;">
                                        <?= Html::activeDropDownList($model, 'identitas_id', ArrayHelper::map($opsi['jenis_identitas'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 mr-3 delete-on-edit']) ?>
                                    </div>
                                    <div class="mr-3">
                                        <?= Html::activeTextInput(
                                            $model,
                                            'identitas_value',
                                        [
                                            'id' => 'identitas_value',
                                            'class' => 'form-control input-sm free-txt ml-4 delete-on-edit',
                                            ]) 
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-left control-label covid-select hidden" style="padding: 0 10px"><b><?= Yii::t("fe", "No. Pengajuan Klaim") ?></b></label>
                                <div class="covid-select hidden">
                                    <?= Html::activeTextInput(
                                        $model,
                                        'no_klaimcovid',
                                        [
                                            'id' => 'no_klaimcovid',
                                            'class' => 'form-control input-sm free-txt',
                                            'disabled' => true
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nama Pasien") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?= isset($info['nama_pasien']) ? $info['nama_pasien'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Jenis Rawat") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?= Yii::t('fe', 'Rawat Jalan') ?> </p>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Rawat") ?></b></label>
                            <div class="col-sm-6">
                                <div style="margin-bottom: 3px"><b><?=Yii::t('fe', 'Masuk')?> :</b> <?=isset($info['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($info['tgl_pendaftaran'])) : '-' ?><br></div>
                                <b><?=Yii::t('fe', 'Keluar')?> :</b> <?=isset($info['tgl_pulang']) ? date('d M Y H:i:s', strtotime($info['tgl_pulang']    )) : '-' ?>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 5px" style="padding: 0 10px">
                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara Masuk") ?></b></label>
                            <div class="col-sm-5">
                                <?= Html::activeDropDownList($model, 'rujukanrs', ArrayHelper::map($opsi['rujukanrs'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 delete-on-edit']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "LOS (hari)") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?=isset($info['los']) ? $info['los'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "ADL Score") ?></b></label>
                            <div class="col-sm-7">
                                <div class="col-sm-6">
                                    <b>Sub Acute:</b> 
                                        <span id="sub-acute">
                                            <?=empty($info['adl_subacute']) ? '-' : $info['adl_subacute']?>
                                        </span>
                                </div>
                                <div class="col-sm-6">
                                    <b>Cronic:</b> 
                                        <span id="cronic">
                                            <?=empty($info['adl_cronic']) ? '-' : $info['adl_cronic']?>
                                        </span>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Dokter Penanggung Jawab") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?=isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif Rumah Sakit") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?=isset($info['layanan_tarif']) ? 'Rp. '.number_format($info['layanan_tarif'], 0, ',', '.') : 0 ?> </p>
                            </div>
                        </div>
                        <div class="row mb-2 pasien_tb_field">
                            <div class="col-md-5" style="padding: 0px;">
                                <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Pasien TB") ?></b></label>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group highlight-addon">
                                            <div class="checkbox">
                                                <?= Html::activeCheckbox($model, 'pasien_tb', [
                                                    'label' => Yii::t("fe", "Ya"),
                                                    'class' => 'pasien_tb',
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="sitb-field" style="display: flex;">
                                            <input type="number" class="form-control mr-1" name="sitb" id="sitb">
                                            <button type="button" class="btn btn-primary btn-xs btn-labeled search-sitb" style="min-width: 110px;"><b><i class="fa fa-search"></i></b>Validasi SITB</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row tarif_poli_eks hidden">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif Poli Eks.") ?></b></label>
                            <div class="col-sm-7">
                                <?=Html::activeTextInput($model, 'tarif_poli_eks', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-md-offset-1">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nomor SEP") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?=isset($info['nosep']) ? $info['nosep'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 5px">
                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas Rawat") ?></b></label>
                            <div class="col-sm-6">
                                <div class="form-group highlight-addon">
                                    <div class="col-md-8">
                                        <div class="checkbox">
                                            <?= Html::activeCheckbox($model, 'jenis_kelasrawat', [
                                                'label' => Yii::t("fe", "Eksekutif"),
                                                'class' => 'jenis_kelasrawat delete-on-edit'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Hak Kelas") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp;Kelas <?=isset($info['kelas_bpjs']) ? $info['kelas_bpjs'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Umur") ?></b></label>
                            <div class="col-sm-5">
                                <p><b>:</b>&nbsp; <?=isset($info['umur']) ? $expUmur : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Berat Lahir") ?></b></label>
                            <div class="col-sm-5">
                                <p style="margin-bottom: 5px"><b>:</b>&nbsp; <?=isset($info['berat_lahir']) ? $info['berat_lahir'] : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 5px" style="padding: 0 10px">
                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara Pulang") ?></b></label>
                            <div class="col-sm-6">
                                <?=Html::activeDropDownList($model, 'carapulang_id', ArrayHelper::map($opsi['carakeluar'], 'lookup_value', 'lookup_name'),['class'=>'form-control select2 delete-on-edit'])?>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif") ?></b></label>
                            <div class="col-sm-6">
                                <?=Html::activeDropDownList($model, 'tarif', $jenistarif,['class'=>'form-control select2 delete-on-edit'])?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Prosedur Bedah") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model,'prosedur_bedah', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Tenaga Ahli") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'tenaga_ahli', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Radiologi") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'radiologi', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Rehabilitasi") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'rehabilitasi', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'obat', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Sewa Alat") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'sewa_alat', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Prosedur Non Bedah") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'prosedur_nonbedah', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Keperawatan") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'keperawatan', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Laboratorium") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'laboratorium', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Kamar/Akomodasi") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'kamar_akomodasi', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Alkes") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'alkes', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat Kemoterapi") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'obat_kemoterapi', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Konsultasi") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'konsultasi', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Penunjang") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'penunjang', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Pelayanan Darah") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'pelayanan_darah', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Rawat Intensif") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'rawat_intensif', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "BMHP") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'bmhp', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat Kronis") ?></b></label>
                            <div class="col-sm-6 marginbottom">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                                    <?=Html::activeTextInput($model, 'obat_kronis', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end tarif -->
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
                <br>
                <div class="text-center"><p><i>
                    <?= Html::checkbox('agree', true, ['disabled' => true]);?> Menyatakan benar bahwa data tarif yang tersebut di atas adalah benar sesuai dengan kondisi yang sesungguhnya.</i></p>
                    <h3 class="covid-select hidden"><b>Unggah Berkas Pendukung Klaim</b></h3>
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
                <div class="row">
                    <div id="previewBerkas" class="modal">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Preview Data</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 10)</b></label></div>
                    <div class="col-md-5">
                        <table class="table table-bordered tbl-icd-10" id="tbl-icd-10" style="border-collapse: collapse;">
                            <tbody></tbody>
                        </table>
                        <br>
                        <table class="table table-bordered hide-me" style="border-collapse: collapse;table-layout: fixed;">
                            <tr>
                                <td style="border-right: 0px;width: 250px"><label>Tambah diagnosa</label><br><?=Html::dropDownList('diagnosa', '',[], ['class'=>'form-control select2 select-diagnosa-10','data-url'=>Url::to(['get-diagnosa', 'type'=>10]), 'data-type' => '10'])?></td>
                                <td width="2"  style="border-left: 0px">
                                    <br>
                                    <button type="button" class="btn btn-success btn-sm btn-add-diagnosa" data-type="10" data-target="tbl-icd-10"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 9)</b></label></div>
                    <div class="col-md-5">
                        <table class="table table-bordered tbl-icd-9" id="tbl-icd-9" style="border-collapse: collapse;">
                           <tbody></tbody>
                        </table>
                        <br>
                        <table class="table table-bordered hide-me" style="border-collapse: collapse;table-layout: fixed;">
                            <tr>
                                <td style="border-right: 0px;width: 250px"><label>Tambah diagnosa</label><br><?=Html::dropDownList('diagnosa', '',[], ['class'=>'form-control select2 select-diagnosa-9', 'data-url'=>Url::to(['get-diagnosa', 'type'=>9]), 'data-type' => 9])?></td>
                                <td width="2"  style="border-left: 0px">
                                    <br>
                                    <button type="button" class="btn btn-success btn-sm btn-add-diagnosa" data-type="9" data-target="tbl-icd-9"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-proses"><b><i class="fa fa-save"></i></b> <?=Yii::t('fe', 'Proses')?></button>
                        <button disabled="disabled" type="button" class="btn btn-danger btn-xs btn-labeled" id="btn-hapus-klaim"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Hapus Klaim')?></button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                <br>
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
                                            <?=Html::dropDownList('sproc_combo', '', [], ['class'=>'form-control sproc-combo', 'id' => 'sproc-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="sproc-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="sproc-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Prosthesis')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('spros_combo', '', [], ['class'=>'form-control spros-combo', 'id' => 'spros-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="spros-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="spros-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Investigation')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('inv_combo', '', [], ['class'=>'form-control inv-combo', 'id' => 'inv-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="inv-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="inv-val" class="text-right">Rp. 0</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Special Drug')?></th>
                                        <td class="text-center">
                                            <?=Html::dropDownList('drug_combo', '', [], ['class'=>'form-control drug-combo', 'id' => 'drug-combo', 'data-placeholder' => 'none'])?>
                                        </td>
                                        <td id="drug-kode" class="text-center">-</td>
                                        <td class="text-center"></td>
                                        <td id="drug-val" class="text-right">Rp. 0</td>
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
                                </table>
                            </div>
                        </div>
                        <div class="row header-tambahan-biaya hidden">
                            <div class="col-md-10">
                                <center><h4><?=Yii::t('fe', 'Tambahan Biaya yang Dibayar Pasien')?></h4></center>
                            </div>
                        </div>
                        <div class="row tambahan-biaya hidden">
                            <div class="col-md-10">
                                <table class="table">
                                    <tr>
                                        <th style="width: 150px"><?=Yii::t('fe', 'Tambahan Biaya')?></th>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right">Rp. 0</td>
                                    </tr>
                                </table>
                            </div>
                        </div><br>
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
            </div>
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var _detailDiagnosa = ".$detailDiagnosa.";
    var _final = '".$state."';
    var _updated = '".$update_dec."'
    var _diajukan = '".$isAjukan."'
    var jenisRawat = '".$jenisRawat."';
    var infoTxt = '".$infoTxt."';
    var instalasiNama = '".$instalasiNama."';
    var klaiminacbg_id = '".$klaimInacbgId."';
    var infoNoSep = '" . $nosep . "';
    var infoNoKartu = '" . $noKartu . "';
    var COVID = '".$payorCovid."';
    var KIPI = '".$payorKipi."';
    var JAMPERSAL = '".$payorJampersal."';
    var JKN = '".$payorJkn."';
    var dpjpId = '".$dokterKode."';
    var _diagnosaMapping10 = '". json_encode($diagnosa10) ."';
    var _diagnosaMapping9 = '". json_encode($diagnosa9) ."';
    var kunjunganId = '" . $id . "';

    ".$this->render('eklaim.js'), View::POS_END, 'js' );

?>