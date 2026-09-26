<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'title' => Yii::t("fe", "Ulang"),
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'detail' => [
                            'title'=> Yii::t("fe", "Cetak Transaksi"),
                            'attributes' => [
                                'data-target' => $module.'cetak-transaksi?id=',
                                'data-pages' => '_blank',
                            ]
                        ],
                        'excel' => [
                            'title'=> Yii::t("fe", "Excel"),
                            'attributes' => [
                                'data-target' => $module.'export-excel?'
                            ]
                        ],
                        'pdf' => [
                            'title'=> Yii::t("fe", "Print"),
                            'attributes' => [
                                'data-target' => $module.'cetak-pdf?'
                            ]
                        ],
                    ]);?>
                </div>

                <div class="panel-body">
                    <div class="advanced-filter">
                    </div>

                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th><?= Yii::t("fe", "Tanggal Retur") ?></th>
                                <th><?= Yii::t("fe", "No. Retur") ?></th>
                                <th><?= Yii::t("fe", "No. Penerimaan") ?></th>
                                <th><?= Yii::t("fe", "No. Faktur") ?></th>
                                <th><?= Yii::t("fe", "Supplier") ?></th>
                                <th><?= Yii::t("fe", "Nama Barang") ?></th>
                                <th><?= Yii::t("fe", "Qty") ?></th>
                                <th><?= Yii::t("fe", "Alasan") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="9" class="text-center">Data tidak tersedia</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }
            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/informasi-retur-barang/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Retur")).'",
                    data: "tgl_retur"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Retur")).'",
                    data: "no_returpenerimaanbarang"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Penerimaan")).'",
                    data: "no_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Faktur")).'",
                    data: "no_faktur"
                },
                {
                    title: "'.(\Yii::t("fe", "Supplier")).'",
                    data: "supplier_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Barang")).'",
                    data: "barang_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'",
                    data: "qty_input",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Alasan")).'",
                    data: "alasan_retur",
                    searchable: false,
                    orderable: false
                }
            ],
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ]
        ], {
        1:0,
        2:1,
        3:2,
        4:3,
        5:4,
        6:5,
        7:6
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateTerima",".endDateTerima",".targetDateTerima");
    });

'  ,View::POS_END , 'index')
 ?>
