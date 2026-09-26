<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

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
                        <h3 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien Sudah Bayar'); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => Url::home().('kasir/inf-pasien-sudah-bayar/preview?id='),
                            'data-conditions'=>'pendaftaran_id,tipe_pasien'
                        ]
                    ],
                    'batal-bayar' =>[
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'method' => '#',
                        'attributes' => [
                            'style' => $roleBatalBtn,
                            'class'=>'data-batal-bayar',
                            'data-options'=>'click',
                            'data-additional' => 'data-rm',
                            'data-target' => '/kasir/inf-pasien-sudah-bayar/cancel?id=',
                            'data-conditions'=>'pendaftaran_id'
                        ]
                    ],
                    'cetak-kwitansi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Kwitansi'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-cetak-kwitansi',
                            'data-options'=>'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/inf-pasien-sudah-bayar/cetak-kwitansi?id=',
                            'data-conditions'=>'pembayaran_id'
                        ]
                    ],
                    'invoice' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Cetak Invoice'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-invoice',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/pembayaran-tagihan/generate-invoice?id=',
                            'data-conditions'=>'pembayaran_id'
                        ]
                    ],
                    'detailinvoice' => [
                        'title' => Yii::t('fe', 'Cetak Detail Invoice'),
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                            'id'=>'cetak-detail-invoice',
                            'data-options' => 'link',
                            'disabled' =>  true
                        ]
                    ],
                    'excel',
                ],'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                            <th><?=\Yii::t("fe", "No Pembayaran");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan Akhir");?></th>
                            <th><?=\Yii::t("fe", "Instalasi Akhir");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Akhir");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar - Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Pasien");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
var dataCollect;
var PASIEN_ALKES = "'.DocoConstants::PASIEN_ALKES.'";
var INSTALASI_RANAP = '.DocoConstants::INSTALASI_ID_RI.';

$(".data-batal-bayar").click(function(e){
      e.preventDefault()
      var tableData = table.row(".selected").data();
      PNotify.removeAll();
      if (typeof tableData !== "undefined") {
        var primary = tableData.primary;
        var url = window.location.origin;
        var target = $(this).attr("data-target");
        var conditions = $(this).attr("data-conditions") ? $(this).attr("data-conditions").split(",") : "";
        var ext = "";
        if(conditions.length > 0){
            $.each(conditions, function(index, value){
                ext += "&"+value+"="+tableData[value];
            });
        }
        $(this).attr("action", target+primary+ext);
            $(this).docoForm("delete",{
                additional: "data-rm",
                success : function (data) {
                    table.draw();
                }
        });
      } else {
          new PNotify({
            title: "Terjadi Kesalahan",
            text: "Belum ada data yang dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });

      }

});
// $("#btn-cetak-kwitansi").click(function(e){
//     e.preventDefault()
//     var tableData = table.row(".selected").data();
//     if(typeof tableData !== "undefined") {
//         var primary = tableData.primary;
//         var pembayaran_id = tableData.pembayaran_id;
//         var url = "/kasir/inf-pasien-sudah-bayar/cetak-kwitansi?id=" + primary + "&pembayaran_id=" + pembayaran_id;
//         $(this).attr("data-url", url);
//     }
// });
$(function(){
    table = $("#table-informasi").docoTabel({
        filter: true,
        columnDefs: [{
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
        ajax: baseUrl+"kasir/inf-pasien-sudah-bayar/get-data",
        columns: [
            {
                data : null,
                render : function ( data, type, full, meta ) {
                    return null;
                },
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'",
                data: "tgl_pembayaran",
                name:"tgl_pembayaran"
            },
            {
                title: "'.(\Yii::t("fe", "No Pembayaran")).'",
                data: "no_pembayaran",
                name:"no_pembayaran"
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi - Ruangan Akhir")).'",
                data: "instalasi_nama",
                name:"instalasi_nama",
                searchable: false,
            },
            {title: "'.(\Yii::t("fe", "Instalasi Akhir")).'", data: "instalasi_nama", visible: false},
            {title: "'.(\Yii::t("fe", "Ruangan Akhir")).'", data: "ruangan_nama", visible: false},
            {
                title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                data: "no_pendaftaran",
                name:"no_pendaftaran"
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                data: "nama_pasien",
                name:"nama_pasien",
                searchable: false,
            },
            {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", visible: false},
            {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", visible: false},
            {
                title: "'.(\Yii::t("fe", "Cara Bayar - Penjamin")).'",
                data: "carabayar_nama",
                name:"carabayar_nama",
                searchable: false,
            },
            {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama", visible: false},
            {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", visible: false},
            {
                title: "'.(\Yii::t("fe", "Jumlah Tagihan")).'",
                data: "tagihan",
                name:"tagihan",
                searchable: false,
                className: "text-right",
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Dibayar Penjamin")).'",
                data: "total_dijamin",
                name:"total_dijamin",
                searchable: false,
                className: "text-right",
            },
            {
                title: "'.(\Yii::t("fe", "Jumlah Dibayar Pasien")).'",
                data: "total_dibayar",
                name:"total_dibayar",
                searchable: false,
                className: "text-right",
            },
            {
                title: "",
                data: "pendaftaran_id",
                visible: false,
                searchable: false
            }
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                12,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('carabayar_nama', '',
                        ArrayHelper::map($resMaster['carabayar'], 'carabayar_nama', 'carabayar_nama'),
                        [
                            'id' => 'filter_carabayar',
                            'class' => 'form-control select2 dep-to-child',
                            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-penjamin',
                            'data-depend_id' => 'filter_penjamin',
                            'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-storage' => 'penjamin',
                            'data-key' => 'penjamin_nama',
                        ]
                    )
                )).'</div>\'
            ],
            [
                13,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('penjamin_nama', '',
                        ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama'),
                        [
                            'id' => 'filter_penjamin',
                            'class' => 'form-control select2 dep-to-parent',
                            'prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-carabayar',
                            'data-depend_id' => 'filter_carabayar',
                        ]
                    )
                )).'\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                      Html::dropDownList('instalasi_nama', '',
                          ArrayHelper::map($resMaster['instalasi'], 'instalasi_nama', 'instalasi_nama'),
                          [
                              'id' => 'filter_instalasi',
                              'class' => 'form-control select2 dep-to-child',
                              'prompt' => \Yii::t('fe', '--Pilih Instalasi akhir--'),
                              'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan',
                              'data-depend_id' => 'filter_ruangan',
                              'data-depend_prompt' => \Yii::t('fe', '--Pilih Ruangan akhir--'),
                              'data-storage' => 'ruangan',
                              'data-key' => 'ruangan_nama',
                          ]
                      )
                )).'</div>\'
            ],
            [
                6,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan_nama', '',
                        ArrayHelper::map($resMaster['ruangan'], 'ruangan_nama', 'ruangan_nama'),
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2 dep-to-parent',
                            'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-instalasi',
                            'data-depend_id' => 'filter_instalasi',
                            'prompt' => \Yii::t('fe', '--Pilih Ruangan akhir--')
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            2:0,
            3:1,
            7:2,
            9:3,
            10:4,
        }, true);
        
    $(document).on("click", "#table-informasi tbody tr", function(){
        var returbayarpelayanan_id = null;
        var instalasi_id = null;
        console.log(table.row(".selected").data());
        try {
            returbayarpelayanan_id = table.row(".selected").data().returbayarpelayanan_id;
            instalasi_id = table.row(".selected").data().instalasi_id1;
        }
        catch(e) {
            returbayarpelayanan_id = null;
            instalasi_id = null;
        }

        if (returbayarpelayanan_id == null ) {
            $(".data-batal-bayar").attr("disabled", false);
        }
        else {
            $(".data-batal-bayar").attr("disabled", true);
        }

        if(instalasi_id == INSTALASI_RANAP) {
            $("#cetak-detail-invoice").prop("disabled", false);
        }
        else {
            $("#cetak-detail-invoice").prop("disabled", true);
        }
    })

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

    $(".no_pendaftaran").select2({
        placeholder: "",
        minimumInputLength: 2,
        ajax: {
            url: "/kasir/inf-pasien-sudah-bayar/get-pendaftaran",
            dataType: "json",
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    z: $(".targetDate").val(),
                    page: page
                };
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });

    $(".no_pembayaran").select2({
        placeholder: "",
        minimumInputLength: 2,
        ajax: {
            url: "/kasir/inf-pasien-sudah-bayar/get-data-pembayaran",
            dataType: "json",
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    z: $(".targetDate").val(),
                    page: page
                };
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });

});

$(".data-excel").on("click", function(e){
    e.preventDefault();
    window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-sudah-bayar/export-excel?"+$.param(table.ajax.params()));
    return false;
});

$("#cetak-detail-invoice").click(function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    var pendaftaran_id = null;
    var pembayaran_id = null;

    if(typeof tableData !== "undefined") {
        var pendaftaran_id = tableData.pendaftaran_id;
        var pembayaran_id = tableData.pembayaran_id;

        var url = "/kasir/pembayaran-tagihan/cetak-detail-invoice?id=" + pendaftaran_id + "&invoice_id=" + pembayaran_id;

        window.open(url);

    }
});
',View::POS_END,'b-index');
