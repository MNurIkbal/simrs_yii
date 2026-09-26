<?php

/**
* @author Ramdhan Nurrachman
* @edited Yaya
* Pemindahan Service Ke Gudang
* 24-Maret-2018
**/

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = \Yii::t('fe', 'Informasi Stok Barang');
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
.select2-container .select2-selection--single {
    height: 35px !important;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'resetfilter'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Muat ulang'),
                        'icon' => 'fa fa-refresh',
                        'method' => 'not exist',
                        'attributes' => [
                            'data-options'=>'click',
                            'class' => 'data-resetfilter',
                        ]
                    ],
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'gudang/inf-stok-barang/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ]);?>

                </div>
            </div>

            <div class="panel-body">
                <!-- <div class="col-md-12 filter-form"></div> -->
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="70">No</th>
                            <th><?=\Yii::t("fe", "Nama ruangan");?></th>
                            <th><?=\Yii::t("fe", "Nama barang");?></th>
                            <th><?=\Yii::t("fe", "Kode barang");?></th>
                            <th><?=\Yii::t("fe", "Kelompok barang");?></th>
                            <th><?=\Yii::t("fe", "Qty dipesan");?></th>
                            <th><?=\Yii::t("fe", "Qty tersedia");?></th>
                            <th><?=\Yii::t("fe", "Stok total");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-stok-barang/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    sortable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama ruangan")).'",
                    data: "ruangan_nama",
                    name : "ruangan_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama barang")).'",
                    data: "barang_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Instalasi")).'",
                    data: "instalasi_nama",
                    name: "instalasi_id",
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode barang")).'",
                    data: "barang_kode"
                },
                {
                    title: "'.(\Yii::t("fe", "Kelompok barang")).'",
                    data: "kelompokbarang_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty dipesan")).'",
                    data: "qty_dipesan",
                    searchable: false,
                    "class":"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty tersedia")).'",
                    data: "qty_tersedia",
                    searchable: false,
                    "class":"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Stok total")).'",
                    data: "qty_stok",
                    searchable: false,
                    "class":"text-right"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    3,
                    \''.(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\"',
                        Html::dropDownList('instalasi_nama', '',
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
                            [
                                'class' => 'form-control select2 selectInstalasi',
                                'id'=>'filter_instalasi',
                                'prompt' => \Yii::t('fe', 'Semua'),
                                'disabled' => $visibility
                            ]
                        )
                    ))).'\'
                ],
                [
                    1,
                        \''.(preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\"',
                            DepDrop::widget([
                                'name' => 'ruangan_nama',
                                'data'=> $ruangan_aktif,
                                'options' => [
                                    'disabled' => $visibility,
                                    'class' => 'form-control select2 selectRuangan'
                                ],
                                'pluginOptions' => [
                                   'depends'  => ['filter_instalasi'],
                                   'placeholder' => 'Semua',
                                   'url' => Url::to(['/gudang/inf-stok-barang/get-ruangan'])
                                ]
                            ])
                            )
                        )
                        ).'\'

                ],
            ], {
                3:0,
                4:3
            }, true
        );
        $(".selectInstalasi").val("' . $instalasi_aktif . '").trigger("change");
        $(".selectRuangan").select2({
            placeholder: "Semua",
        });
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });
    $(document).on("click", ".data-resetfilter", function(){
        let tgl = new Date()
        $(".advancedFilter [type=reset]").click();

        $(".selectInstalasi").val("'.Yii::$app->docoVars->workspace("instalasi_id").'").trigger("change");
        if ($(".selectRuangan").find("option[value='.Yii::$app->docoVars->workspace('ruangan_id').']").length) {
            $(".selectRuangan").val('.Yii::$app->docoVars->workspace('ruangan_id').').trigger("change");
        } else {
            var newOption = new Option("'.Yii::$app->docoVars->workspace('ruangan_name').'", "'.Yii::$app->docoVars->workspace('ruangan_id').'", false, false);
            $(".selectRuangan").append(newOption).trigger("change");
        }
        $(".advancedFilterDo").click();
        $(".obatalkes_namalain").val("").trigger("change");
        $(".pickadate").val("'.date("Y-m-d").'")
    });

', View::POS_END, 'b-index');
?>
