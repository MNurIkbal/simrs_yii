<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medik") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '-' ?> </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['tanggal_lahir']) ? date('d M Y', strtotime($data['tanggal_lahir'])) : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5">
                            <b><?= Yii::t("fe", "Tanggal Perawatan") ?></b>
                        </label>
                        <div class="col-sm-7">
                            <p><b>:</b>&nbsp;<?= isset($data['tgl_pendaftaran']) 
                                    ? date('d-M-Y',strtotime($data['tgl_pendaftaran'])) : '-' ?>  
                                    s/d 
                                    <?= !empty($data['tglpasienpulang']) 
                                    ? date('d-M-Y',strtotime($data['tglpasienpulang'])) : '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['umur']) ? $data['umur'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '-' ?>  </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter pemeriksa") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['dokter_dpjp']) ? $data['dokter_dpjp'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?>  </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['kelaspelayanan_nama']) ? $data['kelaspelayanan_nama'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['jenis_kelamin']) ? $data['jenis_kelamin'] : '-' ?>  </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['carabayar_nama']) ? $data['carabayar_nama'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['jeniskasuspenyakit_nama']) ? $data['jeniskasuspenyakit_nama'] : '-' ?>  </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['penjamin_nama']) ? $data['penjamin_nama'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis Layanan") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['instalasi_nama']) ? $data['instalasi_nama'] : '-' ?>  </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <?php 
                $filename = !empty($data['photopasien']) ? file_exists('/media/img/pasien/'.$data['photopasien']) ? '/media/img/pasien/'.$data['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                ?>
                <?=Html::img($filename, ['style'=>'height: 150px;margin: 5px auto', 'class'=>'img-responsive'])?>
            </div>
        </div>
    </div>
</div>