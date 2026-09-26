<?php

use app\components\DHtml;
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

$this->title = DHtml::getTitleMenu();
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
$this->registerCss('
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
        width: 150px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 20px;
        width: 150px;
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
    .legend-information {
        padding: 8px;
        margin-right: 8px;
        margin-top: 20px;
        border: 1px solid #dddddd;
        border-radius: 10px;
        display: flex;
    }
    .legend-index {
        margin: 8px 0px;
    }
    .legend-information__color {
        min-width: 16px;
        min-height: 16px;
        height: 16px;
        width: 16px;
        border-radius: 16px;
        margin-right: 8px;
        border: 1px solid #dddddd;
    }
    
    .legend-wrapper {
            display: flex;
            flex-flow: wrap;
    }
    .filter-selected {
        border-color : #2ca38b;
    }
');
?>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
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
                <?php
                $defaultBtn= [
                    'search',
                    'reset',
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/inf-pasien-belum-bayar/show-popup-excel?id=',
                        ]
                    ],
                    'rincian' => [
                        'title' => 'Cetak Summary',
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'target'=>'_blank'
                        ]
                    ],
                    'rincian-detail' => $btnInvoice,
                ];
                if (DHtml::cekHakAkses('detail-invoice-inacbg')) {
                    $defaultBtn['detail-invoice-inacbgs'] = [
                        'title' => Yii::t('fe', "Claim INACBGS"),
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                            'id'=>'detail-invoice-inacbgs',
                            'data-options' => 'link',
                            'disabled' => true
                        ]
                    ];
                }else {
                    $defaultBtn['detail-invoice-inacbgs'] = [
                        'title' => Yii::t('fe', "Claim INACBGS"),
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                            'id'=>'detail-invoice-inacbgs',
                            'data-options' => 'link',
                            'disabled' => true
                        ]
                    ];
                }
                
                $defaultBtn['export-excel-detail'] = [
                    'type' => 'button',
                    'title' => 'Detail export excel',
                    'icon' => 'fa fa-file-excel-o',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id'=>'export-excel-detail',
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '50%',
                        'data-url' => '/kasir/inf-pasien-belum-bayar/show-popup-excel?action=detail&id=',
                        'data-conditions' => 'pendaftaran_id'
                    ]
                ];
                $defaultBtn['payment'] = [
                    // 'type' => 'link',
                    'title' => \Yii::t('fe', 'Edit Tagihan'),
                    'icon' => 'fa fa-edit',
                    'method' => 'not exist',
                    'attributes' => [
                        'id' => 'btn-edit-tagihan',
                        'class' => 'data-payment',
                        'data-target' => Url::home() . (Yii::$app->controller->module->id) . '/inf-pasien-pulang/view?is_belumbayar=1&id=',
                        'style' => $roleHideBtn,
                    ]
                ];
                
                $defaultBtn['cetak-sep'] = [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Print sep'),
                    'icon' => 'fa fa-print',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id' => 'btn-print-sep',
                        'data-target' => Url::home() . 'pendaftaran/end-point/print-sep?',
                        'id' => 'btn-print-sep',
                        'data-options'=>'click',
                        'disabled' => true
                    ],
                ];
                $defaultBtn['plafon-bpjs'] = [
                    'title' => \Yii::t('fe', 'Plafon BPJS'),
                    'icon' => 'fa fa-eye',
                    'attributes' => [
                        'id' => 'plafon-bpjs',
                        'data-popup'=>'tooltip',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modal_backdrop',
                        'data-width' => "50%",
                    ]
                ]
                ?>

            <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>

               
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <br />
                <div class="row _legend">
                    <div class="col-md-6">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan</div>
                            <div class="legend-wrapper">
                                <div class="legend-information ket-pasien-titipan">
                                    <div class="legend-information__color" style="background-color: #ffcccc"></div>
                                    <div class="legend-information__text">Pasien Titipan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (isset($resMaster['carabayar'])) : ?>
                        <div class="col-md-6">
                            <div class='legend-index'>
                                <div class='legend-header'>Keterangan Cara Bayar</div>
                                <div class="legend-wrapper">
                                    <?php
                                    foreach ($resMaster['carabayar'] as $key => $value) { ?>
                                        <div class="legend-information" id="<?= $value['carabayar_id']; ?>" data-type="<?= $value['carabayar_id']; ?>">

                                            <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                                            <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                

                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelas Ditempati / Kelas Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Status Kamar");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Akhir");?></th>
                            <th><?=\Yii::t("fe", "Kamar / Tempat Tidur");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Perkiraan Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Limit Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Pembayaran Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Uang Masuk");?></th>
                            <th><?=\Yii::t("fe", "Sisa Tagihan");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nominal");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
//
$this->registerJs('
var table;
const cetakRincian = "/kasir/inf-pasien-belum-bayar/cetak-rincian?id=";
const cetakDetailRincian = "/kasir/inf-pasien-belum-bayar/show-popup?id=";
var instalasiRanap = "'.DocoConstants::INSTALASI_ID_RI.'";
var groupBpjs = "'.DocoConstants::GROUP_BPJS.'";
var WS_RANAP = '.DocoConstants::WS_RANAP.';
$(document).ready(function() {
    $("#plafon-bpjs").prop("disabled", true);
    table = $("#table-informasi").docoTabel({
        info: false,
        filter: true,
        stateSave: false,
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
        ajax: baseUrl+"kasir/inf-pasien-belum-bayar/get-data",
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
                title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'",
                data: "tgl_pendaftaran",
                searchable: true,
            },
            {
                title: "'.(\Yii::t("fe", "No Pendaftaran")).'",
                data: "no_pendaftaran",
                name:"no_pendaftaran"
            },
            {
                title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                data: "nama_pasien",
                name:"nama_pasien"
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Kelamin")).'",
                data: "jenis_kelamin",
                name:"jenis_kelamin",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Penjamin")).'",
                data: "penjamin_nama",
                name:"penjamin_nama",
                searchable: false,
            },
            {
                data: "hak_kelas",
                orderable: false,
                searchable: false,
                render: (data, rowElement, rowData) => {
                    let _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                    if(rowData.is_pasientitipan) {
                        _wording = `${data != null ? data : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                    } else if (data != null) {
                        _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                    }
                    return _wording
                }
            },
            {
                title: "'.(\Yii::t("fe", "Status Kamar")).'",
                orderable: false,
                searchable: false,
                data: "status_kelas",
                name:"status_kamar",
            },
            {
                title: "'.(\Yii::t("fe", "Ruangan Akhir")).'",
                data: "ruangan_nama",
                name:"ruangan_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Kamar / Tempat Tidur")).'",
                orderable: false,
                searchable: false,
                render: (data, rowElement, rowData) => {
                    return `${rowData.kamarruangan_nokamar != null ? rowData.kamarruangan_nokamar : "-"} / ${rowData.no_tempattidur != null ? rowData.no_tempattidur : "-"}`
                }
            },
            {
                title: "'.(\Yii::t("fe", "Dokter")).'",
                data: "nama_dokter",
                name:"nama_dokter",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Status")).'",
                data: "status_periksa",
                name:"status_periksa"
            },
            {
                title: "'.(\Yii::t("fe", "Perkiraan Tagihan")).'",
                data: null,
                searchable: false,
                className: "text-right",
                render: (data) => {
                    return `<div id="${data.primary}">${data.total_tagihan}</div>` 
                }
            },
            {
                title: "'.(\Yii::t("fe", "Limit Penjamin")).'",
                data : null,
                searchable: false,
                className: "text-right",
                render: (data) => {
                    return `<div id="lt-${data.primary}">${data.limit_tagihan}</div>` 
                }
            },
            {
                title: "'.(\Yii::t("fe", "Uang Muka")).'",
                data: null,
                searchable: false,
                className: "text-right",
                render: (data) => {
                    return `<div id="um-${data.primary}">${data.uang_muka}</div>` 
                }
            },
            {
                title: "'.(\Yii::t("fe", "Pembayaran Tagihan")).'",
                data: "uang_masuk",
                name:"uang_masuk",
                searchable: false,
                className: "text-right",
            },
            {
                title: "'.(\Yii::t("fe", "Sisa Tagihan")).'",
                data: null,
                searchable: false,
                className: "text-right",
                render: (data) => {
                    return `<div id="st-${data.primary}">${data.sisa_tagihan}</div>` 
                }
            },
            {
                title: "'.(\Yii::t("fe", "No Rekam Medik")).'",
                data: "no_rekam_medik",
                name:"no_rekam_medik",
                visible: false,
            },
            {
                title: "'.(\Yii::t("fe", "Manage Tagihan Minimum")).'",
                data: "is_kelola_tagihan",
                name:"is_kelola_tagihan",
                visible: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi")).'",
                data: "instalasi_nama",
                name:"instalasi_nama",
                visible: false,
                orderable: false,
            },
        ],
        drawCallback: function(settings)  {
            const dataRes = settings.jqXHR.responseJSON
            payload = [];
            dataRes.data.map((data) => {
                tmpData = {
                    primary : data.primary,
                    pendaftaranId : data.pendaftaran_id,
                    penjaminId : data.penjamin_id,
                    kelasPelayananId : data.kelaspelayanan_id,
                    totalTagihan : docoHelper.convertToAngka(data.total_tagihan),
                    pasienAdmisiId : data.pasienadmisi_id,
                }
                payload.push(tmpData)
            })
            getBulkAdmin(payload)
        },
        createdRow: (rowElement, data) => {
            var is_pasientitipan = (data.is_pasientitipan != null) ? data.is_pasientitipan : false;
            var is_pasienadmisi = (data.pasienadmisi_id != null) ? true : false;
            var is_stoptitipan = (data.is_stoptitipan != null) ? data.is_stoptitipan : false;
            if(is_pasientitipan && is_pasienadmisi && !is_stoptitipan) {
                $(rowElement).css("background-color", "#ffcccc")
                $(rowElement).css("font-weight", "bold")
            }
            if (data.carabayar_kode_warna != null) {
                $($(rowElement).find("td")[6]).css(
                  "background-color",
                  data.carabayar_kode_warna
                );
                $($(rowElement).find("td")[6]).css(
                  "color",
                  invertColor(data.carabayar_kode_warna, true)
                );
              }
        },
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2, 
            \'<div class="input-group"><input type="text" value="" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=""  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
        [
            19, 
            \'<div class="input-group"><input type="checkbox" id="check_nominal" '.$disableNominal.'/><input type="hidden" id="is_kelola_tagihan" class="form-control" value=0/>&nbsp;&nbsp;Rp. '.DocoHelpers::formatNumber($nominalKonfig).'</div>\'
        ], 
        [
            20,
            \''.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('instalasi_nama', '',
                    ArrayHelper::map($resMaster['instalasi'], 'instalasi_nama', 'instalasi_nama'),
                    [
                        'id' => 'filter_instalasi',
                        'class' => 'form-control select2 dep-to-child',
                        'prompt' => \Yii::t('fe', '--Instalasi akhir--'),
                        'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan',
                        'data-depend_id' => 'filter_ruangan',
                        'data-depend_prompt' => \Yii::t('fe', '--Ruangan akhir--'),
                        'data-storage' => 'ruangan',
                        'data-key' => 'ruangan_nama',
                    ]
                )
            )).'\'
        ],
        [
            9,
            \''.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('ruangan_nama', '',
                    ArrayHelper::map($resMaster['ruangan'], 'ruangan_nama', 'ruangan_nama'),
                    [
                        'id' => 'filter_ruangan',
                        'class' => 'form-control select2 dep-to-parent',
                        'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-instalasi',
                        'data-depend_id' => 'filter_instalasi',
                        'prompt' => \Yii::t('fe', '--Ruangan akhir--')
                    ]
                )
            )).'\'
        ],
        [
            11, 
            \'' . (preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('status_periksa', '',
                    ArrayHelper::map($resMaster['status_periksa'], 'statusperiksa_nama', 'statusperiksa_nama'),
                    [
                        'id' => 'filter_status_periksa',
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Status--')
                    ]
                )
            )) . '\'
        ],
    ], {
        2:0,
        3:1,
        4:2,
        18:3,
        17:4,
        19:5,
        20:7,
    }, true);

    dateRangeHelper(".startDate",".endDate",".targetDate");
    $(".data-reset").on("click", function(){
        $(".startDate").val("");
        $(".endDate").val("");
    })
});

$(document).ready(function(){
    $(".startDate").val("");
    $(".endDate").val("");
    $(".data-payment").prop("disabled",true);
});

$(document).on("click", ".data-reset", function() {
    $(".startDate").val("");
    $(".endDate").val("");
    $("#plafon-bpjs").prop("disabled", true);
});

$(".legend-information").css("cursor", "pointer");
$(document).on("click", ".ket-pasien-titipan", function() {
    var isPasienTitipan = false
    const tableElement = $(`#table-informasi`).DataTable()
    if ($(this).attr("selected-filter") == "true") {
        $(this).css("border", "1px solid #dddddd");
        $(this).attr("selected-filter", false);
        isPasienTitipan = false
    } else {
        $(this).css("border", "2px solid #2ca38b");
        $(".ket-pasien-titipan").attr("selected-filter", true);
        isPasienTitipan = true
    }
    tableElement.ajax.url(baseUrl+"kasir/inf-pasien-belum-bayar/get-data?is_pasientitipan=" + isPasienTitipan).load()
});


$(".legend-information").each(function (params) {
    var _id = $(this).attr("id");
    var isLegendTitipan = $(this).hasClass("ket-pasien-titipan");
    if(!isLegendTitipan) {
        $(document).on("click", "#" + _id, function () {
            const tableElement = $(`#table-informasi`).DataTable()
            $(".legend-information").css("border", "1px solid #dddddd");
            if($(this).attr("selected-filter") == "true"){
                _id = 0;
                $(this).attr("selected-filter", false);
            }else{
                $("#" + _id).css("border", "2px solid #2ca38b");
                $(".legend-information").attr("selected-filter", false);
                $(this).attr("selected-filter", true);
            }
            tableElement.ajax.url(baseUrl+"kasir/inf-pasien-belum-bayar/get-data?caraBayar=" + _id).load()
        })
    }
})

$(document).on("click", "#table-informasi tbody tr", function(){
    var pendaftaran_id = null;
    var instalasi_id = null;
    var no_sep = null;
    var pasienpulang_id = null;
    var is_stopakomodasi = null
    var _is_close_bill = false;
    var status_approve = null;
    var groupcarabayar_id = null;

    try {
        pendaftaran_id = table.row(".selected").data().pendaftaran_id;
        uid = table.row(".selected").data().primary;
        instalasi_id = table.row(".selected").data().instalasi_id;
        no_sep = table.row(".selected").data().no_sep;
        pasienpulang_id = table.row(".selected").data().pasienpulang_id;
        is_stopakomodasi = table.row(".selected").data().is_stopakomodasi;
        _is_close_bill = table.row(".selected").data().is_close_bill
        status_approve = table.row(".selected").data().status_approve_id
        groupcarabayar_id = table.row(".selected").data().groupcarabayar_id
    }
    catch(e) {
        pendaftaran_id = null;
        instalasi_id = null;
        no_sep = null;
        pasienpulang_id = null;
        is_stopakomodasi = null
        _is_close_bill = false;
        groupcarabayar_id = null;
    }

    if(pendaftaran_id) {
        $("#cetak-rincian-tagihan").attr("data-target",cetakRincian+pendaftaran_id+"&instalasi_id="+instalasi_id);
        $(".data-payment").prop("disabled",false)
        if (groupcarabayar_id == groupBpjs && instalasi_id != instalasiRanap) {
            $("#plafon-bpjs").prop("disabled", false);
            $("#plafon-bpjs").attr(
                "action",
                "/kasir/inf-pasien-pulang/modal-plafon?pendaftaran_id=" +
                pendaftaran_id
            );
        }
        else {
            $("#plafon-bpjs").prop("disabled", true);
        }
        
    } else {
        $(".data-payment").prop("disabled",true)
        $("#export-excel-detail").prop("disabled", false);
        $("#plafon-bpjs").prop("disabled", true);
    }
    if((no_sep != null && pasienpulang_id != null) || (no_sep != null && is_stopakomodasi == true) ) {
        $("#detail-invoice-inacbgs").prop("disabled", false);
    }
    else {
        $("#detail-invoice-inacbgs").prop("disabled", true);
    }
    
    if (no_sep != null && no_sep != "") {
        $("#btn-print-sep").attr("disabled", false);
    } else {
        $("#btn-print-sep").attr("disabled", true);
    }

    if(status_approve != null) {
        if(status_approve == 1323) {
            $("#btn-edit-tagihan").attr("disabled", true);
        }
    }
});

$("#check_nominal").on("change", function(){
    if(this.checked) {
        $("#is_kelola_tagihan").val(this.checked);        
    }
});

$("#detail-invoice-inacbgs").click(function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    var primary = null;

    if(typeof tableData !== "undefined") {
        var primary = tableData.primary;
        var url = "/kasir/pembayaran-tagihan/cetak-detail-invoice-inacbg?id=" + primary;
        window.open(url);

    }
});

$("#cetak-detail-rincian-tagihan-designer").click(function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    var primary = null;
    if(typeof tableData !== "undefined") {
        var pendaftaran_id = tableData.pendaftaran_id;
        var instalasi_id = tableData.instalasi_id;
        var url = "/kasir/inf-pasien-belum-bayar/show-popup?id=" + pendaftaran_id+"&instalasi_id="+instalasi_id;
        window.open(url);
    }
});

$("#export-excel-detail").click(function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    if(typeof tableData == "undefined") {
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        return false;
    }
})

$(document).on("click","#btn-print-sep",function(e){
    e.preventDefault();
    var tableData = table.row(".selected").data();
    if (typeof tableData !== "undefined") {
        var _data = table.rows(".selected").data();
        var total_checklist = _data.length;

        if (total_checklist > 1) {
            docoNotification("warning", "Terjadi Kesalahan", "Data yang di pilih lebih dari 1");
        } else {
            let target = $(this).attr("data-target");
            let dataJenis = tableData.jenis;
            let ruangan_id = null; // buat ngebedain ranap dan igd dari ruangan id  = ws_ranap
            if (_data[0].pasienadmisi_id != null) {
                ruangan_id = WS_RANAP;
            }
            
            window.open(target+"ruangan_id="+ruangan_id+"&pendaftaran_id="+_data[0].primary);
        }

    }else{
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
    }
});

function getBulkAdmin(payload){
    $().docoForm("click", {
        url: "/kasir/inf-pasien-belum-bayar/get-bulk-biaya-admin",
        type: "POST",
        skipConfirm: true,
        skipSuccessNotif: true,
        data: {
            payload : payload,
        },
        success: function (data) {
            let response = data.response.data;
            for (const [key, value] of Object.entries(response)) {
                let tagihan =  parseFloat(docoHelper.convertToAngka($("#"+key).text()))
                let uangMuka =  parseFloat(docoHelper.convertToAngka($("#um-"+key).text()))
                let sisaTagihan =  parseFloat(docoHelper.convertToAngka($("#st-"+key).text()))
                let limitPenjamin =  parseFloat(docoHelper.convertToAngka($("#lt-"+key).text()))
                tagihan = tagihan + parseFloat(value);
                sisaTagihan = (sisaTagihan + parseFloat(value)) - (uangMuka + limitPenjamin);
                $("#"+key).text(docoHelper.convertToRupiah(tagihan))
                $("#st-"+key).text(docoHelper.convertToRupiah(sisaTagihan))

                if(key === response.length) {
                    hideLoader();
                }
            }
        },
    })
}

function showLegend(isRawatInap){
    if(isRawatInap == "yes") {
        $("._legend").show();
    } else {
        $("._legend").hide();
    }
}
',View::POS_END,'b-index');
