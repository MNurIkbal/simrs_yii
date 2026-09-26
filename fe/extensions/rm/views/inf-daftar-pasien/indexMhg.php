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
                    'search',
                    'reset',
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
                            'data-conditions' => 'pasien_id,instalasi_id',
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
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
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
                            <th><?=\Yii::t("fe", "Status Monitoring");?></th>
                            <th><?=\Yii::t("fe", "Jenis Reservasi");?></th>
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
<?php
$this->registerJs('
var table;
$("#btn-proses").attr("disabled", true);
// $(document).on("click", ".data-reload", function () {
//     $(".advancedFilter [type=reset]").click();
//     table.draw();
// });

$(document).ready(function () {
    // Generate Table
    table = $("#table-inf-daftar-pasien").docoTabel({
        filter: true,
        order: [],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0
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
            style: "os",
            selector: "tr"
        },
        ajax: baseUrl + "rm/inf-daftar-pasien/get-data",
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
                name: "instalasi_id",
                searchable: false,
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
                searchable: false,
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
                title: "Status Monitoring",
                data: "status_rekam_medik_nama",
                searchable: false
            },
            {
                title: "Jenis Reservasi",
                data: "jenis_reservasi_nama"
            },
            {
                title: "Status Monitoring",
                data: "status_rekam_medik_id",
                visible: false
            },
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.status_konfirmasirm_id == "664") {
                $(row).addClass("row-jingga");
            } else {
                $(row).removeClass("row-jingga");
            }
        }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ],
            [
                9,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_konfirmasirm_id', '',
                        [664 => 'Belum Proses', 665 => 'Proses'],
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Status --')
                        ]
                    )
                )).'</div>\'
            ],
            [
                12,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenis_reservasi_nama', '', $jenisReservasi,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                )).'</div>\'
            ],
            [
                13,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_rekam_medik_id', '', $statusMonitoringList,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Status Monitoring --')
                        ]
                    )
                )).'</div>\'
            ],
            
        ]
    );

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $(document).on("click", "#table-inf-daftar-pasien tr", function () {
        var _data = table.row(".selected").data();
        if (typeof _data !== "undefined") {
            if (_data.status_rekam_medik_id == "1024" && _data.status_konfirmasirm_id == "664") {
                $("#btn-proses").attr("disabled", false);
            } else {
                $("#btn-proses").attr("disabled", true);
            }
        }
    });
    $(document).on("click", "#btn-proses", function () {
        var tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined" && "pendaftaran_id" in tableData) {
            $(this).docoForm("click", {
                url: "/rm/inf-daftar-pasien/update-proses?id=" + tableData.pendaftaran_id,
                title: "Sukses",
                method: "POST",
                type: "json",
                success: function () {
                    table.draw();
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
                notif();
                table.draw();
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
});
',View::POS_END,'b-index');