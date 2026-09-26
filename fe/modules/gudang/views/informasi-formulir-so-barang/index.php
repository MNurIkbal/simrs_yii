<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => []];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'export-pdf-serconn' => [
                        'title' => 'Cetak Formulir SO',
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id' => 'cetak-formulir-so-barang',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-table-id' => 'inf-formulir-so',
                            'data-url' => Url::home().('gudang/informasi-formulir-so-barang/show-popup?'),
                            'data-conditions' => 'primary',
                            'disabled' => 'true',
                        ]
                    ],
                    'custom' => [
                        'title' => 'Detail',
                        'type' => 'button',
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id' => 'detailSo',
                            'disabled' => 'true',
                            'data-target' => Url::home().('gudang/informasi-formulir-so-barang/detail-so?id='),
                            'data-conditions' => 'stokopnamebarang_id',
                        ]
                    ],
                    'stok-opname' => [
                        'type' => 'link',
                        'title' => 'Stok Opname',
                        'icon' => 'fa fa-briefcase',
                        'attributes' => [
                            'url' => '/gudang/informasi-formulir-so-barang',
                            'data-target' => '/gudang/informasi-formulir-so-barang/view?id=',
                        ]
                    ],
                    'delete',
                ],'#inf-formulir-so');?>
            </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                    </br>
                    <table id="inf-formulir-so" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th>No</th>
                                <th><?=Yii::t('fe','Tanggal Formulir')?></th>
                                <th><?=Yii::t('fe','Nomor Formulir')?></th>
                                <th><?=Yii::t('fe','Tanggal Stok Opname')?></th>
                                <th><?=Yii::t('fe','Nomor Stok Opname')?></th>
                                <th><?=Yii::t('fe','Instalasi / Ruangan')?></th>
                                <th><?=Yii::t('fe','Pegawai Verifikasi')?></th>
                                <th><?=Yii::t('fe','Status')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalInformasiDetailStokOpname" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Informasi Detail Stok Opname</h5>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'cetak' => [
                            'title' => 'Cetak',
                            'type' => 'button',
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'cetakDetailBtn',
                                'data-toggle' => 'Cetak Detail Formulir'
                            ]
                        ],
                    ], '#tableDetailFormulir');?>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-12 text-center">
                            <h3 class="text-center" id="ruanganNamaSection">Apotek RJ</h3>
                        </div>
                        <div class="col-sm-12">
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="col-sm-5">
                                    <b>Tanggal Formulir</b>
                                </div>
                                <div class="col-sm-7">
                                    <p id="tanggalFormulirSection"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-5">
                                    <b>Nomor Formulir</b>
                                </div>
                                <div class="col-sm-7">
                                    <p id="nomorFormulirSection"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row table-responsive">
                    <table class="table table-striped table-condensed table-hover" id="tableDetailFormulir" style="width:100% !important;">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="80">No</th>
                                <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                <th><?=\Yii::t("fe", "Kelompok barang");?></th>
                                <th><?=\Yii::t("fe", "Sub Kelompok Barang");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                                <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                <th><?=\Yii::t("fe", "Kondisi");?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
const dropdowninstalasi = ' . json_encode($instalasi) . '
$(document).on("click", "#example tr", function(){
    var tbl = table.row(".selected").data();
    $(".data-delete").show();
    $(".btn-custom").attr("disabled",false);
    $(".btn-stok-opname").attr("disabled",false);
    $(".data-delete").attr("disabled",false);
    if (typeof tbl === "undefined") return true;

    if(tbl.status_so == "Belum Input Hasil"){
        $(".btn-custom").attr("disabled",true);
    }else if(tbl.status_so == "Belum Verifikasi"){
        $(".btn-custom").attr("disabled",false);
    }else if(tbl.status_so == "Sudah Verifikasi"){
        $(".btn-custom").attr("disabled",false);
        $(".btn-stok-opname").attr("disabled",true);
        $(".data-delete").attr("disabled",true);
    }


});
$(document).ready(function(){
    $("#detailBtn").bind("click", (event) => {
        event.preventDefault()
        const table = $("#example").DataTable()
        const tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if ($("#tableDetailFormulir").hasClass("dataTable")) {
                $("#tableDetailFormulir").DataTable().destroy()
            }
            $("#tableDetailFormulir").data("source", `/gudang/informasi-formulir-so-barang/datatable-detail?noFormulir=${tableData.noformulir}`)
            const tableDetail = $("#tableDetailFormulir").docoTabel({
                columns: [
                    {
                        title: "No",
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) => {
                            return (meta.row + 1) + tableDetail.page.info().page * 10
                        }
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Nama Barang')).'", 
                        data: "barang_nama",
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Kelompok Barang')).'", 
                        data: "kelompok_barang",
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Sub Kelompok Barang')).'", 
                        data: "subkelompok_barang",
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Tanggal Kadaluarsa')).'", 
                        data: "tgl_kadaluarsa",
                        render: (data) => {
                            return data !== null ? convertDateByFormat(data, "d m Y") : "-"
                        }
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Stok Sistem')).'", 
                        data: "stok",
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Stok Fisik')).'", 
                        data: "volume_fisik",
                    },
                    {
                        title: "'.(\Yii::t('fe', 'Kondisi')).'", 
                        searchable: false,
                        orderable: false,
                        data: "kondisibarang",
                    },
                ]
            })
            $("#tableDetailFormulir_filter").hide()
            $("#tableDetailFormulir_length").hide()
            $("#ruanganNamaSection").html(tableData.ruangan_nama)
            $("#tanggalFormulirSection").html(tableData.tglformulir)
            $("#nomorFormulirSection").html(tableData.noformulir)
            $("#modalInformasiDetailStokOpname").modal("show")
            $("#cetakDetailBtn").unbind("click")
            $("#cetakDetailBtn").bind("click", () => {
                window.open(`/gudang/informasi-formulir-so-barang/print-detail-formulir?noFormulir=${tableData.noformulir}`)
            })
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    })
    });
', VIEW::POS_END, "js-kunings");

$this->registerJs($this->render('js/datatable.js'));
?>

