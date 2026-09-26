<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-21 14:48:58
 */

use yii\web\View;
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 ><?= Yii::t('fe', 'Riwayat Personal'); ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Pekerjaan") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= isset($data_pasien['pekerjaan_nama']) ? $data_pasien['pekerjaan_nama'] : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Status Merokok") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= $merokok ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Riwayat Penyakit Keluarga") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= $r_penyakitkeluarga ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Diagnosa Dokter") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= $diagnosa_nama ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Riwayat Sosial Ekonomi") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= isset($data_pasien['r_peskk']) ? $data_pasien['r_peskk'] : '-' ?>  </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>