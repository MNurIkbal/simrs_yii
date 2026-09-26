<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-01 14:11:41
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
    <h5 class="modal-title"><?= Yii::t('fe', 'Riwayat Reseptur') ?></h5>
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
                        <p class="form-control-static"><?= ': '.@$data_pasien['nama_pasien'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Alamat Pasien') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ': '.@$data_pasien['alamat_pasien'] ?></p>
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
                        <p class="form-control-static"><?= ': '.@$data_pasien['umur'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No RM') ?>
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= ': '.@$data_pasien['no_rekam_medik'] ?></p>
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
                        <th><?=Yii::t('fe', 'Tanggal')?></th>
                        <th><?=Yii::t('fe', 'Nomor Reseptur')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!empty($data_reseptur)) {
                            foreach ($data_reseptur as $key => $value) {
                    ?>
                                <tr>
                                    <td><?= !empty($value['tglreseptur']) ? DocoHelpers::convDateTime($value['tglreseptur'], false, true) : DocoHelpers::convDateTime($value['tglresep'], false, true) ?></td>
                                    <td><?= !empty($value['no_reseptur']) ? $value['no_reseptur'] : $value['no_resep']?></td>
                                    <td><?= $value['status_reseptur'] ?></td>
                                    <td>
                                        <?php //Html::a(Yii::t('fe', 'Cetak'), ['/rajal/pemeriksaan/export-pdf-reseptur?reseptur_id='.DocoHelpers::encrypt($value['reseptur_id']).'&noresep='.DocoHelpers::encrypt($value['noresep'])], ['id' => 'btn-cetak-reseptur-rajal-'.$key, 'class'=>'btn btn-info btn-sm btn-riwayat', 'target' => '_blank']); ?>
                                        <?php
                                        echo Html::button('Cetak',[
                                                'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan',
                                                'data-url' => '/apotek/informasi-reseptur/print-resep-detail?id='.DocoHelpers::encrypt($value['reseptur_id']).'&nomor='.DocoHelpers::encrypt($value['noresep_penjualan']),
                                            ]
                                        );
                                        ?>
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