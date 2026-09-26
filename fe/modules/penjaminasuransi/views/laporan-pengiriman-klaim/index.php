<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    .square-sukses {
        height: 30px;
        width: 120px;
        background-color: #26A65B;
        color: #ffffff;
        padding: 5px 0 5px 10px;
        margin-right: 20px;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: #D24D57;
        color: #ffffff;
        padding: 5px 0 5px 10px;
    }

    .belum-koreksi {
        background-color: #ffcccc !important;
        color: #484646;
    }

    .sudah-koreksi {
        background-color: #ffcc99 !important;
        color: #484646;
    }

    .proses-klaim {
        background-color: #c2e6f8 !important;
        color: #484646;
    }

    .final-klaim {
        background-color: #b5e4b5 !important;
        color: #484646;
    }

    .belum-ada-sep {
        background-color: #ff7f00 !important;
        color: #484646;
    }
</style>

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                            'data-table-id' => 'tableLaporan',
                            'data-options' => 'click',
                            'id' => 'search-button'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/laporan-pengiriman-klaim/export-excel?',
                        ],
                    ],
                ], '#tableLaporan'); ?>
            </div>
            <div class="mb-2">
                <div class="panel-body">
                    <div class="panel panel-default" style="background-color: #dae8fc; font-size: 17px; margin: 10px; padding: 20px; border-radius: 10px; border: 1px solid #b3b3b3">
                        <div>
                            <p style="color: black;">Laporan  ini menampilkan daftar klaim yang telah dikirim ke E-Klaim dan memiliki status final klaim berdasarkan tanggal pendaftaran atau tanggal pulang yang dipilih. Gunakan filter pencarian untuk melihat detail klaim, termasuk informasi tarif dan status klaim.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div style="margin-top: 30px;">
                    <table style="font-size: 20px; font-weight: bold">
                        <tr>
                            <th>Total Tagihan RS</th>
                            <th><span style="margin: 10px;">:</span></th>
                            <th>Rp. <span id="total_tagihan_rs">0</span></th>
                        </tr>
                        <tr>
                            <th>Total Tarif Klaim</th>
                            <th><span style="margin: 10px;">:</span></th>
                            <th>Rp. <span id="total_klaim">0</span></th>
                        </tr>
                    </table>
                </div>
                <table id="tableLaporan" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "No. SEP"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Klaim"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien / No. RM"); ?></th>
                            <th><?= \Yii::t("fe", "INACBG Group"); ?></th>
                            <th><?= \Yii::t("fe", "Special Group"); ?></th>
                            <th><?= \Yii::t("fe", "Tarif Klaim"); ?></th>
                            <th><?= \Yii::t("fe", "Tagihan RS"); ?></th>
                            <th><?= \Yii::t("fe", "Status Data Klaim"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="12"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div id="modal_progress" class="modal fade" style="z-index:1065;" data-backdrop="static">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title">Sinkronisasi Data</h5>
                            </div>
                            <hr>
                            <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                            <div class="modal-body">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span class="label-persentase"></span>%
                                    </div>
                                </div>
                                <span class="help-block label-progress"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('index.js'), View::POS_END);
?>