<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-27 09:43:30
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-21 13:38:50
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = "Laporan Pemakaian Obat Alkes";
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
                                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                        </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'print',
                    'excel',
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th>Tanggal Pemakaian</th>
                            <th>Nomor Pemakaian</th>
                            <th>Nama Penginput</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$this->registerCss($this->render('../assets/css/apotek.css'));
$this->registerJs('
var table;
    // Event Reload
$(document).on("click", ".data-reload", function() {
    table.draw();
});

$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"apotek/laporan-pemakaian-obatalkes/get-data-pemakaian",
        columns: [
            {
                title: "",
                data: "detail",
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pemakaian")).'",
                data: "tglpemakaianobat"
            },
            {
                title: "'.(\Yii::t("fe", "No Pemakaian")).'",
                data: "nopemakaian_obat"
            },
            {
                title: "'.(\Yii::t("fe", "Nama Penginput")).'",
                data: "nama_pegawai",
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" readonly placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate" ></div>\'
        ],
        
    ]);
    dateRangeHelper(".startDate", ".endDate", ".targetDate");
});

', VIEW::POS_END, "js-kunings");
?>
