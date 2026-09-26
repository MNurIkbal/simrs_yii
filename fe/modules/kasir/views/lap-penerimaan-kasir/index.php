<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    
    tr.odd td:first-child,
    tr.even td:first-child {
        padding-left: 4em;
    }
    .dtrg-level-0 {
        background: #E8F8F5;
        font-weight: bold;
    }
    .dtrg-level-1 {
        background: #ddd;
    }
    .dtrg-end.dtrg-level-0 {
        display: none;
        /*font-weight: bold;*/
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
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar(['search','reset','excel']);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-condensed" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <!-- <th width="1">No</th> -->
                            <th><?=\Yii::t("fe", "Kasir");?></th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "No Kwitansi");?></th>
                            <th><?=\Yii::t("fe", "No Registrasi");?></th>
                            <th><?=\Yii::t("fe", "Info Pasien");?></th>
                            <th><?=\Yii::t("fe", "Rupiah");?></th>
                            <th><?=\Yii::t("fe", "Metode Pembayaran");?></th>
                            <th><?=\Yii::t("fe", "Bank");?></th>
                            <th><?=\Yii::t("fe", "Keterangan");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi/Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Deskripsi");?></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    // Global Var
    var table;
    var groupColumn = ["transaksi", "kasir"];

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            paging: false,
            info: false,
            scrollX: true,
            scrollY: true,
            order: [[0, "asc"], [6, "asc"], [2, "asc"]],
            rowGroup: {
                endRender: function (rows, group) {
                    var totalPenerimaan = rows
                            .data()
                            .pluck("rupiah")
                            .reduce( function (a, b) {
                                return a + b;
                            }, 0);

                    totalPenerimaan = $.fn.dataTable.render.number(".", ",", 0, "").display( totalPenerimaan );
                    
                    return $("<tr/>")
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append("<td style=\'background:#FEF9E7;font-weight:bold;\' class=\'text-right;\'>TOTAL </td>")
                        .append( "<td style=\'background:#FEF9E7;font-weight:bold;\'  class=\'text-right\'> " + totalPenerimaan + " </td>")
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "<td style=\'background:#FEF9E7;\'> </td>" )
                        .append( "</tr>" );
                },
                dataSrc: groupColumn
            },
            columnDefs: [
                {
                    "visible" : false, 
                    "targets": groupColumn
                }
            ],
            processing: true,
            serverSide: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-penerimaan-kasir/get-data",
            columns: [
                {title: "'.(\Yii::t("fe", "Kasir")).'", data: "kasir", visible: false},
                {title: "'.(\Yii::t("fe", "Tanggal")).'", data: "tanggal"},
                {title: "'.(\Yii::t("fe", "No Kwitansi")).'", data: "no_kwitansi", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "No Registrasi")).'", data: "no_registrasi", searchable: false},
                {title: "'.(\Yii::t("fe", "Info Pasien")).'",  data: "info_pasien", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Rupiah")).'", data: "rupiah", searchable: false, class: "text-right", render: $.fn.dataTable.render.number( ".", ",", 0, "" ), orderable: false},
                {title: "'.(\Yii::t("fe", "Metode Pembayaran")).'", data: "transaksi", visible: false},
                {title: "'.(\Yii::t("fe", "Bank")).'", data: "nama_bank", visible: false},
                {title: "'.(\Yii::t("fe", "Keterangan")).'", data: "keterangan", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "cara_bayar", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", data: "kelas_pelayanan", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Instalasi/Ruangan")).'", data: "instalasi_ruangan", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", visible: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", visible: false},
                {title: "' . (\Yii::t("fe", "Deskripsi")) . '", 
                    data: "deskripsi", 
                    render: function ( data, type, row ) {
                        return (row.deskripsi != null) ? ((row.deskripsi.length > 50) ? `${row.deskripsi.substr(0, 50)}...` : row.deskripsi) : "-";
                    },
                    searchable: false
                },
            ],
            responsive: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'

                ], [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('transaksi', '', [], [
                        'class' => 'form-control select2', 
                        'id' => 'selectTransaksi',
                        'prompt' => \Yii::t('fe', '— Pilih Metode Pembayaran — ')]))).'\'
                ], 
                [
                    7, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_bank', '', [], [
                        'class' => 'form-control select2', 
                        'id' => 'selectBank',
                        'prompt' => \Yii::t('fe', '— Pilih Bank — ')]))).'\'
                ],
                [
                    13,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_id', '', [], [
                        'class' => 'form-control select2', 
                        'id' => 'selectInstalasi',
                        'prompt' => \Yii::t('fe', '— Pilih Instalasi — ')]))).'\'
                ],
                [
                    14,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_id', '', [], [
                        'class' => 'form-control select2', 
                        'id' => 'selectRuangan',
                        'prompt' => \Yii::t('fe', '— Pilih Ruangan — ')]))).'\'
                ],
            ], {
                1:0,
                2:6,
                3:7,
            }, true
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("d-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

        // $("#tanggal").pickadate({
        //     format: "dd mmm yyyy",
        // });

        $("#selectBank").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Bank --",  
                _api : "/kasir/master-api/get-data-bank",
            }
        )
        $("#selectInstalasi").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Instalasi --",  
                _api : "/kasir/master-api/get-data-instalasi",
            }
        )
        $("#selectRuangan").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Ruangan --",  
                _api : "/kasir/master-api/get-data-ruangan",
            }
        )
        $("#selectTransaksi").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Metode Pembayaran --",  
                _api : "/kasir/master-api/get-data-metode-transaksi",
            }
        )
        $("#selectTransaksi").on("change", function(e){
            e.preventDefault()
            $("#selectBank").val(null).trigger("change");
            let trans = $(this).find("option:selected").val()
            if(trans === "NON TUNAI"){
                $("#selectBank").prop("disabled", false);
            }else{
                $("#selectBank").prop("disabled", true);
            }
        })
    });
', View::POS_END, 'b-index');
?>
