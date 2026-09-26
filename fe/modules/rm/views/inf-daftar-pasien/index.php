<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rm'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<audio id="playerNewData" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-cari',
                            'data-table-id' => 'table-inf-daftar-pasien',
                            'data-options' => 'click',
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                            'data-table-id' => 'table-inf-daftar-pasien',
                            'data-options' => 'click',
                        ]
                    ],
                    // 'muat-ulang' => [
                    //     'type' => 'button',
                    //     'title' => \Yii::t('fe', 'Muat Ulang'),
                    //     'icon' => 'fa fa-refresh',
                    //     'attributes'=>[
                    //         'id' => 'btn-print-voucher-berkas',
                    //         'class' => 'data-reload',
                    //         'data-options' => 'click'
                    //     ],
                    // ],
                    'proses' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-check',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-proses',
                            'data-options'=>'click'
                        ]
                    ],
                    'print-gelang-pasien'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Gelang Pasien'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                            'id'=>'btn-print-gelang-pasien',
                            'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien?pendaftaran_id=',
                            'data-pages'=>'_blank',
                        ],
                    ],
                    // 'print-label-pasien'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'Print Label Pasien'),
                    //     'icon' => 'fa fa-print',
                    //     'method' => 'not-exist',
                    //     'attributes'=>[
                    //         'id'=>'btn-print-label-pasien',
                    //         'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien?pendaftaran_id=',
                    //         'data-pages'=>'_blank',
                    //     ],
                    // ],
                    'print-label-pasien-multiple' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Label Pasien Multiple'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-label-pasien',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '30%',
                            'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/pilih-jumlah-cetakan?pendaftaran_id=',
                            'data-conditions' => 'pasien_id,no_pendaftaran'
                        ]
                    ],
                    'print-tracer'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Tracer'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                            'id'=>'btn-print-ltracer-pasien',
                            'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-status-pasien?pendaftaran_id=',
                            'data-conditions' => 'pasien_id,instalasi_id,no_pendaftaran',
                            'data-pages'=>'_blank',
                        ],
                    ],'print-voucher'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Voucher Berkas'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                            'id'=>'btn-print-voucher-berkas',
                            'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-voucher-berkas?pendaftaran_id=',
                            'data-conditions' => 'pasien_id,instalasi_id',
                            'data-pages'=>'_blank',
                        ],
                    ],
                    'print-voucher'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Voucher Berkas'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                            'id'=>'btn-print-voucher-berkas',
                            'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-voucher-berkas?pendaftaran_id=',
                            'data-conditions' => 'pasien_id,instalasi_id',
                            'data-pages'=>'_blank',
                        ],
                    ],
                    'print-belum-proses'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Print Pasien Belum Proses'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes'=>[
                            'id'=>'btn-print-belum-proses',
                            'data-target'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-belum-proses?pendaftaran_id=',
                            'data-pages'=>'_blank',
                            'data-options'=> 'pdf',
                        ],
                    ],
                ], '#table-inf-daftar-pasien');?>
            </div>
            <div class="panel-body">
                <div class="table-wrapper table-scroll-x">
                    <table id="table-inf-daftar-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                                <th><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                                <th><?=\Yii::t("fe", "Status Pasien");?></th>
                                <th><?=\Yii::t("fe", "No. RM - Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar - Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Dokter");?></th>
                                <th><?=\Yii::t("fe", "Status");?></th>
                                <th><?=\Yii::t("fe", "No. SEP");?></th>
                                <th><?=\Yii::t("fe", "No. Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Jenis Pendaftaran");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
var table;
var reportEngineStatus = parseInt(`'.($reportEngine ? 1 : 0).'`)
$("#btn-proses").attr("disabled", true);
$("#btn-print-gelang-pasien").attr("disabled", true);
$("#btn-print-voucher-berkas").attr("disabled", true);
// $(document).on("click", ".data-reload", function () {
//     $(".advancedFilter [type=reset]").click();
//     table.draw();
// });
let _extendedParams = {
    columnDefs: [ 
        {
            orderable: false,
            className: "select-checkbox",
            targets: 0,
        }, {
            targets: 5,
            render: function (data, type, row) {
                return row["no_rekam_medik"] + " - " + row["nama_pasien"];
            }
        }, {
            targets: 6,
            render: function (data, type, row) {
                return row["instalasi_nama"] + " - " + row["ruangan_nama"];
            }
        }, {
            targets: 7,
            render: function (data, type, row) {
                return row["carabayar_nama"] + " - " + row["penjamin_nama"];
            }
        }
    ],
    select: {
        style: "os",
    }
}

if ( !reportEngineStatus ) { // when report engine is disabled, then user can use multiple checklist
    _extendedParams = {
        columnDefs: [ 
            {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true,
                    selectAllPages: false
                }
            }, {
                targets: 5,
                render: function (data, type, row) {
                    return row["no_rekam_medik"] + " - " + row["nama_pasien"];
                }
            }, {
                targets: 6,
                render: function (data, type, row) {
                    return row["instalasi_nama"] + " - " + row["ruangan_nama"];
                }
            }, {
                targets: 7,
                render: function (data, type, row) {
                    return row["carabayar_nama"] + " - " + row["penjamin_nama"];
                }
            }
        ],
        select: {
            style: "multi",
        }
    }
}

var listHistoryRegist = JSON.parse(localStorage.getItem("list-history-regist")) || []

$(document).ready(function () {
    moment.locale("en");
    // Generate Table
    table = $("#table-inf-daftar-pasien").docoTabel($.extend({
        filter: false,
        order: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0,
            checkboxes: {
                selectRow: true,
                selectAllPages: false
            }
        }, {
            targets: 5,
            render: function (data, type, row) {
                return row["no_rekam_medik"] + " - " + row["nama_pasien"];
            }
        }, {
            targets: 6,
            render: function (data, type, row) {
                return row["instalasi_nama"] + " - " + row["ruangan_nama"];
            }
        }, {
            targets: 7,
            render: function (data, type, row) {
                return row["carabayar_nama"] + " - " + row["penjamin_nama"];
            }
        }],
        select: {
            style: "multi",
        },
        ajax: {
           url:  baseUrl + "rm/inf-daftar-pasien/get-data",
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                width: "50px",
                defaultContent: ""
            },
            {
                data: "rowNum",
                name: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Kunjungan",
                data: "tgl_pendaftaran"
            },
            {
                title: "No Pendaftaran",
                data: "no_pendaftaran", searchable: false
            },
            {
                title: "Status Pasien",
                data: "status_pasien_nama", searchable: false
            },
            {
                title: "No. RM - Nama Pasien",
                data: "no_rekam_medik",
                searchable: false
            },
            {
                title: "Instalasi - Ruangan",
                data: "instalasi_nama",
                searchable: true,
            },
            {
                title: "Cara Bayar - Penjamin",
                data: "carabayar_nama",
                name: "carabayar_id",
                searchable: false,
            },
            {
                title: "Dokter",
                data: "nama_pegawai",
                searchable: true,
            },
            {
                title: "Status",
                data: "status_konfirmasirm",
            },
            {
                title: "No. SEP",
                data: "nosep",
                searchable: false,
            },
            {
                title: "No. Rekam Medik",
                data: "no_rekam_medik",
                searchable: true,
                visible: false,
            },
            {
                title: "Jenis Pendaftaran",
                data: "jenis_reservasi_nama",
                searchable: true,
                visible: true,
            },
            {
                title: "Ruangan",
                data: "ruangan_nama",
                searchable: true,
                visible: false,
            }
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.status_konfirmasirm_id == "664") {
                $(row).addClass("row-jingga");
            } else {
                $(row).removeClass("row-jingga");
            }
        },
        formFilters: [
            {
                fieldName: "tgl_pendaftaran",
                label: "Tanggal Pendaftaran",
                type: {
                   name: "rangeDate",
                }
            },
            {
               fieldName: "status_konfirmasirm",
               label: "Status",
               type: {
                  name: "dropdownScroll",
                  url: "/rm/inf-daftar-pasien/filters",
                  additionalPayload: {
                     type: "status_rm",
                  }
               }
            },
            {
               fieldName: "no_rekam_medik",
               label: "No.Rekam Medik"
            },
            {
               fieldName: "pegawai_id",
               label: "Dokter",
               type: {
                  name: "dropdownScroll",
                  url: "/rm/inf-daftar-pasien/filters",
                  additionalPayload: {
                     type: "dokter",
                  }
               }
            },
            {
               fieldName: "instalasi_id",
               label: "Instalasi",
               type: {
                  name: "dropdownScroll",
                  url: "/rm/inf-daftar-pasien/filters",
                  additionalPayload: {
                     type: "instalasi",
                  }
               }
            },
            {
               fieldName: "ruangan_id",
               label: "Ruangan",
               type: {
                  name: "dropdownScroll",
                  url: "/rm/inf-daftar-pasien/filters",
                  additionalPayload: {
                     type: "ruangan",
                  }
               }
            },
            {
               fieldName: "jenis_reservasi_nama",
               label: "Jenis Pendaftaran",
               type: {
                  name: "dropdownScroll",
                  url: "/rm/inf-daftar-pasien/filters",
                  additionalPayload: {
                     type: "jenis_pendaftaran",
                  }
               }
            },
        ],
        filterRendered: (wrapper) => {
           var defaultPlaceHolder = [
              {
                 id: "",
                 text: "- Semua -"
              }
           ]
           $(wrapper).find("[name=\'instalasi_id\']").bind("change", ({ currentTarget }) => {
              if ($(currentTarget).val() == "" || $(currentTarget).val() == null) {
                 $(wrapper).find("[name=\'ruangan_id\']").html("")
                 $(wrapper).find("[name=\'ruangan_id\']").select2({
                    defaultPlaceHolder,
                 })
              }
              else {
                 $(wrapper).find("[name=\'ruangan_id\']").select2InfinityScroll({
                    url: "/rm/inf-daftar-pasien/filters",
                    callbackData: (params) => {
                       return {
                          term: params.term,
                          page: params.page || 1,
                          limit: params.limit,
                          type: "ruangan",
                          additionalPayload: {
                             instalasi_id: $(currentTarget).val(),
                          }
                       }
                    }
                 });
              }
           });
        },
    }, _extendedParams));
    $("#btn-search__table-inf-daftar-pasien").css("display", "none");
    $("#btn-reset__table-inf-daftar-pasien").css("display", "none");

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(document).on("click", "#table-inf-daftar-pasien tr", function () {
        var _data = table.rows(".selected").data();

        var dataPendaftaran = [];
        for(var i = 0; i < _data.length; i++) {
            if(_data[i][\'status_konfirmasirm_id\'] == "664") {
                dataPendaftaran.push(_data[i][\'pendaftaran_id\']);
            }
        }

        if (typeof _data !== "undefined" && (_data.length >= 1)) {
            if(_data.length == 1) {
                $("#btn-print-gelang-pasien").attr("disabled", false);
                $("#btn-print-voucher-berkas").attr("disabled", false);
            } else {
                $("#btn-print-gelang-pasien").attr("disabled", true);
                $("#btn-print-voucher-berkas").attr("disabled", true);
            }

            if (dataPendaftaran.length > 0) {
                $("#btn-proses").attr("disabled", false);
            } else {
                $("#btn-proses").attr("disabled", true);
            }
        } else {
            $("#btn-proses").attr("disabled", true);
            $("#btn-print-gelang-pasien").attr("disabled", true);
            $("#btn-print-voucher-berkas").attr("disabled", true);
        }
    });

    $(document).on("click", "#btn-proses", function () {
        var tableData = table.rows(".selected").data();
        if (typeof tableData !== "undefined" && (tableData.length >= 1)) {
            var dataPendaftaran = [];
            for(var i = 0; i < tableData.length; i++) {
                dataPendaftaran.push(tableData[i][\'pendaftaran_id\']);
            }

            $(this).docoForm("click", {
                url: "/rm/inf-daftar-pasien/update-proses?id=" + dataPendaftaran.join(),
                title: "Sukses",
                method: "POST",
                type: "json",
                success: function () {
                    table.draw(false);
                }
            });
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih");
        }
    });

    if (typeof (EventSource) !== "undefined") {
        var evtSource = new EventSource("/rm/inf-daftar-pasien/stream-pendaftaran");

        evtSource.onmessage = function (e) {
            var statusdaftar = JSON.parse(e.data);
            if (statusdaftar.is_new_registration) {
                
                if(statusdaftar.is_print_automatic) {
                    var pendaftarans = statusdaftar.pendaftaran;
                    var async_request=[];
                    var responseprint_success = [];
                    var responseprint_error = [];
                    for(i in pendaftarans){
                        if (!listHistoryRegist.includes(pendaftarans[i].pendaftaran_id)) {
                            notif();
                            table.draw();
                            listHistoryRegist.push(pendaftarans[i].pendaftaran_id)
                            $.ajax({
                                url: "http://localhost:"+statusdaftar.autoprint_port+"/auto-print-tracer",
                                method: "POST",
                                contentType: "application/json",
                                dataType: "json",
                                headers: {
                                    "dh-user-agent": navigator.userAgent,
                                    "dh-cookie": statusdaftar.cookie_value
                                },
                                data: JSON.stringify({
                                    pasien_id: pendaftarans[i].pasien_id,
                                    pendaftaran_id: pendaftarans[i].pendaftaran_id,
                                    nosep: pendaftarans[i].nosep
                                }),
                                success: function(res) {
                                    docoNotification("success", "Harap Tunggu", res.message);
                                },
                                error: function(err) {
                                    docoNotification("error", "Gagal Print Tracer", err.responseJSON.message);
                                }
                            });
                        }
                    }
                    console.log(listHistoryRegist)
                    localStorage.setItem("list-history-regist",JSON.stringify(listHistoryRegist));
                } else {
                    notif();
                    table.draw();
                }
            }
        };

    } else {
        // document.getElementById("result").innerHTML = "Sorry, your browser does not support server-sent events...";
    }

    function notif() {
        var player = $("#playerNewData");

        player[0].defaultPlaybackRate = 1;
        docoHelper.getDataFromAction("/rm/inf-daftar-pasien/get-sound-file", function (response) {
            if (response.data != null) {
                player[0].src = response.data;
                docoHelper.repeatPlayAudio(player[0], 2);
            } else {
                console.warn(response.errorThrown);
            }
        });
    }

    $(document).on("click", ".btn-reset", function (e) {
       const tableId = "table-inf-daftar-pasien";
       const element = $(`#filter-section__${tableId}`)
       const formWrapper = $(`#form-filter__${tableId}`)
       element.find("input").val("")
       element.find("select").val(null).trigger("change")
       element.find("#tgl_pendaftaran-startDate").val(moment().format("DD-MMM-YYYY")).trigger("change");
       element.find("#tgl_pendaftaran-endDate").val(moment().format("DD-MMM-YYYY")).trigger("change");
       const tableElement = $(`#${tableId}`).DataTable()
       showLoader()
       tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
       tableElement.ajax.url("/rm/inf-daftar-pasien/get-data").load()
    });

    $(document).on("click", ".btn-cari", ({ currentTarget }) => {
      const tableId = $(currentTarget).data("table-id");
      const formWrapper = $(`#form-filter__${tableId}`);
      const tableElement = $(`#${tableId}`).DataTable();

      showLoader();
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url("/rm/inf-daftar-pasien/get-data").load()
    });

    $(document).on("click", "#btn-print-ltracer-pasien", function(e) {
        e.preventDefault();
        
        var tableData = table.rows(".selected").data();
        if (typeof tableData !== "undefined" && (tableData.length >= 1)) {
            var dataPendaftaran = [];
            for(var i = 0; i < tableData.length; i++) {
                dataPendaftaran.push(tableData[i][\'pendaftaran_id\']);
            }

            $.ajax({
                url: "/rm/inf-daftar-pasien/update-proses?id=" + dataPendaftaran.join(),
                method: "POST",
                type: "json",
                success: function (data) {
                    if(data.response.message == "Gagal") {
                        docoNotification("warning", "Terjadi Kesalahan", "Terjadi kesalahan pada server");
                    } else {
                        table.draw(false);
                    }
                }, 
                error: function() {
                    docoNotification("warning", "Terjadi Kesalahan", "Terjadi kesalahan pada server");
                }
            });
        }
    });

    $(document).on("click", table, function(){
       var tableData = table.row(".selected").data();
       if(typeof tableData !== "undefined") 
       {
          var _id = tableData.pendaftaran_id;
          var _instalasi_id = tableData.instalasi_id;

          var target_print = "'. Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-belum-proses?pendaftaran_id=";
          var url_proses = target_print + _id + "&";
          $("#btn-print-belum-proses").attr("data-target", url_proses);
       } else {
          var target_print = "'. Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-belum-proses?pendaftaran_id=";
          var url_proses = target_print + "&";
          $("#btn-print-belum-proses").attr("data-target", url_proses);
       }
    })
});
',View::POS_END,'b-index');