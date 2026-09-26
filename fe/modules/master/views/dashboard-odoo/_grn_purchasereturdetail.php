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

$this->title = Yii::t('fe', 'Purchase');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-purchasereturdetail',
                            ]
                        ],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-purchasereturdetail',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-purchasereturdetail');?>    
            </div>
            <div class="panel-body">
                <div class="tab-purchasereturdetail">
                </div>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-purchasereturdetail" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-purchasereturdetail']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'Sync Id Api')?></th>
                            <th><?=Yii::t('fe', 'Order ID')?></th>
                            <th><?=Yii::t('fe', 'Name')?></th>
                            <th><?=Yii::t('fe', 'Product Qty')?></th>
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
    var tablePurchasereturdetail;
    $(document).ready(function() {
        generateFilter("tab-purchasereturdetail", "filter-purchasereturdetail");
        tablePurchasereturdetail = $("#table-purchasereturdetail").docoTabel({
            filter: true,
            sorting: [[3, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/pengadaan-get-data?model=returnpurchaseorderline",
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
                    data: "date_planned",
                },
                {
                    title: "'.(\Yii::t("fe", "Sync ID API")).'", 
                    data: "sync_id_api", 
                },
                {
                    title: "'.(\Yii::t("fe", "Order ID")).'", 
                    data: "order_id", 
                },
                {
                    title: "'.(\Yii::t("fe", "Name")).'", 
                    data: "name", 
                },
                {
                    title: "'.(\Yii::t("fe", "Product Qty")).'", 
                    data: "product_qty", 
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
        $(".filter-purchasereturdetail").datatableBootstrapFilter(tablePurchasereturdetail, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeStartPurchasereturdetail" value="'.date('d-M-Y').'" class="form-control"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeFinishPurchasereturdetail" value="'.date('d-M-Y').'" readonly="true" class="form-control"/><input type="text" style="display:none" id="rangeTargetPurchasereturdetail" col-index=3></div>\'
            ],
            [   
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_proses', '', $statusProses, 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Status —'),
                    'name' => "status_proses",
                    ]))).'\'
            ],
        ], {
            3:0,
            4:1,
            5:2,
            6:3,
            7:4,
            8:5
        }, true);
        dateRangeOnDemand("#rangeStartPurchasereturdetail","#rangeFinishPurchasereturdetail","#rangeTargetPurchasereturdetail");
        $(".resend-all-purchasereturdetail").on("click", function(){
            var rows = tablePurchasereturdetail.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });
        $("#resend-purchasereturdetail").on("click", function(){
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
                    url: baseUrl+"master/dashboard-odoo/pengadaan-resend?model=returnpurchaseorderline",
                    success: function(res) {
                        tablePurchasereturdetail.draw();
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