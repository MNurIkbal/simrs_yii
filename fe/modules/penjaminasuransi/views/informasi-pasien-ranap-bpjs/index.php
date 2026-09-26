<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
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

    .belum-koreksi {
        background-color: #ffcccc !important;
        color: #484646;
    }

    .sudah-koreksi {
        background-color: #ffcc99 !important;
        color: #484646;
    }

    .proses-klaim {
        background-color: #c2e6f8 !important;
        color: #484646;
    }

    .final-klaim {
        background-color: #b5e4b5 !important;
        color: #484646;
    }

    .belum-ada-sep {
        background-color: #ff7f00 !important;
        color: #484646;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                            'data-table-id' => 'example',
                            'data-options' => 'click',
                            'id' => "search-button",
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-table-id' => 'example',
                            'data-options' => 'click',
                        ]
                    ],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-folder',
                        'attributes' => [
                            'id' => 'btn-proses',
                            'data-target' => Url::to(['proses', 'id' => '']),
                            'data-conditions' => 'state',
                            'data-options' => 'click',
                        ],
                    ],
                    'unduh' => [
                        'type' => 'button',
                        'icon' => 'fa fa-file-pdf-o',
                        'title' => \Yii::t('fe', 'Unduh dokumen'),
                        'attributes' => [
                            'class' => 'hidden',
                            'id' => 'btn-unduh-dokumen',
                            'data-options' => 'click'
                        ],
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/export-pdf?',
                            'class' => 'hidden'
                        ],
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/export-excel?',
                            'class' => 'hidden'
                        ],
                    ],
                    'cetak-sep' => [
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
                    ],
                    'sync' => [
                        'title' => \Yii::t('fe', 'Sinkronkan Semua Pasien'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'id' => 'btn-sync',
                            'data-options' => 'click',
                            'style' => 'float: right',
                            'class' => 'hidden'
                        ],
                    ],
                    'add' => [
                        'title' => \Yii::t('fe', 'Sinkronkan Pasien'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/single-sync',
                            'style' => 'float: right'
                        ],
                    ],
                    'sync-all-process' => [
                        'title' => \Yii::t('fe', 'Sinkronkan Semua Pasien'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/sync-all',
                            'style' => 'float: right',
                            'class' => 'hidden'
                        ],
                    ],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/penjamin-asuransi/informasi-pasien-ranap-bpjs/show-popup-excel?',
                            // 'data-conditions' => 'no_pendaftaran'
                        ]
                    ],
                    'cetak-berkas' => [
                        'type' => 'button',
                        'title' => 'Cetak Berkas Klaim',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'cetak-berkas',
                            'data-options' => 'click',
                        ]
                    ],
                ], '#example'); ?>
                <button type="button" id="btn-cek-unduh-dokumen" class="btn btn-labeled btn-info btn-xs" data-width="80%" data-href="/penjamin-asuransi/informasi-pasien-ranap-bpjs/cek-dokumen">
                    <b><i class='fa fa-list'></i></b> Cek Dokumen
                </button>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#ffcccc;'>Belum Koreksi</span></li>
                                    <li><span style='background:#ffcc99'>Sudah Koreksi</span></li>
                                    <li><span style='background:#c2e6f8;'>Proses Klaim</span></li>
                                    <li><span style='background:#b5e4b5;'>Final Klaim</span></li>
                                    <li><span style='background:#ff7f00;'>No. Sep belum dibuat</span></li>
                                    <li><span style='background:#bcbcbc;'>Proses Unduh Dokumen</span></li>
                                    <li><span style='background:#f29ef5;'>Selesai Unduh Dokumen</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"><input type="checkbox" id="check-all"></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Masuk"); ?></th>
                            <th><?= \Yii::t("fe", "Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi / Ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar/Penjamin"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter Penanggung Jawab"); ?></th>
                            <th><?= \Yii::t("fe", "Total Tagihan RS"); ?></th>
                            <th><?= \Yii::t("fe", "Total Klaim BPJS"); ?></th>
                            <th class="text-center"><?= \Yii::t("fe", "Status"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div id="modal_progress" class="modal fade" style="z-index:1065;" data-backdrop="static">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title">Sinkronisasi Data</h5>
                            </div>
                            <hr>
                            <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                            <div class="modal-body">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span class="label-persentase"></span>%
                                    </div>
                                </div>
                                <span class="help-block label-progress"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs('
   var table;
   var id = "";
   var state = "";
   var admisi = "";
   var belum_unduhDokumen = '.DocoConstants::BELUM_UNDUH_DOKUMEN.';
   var onProgres_unduhDokumen = '.DocoConstants::ON_PROGRES_UNDUH_DOKUMEN.';
   var selesai_unduhDokumen = '.DocoConstants::SELESAI_UNDUH_DOKUMN.';
   var instalasiList = ' . json_encode($instalasi) . ';
   var statusList = ' . json_encode($status) . ';
   var optionStatus = '.json_encode($instalasi).';
   var validationDataUnduh = '.json_encode($validasiUnduh).';
   var validasiCutoff = "'. $validasiCutoff.'";
   var hakAccess = "'.$hasAccess.'";
   var instalasiOptions = [];
   var statusOptions = [];
   var instalasiStatus = [];
   var WS_RANAP = '.DocoConstants::WS_RANAP.';
   var SINGKATAN_RI = "'.DocoConstants::SINGKATAN_RI.'";
   $.each(optionStatus, function (index, value) {
    instalasiStatus.push({
            id: index,
            text: value,
        });
    });

    $.each(statusList, function (index, value) {
    statusOptions.push({
             id: index,
             text: value,
         });
     });


    if(hakAccess == true){
        $("#btn-unduh-dokumen").removeClass("hidden")
    }else{
        $("#btn-unduh-dokumen").addClass("hidden")
    }

    $(document).on("click", ".reset-button", function(e){
        e.preventDefault();
        $(".dateStart2").val(moment().locale("en").format("DD-MMM-YYYY")).trigger("change");
        $(".dateEnd2").val(moment().locale("en").format("DD-MMM-YYYY")).trigger("change");
        return false;
    });

    $("#btn-cek-unduh-dokumen").bind("click", () => {
        pendaftaran = table.row(".selected").data().pendaftaran_id;
        const { href, width } = $("#btn-cek-unduh-dokumen").data()
        showLoader("Memuat Halaman...")
        $("#modal_riwayat").find(".modal-dialog").css("width", width)
        $("#modal_riwayat .modal-content").docoLoad({
            url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/cek-dokumen?id="+pendaftaran,
            dataType: "html",
            success: function (data) {
                hideLoader()
                $("#modal_riwayat .modal-content").parents(".modal").modal("show")
                $("#modal_backdrop").css("display", "none")
            },
            error: function () {
                hideLoader()
            }
        })
    });

    $(document).on("click", "#btn-proses", function(e){
        e.preventDefault();
        no_sep = table.row(".selected").data().nosep;
        no_pendaftaran = table.row(".selected").data().no_pendaftaran;
        let status = table.row(".selected").data().status
        if(no_sep == "" || no_sep == null) {
            docoNotification("error", "Proses Gagal.", "Data ini belum memiliki No.SEP.")
            return false;
        }

        if(status.toLowerCase() == "sudah koreksi") {
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=${id}&admisi=${admisi}&state=${state}`);
            return false
        }

        if(status.toLowerCase() == "proses klaim") {
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=${id}&admisi=${admisi}&state=${state}`);
            return false
        }
            
        if(status.toLowerCase() == "final klaim") {
            let _data = table.row(".selected").data();
            let tanggal_klaim = _data.tanggal_klaim
            if (tanggal_klaim != null && _data.status_kunjungan == 551) {
                let today = new Date(tanggal_klaim);
                let cutoffDate = new Date(validasiCutoff);
                if ( today < cutoffDate ) {
                    docoNotification("error", "Proses Gagal.", "Klaim sudah final dan berada di luar periode aktif. Data tidak dapat diakses.")
                    return false;
                }
            }
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=${id}&admisi=${admisi}&state=${state}`);
        }else{
            window.open(baseUrl + `penjamin-asuransi/informasi-pasien-ranap-bpjs/eklaim?id=${id}&admisi=${admisi}&state=${state}`);
        }

        return false;
    });
    
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
                let ruangan_id = null; // buat ngebedain ranap dan igd dari ruangan id = ws_ranap
                if (_data[0].instalasi_kode == SINGKATAN_RI) {
                    ruangan_id = WS_RANAP;
                }
                
                window.open(target+"ruangan_id="+ruangan_id+"&pendaftaran_id="+_data[0].pendaftaran_id_encrypted);
            }

        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

    $(document).on("click", "#cetak-berkas", function(e){
        e.preventDefault();
        no_sep = table.row(".selected").data().nosep;
        no_pendaftaran = table.row(".selected").data().no_pendaftaran;
        let status = table.row(".selected").data().status
        if(status.toLowerCase() != "final klaim") {
            docoNotification("error", "Proses Gagal.", "Status pada data ini belum Final Klaim.")
            return false;
        }

        window.open(baseUrl + `penjamin-asuransi/informasi-pasien-ranap-bpjs/cetak-klaim?sep=${no_sep}`);
        return false;
    });
    

    $(document).on("click", "#example tbody tr", function () {
        var status_verifikasi = null;
        if(table.row(".selected").length){
            let data = table.row(".selected").data()
            state = table.row(".selected").data().state;
            id = table.row(".selected").data().primary;
            status_verifikasi = table.row(".selected").data().status_kunjungan;
            admisi = table.row(".selected").data().admisi;
            
            if (data.nosep != null && data.nosep != "" && data.bpjs_id != null && data.bpjs_id != "") {
                $("#btn-print-sep").attr("disabled", false);
            } else {
                $("#btn-print-sep").attr("disabled", true);
            }
        } else {
            $("#btn-print-sep").attr("disabled", true);
        }
        
        if (!status_verifikasi && status_verifikasi !== 0) {
            $("#btn-proses").attr("disabled", true)
        }else{
            $("#btn-proses").attr("disabled", false);
        }
    })

   ', View::POS_END, "b-index");


$this->registerJs($this->render('index.js'), View::POS_END);

?>