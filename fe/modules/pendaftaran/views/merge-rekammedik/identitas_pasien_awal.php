<?php

use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

?>

<div class="panel panel-default">
    <a id="info-heading" data-toggle="collapse" href="#infopasien_asal" role="button" aria-expanded="true" aria-controls="infopasien_asal">
        <div class="panel-heading flex-container">
            <h6 class="panel-title informasi_pasien">
                <span><b><?= Yii::t('fe', 'Informasi Rekam Medik Asal') ?></b></span>
            </h6>
            
            <ul class="icons-list">
                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
            </ul>
        </div>
    </a>

    <div class="panel-body column-info multi-collapse in" id="infopasien_asal">
        <div class="flex-container">
            <div class="flex-info-pasien">
                <label class="text-left control-label col-sm-12 f-15">
                    <b><?= Yii::t('fe', 'DATA PASIEN') ?></b>
                </label>
            
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'NORM') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="norm_asal"></span> 
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'NAMA') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="nama_asal"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'TANGGAL LAHIR') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="tanggal_lahir_asal"></span> (<span class="umur_asal"></span>)
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'Alamat') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="alamat_asal"></span>
                </p>
            </div>
            <div class="flex-info-pasien">
                <label class="text-left control-label col-sm-12 f-15">
                    <b><?= Yii::t('fe', 'KUNJUNGAN TERKAHIR') ?></b>
                </label>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t('fe', 'NO PENDAFTARAN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="no_pendaftaran_asal"></span>
                </p>
            
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'TGL REGISTRASI') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="tgl_registrasi_asal"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'RUANGAN') ?></b>
                </label>
                
                <p class="col-sm-12">
                    <span class="ruangan_asal"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'DOKTER DPJP') ?></b>
                </label>

                <p class="col-sm-12">
                    <span class="dokter_dpjp_asal"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'Status') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="status_asal"></span>
                </p>
            </div>
        </div>
    </div>
</div>