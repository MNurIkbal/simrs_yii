<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-04 13:59:54
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
    <h5 class="modal-title"><?= Yii::t('fe', 'Riwayat Laboratorium') ?></h5>
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
                        <p class="form-control-static"><?=@$infoPasien['nama_pasien']?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Alamat Pasien') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?=@$infoPasien['alamat_pasien']?></p>
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
                        <p class="form-control-static"><?=@$infoPasien['umur']?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No RM') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?=@$infoPasien['no_rekam_medik']?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No Antrian')?></th>
                        <th><?=Yii::t('fe', 'Tanggal Pendaftaran')?></th>
                        <th><?=Yii::t('fe', 'No Pendaftaran')?></th>
                        <th><?=Yii::t('fe', 'No Rekam Medis')?></th>
                        <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                        <th><?=Yii::t('fe', 'Tanggal Lahir')?></th>
                        <th><?=Yii::t('fe', 'Dokter')?></th>
                        <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                        <th><?=Yii::t('fe', 'No Lab')?></th>
                        <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!empty($listLab)) {
                            foreach ($listLab as $key => $value) {
                    ?>
                                <tr>
                                    <td><?= $value['no_antrian'] ?></td>
                                    <td><?= $value['tglmasukpenunjang'] ?></td>
                                    <td><?= $value['no_pendaftaran'] ?></td>
                                    <td><?= $value['no_rekam_medik'] ?></td>
                                    <td><?= $value['nama_pasien'] ?></td>
                                    <td><?= $value['tanggal_lahir'] ?></td>
                                    <td><?= $value['dokter_penunjang'] ?></td>
                                    <td><?= $value['carabayar_nama'] ?></td>
                                    <td><?= $value['no_masukpenunjang'] ?></td>
                                    <td><?= $value['asalrujukan_nama'] ?></td>
                                    <td><?= $value['status_periksa'] ?></td>
                                    <td><?= Html::a(Yii::t('fe', 'Cetak'), ['/laboratorium/integrasi-lis-hasil/cetak?id='.DocoHelpers::encrypt($value['pasienmasukpenunjang_id'])], ['id' => 'btn-cetak-resume-medis-'.$key, 'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-hasil-lab', 'target' => '_blank']); ?></td>
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