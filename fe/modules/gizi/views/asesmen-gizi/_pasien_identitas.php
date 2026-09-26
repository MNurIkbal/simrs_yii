<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
?>

<?= Yii::$app->controller->renderPartial('//layouts/pasien_identitas', [
    'data_pasien' => $data_pasien
]) ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter DPJP") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= isset($data_pasien['dokter_admisi']) ? $data_pasien['dokter_admisi'] : '-' ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No kamar / No Tempat Tidur") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;
                                    <?=
                                        (isset($data_pasien['kamarruangan_nokamar']) ? $data_pasien['kamarruangan_nokamar'] : '-' ) . ' / ' .
                                        (isset($data_pasien['no_tempattidur']) ? $data_pasien['no_tempattidur'] : '-' );
                                    ?> 
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Status") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= isset($data_pasien['stat_ranap']) ? $data_pasien['stat_ranap'] : '-' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>