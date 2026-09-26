<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-30 10:24:33
 */

use yii\web\View;
use yii\helpers\Html;
$title = isset($title) ? $title : Yii::t('fe', 'Informasi Pasien');
?>
<!-- Informasi Pasien -->

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien">
                <div class="panel-heading flex-container ">
                    <h6 class="panel-title"><b><?=$title?></b></h6>
                    
                    <p class="p-data" id="data-pasien"><?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> - 
                        <b class="font" ><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></b>
                        (<?= isset($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-' ?>)
                        <?php
                            if (array_key_exists('titipan', $data_pasien)) {
                                if ($data_pasien['titipan'] == true) {
                                    echo ' - <b><span style="color:red;"> KELAS TAGIHAN : '.$data_pasien['kelas_ditagihkan'].'</span></b>';
                                }
                            }
                        ?>
                    </p>
                    
                    <ul class="icons-list">
                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                    </ul>
                </div>
            </a>

            <div class="panel-body column-info collapse multi-collapse" id="infopasien">
                <div class="flex-container">
                    <div class="flex-photo-pasien">
                        <div class="border-img">
                            <?php 
                                $filename = isset($data_pasien['photopasien']) ? !empty($data_pasien['photopasien']) ? '/media/img/pasien/'.$data_pasien['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                            ?>
                            <?=Html::img($filename, ['style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive img-fluid'])?>
                        </div>
                    </div>

                    <div class="flex-info-pasien">
                        <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Pasien") ?></b></label>
                        <p class="col-sm-12"><?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                        <?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?> -
                        <?= isset($data_pasien['jenis_kelamin']) ? $data_pasien['jenis_kelamin'] : '-' ?>    
                        </p>
                    
                        <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                        <p class="col-sm-12 "><?= isset($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-' ?> 
                        - (<?= isset($data_pasien['umur']) ? $data_pasien['umur'] : '-' ?>)
                        </p>
                    </div>

                    <div class="flex-info-pasien">
                        <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                        <p class="col-sm-12"><?= isset($data_pasien['no_pendaftaran']) ? $data_pasien['no_pendaftaran'] : '-' ?> 
                        - (<?= isset($data_pasien['tgl_pendaftaran']) ? date('d-m-Y H:i:s', strtotime($data_pasien['tgl_pendaftaran'])) : '-' ?>)
                        </p>
                       
                        <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                        <p class="col-sm-12"><?= isset($data_pasien['kelaspelayanan_nama']) ? $data_pasien['kelaspelayanan_nama'] : '-' ?> -
                        <?= isset($data_pasien['carabayar_nama']) ? $data_pasien['carabayar_nama'] : '-' ?> -
                        <?= isset($data_pasien['penjamin_nama']) ? $data_pasien['penjamin_nama'] : '-' ?>
                        <br>
                        <?php
                            if (array_key_exists('titipan', $data_pasien)) {
                                if ($data_pasien['titipan'] == true) {
                                    echo '<b><span style="color:red;"> KELAS TAGIHAN : '.$data_pasien['kelas_ditagihkan'].'</span></b>';
                                }
                            }
                        ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    $(document).ready(function() {
        $('#info-heading').click(function() {
            $('#data-pasien').toggle();
        });
    });
", View::POS_END); ?>