<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("ruangan_name")];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi ' . $jenis_penjualan), 'url' => $backUrl];

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => $backUrl
                            ]
                        ],
                        'simpan' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'id' => 'save-retur',
                                'data-options'=>'click',
                            ]
                        ],
                    ]);?>
            </div>
            <div class="panel-body" style="min-height : 417px!important;">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Informasi Penjualan</b></h6>
                        </div>

                        <div class="panel-body">
                           <div class="row">
                                <div class="col-md-12" style="margin-top:2px;">
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'No Resep') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= $no_resep ?></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'Nama') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= $nama_pasien ?></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'No. Rekam Medik') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= $no_rm ?></div>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-top:2px;">
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'Jenis Penjualan') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= $jenis_penjualan ?></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'Tanggal Resep') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= date('d M Y', strtotime($tglpenjualan)) ?></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-4 bold"><?= Yii::t('fe', 'Total Tagihan') ?></div>
                                        <div class="col-md-8 text-left">:&nbsp;<?= $totalhargajual ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Detail Penjualan</b></h6>
                        </div>

                        <div class="panel-body">
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat"
                                    data-source="<?=Url::home();?>apotek/transaksi-resep/list-resep"
                                    data-filter=".form-filter">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= Yii::t('fe', 'R ke') ?></th>
                                            <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                            <!-- <th><?= Yii::t('fe', 'Tanggal Kadaluarsa') ?></th> -->
                                            <th><?= Yii::t('fe', 'Harga Satuan (Rp.)') ?></th>
                                            <th><?= Yii::t('fe', 'Qty') ?></th>
                                            <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                                            <th title="Jenis Obat Racikan tidak bisa diretur" class="text-center"><?= Yii::t('fe', 'Qty Retur') ?><sup>*</sup></th>
                                            <th><?= Yii::t('fe', 'Total Retur (Rp.)') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="list-obat">
                                        <tr>
                                            <td colspan="9" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                                        </tr>
                                    </tbody>
                                    <tfooter>
                                        <tr style="background: #fff">
                                            <td colspan="7" style="text-align:right;"><b>Total Rp.</b></td>
                                            <td id="subtotalItem" class="text-right"></td>
                                        </tr>
                                    </tfooter>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop-lg" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php
    $transaksiRetur = ($transaksiRetur) ? $transaksiRetur : '{}';
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs($this->render('../assets/js/transaksi-retur.js'));

    $this->registerJs('
        var transRetur = ' . $transaksiRetur . ';
        var no_resep = "' . $no_resep . '";
    ',View::POS_END, 'b-index');
?>