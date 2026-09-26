<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:33:54
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-30 17:28:09
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
    </div>
    <?=Html::hiddenInput('kelaspelayanan', isset($data['kelaspelayanan_id']) ? $data['kelaspelayanan_id'] : '', [ 'class'=>'kelaspelayanan-id'])?>
    <?=Html::hiddenInput('penjamin', isset($data['penjamin_id']) ? $data['penjamin_id'] : '', ['class'=>'penjamin-id'])?>
    <?=Html::hiddenInput('dr_operator_id', isset($data['dr_operator_id']) ? $data['dr_operator_id'] : '', ['class'=>'dr-operator-id'])?>
    <?=Html::hiddenInput('dok_operator', isset($data['dok_operator']) ? $data['dok_operator'] : '', ['class'=>'dok-operator'])?>
    <?=Html::hiddenInput('dr_anastesi_id', isset($data['dr_anastesi_id']) ? $data['dr_anastesi_id'] : '', ['class'=>'dr-anastesi-id'])?>
    <?=Html::hiddenInput('dok_anastesi', isset($data['dok_anastesi']) ? $data['dok_anastesi'] : '', ['class'=>'dok-anastesi'])?>
    <?=Html::hiddenInput('jam_rencana_mulai', isset($data['jam_rencana_mulai']) ? $data['jam_rencana_mulai'] : '', ['class'=>'jam-rencana-mulai'])?>
    <?=Html::hiddenInput('jam_rencana_selesai', isset($data['jam_rencana_selesai']) ? $data['jam_rencana_selesai'] : '', ['class'=>'jam-rencana-selesai'])?>
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
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal pendaftaran") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['tgl_pendaftaran']) ? date('d M Y', strtotime($data['tgl_pendaftaran'])) : '-' ?>  </p>
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
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['kelaspelayanan_nama']) ? $data['kelaspelayanan_nama'] : '-' ?> </p>
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
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['carabayar_nama']) ? $data['carabayar_nama'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp;<?= isset($data['j_kelamin']) ? $data['j_kelamin'] : '-' ?>  </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                        <div class="col-sm-5">
                            <p><b>:</b>&nbsp; <?= isset($data['penjamin_nama']) ? $data['penjamin_nama'] : '-' ?> </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <?php 
                $filename = isset($data['photopasien']) ? !empty($data['photopasien']) ? '/media/img/pasien/'.$data['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                ?>
                <?=Html::img($filename, ['style'=>'height: 150px;margin: 5px auto', 'class'=>'img-responsive'])?>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Operasi Pasien'); ?></b></h6>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nomor operasi") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['no_masukpenunjang']) ? $data['no_masukpenunjang'] : '-' ?>  </p>
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal operasi") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['tgl_operasi']) ? date('d M Y H:i:s', strtotime($data['tgl_operasi'])) : '-' ?>  </p>
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter perujuk") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['dok_perujuk']) ? $data['dok_perujuk'] : '-' ?>  </p>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Diagnosa") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['diagnosa_nama']) ? $data['diagnosa_nama'] : '-' ?>  </p>
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Catatan") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['catatan_dokterpengirim']) ? $data['catatan_dokterpengirim'] : '-' ?>  </p>
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter pemeriksa") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp; <?= isset($data['dokter_penunjang']) ? $data['dokter_penunjang'] : '-' ?> </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                <div class="col-sm-5">
                    <p><b>:</b>&nbsp;<?= isset($data['jeniskasuspenyakit_nama']) ? $data['jeniskasuspenyakit_nama'] : '-' ?>  </p>
                </div>
            </div>
        </div>
    </div>
</div>