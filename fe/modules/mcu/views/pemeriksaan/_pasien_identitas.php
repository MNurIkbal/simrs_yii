<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 16:11:44
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-09-03 11:23:19
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
    
<div class="col-md-8">
    <?= Yii::$app->controller->renderPartial('//layouts/pasien_identitas', [
        'data_pasien' => $data_pasien
    ]) ?>
</div>


<!--Detail Informasi Pasien-->
<div class="col-md-4">
    <div class="row flex-detail">
        <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#detailinfo" role="button" aria-expanded="false" aria-controls="detailinfo">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi'); ?></b></h6>
                        <div>
                            <ul class="icons-list">
                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                            </ul>
                        </div>
                    </div>
                </a>
                <div class="panel-body column-detail collapse multi-collapse" id="detailinfo">
                    <div class="flex-container">
                        <div class="flex-60">
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Ruangan") ?></b></label>
                            <p class="col-sm-12"><?= (isset($data_pasien['poliklinik']) ? $data_pasien['poliklinik'] : '-' ); ?></p>
                                    
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Penyakit") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
                        </div>
                        <div class="flex-40">  
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Dokter") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['nama_pegawai']) ? $data_pasien['nama_pegawai'] : '-' ?></p>
            
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Status") ?></b></label>                               
                            <p class="col-sm-12"><?= isset($data_pasien['stat_ranap']) ? $data_pasien['stat_ranap'] : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    

