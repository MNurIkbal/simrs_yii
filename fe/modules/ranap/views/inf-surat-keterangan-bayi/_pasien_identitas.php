<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
<style type="text/css">
.column-detail {
    height: 291px!important;
}
</style>
<div class="row row-eq-height">
    <div class="col-md-12 flex-container">

        <div class="flex-info">
            <?php 
            $data_pasien['no_rekam_medik'] =isset($data_pasien['rm_bayi'])?$data_pasien['rm_bayi']:'-';
            $data_pasien['nama_pasien']    =isset($data_pasien['nama_bayi'])?$data_pasien['nama_bayi']:'-';
            $data_pasien['jenis_kelamin']  =isset($data_pasien['jkbayi_nama'])?$data_pasien['jkbayi_nama']:'-';
            $data_pasien['tanggal_lahir']  =isset($data_pasien['tanggal_lahir'])?$data_pasien['tanggal_lahir']:'-';
            ?>
            <?= Yii::$app->controller->renderPartial('//layouts/pasien_identitas', [
                'data_pasien' => $data_pasien
            ]) ?>
        </div>

        <!--detail informasi ranap-->
        <div class="flex-detail">
            <div class="panel panel-default side-margin">
                <a data-toggle="collapse" href="#detailinfo-ranap" role="button" aria-expanded="false" aria-controls="detailinfo-ranap">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi'); ?></b></h6>

                        <ul class="icons-list">
                            <li><i class="fa fa-chevron-down" id="chevron"></i></li>
                        </ul>
                    </div>
                </a>

                <div class="panel-body column-detail collapse multi-collapse" id="detailinfo-ranap" >
                    <div class="row">
                        <div class="col-md-12 flex-container">
                            <div class="flex-detail-info">              
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Dokter DPJP") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['dokter']) ? $data_pasien['dokter'] : '-' ?></p>
                                
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
                            </div>

                            <div class="flex-detail-info">
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Kamar") ?></b></label>
                                <p class="side-margin">
                                    <?=
                                    (isset($data_pasien['ruangan']) ? $data_pasien['ruangan'] : '-' ) . ' / ' .
                                    (isset($data_pasien['no_tempattidur']) ? $data_pasien['no_tempattidur'] : '-' );
                                    ?> 
                                </p>
                                
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Status") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['stat_ranap']) ? $data_pasien['stat_ranap'] : 'Belum Diperiksa' ?></p>
                            </div>

                            <div class="flex-detail-info">
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Nama Ibu") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['nama_ibu']) ? $data_pasien['nama_ibu'] : '-' ?></p>
                                
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "No. KTP") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['no_identitas']) ? $data_pasien['no_identitas'] : '-' ?></p>
                            </div>

                            <div class="flex-detail-info">
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Alamat Rumah") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['alamat']) ? $data_pasien['alamat'] : '-' ?></p>
                                
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Pekerjaan") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['pekerjaan']) ? $data_pasien['pekerjaan'] : '-' ?></p>
                            </div>

                            <div class="flex-detail-info">
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Golongan Darah") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['golongan_darah']) ? $data_pasien['golongan_darah'] : '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</diV>
