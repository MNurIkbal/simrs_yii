<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                        "search"=> [
                            'attributes'=>[
                                'id' => 'find-data',
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'reset-data',
                                'data-parent' => '.filter-form'
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
                                'data-url' => Url::home() . 'gudang/laporan-hasil-stok-opname-barang/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="70">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Form SO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi SO");?></th>
                            <?= !$implementasi ? "<th>".\Yii::t("fe", "Tanggal Implementasi SO")."</th>" : "" ?>
                            <th><?=\Yii::t("fe", "Divalidasi oleh");?></th>
                            <th><?=\Yii::t("fe", "No. Form SO");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Barang");?></th>
                            <th><?=\Yii::t("fe", "Sub Kelompok");?></th>
                            <th><?=\Yii::t("fe", "Kode Barang");?></th>
                            <th><?=\Yii::t("fe", "Nama Barang");?></th>
                            <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                            <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                            <th><?=\Yii::t("fe", "Selisih");?></th>
                            <th><?=\Yii::t("fe", "Stok Akhir");?></th>
                            <th><?=\Yii::t("fe", "Selisih Akhir");?></th>
                            <th><?=\Yii::t("fe", "Total Harga Netto (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Total Selisih (Rp.)");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var isDisabled = "'.$is_disabled.'";
var isTglImplementasi = "'.$implementasi.'";

let list_tabel = [
            {data: null, searchable: false, sortable: false, defaultContent:""},
            {title: "No", data: "rowNum", searchable: false, sortable: false},
            {title: "'.(\Yii::t("fe", "Tanggal Form SO")).'", data: "tgl_form_so"},
            {title: "'.(\Yii::t("fe", "Tanggal Validasi SO")).'", data: "tgl_validasi_so", searchable:false},
            {title: "'.(\Yii::t("fe", "Divalidasi oleh")).'", data: "validasi_by", searchable:false},
            {title: "'.(\Yii::t("fe", "No. Form SO")).'", data: "no_form_so"},
            {title: "'.(\Yii::t("fe", "Instalasi - Ruangan")).'", data: "instalasi_ruangan"},
            {title: "'.(\Yii::t("fe", "Kelompok Barang")).'", data: "kelompokbarang_nama", searchable:false},
            {title: "'.(\Yii::t("fe", "Sub Kelompok")).'", data: "subkelompok_nama", searchable:false}, 
            {title: "'.(\Yii::t("fe", "Kode Barang")).'", data: "kode_barang", searchable:false},
            {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "nama_barang", searchable:false},
            {title: "'.(\Yii::t("fe", "Satuan Kecil")).'", data: "satuan_kecil", searchable:false},
            {title: "'.(\Yii::t("fe", "Harga Netto (Rp.)")).'", data: "harganetto", searchable:false},
            {title: "'.(\Yii::t("fe", "Stok Sistem ")).'", data: "stok_sistem", searchable:false},
            {title: "'.(\Yii::t("fe", "Stok Fisik")).'", data: "stok_fisik", searchable:false},
            {title: "'.(\Yii::t("fe", "Selisih")).'", data: "selisih", searchable:false},
            {title: "'.(\Yii::t("fe", "Stok Akhir")).'", data: "stok_akhir", searchable:false},
            {title: "'.(\Yii::t('fe', 'Selisih Akhir')).'", data: "selisih_akhir",searchable : false},
            {title: "'.(\Yii::t('fe', 'Total Harga Netto (Rp.)')).'", data: "total_harga_netto",searchable : false},
            {title: "'.(\Yii::t('fe', 'Total Selisih (Rp.)')).'", data: "total_harga_selisih",searchable : false}
        ];

    if(isTglImplementasi!=true){
        list_tabel.splice(4,0,{title: "'.(\Yii::t("fe", "Tanggal Implementasi SO")).'", data: "tgl_implementasi", searchable:false})
    }

    $(document).on("keydown", null, "enter", function (event) {
        $("#find-data").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }
    });

    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
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
            sorting: [[2, "asc"], [5, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/laporan-hasil-stok-opname-barang/get-data",
            columns: list_tabel
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" value="'.date("d-M-Y").'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date("d-M-Y").'" readonly="readonly" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    7,
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_nama', $ruangan_aktif,
                            $daftar_ruangan,
                            [
                                'class' => 'form-control select2 ruangan_nama',
                                'prompt' => \Yii::t('fe', ''),
                                'id' => 'filter_ruangan_nama',
                                'prompt' => $ruangan_aktif == '' ? \Yii::t('fe', '-- Pilih Ruangan --') : $daftar_ruangan[$ruangan_aktif]
                            ]
                        )
                    )).'</div>\'
                ],
            ],{
                2:0,
                6:1,
                7:2,
            });

        if(isDisabled) {
            $(".ruangan_nama").prop("disabled", true);
        }
        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
    });
', View::POS_END, 'b-index');
?>
