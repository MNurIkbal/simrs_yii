<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    .square-sukses {
        height: 30px;
        width: 120px;
        background-color: #26A65B;
        color: #ffffff;
        padding: 5px 0 5px 10px;
        margin-right: 20px;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: #D24D57;
        color: #ffffff;
        padding: 5px 0 5px 10px;
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
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'edit' => [
                        'title' => 'Penerimaan',
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-url' => $module . 'detail?id=',
                            'data-options' => 'click',
                            'data-conditions' => 'type',
                            'id' => 'btn-penerimaan',
                            'type' => 'button'
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => $module . 'export-pdf-barang?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => $module . 'export-excel-barang?'
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#F6C1C1;'></span>Belum Verifikasi</li>
                                    <li><span style='background:#fff;'></span>Bisa melakukan penerimaan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?= \Yii::t("fe", "Tanggal PO"); ?></th>
                            <th><?= \Yii::t("fe", "Nomor PO"); ?></th>
                            <th><?= \Yii::t("fe", "Total Harga PO"); ?></th>
                            <th><?= \Yii::t("fe", "Supplier"); ?></th>
                            <th><?= \Yii::t("fe", "Rencana Terima"); ?></th>
                            <th><?= \Yii::t("fe", "Status Penerimaan"); ?></th>
                            <th><?= \Yii::t("fe", "Status Verifikasi"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    var _urlPenerimaan = "' . $module . 'detail?id=' . '";
    var _type = "' . $type . '"
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/informasi-po/get-data-barang",
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
                    title: "' . (\Yii::t("fe", "Tanggal PO")) . '",
                    data: "tanggal_po"
                },
                {
                    title: "' . (\Yii::t("fe", "Nomor PO")) .'",
                    data: "no_transaksi"
                },
                {
                    title: "' . (\Yii::t("fe", "Total Harga PO")) . '",
                    data: "total_harga_po",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Supplier")) . '",
                    data: "supplier_nama"
                },
                {
                    title: "' . (\Yii::t("fe", "Rencana Terima")) . '",
                    data: "tgl_rencanaterima",
                    searchable: false
                },
                {
                    title: "' . (\Yii::t("fe", "Status Penerimaan")) . '",
                    data: "stat_penerimaan",
                    name : "status_penerimaan"
                },
                {
                    title: "' . (\Yii::t("fe", "Status Verifikasi")) . '",
                    data: "status_verifikasi",
                    name : "is_verifikasi"
                },
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData.is_verifikasi) {
                    $(nRow).css("background", "#F6C1C1");
                } else {
                    $(nRow).css("background-color", "fff");
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="' . date('d-M-Y') . '" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="' . date('d-M-Y') . '" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                7,
                \'<div class=\"form-group\">' . (preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('stat_penerimaan', '',
                        $request['status_penerimaan'],
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )) . '</div>\'
            ],
            [
                8,
                \'<div class=\"form-group\">' . (preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('is_verifikasi', '',
                        [0 => "Bisa melakukan penerimaan", 1 => "Belum Diverifikasi"],
                        [
                            'id' => 'filter_status_verif',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )) . '</div>\'
            ],
        ], {
        2:0,
        5:1,
        3:2,
        7:3,
        8:4,
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateTerima",".endDateTerima",".targetDateTerima");

        $("#filter_supplier").select2({
            minimumInputLength: 3,
            ajax : {
                url: "/gudang/informasi-po/search-supplier",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });
    });

    $(document).on("click", "#example tbody tr", function () {
        var _data = table.row(".selected").data() ? table.row(".selected").data() : {};
        if (_data.is_verifikasi == true) {
            stat_verif = false;
        }else{
            stat_verif = true;
        }
    });

    $(document).on("click", "#btn-penerimaan", function(e){
        e.preventDefault();
        var penerimaan_id = "";

        var url = $(this).attr("data-url");
        var _data = table.row(".selected").data() ? table.row(".selected").data() : {};
        var url_data = url + _data.primary +"&type="+_data.type;
        var no_po = _data.no_transaksi;

        if(stat_verif)
        {
            window.location = url_data;
        }else
        {
            $.ajax({
                url : "' . $module . 'get-penerimaan-terakhir?no_po="+no_po+"&type="+_type,
                success : function (data){
                    var id_penerimaan = data.response.penerimaan_id;
                    if(id_penerimaan == null){
                        var url_penerimaan = "/gudang/informasi-penerimaan-po-barang/";
                    }else{
                        var url_penerimaan = "/gudang/informasi-penerimaan-po-barang/detail?id="+id_penerimaan;
                    }
                    (new PNotify({
                        title: "Transaksi belum terverifikasi",
                        text: "Transaksi sebelumnya belum terverifikasi, silahkan lakukan verifikasi pada informasi penerimaan",
                        addclass: "alert alert-info alert-arrow-right alert-styled-right",
                        type: "info",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: \'Verifikasi Penerimaan\',
                                    addClass: \'btn btn-xs btn-info\',
                                },
                                {
                                    text: \'Tutup\',
                                    addClass: \'btn btn-xs btn-danger\',
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on(\'pnotify.confirm\', function() {
                        window.open(url_penerimaan, "_blank");
                    });
                }
            });
        }
    });
', View::POS_END, 'b-index');
?>
