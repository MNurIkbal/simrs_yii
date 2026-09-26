<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-23 16:58:41
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
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Riwayat Order Obat');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title) ?></b></h3>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row col-md-12">
                    <div class="table-responsive">
                        <table id="tabel-resep" class="table table-striped table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?=Yii::t('fe', 'Tanggal') ?></th>
                                    <th><?=Yii::t('fe', 'No Resep') ?></th>
                                    <th><?=Yii::t('fe', 'Status') ?></th>
                                    <th><?=Yii::t('fe', 'Aksi') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $btn_aksi = '';
                                    $rowNum = 1;
                                    foreach ($data_reseptur as $value) {
                                ?>
                                    <tr>
                                        <td><?= $value['tglreseptur'] ?></td>
                                        <td><?= $value['noresep'] ?></td>
                                        <td><?= $value['status_reseptur'] ?></td>
                                        <td>
                                            <?php
                                                echo Html::button('Review', [
                                                        'class' => 'btn btn-info btn-sm',
                                                        'id' => 'btn-review',
                                                        'data-noresep' => $value['noresep'],
                                                        'style' => 'margin-left:5px; margin-right:5px',
                                                    ]
                                                );

                                                if ($instalasi_id == 3) {
                                                    echo Html::a('Cetak',
                                                        [
                                                            '/ranap/pemeriksaan-rawat-inap/cetak-reseptur?id='.$value['pendaftaran_id'].'&instruksi_id='.$value['instruksi_id']
                                                        ], [
                                                            'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-resep',
                                                            'target' => '_blank',
                                                            'data-noresep' => $value['noresep'],
                                                            'style' => 'margin-left:5px; margin-right:5px',
                                                        ]
                                                    );
                                                } else {
                                                    echo Html::a('Cetak',
                                                        [
                                                            '/igd/riwayat-pasien/cetak-resep?no_resep='.$value['noresep']
                                                        ], [
                                                            'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-resep',
                                                            'target' => '_blank',
                                                            'data-noresep' => $value['noresep'],
                                                            'style' => 'margin-left:5px; margin-right:5px',
                                                        ]
                                                    );
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                        $rowNum++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="table-responsive">
                        <table id="tabel-resep" class="table table-striped table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?=Yii::t('fe', 'Tanggal') ?></th>
                                    <th><?=Yii::t('fe', 'R/ Nama Obat & Bentuk Sediaan') ?></th>
                                    <th><?=Yii::t('fe', 'Jumlah') ?></th>
                                    <th><?=Yii::t('fe', 'Signa/Rute Pemberian Obat') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>