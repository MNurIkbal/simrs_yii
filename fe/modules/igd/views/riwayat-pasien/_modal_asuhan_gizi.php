<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-18 10:08:26
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Riwayat Asuhan Gizi') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No Rekam Medik') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data_pasien['no_rekam_medik'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Tanggal Lahir') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data_pasien['tanggal_lahir'] ?></p>
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
                        <?= Yii::t('fe', 'Nama Pasien') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data_pasien['nama_pasien'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Dokter DPJP') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data_pasien['dok_dpjp'] ?></p>
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
                        <?= Yii::t('fe', 'Jenis Kelamin') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data_pasien['jenis_kelamin'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="table-responsive">
            <table id="tb-asuhan-gizi" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th><?= Yii::t('fe', 'Ruangan') ?></th>
                        <th><?= Yii::t('fe', 'Dietisen') ?></th>
                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($data_gizi)): ?>
                <?php foreach ($data_gizi as $key => $value): ?>
                    <tr>
                        <td><?= $value['created_date'] ?></td>
                        <td><?= $value['ruangan_nama'].' / '.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'] ?></td>
                        <td><?= $value['dietisen_nama'] ?></td>
                        <td><?= $value['aksi'] ?></td>
                    </tr>
                <?php endforeach ?>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>