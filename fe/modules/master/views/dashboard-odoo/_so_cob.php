<?php

/**
 * @Author: [Dede][dede.herdiana@sirs.co.id]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Sale Order Cob');
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
                        'reset' => [
                            'attributes' => [
                                'data-parent' => '.filter-saleordercob',
                            ]
                        ],
                        'resend' => [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-saleordercob',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-saleordercob');?>    
            </div>
            <div class="panel-body">
                <div class="tab-filter-saleordercob"></div><br>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-saleordercob" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?>
                                <br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-saleordercob']); ?>
                            </th>
                            <th><?=Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Sync ID API')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'Admission ID')?></th>
                            <th><?=Yii::t('fe', 'Nomor Cob')?></th>
                            <th><?=Yii::t('fe', 'Payer ID')?></th>
                            <th><?=Yii::t('fe', 'Status Proses')?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var tablePatient;

    $(document).ready(function() {
        generateFilter("tab-filter-saleordercob", "filter-saleordercob");

        tablePatient = $("#table-saleordercob").docoTabel({
            autoWidth: false,
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/get-data?type=saleordercob",
            columns: [
                {
                    data: "select_item",
                    searchable: false,
                    orderable: false,
                    className: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Sync ID API")).'",
                    data: "sync_id_api",
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Cob")).'", 
                    data: "cob_date",
                },
                {
                    title: "'.(\Yii::t("fe", "Billing ID")).'", 
                    data: "billing_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Admission ID")).'", 
                    data: "admission_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Cob")).'", 
                    data: "cob_no"
                },
                {
                    title: "'.(\Yii::t("fe", "Payer ID")).'", 
                    data: "payer_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Keterangan")).'", 
                    data: "keterangan"
                },
                {
                    title: "'.(\Yii::t("fe", "Status Proses")).'", 
                    data: "status_proses",
                    orderable: false,
                    visible: false
                }
            ],        
            rowCallback: function( row, data ) {
                var status_proses = data.status_proses;
                if (status_proses.toLowerCase() == "gagal") {
                    $(row).css("background-color", "#D24D57");
                    $(row).find(".select_item").prop("disabled", false);
                } else {
                    $(row).find(".select_item").prop("disabled", true);
                    if (status_proses == "SUKSES") {
                        $(row).css("background-color", "#ffffff");
                        $(row).find(".select_item").prop("disabled", false);
                    } else if(status_proses == "MENUNGGU PROSES") {
                        $(row).css("background-color", "#D1F2EB");
                    } else {
                        $(row).css("background-color", "#DAF7A6");
                    }
                }
              }
        });

        $(".dataTables_filter").hide();

        $(".filter-saleordercob").datatableBootstrapFilter(tablePatient, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartcob" value="'.date('d-M-Y').'" class="form-control startDatecob"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishcob" value="'.date('d-M-Y').'" readonly="true" class="form-control endDatecob"/><input type="text" style="display:none" class="targetDatecob" col-index=3></div>\'
            ],
            [   
                9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_proses', '', $statusProses, 
                    [
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '— Pilih Status —'),
                        'name' => "status_proses",
                    ]
                ))).'\'
            ],
        ], {
            3:0,
            2:1,
            6:2,
            9:3,
        }, true);
        dateRangeHelper(".startDatecob",".endDatecob",".targetDatecob");
        $(".resend-all-cob").on("click", function(){
            var rows = tablecob.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $(".resend-all-saleordercob").on("click", function(){
            var rows = tablePatient.rows({"search" : "applied"}).nodes();

            if ($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-saleordercob").on("click", function(){
            var _data = [];

            $(".select_item").each(function(){
                if (this.checked == true) {
                    _data.push($(this).val());
                }
            });

            if (_data.length === 0) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Tidak Ada Data yang dapat di Resend"));
            } else {
                var _dataPost = {
                    sync_id_api: _data,
                }

                $.ajax({
                    method: "POST",
                    data: _dataPost,
                    url: baseUrl+"master/dashboard-odoo/resend?model=saleordercob",
                    success: function(res) {
                        tablePatient.draw();
                        docoNotification("success", i18next.t("Proses Berhasil"), i18next.t(data.response.message));
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