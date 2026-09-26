<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-21 17:43:56
 */

use yii\web\View;
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#riwayatpersonal" role="button" aria-expanded="false" aria-controls="riwayatpersonal">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Riwayat Personal'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i class="fa fa-chevron-down" id="chevron"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse" id="riwayatpersonal">
                <div class="flex-container">
                    <div class="flex-30">
                        <div class="row">
                            <label class="text-left control-label font-design col-md-6"><b><?= Yii::t("fe", "Pekerjaan") ?></b></label>
                            <p class="col-md-6"><?= isset($data_pasien['pekerjaan_nama']) ? $data_pasien['pekerjaan_nama'] : '-' ?></p>
                        </div>
                        <div class="row">
                            <label class="text-left control-label font-design col-md-6"><b><?= Yii::t("fe", "Status Merokok") ?></b></label>
                            <p class="col-md-6"><?= $merokok ?> </p>    
                        </div>
                    </div>
                    
                    <div class="flex-30">
                        <div class="row">
                            <label class="text-left control-label font-design col-md-6"><b><?= Yii::t("fe", "Riwayat Penyakit Keluarga") ?></b></label>
                            <p class="col-md-6"><?= $r_penyakitkeluarga ?> </p>
                        </div>
                        <div class="row">
                            <label class="text-left control-label font-design col-md-6"><b><?= Yii::t("fe", "Riwayat Sosial Ekonomi") ?></b></label>
                            <p class="col-md-6"><?= isset($data_pasien['r_peskk']) ? $data_pasien['r_peskk'] : '-' ?>  </p>
                        </div>
                    </div>

                    <div class="flex-30">
                        <div class="row">
                            <label class="text-left control-label font-design col-md-6"><b><?= Yii::t("fe", "Diagnosa Dokter") ?></b></label>
                            <p class="col-md-6"><?= $diagnosa_nama ?> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>