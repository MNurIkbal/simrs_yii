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
                    'ubah' => [
                        'title' => Yii::t('fe', "Detail"),
                        'icon' => "fa fa-eye",
                        'attributes' => [
                            "data-target" => $module.'edit-penerimaan-form?id=',
                            "id" => "btn-edit-penerimaan"
                        ]
                    ],
                    'retur' => [
                        'title' => Yii::t('fe', "Retur"),
                        'icon' => "fa fa-undo",
                        'attributes' => [
                            "data-target" => $module."retur-penerimaan?id=",
                            "id" => "btn-retur-penerimaan",
                        ]
                    ],
                    'reset'=> [
                        'title' => "Ulang",
                        'attributes'=>[
                            'data-parent'=>'.filter-form'
                        ]
                    ],
                    'pdf' => [
                        'title'=> "Print",
                        'attributes' => [
                            'data-target' => $module.'export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'title'=> "Excel",
                        'attributes' => [
                            'data-target' => $module.'export-excel?'
                        ]
                    ],
                    // 'verifikasi' => [
                    //     'title' => Yii::t('fe', "Verifikasi Transaksi"),
                    //     'icon' => "fa fa-check",
                    //     'attributes' => [
                    //         'data-target' => $module.'verifikasi-penerimaan?id=',
                    //         'data-options' => "delete",
                    //         'id' => "btn-verifikasi",
                    //         'data-confirm-message' => "Anda yakin untuk memverifikasi transaksi ini ?",
                    //         'skip-success-notif' => "Data berhasil di verifikasi"
                    //     ]
                    // ],
                    // 'batal' => [
                    //     'title' => Yii::t('fe', "Batalkan"),
                    //     'icon' => "fa fa-ban",
                    //     'attributes' => [
                    //         'data-target' => $module.'batal-penerimaan?id=',
                    //         'data-options' => "delete",
                    //         'id' => "btn-batal-penerimaan",
                    //         'data-confirm-message' => "Anda yakin untuk membatalkan transaksi ini ?",
                    //         'skip-success-notif' => "Data berhasil di batalkan"
                    //     ]
                    // ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty Diterima");?></th>
                            <th><?=\Yii::t("fe", "Total Harga");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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

    $(document).ready(function() {
        // Generate Table
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
            ajax: baseUrl+"gudang/informasi-penerimaan-obat/get-data",
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
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'",
                    data: "tgl_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'",
                    data: "no_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor PO")).'",
                    data: "nomor_po"
                },
                {
                    title: "'.(\Yii::t("fe", "Supplier")).'",
                    data: "supplier_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat")).'",
                    data: "obatalkes_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Diterima")).'",
                    data: "qty_diterima",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Total Harga")).'",
                    data: "harga_total",
                    searchable: false,
                    className : "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    data: "status_invoice",
                    name: "status_invoice"
                }
            ],
            createdRow: function (row, data, index){
                if(data.is_verifikasi == 0){
                    // $(row).css("background-color" ,"#EED8ED");
                }else if(data.is_verifikasi == 1){
                    $(row).css("background-color" ,"");
                }else if(data.is_verifikasi == 2){
                    $(row).css("background-color" ,"");
                }else{
                    $(row).css("background-color" ,"");
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                9,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_verifikasi', '',
                        [0 => "Belum Diverifikasi" , 1 => "Sudah Diverifikasi", 2 => "Dibatalkan"],
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
        ], {
        2:0,
        3:1,
        4:2,
        5:3,
        6:4,
        9:5
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateTerima",".endDateTerima",".targetDateTerima");

    });

    $(document).on("click", "#example tr", function(){
        var tbl = table.row(".selected").data();
        if(tbl)
        {
            if(tbl.status_invoice == "Sudah Diverifikasi")
            {
                $("#btn-verifikasi").prop("disabled", true);
                $("#btn-batal-penerimaan").prop("disabled", true);
                $("#btn-retur-penerimaan").prop("disabled", false);
            }
            else if(tbl.status_invoice == "Dibatalkan")
            {
                $("#btn-verifikasi").prop("disabled", true);
                $("#btn-batal-penerimaan").prop("disabled", true);
                $("#btn-retur-penerimaan").prop("disabled", true);
            }
            else{
                $("#btn-verifikasi").prop("disabled", false);
                $("#btn-batal-penerimaan").prop("disabled", false);
                $("#btn-retur-penerimaan").prop("disabled", true);
            }
        }else{
            $("#btn-verifikasi").prop("disabled", false);
            $("#btn-edit-penerimaan").prop("disabled", false);
            $("#btn-retur-penerimaan").prop("disabled", false);
            $("#btn-batal-penerimaan").prop("disabled", false);
        }
    });


', View::POS_END, 'b-index');
?>
