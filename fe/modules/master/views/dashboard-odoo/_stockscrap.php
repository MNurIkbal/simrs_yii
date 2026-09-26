<?php

/**
 * @Author: Ardi Pratama [ardi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Store Consumption');
?>
<style type="text/css">
    .square-batal {
        background-color: #ffcccc !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-scrap',
                            ]
                        ],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-scrap',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-scrap');?>    
            </div>
            <div class="panel-body">
                <div class="tab-scrap">
                </div>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-scrap" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-scrap']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'Origin')?></th>
                            <th><?=Yii::t('fe', 'Name')?></th>
                            <th><?=Yii::t('fe', 'Qty')?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var tableScrap;
    $(document).ready(function() {
        generateFilter("tab-scrap", "filter-scrap");
        tableScrap = $("#table-scrap").docoTabel({
            filter: true,
            sorting: [[3, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/farmasi-get-data?model=storeconsumption",
            columns: [
                {
                    data: "select_item",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail", 
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Transaksi")).'", 
                    data: "tanggal_transaksi",
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Transaksi")).'",
                    data: "tipe_rekap",
                    render: function(v) {
                        var r;
                        switch (v) {
                            case "pemakaian_obat":
                                r = "Pemakaian Obat/Alkes";
                                break;
                            case "adj_masuk":
                                r = "Adjustment Obat/Alkes Masuk";
                                break;
                            case "adj_keluar":
                                r = "Adjustment Obat/Alkes Keluar";
                                break;
                            case "pemusnahan_obat":
                                r = "Pemusnahan Obat/Alkes";
                                break;
                            case "stokopname_obat":
                                r = "Stock Opname Obat/Alkes";
                                break;
                            case "pemakaian_barang":
                                r = "Pemakaian Barang";
                                break;
                            case "adj_masuk_barang":
                                r = "Adjustment Barang Masuk";
                                break;
                            case "adj_keluar_barang":
                                r = "Adjustment Barang Keluar";
                                break;
                            case "pemusnahan_barang":
                                r = "Pemusnahan Barang";
                                break;
                            case "stokopname_barang":
                                r = "Stock Opname Barang";
                                break;
                            default:
                                r = v;
                                break;
                        }
                        return r;
                    },
                },
                {
                    title: "'.(\Yii::t("fe", "Origin")).'", 
                    data: "origin",
                },
                {
                    title: "'.(\Yii::t("fe", "Name")).'", 
                    data: "name",
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "scrap_qty",
                    searchable:false
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "status_proses", 
                    visible: false
                },
            ],
            drawCallback: function(e) {
                var api = this.api();
                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    var is_sent = rowData.is_sent;
                    var is_sending = rowData.is_sending;
                    var sync_id_api = rowData.sync_id_api;

                    if(rowData.status_proses == "GAGAL"){
                        $(rowNode).css("background-color", "#ffcccc");
                    }else if(rowData.status_proses == "SUKSES"){
                        $(rowNode).css("background-color", "#ffffff");
                    }else if(rowData.status_proses == "DALAM PROSES"){
                        $(rowNode).css("background-color", "#DAF7A6");
                    }else if(rowData.status_proses == "MENUNGGU PROSES"){
                        $(rowNode).css("background-color", "#D1F2EB");
                    }
                }
            },
        });

        $(".dataTables_filter").hide();
        $(".filter-scrap").datatableBootstrapFilter(tableScrap, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeStartScrap" value="'.date('d-M-Y').'" class="form-control"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeFinishScrap" value="'.date('d-M-Y').'" readonly="true" class="form-control"/><input type="text" style="display:none" id="rangeTargetScrap" col-index=3></div>\'
            ],
            [   
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_proses', '', $statusProses, 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Status —'),
                    'name' => "status_proses",
                    ]))).'\'
            ],
            [
                4,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('tipe_rekap', '',
                        [
                            'pemakaian_obat' => 'Pemakaian Obat/Alkes',
                            'adj_masuk' => 'Adjustment Obat/Alkes Masuk',
                            'adj_keluar' => 'Adjustment Obat/Alkes Keluar',
                            'pemusnahan_obat' => 'Pemusnahan Obat/Alkes',
                            'stokopname_obat' => 'Stock Opname Obat/Alkes',
                            'pemakaian_barang' => 'Pemakaian Barang',
                            'adj_masuk_barang' => 'Adjustment Barang Masuk',
                            'adj_keluar_barang' => 'Adjustment Barang Keluar',
                            'pemusnahan_barang' => 'Pemusnahan Barang',
                            'stokopname_barang' => 'Stock Opname Barang',
                        ],
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            3:0,
            5:1,
            6:2,
            8:3
        }, true);
        dateRangeOnDemand("#rangeStartScrap","#rangeFinishScrap","#rangeTargetScrap");
        $(".resend-all-scrap").on("click", function(){
            var rows = tableScrap.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-scrap").on("click", function(){
            var _data = [];
            $(".select_item").each(function(){
                if(this.checked == true) {
                    _data.push($(this).val());
                }
            });

            if(_data.length === 0) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Tidak Ada Data yang dapat di Resend"));
            }
            else {
                var _dataPost = {
                    sync_id_api: _data,
                }
                $.ajax({
                    method: "POST",
                    data: _dataPost,
                    url: baseUrl+"master/dashboard-odoo/farmasi-resend?model=storeconsumption",
                    success: function(res) {
                        tableScrap.draw();
                    },
                    error: function(data) {
                        docoNotification("error", i18next.t("Proses Gagal"), i18next.t(data.response.message));
                    }
                });
            }
        });
    });

', View::POS_END, 'e-index');
?>