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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'add-piutang' => [
                            'title' => \Yii::t('fe', 'Tambah Piutang'),
                            'icon' => 'fa fa fa-plus',
                            'attributes' => [
                                'data-options' => 'link',
                                'data-target' => Url::home().(Yii::$app->controller->module->id).'/pemberian-piutang',
                            ] 
                        ],
                        'bayar' => [
                            'title' => \Yii::t('fe', 'Bayar'),
                            'icon' => 'fa fa fa-plus',
                            'attributes' => [
                                'class' => 'btn-bayar',
                                'disabled' => true,
                                'data-target' => Url::home().(Yii::$app->controller->module->id).'/informasi-pemberian-piutang/pembayaran-piutang?id=',
                            ] 
                        ],
                        'batal-piutang' =>[
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Batal'),
                            'icon' => 'fa fa-close',
                            'method' => '#',
                            'attributes' => [
                                'style' => $roleBatalBtn,
                                'class'=>'data-batal-piutang',
                                'data-options'=>'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-additional' => 'data-rm',
                                'data-url' => '/kasir/informasi-pemberian-piutang/batal?id=',
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
                                'data-url' => Url::home() . 'kasir/informasi-pemberian-piutang/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                        'kwitansi' => [
                            'title' => Yii::t('fe', 'Cetak Kwitansi'),
                            'icon' => 'fa fa-print',
                            'method' => '#',
                            'attributes' => [
                            'id'=>'cetak-kwitansi',
                            'data-options' => 'link',
                            'target'=>'_blank',
                            ]
                        ]
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pemberian Piutang");?></th>
                            <th><?=\Yii::t("fe", "Info Pasien");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Karyawan");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran/No. Resep");?></th>
                            <th><?=\Yii::t("fe", "Tgl Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tgl Pulang");?></th>
                            <th><?=\Yii::t("fe", "Piutang");?></th>
                            <th><?=\Yii::t("fe", "Sudah Dibayar");?></th>
                            <th><?=\Yii::t("fe", "Sisa Piutang");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
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

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        $("#cetak-kwitansi").prop("disabled", true);
        table = $("#example").docoTabel({
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/informasi-pemberian-piutang/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Pemberian Piutang")).'", data: "tgl_pemberianpiutang"},
                {title: "'.(\Yii::t("fe", "Info Pasien")). '", 
                    data: "nama_pasien", 
                    searchable: false,
                    render: function ( data, type, row ) {
                        return row.info_pasien
                    }
                },
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", visible: false},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", visible: false},
                {title: "'.(\Yii::t("fe", "Nama Karyawan")). '", data: "pegawaidibebankan_nama", name: "pegawaidibebankan_id"},
                {title: "'.(\Yii::t("fe", "No Pendaftaran/No. Resep")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "Tgl Masuk")).'", data: "tgl_pendaftaran", searchable: false}, //08
                {title: "'.(\Yii::t("fe", "Tgl Pulang")).'", data: "tglpasienpulang"}, //09
                {title: "'.(\Yii::t("fe", "Piutang (Rp.)")).'", data: "total_piutang", class: "text-right", searchable: false},
                {title: "'.(\Yii::t("fe", "Sudah Dibayar (Rp.)")).'",  data: "total_bayarpiutang", class: "text-right", searchable: false},
                {title: "'.(\Yii::t("fe", "Sisa Piutang (Rp.)")).'", data: "total_sisapiutang", class: "text-right", searchable: false},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_piutang_nama", name:"status_piutang"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>\'
                ], [
                    13,
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('status_piutang_nama', '', 
                            $status, 
                            [
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Pilih Status')
                            ]
                        )
                    )).'\'
                ],
                [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('pegawaidibebankan_nama', '', 
                            [], 
                            [
                                'class' => 'form-control select2 selectKaryawan', 
                            ]
                        )
                    )).'\'
                ], 
                [
                    9,
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartOut" class="form-control startDateOut"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishOut" class="form-control endDateOut" readonly="readonly"/><input type="text" style="display:none" class="targetDateOut"></div>\'
                ],
            ], {
                2: 0,
                4: 1,
                5: 2,
                6: 3,
                7 :4,
                9 :5,
                13: 6
            }, true
        );
        
        $(document).on("click", "#example tbody tr", function () {
            $(".btn-bayar").prop("disabled", true);
            var status_piutang = table.row(".selected").data().status_piutang ? table.row(".selected").data().status_piutang : null;
            var primary = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;

            if (status_piutang == 349) {
                $(".btn-bayar").prop("disabled", false);
                $(".data-batal-piutang").prop("disabled", false);
            }
            else {
                $(".btn-bayar").prop("disabled", true);
                $(".data-batal-piutang").prop("disabled", true);
            }

            if (status_piutang == 348) {
                $("#cetak-kwitansi").attr("disabled", false);
            }
            else {
                $("#cetak-kwitansi").attr("disabled", true);
            }
        });

        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateOut",".endDateOut",".targetDateOut");
        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

        $(document).ready(function(){
            $(".startDate").val("' . date('d-M-Y', strtotime('-1 months')) .'");
            $(".endDate").val("' . date('d-M-Y') .'");
        });

        $(document).on("click", ".data-reset", function() {
            $(".startDate").val("' . date('d-M-Y', strtotime('-1 months')) . '");
            $(".endDate").val("' . date('d-M-Y') . '");
        });

        $(".selectKaryawan").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Karyawan --",  
                _api : "/kasir/informasi-pemberian-piutang/list-karyawan",
            }
        )
        $("#cetak-kwitansi").click(function(e){
            e.preventDefault();
            var tableData = table.row(".selected").data();
            var primary = null;
            if(typeof tableData !== "undefined") {
                $("#cetak-kwitansi").prop("disabled", false);
                primary = tableData.primary;
                var url = "/kasir/informasi-pemberian-piutang/cetak-kwitansi?id=" + primary;
                $(this).attr("data-target", url);
            }
        });
    });
', View::POS_END, 'b-index');
?>
