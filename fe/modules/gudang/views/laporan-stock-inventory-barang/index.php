<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .datepicker>div{
        display:block;
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
                                'data-url' => Url::home() . 'gudang/laporan-stock-inventory-barang/show-popup-excel?',
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
                            <th><?=\Yii::t("fe", 'Instalasi'); ?></th>
                            <th><?=\Yii::t('fe', 'Nama Ruangan'); ?></th>
                            <th><?=\Yii::t("fe", 'Kode Barang'); ?></th>
                            <th><?=\Yii::t("fe", 'Nama Barang'); ?></th>
                            <th><?=\Yii::t("fe", 'Kelompok Barang'); ?></th>
                            <th><?=\Yii::t("fe", 'Sub Kelompok'); ?></th>
                            <th><?=\Yii::t("fe", 'Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Nilai Konversi'); ?></th>
                            <th><?=\Yii::t("fe", 'Stok Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Stok Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Harga Netto Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Harga Netto Satuan Besar'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Satuan Kecil'); ?></th>
                            <th><?=\Yii::t("fe", 'Total (Rp.) Satuan Besar'); ?></th>
                            <th><?=\Yii::t('fe', 'Ruangan'); ?></th>
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
        ajax: baseUrl+"gudang/laporan-stock-inventory-barang/get-list-data",
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
                title: "'.(\Yii::t("fe", "Instalasi")).'",
                data: "instalasi_id",
                visible : false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Ruangan")).'",
                data: "ruangan_nama",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Kode Barang")).'",
                data: "barang_kode",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Nama Barang")).'",
                data: "barang_nama",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Kelompok Barang")).'",
                data: "kelompok_barang",
            },
            {
                title: "'.(\Yii::t("fe", "Sub Kelompok")).'",
                data: "subkelompok_nama",
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
                title: "'.(\Yii::t("fe", "Harga Netto Satuan Kecil")).'",
                data: "baseprice_kecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Harga Netto Satuan Besar")).'",
                data: "baseprice_besar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Satuan Kecil")).'",
                data: "total_satuankecil",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total (Rp.) Satuan Besar")).'",
                data: "total_satuanbesar",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Ruangan")).'",
                data: "ruanganid",
                visible : false
            },

        ]
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            1,
            \'<div class=\"form-group\"> '.(preg_replace("/[\n\t\r]/i", '',
                DatePicker::widget([
                  'name' => 'tanggalInventory',
                  'value' => date('Y-m-d'),
                  'id' => 'tanggalInventory',
                  'readonly' => true,
                  'language' => 'en',
                  'pluginOptions' => [
                      'autoclose' => true,
                      'format' => 'dd-M-yyyy',
                       'endDate' => "0d",
                  ]
                ])
            )).'</div>\'
        ],
        [
            2,
            \''.(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\"',
                Html::dropDownList('instalasi_id', '',
                    ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
                    [
                        'class' => 'form-control select2 selectInstalasi',
                        'id'=>'filter_instalasi',
                        'prompt' => \Yii::t('fe', 'Semua'),
                        'disabled' => $visibility
                    ]
                )
            ))).'\'
        ],
        [
            17,
                \''.(preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\"',
                    DepDrop::widget([
                        'name' => 'ruanganid',
                        'data'=> $ruangan_aktif,
                        'options' => [
                            'disabled' => false,
                            'class' => 'form-control select2 ruanganid'
                        ],
                        'pluginOptions' => [
                           'depends'  => ['filter_instalasi'],
                           'placeholder' => 'Semua',
                           'url' => Url::to(['/gudang/laporan-stock-inventory-barang/get-ruangan'])
                        ]
                    ])
                    )
                )
                ).'\'
        ],
        [
            6,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('kelompok_barang', $jenisbarang_aktif, 
                    $daftar_jenisbarang, 
                    [
                        'class' => 'form-control select2 kelompok_barang', 
                        'prompt' => \Yii::t('fe', ''),
                        'id' => 'filter_kelompok_barang',
                        'prompt' => $jenisbarang_aktif == '' ? \Yii::t('fe', '-- Pilih Kelompok Barang --') : $daftar_jenisbarang[$jenisbarang_aktif]
                    ]
                )
            )).'</div>\'
        ],
    ],{
        1:0,
        2:1,
        17:2,
        6:3,
    });

    var tglDefault = new AnyTime.Converter({ format: "%e-%b-%Y", moment: moment() });
    $("#tanggalInventory").val(tglDefault.format(new Date))({
        format: "%e-%b-%Y"
    });

    if(isDisabled) {
        $(".ruangan_nama").prop("disabled", true);
    } 
    
    $("#get-file").on("click", function () {
        var data = table.ajax.params(); 
        $.ajax({
            url: baseUrl+"gudang/laporan-stock-inventory-barang/export-excel?"+$.param(data),
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