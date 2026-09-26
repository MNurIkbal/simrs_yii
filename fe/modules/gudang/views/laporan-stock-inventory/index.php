<?php

use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
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
                <?=
                    DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'gudang/laporan-stock-inventory/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <div class="container-fluid">
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'No.'); ?></th>
                            <th style="display: none;"></th>
                            <th><?=\Yii::t('fe', 'Nama Ruangan'); ?></th>
                            <th><?=\Yii::t("fe", 'Kode Obat'); ?></th>
                            <th><?=\Yii::t("fe", 'Nama Obat Alkes'); ?></th>
                            <th><?=\Yii::t("fe", 'Jenis Obat Alkes'); ?></th>
                            <th><?=\Yii::t("fe", 'Generik'); ?></th>
                            <th><?=\Yii::t("fe", 'Oral'); ?></th>
                            <th><?=\Yii::t("fe", 'Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Nilai Konversi'); ?></th>
                            <th><?=\Yii::t("fe", 'Stok Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Stok Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Weighted Average Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Weighted Average Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Weighted Average Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Weighted Average Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Base Price Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Base Price Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Base Price Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Base Price Satuan Besar'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="16"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

var isDisabled = "'.$is_disabled.'";
var table;

$(document).on("click", ".data-reload", function() {
    table.draw();
});

$(document).ready(function() {
    // Generate Table
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[2, "asc"],[4, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        scrollY : true,
        ajax: baseUrl+"gudang/laporan-stock-inventory/get-list-data",
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal",
                data: "tanggal_inventory",
                visible: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Ruangan")).'",
                data: "ruangan_nama"
            },
            {
                title: "'.(\Yii::t("fe", "Kode Obat")).'",
                data: "obatalkes_kode",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",
                data: "obatalkes_nama",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Obat Alkes")).'",
                data: "jenis_obat",
            },
            {
                title: "'.(\Yii::t("fe", "Generik")).'",
                data: "is_generik",
                searchable:false,
            },
            {
                title: "'.(\Yii::t("fe", "Oral")).'",
                data: "is_oral",
                searchable:false,
            },
            {
                title: "'.(\Yii::t("fe", "Satuan Kecil")).'",
                data: "satuan_kecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Satuan Besar")).'",
                data: "satuan_besar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Nilai Konversi")).'",
                data: "nilai_konv",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Stok Satuan Kecil")).'",
                data: "qty_satuankecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Stok Satuan Besar")).'",
                data: "qty_satuanbesar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Weighted Average Satuan Kecil")).'",
                data: "wa_satuan_kecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Weighted Average Satuan Besar ")).'",
                data: "wa_satuan_besar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Weighted Average Satuan Kecil")).'",
                data: "total_satuankecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Weighted Average Satuan Besar")).'",
                data: "total_satuanbesar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Base Price Satuan Kecil")).'",
                data: "baseprice_kecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Base Price Satuan Besar")).'",
                data: "baseprice_besar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Base Price Satuan Kecil")).'",
                data: "total_satuankecil_netto",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Base Price Satuan Besar")).'",
                data: "total_satuanbesar_netto",
                searchable: false,
            }

        ]
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
     
        [
            1,
            \'<div class=\"form-group\"> '.(preg_replace("/[\n\t\r]/i", '',
                Html::textInput('tanggalInventory',null,['class' => 'form-control', 'id' => 'tanggalInventory','style' => 'width: 200px'])
            )).'</div>\'
        ],
        [
            2,
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
        [
            5,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('jenis_obat', $jenisobat_aktif, 
                    $daftar_jenisobat, 
                    [
                        'class' => 'form-control select2 jenis_obat', 
                        'prompt' => \Yii::t('fe', ''),
                        'id' => 'filter_jenisobat',
                        'prompt' => $jenisobat_aktif == '' ? \Yii::t('fe', '-- Pilih Jenis Obat --') : $daftar_jenisobat[$jenisobat_aktif]
                    ]
                )
            )).'</div>\'
        ],
    ],{
        1:0,
        2:1,
        5:2,
    });

    $("#tanggalInventory").attr("readonly", true);

    var tglDefault = new AnyTime.Converter({ format: "%e-%b-%Y", moment: moment() });
    $("#tanggalInventory").AnyTime_noPicker().val(tglDefault.format(new Date)).AnyTime_picker({
        format: "%e-%b-%Y"
    });

 

    // dateRangeHelper(".startDate", ".endDate", ".targetDate");

    // $(document).on("change", ".startDate, .endDate", function(){
    //     $(".data-filter").click();
    // });

    if(isDisabled) {
        $(".ruangan_nama").prop("disabled", true);
    }
    
    $("#get-file").on("click", function () {
        var data = table.ajax.params(); 
        $.ajax({
            url: baseUrl+"gudang/laporan-stock-inventory/export-excel?"+$.param(data),
            method: "GET",
            xhrFields: {
                responseType: "blob"
            },
            beforeSend:function(){
                showLoader();
            },
            success: function (data) {
                var a = document.createElement("a");
                var url = window.URL.createObjectURL(data);
                a.href = url;
                a.download = "laporan-stock-inventory.xlsx";
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                hideLoader();
            }
        });
    });    
    
});

', View::POS_END, 'b-index');
?>