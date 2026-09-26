<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = DHtml::getTitleMenu();

$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                <?php
                    $defaultBtn = [
                    'search',
                    'reset',
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/lap-batal-uang-muka/show-popup?id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],

                ];

                ?>
                <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
// Global Var
var table;
$(document).ready(function() {
    table = $("#table-informasi").docoTabel({
        filter: true,
        sorting: [[1, "desc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-batal-uang-muka/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Batal")).'",
                data: "tgl_batal",
                name:"tgl_batal",
                searchable : true,
            },
            {
                title: "'.(\Yii::t("fe", "No Pembayaran")).'",
                data: "no_uangmuka",
                name:"no_uangmuka",
                searchable : true,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'",
                data: "tgl_uangmuka",
                name:"tgl_uangmuka",
            },
            {
                title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                data: "no_pendaftaran",
                name:"no_pendaftaran",
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien / No RM")).'",
                data: "nama_pasien",
                name:"nama_pasien",
                render: (data, rowElement, rowData) => {
                    return `
                        <p style="margin-bottom: 2px"> ${rowData.nama_pasien != null ? rowData.nama_pasien : "-"}</p>
                        <p style="margin-bottom: 2px"> ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : "-"} </p>
                    `
                 }
     
            },
            {
                title: "'.(\Yii::t("fe", "Alasan Batal")).'",
                data: "alasan_batal",
                name:"alasan_batal",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Uang Muka")).'",
                data: "jumlah_uangmuka",
                name:"jumlah_uangmuka",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "No Rm")).'",
                data: "no_rekam_medik",
                name:"no_rekam_medik",
                visible: false,
                searchable: false,
            },

        ],
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartPembayaran" value="'.date('d-M-Y').'" class="form-control startDatePembayaran"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishPembayaran" value="'.date('d-M-Y').'" readonly="true" class="form-control endDatePembayaran"/><input type="text" style="display:none" class="targetDatePembayaran" col-index=2></div>\'
            ],
        ], {
             1:0,
             3:1,
             2:2,
             4:3,
             5:4,
        }, 
    true);
    
    dateRangeHelper(".startDate",".endDate",".targetDate");
    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("d-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    dateRangeHelper(".startDatePembayaran",".endDatePembayaran",".targetDatePembayaran");
    $(".daterange-basic").daterangepicker({
        startDatePembayaran: "'.(date("d-M-Y")).'", autoUpdateInput: true,
        endDatePembayaran: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $(".data-excel").on("click", function(e){
        e.preventDefault();

        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/lap-batal-uang-muka/export-excel?"+$.param(table.ajax.params()));
        return false;
    });
});

',View::POS_END,'b-index');
