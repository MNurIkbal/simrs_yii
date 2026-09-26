<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-04 15:45:51
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Riwayat Asesmen Medis Rawat Inap') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Nama Pasien') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ArrayHelper::getValue($infoPasien, 'nama_pasien') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Alamat Pasien') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ArrayHelper::getValue($infoPasien, 'alamat_pasien') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Umur') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ArrayHelper::getValue($infoPasien, 'umur') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No RM') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ArrayHelper::getValue($infoPasien, 'no_rekam_medik') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="tabel-asmed-spesialis" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Nama Dokumen')?></th>
                        <th><?=Yii::t('fe', 'Tanggal')?></th>
                        <th><?=Yii::t('fe', 'User Input')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!empty($listData)) {
                            $no = 1;
                            foreach ($listData as $key => $value) {
                                $tglPeriksa = ArrayHelper::getValue($value, 'tanggal'); 
                                $asesmenMedisId = ArrayHelper::getValue($value, 'asesmenmedis_id'); 
                                $reportCode = ArrayHelper::getValue($value, 'lookup_kode');
                    ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= ArrayHelper::getValue($value, 'nama_dokumen') ?></td>
                                    <td><?= $tglPeriksa ? date('d/M/Y H:i:s', strtotime($tglPeriksa)) : '-' ?></td>
                                    <td><?= ArrayHelper::getValue($value, 'user_input') ?></td>
                                    <td>
                                        <?= Html::a(Yii::t('fe', 'Lihat Dokumen'), ['/reports/viewer/'.$reportCode.'?asesmenmedis_id='.$asesmenMedisId], ['id' => 'btn-cetak-asmed-spesialis-'.$key, 'class'=>'btn btn-info btn-sm btn-riwayat', 'target' => '_blank']); ?>
                                    </td>
                                </tr>
                    <?php           
                            }
                        }

                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>