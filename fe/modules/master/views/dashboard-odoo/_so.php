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

$this->title = Yii::t('fe', 'Patient');
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
                                'data-parent' => '.filter-saleorder',
                            ]
                        ],
                        'resend' => [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-saleorder',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-saleorder');?>    
            </div>
            <div class="panel-body">
                <div class="tab-filter-saleorder"></div><br>
                <?= Yii::$app->controller->renderPartial('_warna'); ?>
                <table id="table-saleorder" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?>
                                <br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-saleorder']); ?>
                            </th>
                            <th><?=Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Sync ID API')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No. Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Nomor Billing')?></th>
                            <th><?=Yii::t('fe', 'Kode Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Nama Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Event')?></th>
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
        generateFilter("tab-filter-saleorder", "filter-saleorder");

        tablePatient = $("#table-saleorder").docoTabel({
            autoWidth: false,
            filter: true,
            sorting: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-odoo/get-data-admission?type=saleOrder",
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
                    orderable: false,
                    visible: false,
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", 
                    data: "date_order",
                },
                {
                    title: "'.(\Yii::t("fe", "No. Pendaftaran")).'", 
                    data: "name"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Billing")).'", 
                    data: "billno"
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Penjamin")).'", 
                    data: "payer_code",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Penjamin")).'", 
                    data: "payer_type",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "status_proses",
                    orderable: false,
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Event")).'", 
                    data: "keterangan",
                    orderable: false,
                    visible: true
                }
            ],
            drawCallback: function(e) {
                var api = this.api();

                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    var status_proses = rowData.status_proses;

                    if (status_proses == "GAGAL") {
                        $(rowNode).css("background-color", "#D24D57");
                        $(".select_item").prop("disabled", false);
                    } else {
                        if (status_proses == "SUKSES") {
                            $(rowNode).css("background-color", "#ffffff");
                        } else if(status_proses == "MENUNGGU PROSES") {
                            $(rowNode).css("background-color", "#D1F2EB");
                        } else {
                            $(rowNode).css("background-color", "#DAF7A6");
                        }

                        $(".select_item").prop("disabled", true);
                    }
                }
            },
        });

        $(".dataTables_filter").hide();

        $(".filter-saleorder").datatableBootstrapFilter(tablePatient, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartRegistrasi" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishRegistrasi" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDateRegistrasi" col-index=3></div>\'
            ],
            [   
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_proses', '', $statusProses, 
                    [
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '— Pilih Status —'),
                        'name' => "status_proses",
                    ]
                ))).'\'
            ],
        ], {
            3:0,
            4:1,
            5:2,
            6:3,
            7:4,
            8:5,
        }, true);

        dateRangeHelper(".startDate",".endDate",".targetDateRegistrasi");

        $(".resend-all-saleorder").on("click", function(){
            var rows = tablePatient.rows({"search" : "applied"}).nodes();

            if ($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-saleorder").on("click", function(){
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
                    id: _data,
                }

                $.ajax({
                    method: "POST",
                    data: _dataPost,
                    url: baseUrl+"master/dashboard-odoo/resend-admission?model=saleOrder",
                    success: function(res) {
                        tablePatient.draw();
                    },
                    error: function(data) {
                        docoNotification("error", i18next.t("Proses Gagal"), i18next.t(data.response.message));
                    }
                });
            }
        });
    });
', View::POS_END, '_saleOrder');
?>
