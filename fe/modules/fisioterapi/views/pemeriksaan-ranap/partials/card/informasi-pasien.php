<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;

$dataPasien = ArrayHelper::getValue($dataView, 'dataPasien');

?>
<div class="panel panel-default" style="margin-top: 10px; margin-bottom: 15px;">
    <a id="heading-history-patient" data-toggle="collapse" href="#patient-history-tab" role="button" aria-expanded="false" aria-controls="patient-history-tab">
        <div class="panel-heading flex-container ">
            <h6 class="panel-title text-bold">Informasi Pasien</h6>
            <p class="p-data" id="data-pasien">
                <?= isset($dataPasien['no_rekam_medik']) ? $dataPasien['no_rekam_medik'] : '-' ?> -
                <span class="text-bold"><?= isset($dataPasien['nama_pasien']) ? $dataPasien['nama_pasien'] : '-' ?></span>
            </p>
            <ul class="icons-list">
                <li><i id="chevron" class="fa fa-chevron-down" style="margin-top: 3px;"></i></li>
            </ul>
        </div>
    </a>
    <div class="panel-body collapse multi-collapse label-information" id="patient-history-tab">
        <div class="row" style="margin-left: 0px; margin-top: 5px;">
            <div class="col-md-3">
                <label>Pasien</label>
                <p><?= !empty($dataPasien['no_rekam_medik']) ? $dataPasien['no_rekam_medik'] : '-' ?></p>
                <p><?= !empty($dataPasien['nama_pasien']) ? $dataPasien['nama_pasien'] : '-' ?> - <?= !empty($dataPasien['jeniskelamin_nama']) ? $dataPasien['jeniskelamin_nama'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Nomor Pendaftaran</label>
                <p><?= !empty($dataPasien['no_pendaftaran']) ? $dataPasien['no_pendaftaran'] : '-' ?></p>
                <p><?= DocoHelpers::convertDate($dataPasien['tgl_pendaftaran'], 'd-m-Y H:i') ?></p>
            </div>
            <div class="col-md-3">
                <label>Dokter DPJP</label>
                <p><?= !empty($dataPasien['dokterdpjp_nama']) ? $dataPasien['dokterdpjp_nama'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Nama Terapis</label>
                <p><?= !empty($dataPasien['terapis_nama']) ? $dataPasien['terapis_nama'] : '-' ?></p>
            </div>
        </div>
        <div class="row" style="margin-left: 0px;">
            <div class="col-md-3">
                <label>Tanggal Lahir</label>
                <p><?= DocoHelpers::convertDate($dataPasien['tanggal_lahir'], 'd-m-Y') ?></p>
            </div>
            <div class="col-md-3">
                <label>Status</label>
                <p><?= !empty($dataPasien['status_periksa_nama']) ? $dataPasien['status_periksa_nama'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Kamar</label>
                <p>
                    <?= !empty($dataPasien['ruangan_nama']) ? $dataPasien['ruangan_nama'] : '-' ?> 
                    <br/>   
                    <?= !empty($dataPasien['kamar']) ? $dataPasien['kamar'] : '-' ?>
                    <?= !empty($dataPasien['no_tempattidur']) ? '- '.(string) $dataPasien['no_tempattidur'] : '-' ?>
                </p>
            </div>
            <div class="col-md-3">
                <label>Alamat Pasien</label>
                <p><?= !empty($dataPasien['alamat_pasien']) ? $dataPasien['alamat_pasien'] : '-' ?></p>
            </div>
        </div>
        <div class="row" style="margin-left: 0px;">
            <div class="col-md-3">
                <label>Umur</label>
                <p><?= !empty($dataPasien['umur']) ? $dataPasien['umur'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Kasus Penyakit</label>
                <p><?= !empty($dataPasien['jeniskasuspenyakit_nama']) ? $dataPasien['jeniskasuspenyakit_nama'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Kelas Pelayanan</label>
                <p><?= !empty($dataPasien['kelaspelayanan_nama']) ? $dataPasien['kelaspelayanan_nama'] : '-' ?> - <?= !empty($dataPasien['carabayar_nama']) ? $dataPasien['carabayar_nama'] : '-' ?> - <?= !empty($dataPasien['penjamin_nama']) ? $dataPasien['penjamin_nama'] : '-' ?></p>
            </div>
            <div class="col-md-3">
                <label>Dokter Perujuk</label>
                <p><?= !empty($dataPasien['dokterperujuk_nama']) ? $dataPasien['dokterperujuk_nama'] : '-' ?></p>
            </div>
        </div>
    </div>
</div>