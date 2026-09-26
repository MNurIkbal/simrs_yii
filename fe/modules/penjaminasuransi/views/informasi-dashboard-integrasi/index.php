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
                            'data-table-id' => 'tableDataIntegrasi',
                            'data-options' => 'click',
                            'id' => 'search-button'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-table-id' => 'tableDataIntegrasi',
                            'data-options' => 'click',
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-dashboard-integrasi/export-excel?'
                        ]
                    ],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-folder',
                        'attributes' => [
                            'id' => 'btn-proses',
                        ],
                    ],
                    'cetak-pendaftaran' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Cetak Pendaftaran'),
                        'attributes' => [
                            'id' => 'btn-cetak-pendaftaran',
                            'data-options' => 'click',
                            'disabled' => true
                        ],
                    ],
                    'cetak-pengesahan' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Cetak Pengesahan'),
                        'attributes' => [
                            'id' => 'btn-cetak-pengesahan',
                            'data-options' => 'click',
                            'disabled' => true
                        ],
                    ],
                    'cetak-jaminan' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Cetak Jaminan'),
                        'attributes' => [
                            'id' => 'btn-cetak-jaminan',
                            'data-options' => 'click',
                            'disabled' => true
                        ],
                    ],
                    'cob-pasien' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'COB Pasien'),
                        'attributes' => [
                            'id' => 'btn-cob-pasien',
                            'data-options' => 'click',
                        ],
                    ],
                    'cek-pasien' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Cek Pasien'),
                        'attributes' => [
                            'id' => 'btn-cek-pasien',
                            'data-width'  => '50%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-dashboard-integrasi/cek-pasien',
                            'disabled' => false
                        ],
                    ],
                    'edit-pasien' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Edit Data Pasien'),
                        'attributes' => [
                            'id' => 'btn-edit-pasien',
                            'data-options' => 'click',
                            'disabled' => true
                        ],
                    ],
                ], '#tableDataIntegrasi');?>
            </div>
            <div class="panel-body">
            <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#FFD44D;'>Pasien COB</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <table id="tableDataIntegrasi" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Info Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Alamat"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis Kelamin"); ?></th>
                            <th><?= \Yii::t("fe", "Poliklinik"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis Kasus Penyakit"); ?></th>
                            <th><?= \Yii::t("fe", "Kelas Pelayanan"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter DPJP"); ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar"); ?></th>
                            <th><?= \Yii::t("fe", "Penjamin / Asuransi"); ?></th>
                            <th><?= \Yii::t("fe", "Status Periksa"); ?></th>
                            <th><?= \Yii::t("fe", "No Klaim"); ?></th>
                            <th><?= \Yii::t("fe", "Petugas"); ?></th>
                            <th><?= \Yii::t("fe", "Status Klaim"); ?></th>
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
    $this->registerJs('
        var optionStatus = '.json_encode($statusPeriksa).';
        var statusPeriksa = [];
        $.each(optionStatus, function (index, value) {
            statusPeriksa.push({
                id: index,
                text: value,
            });
        });

    ', View::POS_END, "b-index");
    $this->registerJs($this->render('index.js'), View::POS_END);
?>