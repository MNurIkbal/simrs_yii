<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
<div class="row row-eq-height">
    <div class="col-md-12 flex-container">
        
        <div class="flex-info">
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

                <div class="panel-body column-detail collapse multi-collapse" id="detailinfo-ranap">
                    <div class="row">
                        <div class="col-md-12 flex-container">
                            <div class="flex-detail-info">              
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Dokter DPJP") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['dokter_admisi']) ? $data_pasien['dokter_admisi'] : '-' ?></p>
                                
                                <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                                <p class="side-margin"><?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
                            </div>
                    
                            <div class="flex-detail-info">
                            <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Kamar") ?></b></label>
                            <p class="side-margin">
                                <?=
                                    (isset($data_pasien['kamarruangan_nokamar']) ? $data_pasien['kamarruangan_nokamar'] : '-' ) . ' / ' .
                                    (isset($data_pasien['no_tempattidur']) ? $data_pasien['no_tempattidur'] : '-' );
                                ?> 
                            </p>
                            
                            <label class="text-left control-label font-design"><b><?= Yii::t("fe", "Status") ?></b></label>
                            <p class="side-margin"><?= isset($data_pasien['stat_ranap']) ? $data_pasien['stat_ranap'] : '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</diV>
