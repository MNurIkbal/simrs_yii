<?php

use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

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
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
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
                            'data-table-id' => 'example',
                            'data-options' => 'click',
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-table-id' => 'example',
                            'data-options' => 'click',
                        ]
                    ],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-folder',
                        'attributes' => [
                            'id'   => 'btn-proses',
                            // 'data-target' => Url::to(['proses', 'id' => '']),
                            'data-options' => 'click'
                        ]
                    ],
                    'sync' => [
                        'title' => \Yii::t('fe', 'Sinkronkan Semua Pasien'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'id' => 'btn-sync',
                            'data-options' => 'click',
                            'style' => 'float: right',
                            'class' => 'hidden'
                        ]
                    ],
                    'add' => [
                        'title' => \Yii::t('fe', 'Sinkronkan Pasien'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-pasien-rajal-bpjs/single-sync',
                            'style' => 'float: right',
                            'data-width' => '60%',
                            'class' => 'hidden'
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <div class="row">
                        <div class="col-md-12">
                            <div class='my-legend'>
                                <div class='legend-title'>Keterangan</div>
                                <div class='legend-scale'>
                                    <ul class='legend-labels'>
                                        <li><span style='background:#ffcccc;'>Belum Koreksi</span></li>
                                        <li><span style='background:#ffcc99'>Sudah Koreksi</span></li>
                                        <li><span style='background:#c2e6f8;'>Proses Klaim</span></li>
                                        <li><span style='background:#b5e4b5;'>Final Klaim</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="5%"></th>
                                <th width="1"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span class="label-persentase"></span>%</div>
                </div>
                <span class="help-block label-progress"></span>
            </div>
        </div>
    </div>
</div>

<?php 
$genderPerempuan = DocoConstants::LOOKUP_PEREMPUAN;
$this->registerJs('
const STATUS_VERIFIKASI_BPJS_FNL = "'.DocoConstants::STATUS_VERIFIKASI_BPJS_FNL.'";
const STATUS_VERIFIKASI_BPJS_BLM = "'.DocoConstants::STATUS_VERIFIKASI_BPJS_BLM.'";
const STATUS_VERIFIKASI_BPJS_SDH = "'.DocoConstants::STATUS_VERIFIKASI_BPJS_SDH.'";
const STATUS_VERIFIKASI_BPJS_PRS = "'.DocoConstants::STATUS_VERIFIKASI_BPJS_PRS.'";
');
$this->registerJs($this->render('index.js'), View::POS_END);
?>

