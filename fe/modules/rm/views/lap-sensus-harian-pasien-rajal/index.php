<?php

use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;


$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$JK_L = $api[$jk][0]['lookup_kode'];
$JK_P = $api[$jk][1]['lookup_kode'];
$countCarabayar = 1;
$countCarabayar = count($api['cara_bayar']);
$colspanCounter = $countCarabayar * 2;
$coljumlahpasien = ($colspanCounter * 2) + 2;
// dump($columns);die;
?>
<style>
.dataTables_scroll {
    max-height: 99999em !important
}

.border-tab {
        border-right: 1px solid white;
}

.t-b {
    font-weight: bold;
}

.bg-sub-tot {
        background-color: #e9eceb !important;
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
                    <h3 class="panel-title"><b>
                        <?php
                                echo $this->title;
                            ?>
                    </b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', ' Cari'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'cari'
                        ]);
                    ?>
                    <?= Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'),
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'export-excel'
                        ]);
                    ?>
                    <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'reset'
                        ]);
                    ?>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-md-12 filter-form">
                    <form class="advancedFilter" onsubmit="return false;">
                        <div class="row" id="ffBody"></div>
                        <div class="row" id="ffFoot">
                            <div class="col-md-12" style="display: none">
                                <center>
                                    <button type="button" class="btn btn-sm btn-primary btn-xs advancedFilterDo">
                                        <i class="fa fa-search"></i> Cari
                                    </button>&nbsp;
                                    <button type="reset" class="btn btn-sm btn-aqua btn-xs -advancedFilterHide">
                                        <i class="fa fa-repeat"></i> Ulang
                                    </button>
                                </center>
                            </div>
                        </div>
                        <div class="row" id="ffBody">
                            <div class="form-group col-md-3">
                                <label>Tanggal Pendaftaran :</label>
                                <div class="form-group">
                                    <div class='input-group'>
                                        <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoStart' class='form-control startDate' />
                                        <span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span>
                                        <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoFinish' class='form-control endDate' />
                                        <input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'>
                                    </div>
                                    <!-- <div class='input-group' style='width:100%;'>
                                        <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoStart' class='form-control startDate' />
                                        <input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'>
                                    </div> -->
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Jenis pendaftaran :</label>
                                    <div class="form-group">
                                    <?= 
                                      Html::dropDownList('jenis_pendaftaran', '', 
                                            ArrayHelper::map($api['jenis_pendaftaran'], 'lookup_id', 'lookup_name'), 
                                            [
                                                'id' => 'jenis_laporan', 
                                                'class' => 'form-control select2', 
                                                // 'prompt' => \Yii::t('fe', '-- Pilih --'),
                                            ]
                                        )
                                    ?>
                                    </div>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>

                    <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                    <div class="progress" style="margin-left: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span class="label-persentase">0</span>%</div>
                    </div>
                    <span class="help-block label-progress" style="margin-left: 12px;"></span>

                    <hr>
                </div>

                <div class="row content" id="content">
                    <div class="col-md-12 content-data" style="display: none">
                        <table id="lap-sensus-harian-rajal" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th rowspan="4" class="border-tab" width="1"><?=\Yii::t("fe", "NO");?></th>
                                <th rowspan="4" class="text-center border-tab" ><?=\Yii::t("fe", "DEPARTMENT");?></th>
                                <th colspan="<?= $coljumlahpasien ?>" class="text-center border-tab">
                                    <?=\Yii::t("fe", "JUMLAH PASIEN");?>
                                </th>
                            <th colspan="2" class="text-center border-tab">
                                    <?=\Yii::t("fe", "JUMLAH");?>
                            </th>
                                <th rowspan="4" class="text-center border-tab"><?=\Yii::t("fe", "ADOA");?></th>
                                <th rowspan="4" class="text-center border-tab"><?=\Yii::t("fe", "ADOAD");?></th>
                                <th rowspan="4" class="text-center border-tab"><?=\Yii::t("fe", "ADOAPP");?></th>
                            </tr>
                            <tr class="bg-inverse">
                                <th colspan="<?= $colspanCounter ?>" class="text-center border-tab"><?=\Yii::t("fe", "BARU");?></th>
                                <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", "JUMLAH BARU");?></th>
                                <th colspan="<?= $colspanCounter ?>" class="text-center border-tab"><?=\Yii::t("fe", "LAMA");?></th>
                                <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", "JUMLAH LAMA");?></th>
                                <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", "KUNJUNGAN");?></th>
                                <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", "Hp");?></th>
                            </tr>
                            <tr class="bg-inverse">
                            <?php 
                                $counter = 2;
                                for ($i=0; $i < $counter; $i++) { 
                                    foreach($api['cara_bayar']  as $k => $v) { ?>
                                        <th id="<? $k ?>" colspan="2" class="text-center border-tab"><?=$v;?></th>
                            <?php }
                                }
                            ?>

                            </tr>
                            <tr class="bg-inverse">
                            <?php 
                                for ($i=0; $i < $colspanCounter; $i++) { ?>
                                    <th id="<? $i ?>"class="text-center border-tab"><?= $JK_L ?></th>
                                    <th id="<? $i ?>"class="text-center border-tab"><?= $JK_P ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="25"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
const progress = $('.progress');
const progressBar = $('.progress .progress-bar');
const labelProgress = $('.label-progress');
const labelPercent = $('.label-persentase');
const btnDownload = $('.btn-download');
const btnExcel = $('#data-export-excel-serconn')
const content = $('.content');
const contentData = $('#content .content-data');
var table;
var jenis;
var draw = 0;
var randString = '".$randString."'

$(document).ready(function () {
    $('.flex-1').addClass('hidden')
    dateRangeHelper('.startDate', '.endDate', '.targetDate');
    $('#cari').prop('disabled', false);
});

", View::POS_END , "b-index");
$this->registerJs($this->render('index.js'), View::POS_END);
?>