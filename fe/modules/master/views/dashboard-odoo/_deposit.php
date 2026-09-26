<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Deposit');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
                            'attributes'=>[
                                'data-parent'=>'.filter-deposit',
                            ]
                        ],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-deposit',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-deposit');?>    
            </div>
            <div class="panel-body">
                <div class="tab-deposit">
                </div>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-deposit" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-dp']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'Tipe Transaksi')?></th>
                            <th><?=Yii::t('fe', 'No Transaksi')?></th>
                            <th><?=Yii::t('fe', 'No Admisi')?></th>
                            <th><?=Yii::t('fe', 'Jumlah')?></th>
                            <th><?=Yii::t('fe', 'User')?></th>
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
    var tableDeposit;
    $(function() {
        generateFilter("tab-deposit", "filter-deposit");
        tableDeposit = $("#table-deposit").docoTabel({
            filter: true,
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/get-data?type=pasiendeposit",
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
                    data: "trans_date",
                },
                {
                    title: "'.(\Yii::t("fe", "Tipe Transaksi")).'", 
                    data: "trans_type", 
                },
                {
                    title: "'.(\Yii::t("fe", "No Transaksi")).'", 
                    data: "trans_no", 
                },
                {
                    title: "'.(\Yii::t("fe", "No Admisi")).'", 
                    data: "admission_no", 
                },
                {
                    title: "'.(\Yii::t("fe", "Nominal")).'", 
                    data: "amount", 
                    searchable: false,
                    className: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "User")).'", 
                    data: "user_name", 
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
                    var status_proses = rowData.status_proses;
                    if(status_proses == "GAGAL") {
                        $(rowNode).css("background-color", "#D24D57");
                        $(".select_item").prop("disabled", false);
                    }
                    else {
                        if(status_proses == "SUKSES") {
                            $(rowNode).css("background-color", "#ffffff");
                        }
                        else if(status_proses == "MENUNGGU PROSES") {
                            $(rowNode).css("background-color", "#D1F2EB");
                        }
                        else {
                            $(rowNode).css("background-color", "#DAF7A6");
                        }
                        $(".select_item").prop("disabled", true);
                    }
                }
            },
        });

        $(".dataTables_filter").hide();
        $(".filter-deposit").datatableBootstrapFilter(tableDeposit, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartDp" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishDp" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=3></div>\'
            ],
            [   
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('trans_type', '', $transTypeUm, 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Tipe Transaksi —'),
                    'name' => "trans_type",
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

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".resend-all-dp").on("click", function(){
            var rows = tableDeposit.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-deposit").on("click", function(){
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
                    url: baseUrl+"master/dashboard-odoo/resend?model=pasiendeposit",
                    success: function(res) {
                        tableDeposit.draw();
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