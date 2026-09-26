<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-10 17:51:32
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="flex-container">
    <div class="flex-info">
    <!--Informasi Pasien-->
    <?= Yii::$app->controller->renderPartial('//layouts/pasien_identitas', [
        'data_pasien' => $data_pasien
    ]) ?>
    </div>

    <!--Detail Informasi Pasien-->
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
                            <p class="col-sm-12"><?= isset($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : '-' ?></p>
                                    
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Kasus Penyakit") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
            
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Status") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['status_periksa']) ? $data_pasien['status_periksa'] : '-' ?></p>
                        </div>
                        <div class="flex-40">  
                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Dokter DPJP") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['dokter']) ? $data_pasien['dokter'] : '-' ?></p>

                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Dokter Jaga") ?></b></label>
                            <p class="col-sm-12"><?= isset($data_pasien['dokter_jaga']) ? $data_pasien['dokter_jaga'] : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>