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

$this->title = Yii::t('fe', 'Stock Picking');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-picking',
                            ]
                        ],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-picking',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-picking');?>    
            </div>
            <div class="panel-body">
                <div class="tab-picking">
                </div>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-picking" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-picking']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'Sync Id Api')?></th>
                            <th><?=Yii::t('fe', 'Name')?></th>
                            <th><?=Yii::t('fe', 'Partner')?></th>
                            <th><?=Yii::t('fe', 'Location')?></th>
                            <th><?=Yii::t('fe', 'Tipe')?></th>
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
    var tableObat;
    $(document).ready(function() {
        generateFilter("tab-picking", "filter-picking");
        tablePicking = $("#table-picking").docoTabel({
            filter: true,
            sorting: [[3, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/farmasi-get-data?model=stockout",
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
                    title: "'.(\Yii::t("fe", "Sync ID API")).'", 
                    data: "sync_id_api", 
                },
                {
                    title: "'.(\Yii::t("fe", "Name")).'", 
                    data: "name", 
                },
                {
                    title: "'.(\Yii::t("fe", "Partner")).'", 
                    data: "partner_name", 
                },
                {
                    title: "'.(\Yii::t("fe", "Location")).'", 
                    data: "location_name", 
                    className: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Tipe")).'", 
                    name: "tipe_rekap",
                    data: "tipe_rekap_", 
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
        $(".filter-picking").datatableBootstrapFilter(tablePicking, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeStartPicking" value="'.date('d-M-Y').'" class="form-control"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeFinishPicking" value="'.date('d-M-Y').'" readonly="true" class="form-control"/><input type="text" style="display:none" id="rangeTargetPicking" col-index=3></div>\'
            ],
            [   
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('tipe', '', $tipe, 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Tipe —'),
                    'name' => "tipe",
                    ]))).'\'
            ],
            [   
                9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_proses', '', $statusProses, 
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
            8:4,
            9:5,
        }, true);
        dateRangeOnDemand("#rangeStartPicking","#rangeFinishPicking","#rangeTargetPicking");
        $(".resend-all-picking").on("click", function(){
            var rows = tablePicking.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });
        $("#resend-picking").on("click", function(){
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
                    url: baseUrl+"master/dashboard-odoo/farmasi-resend?model=stockout",
                    success: function(res) {
                        tablePicking.draw();
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