<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
$ruangan_title = (\Yii::t("fe", "Poliklinik"));
$kelasPelayanan = \Yii::t("fe", "Kelas pelayanan");
$ruangan_filter_state = 'false';
$visibleIns = false;
if ($jenis == 'ranap') {
    $ruangan_title = (\Yii::t("fe", "Ruangan")) . '-' . (\Yii::t("fe", "Kamar"));
    $kelasPelayanan = (\Yii::t("fe", "Kelas Pelayanan"));
    $ruangan_filter_state = 'true';
} else if ($jenis == 'penunjang') {
    $ruangan_title = (\Yii::t("fe", "Rujukan"));
    $visibleIns = true;
}
?>
<style type="text/css">
    .row-nosep {
        background-color: #ff7f00 !important;
        color: #FFFFFF;
        font-weight: bold;
    }
</style>
<style>
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
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
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

    .info-sync {
        font-size: 19px;
        font-weight: bold;
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
                    <div class="column-2" style="width: 40% !important">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                    <div class="column-2" style="float: right; width: 9% !important">
                        <button id="re-sync" type="button" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click"><b><i class="fa fa-refresh"></i></b><?=Yii::t('fe', 'Sinkron Ulang')?></button>
                    </div>
                    <div class="column-2" style="float: right; width: 20% !important">
                        <p id="status-sync" class="info-sync"><?= $countSyncData ?> Data Butuh Di Sinkron</p>
                    </div>
                </div>
                <!-- end -->

                <?= Html::hiddenInput('jenis', $jenis, ['id' => 'hide-jenis']); ?>
            </div>


            <div class="panel-toolbar clearfix">
                <?php
                $urlbatal = Url::home() . 'pendaftaran/informasi-pasien/confirm-batal?jenis=' . $jenis . '&no_pendaftaran=';
                switch ($jenis) {
                    case 'rajal':
                        echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/indexSty/rajal', 
                        [
                            'jenis' => $jenis,
                            'urlbatal' => $urlbatal,
                        ]);
                        break;
                    case 'igd':
                        echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/indexSty/igd', 
                        [
                            'jenis' => $jenis,
                            'urlbatal' => $urlbatal,
                        ]);
                        break;
                    case 'ranap':
                        $urlbatal = Url::home() . 'pendaftaran/informasi-pasien/confirm-batal-ranap?jenis=' . $jenis . '&no_pendaftaran=';
                        echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/indexSty/ranap', 
                        [
                            'jenis' => $jenis,
                            'urlbatal' => $urlbatal,
                        ]);
                        break;
                    case 'penunjang':
                        echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/indexSty/penunjang', 
                        [
                            'jenis' => $jenis,
                            'urlbatal' => $urlbatal,
                        ]);
                        break;
                    default:
                        echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/indexSty/default', 
                        [
                            'jenis' => $jenis,
                            'urlbatal' => $urlbatal,
                        ]);
                        break;
                }
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row">
                    <?php if ($jenis != 'mcu') : ?>
                        <div class="col-md-5">
                            <div class='my-legend'>
                                <div class='legend-title'>Keterangan</div>
                                <div class='legend-scale'>
                                    <ul class='legend-labels'>
                                        <li><span style='background:#ff7f00;'></span>No SEP Belum di Buat</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- <?php //if (isset($listCaraBayar)) : ?>
                        <div class="col-md-6">
                            <div class='my-legend'>
                                <div class='legend-title'>Keterangan Cara Bayar</div>
                                <div class='legend-scale'>
                                    <ul class='legend-labels'>
                                        <?php
                                        // foreach ($listCaraBayar as $key => $value) {
                                        //     echo "<li><span style='background:" . $value['carabayar_kode_warna'] . "'></span>" . $value['carabayar_nama'] . "</li>";
                                        // }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php //endif; ?> -->
                    <?php if(isset($listCaraBayar)) : ?>
                    <div class="col-md-7">
                        <div class='legend-index'>
                            <div class='legend-header'>Keterangan Cara Bayar</div>
                            <div class="legend-wrapper">
                                <?php
                                foreach ($listCaraBayar as $key => $value) {?>
                                    <div class="legend-information" id="<?= $value['carabayar_id'];?>" data-type="<?= $value['carabayar_id'];?>">
                                        <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna'];?>"></div>
                                        <div class="legend-information__text" ><?= $value['carabayar_nama'];?></div>
                                    </div>
                                    <!-- echo "<li><span style='background:".$value['carabayar_kode_warna']."'></span>".$value['carabayar_nama']."</li>"; -->
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "No Urut"); ?></th>
                            <th><?= \Yii::t("fe", "Tgl pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "No pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Info pasien"); ?></th>
                            <th style="min-width: 300px !important"><?= \Yii::t("fe", "Alamat"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis kelamin"); ?></th>
                            <th><?= $ruangan_title ?></th>
                            <th><?= \Yii::t("fe", "Jenis kasus penyakit"); ?></th>
                            <th><?= $kelasPelayanan ?></th>
                            <th><?= \Yii::t("fe", "Status Kamar"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter Poliklinik"); ?></th>
                            <th><?= \Yii::t("fe", "Info cara bayar"); ?></th>
                            <th><?= \Yii::t("fe", "Status periksa"); ?></th>
                            <th><?= \Yii::t("fe", "No SEP"); ?></th>
                            <th><?= \Yii::t("fe", "Limit Tagihan (Rp.)"); ?></th>
                            <th><?= \Yii::t("fe", "Petugas"); ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar"); ?></th>
                            <th><?= \Yii::t("fe", "Penjamin"); ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    $(document).on("click", ".data-reload", function () {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $("#laporan").docoTabel({
            //add for handle checkbox
            select: {
                style: "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            // scrollY: "40vh",
            ajax: baseUrl+"pendaftaran/informasi-pasien/get-data-informasi?jenis=" + $("#hide-jenis").val(),
            columnDefs:[
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                },
                {
                    targets: [9,10,16],
                    visible: "' . $visible_mcu . '",
                },
                {
                    targets: [11],
                    visible: "' . $visible . '",
                },
                {
                    targets: [16],
                    searchable: "' . $visible_mcu . '",
                },
                {
                    targets: [8],
                    visible: "' . $visibleIns . '",
                },
            ],
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                }, //0
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                }, //1
                {title: "' . (\Yii::t("fe", "Nomor Urut")) . '", data: "no_antrian_poli", searchable: false, visible: false}, //2
                {title: "' . (\Yii::t("fe", "Tanggal pendaftaran")) . '", data: "tgl_pendaftaran"}, //3
                {title: "' . (\Yii::t("fe", "No pendaftaran")) . '", data: "no_pendaftaran", searchable: false}, //4
                {title: "' . (\Yii::t("fe", "Info pasien")) . '", data: "info_pasien", searchable: false}, //5
                {title: "' . (\Yii::t("fe", "Alamat")) . '", data: "alamat_pasien",searchable: false}, //6
                {title: "' . (\Yii::t("fe", "Jenis kelamin")) . '", data: "jenis_kelamin",searchable: false}, //7
                {title: "' . (\Yii::t("fe", "Penunjang Medis")) . '", data: "instalasi_nama", searchable: false}, //8
                {title: "' . $ruangan_title . '", data: "ruangan", name: "ruangan_id",searchable: ' . $ruangan_filter_state . '}, //9
                {title: "' . (\Yii::t("fe", "Jenis kasus penyakit")) . '", data: "jeniskasuspenyakit_nama",searchable: false, visible: false}, //10
                {title: "' . $kelasPelayanan . '", data: "kelaspelayanan_nama",searchable: false}, //11
                {title: "' . Yii::t('fe', 'Status Kamar') . '", data: "status_kamar",searchable: false, visible: false}, //12
                {title: "' . (\Yii::t("fe", "Dokter Utama / Dokter Pengganti")) . '", data: "nama_pegawai",searchable: false}, //13
                {title: "' . (\Yii::t("fe", "Info cara bayar")) . '", data: "carabayar_penjamin", searchable: false}, //14
                {title: "' . (\Yii::t("fe", "Status periksa")) . '", data: "status_periksa", name: "status_periksa_id"}, //15
                {title: "' . (\Yii::t("fe", "No SEP")) . '", data: "nosep", searchable: true}, //16
                {title: "' . (\Yii::t("fe", "Limit Tagihan (Rp.)")) . '", data: "limit_tagihan", searchable: false, visible: false}, //17
                {title: "' . (\Yii::t("fe", "Petugas")) . '", data: "petugas", searchable: true}, //18
                {title: "' . (\Yii::t("fe", "Cara bayar")) . '", data: "carabayar_id", visible: false}, //19
                {title: "' . (\Yii::t("fe", "Penjamin")) . '", data: "penjamin_id", visible: false}, //20
                {title: "' . (\Yii::t("fe", "No pendaftaran")) . '", data: "no_pendaftaran", visible: false}, //21
                {title: "' . (\Yii::t("fe", "No rekam medik")) . '", data: "no_rekam_medik", visible: false}, //22
                {title: "' . (\Yii::t("fe", "Nama pasien")) . '", data: "nama_pasien", visible: false}, //23
            ],
            drawCallback: function(e) {
                var api = this.api();
                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    if(rowData.carabayar_id == 6 && (rowData.nosep == "" || rowData.nosep == null)) {
                        $(rowNode).addClass("row-nosep");
                    }
                    else {
                        $(rowNode).removeClass("row-nosep");
                    }
                }
            },
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 4,
            // },
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.carabayar_kode_warna == null) {
                    var kode_warna_carabayar = \'#ffffff\';
                } else {
                    var kode_warna_carabayar = aData.carabayar_kode_warna; 
                }

                var textColor = convertColor(kode_warna_carabayar, kode_warna_carabayar);

                if ("' . $jenis . '" == "mcu") {
                    $("td:eq(10)", nRow).css("background-color", kode_warna_carabayar);
                    $("td:eq(10)", nRow).css("color", textColor);
                } else if ("' . $jenis . '" == "ranap") {
                    $("td:eq(10)", nRow).css("background-color", kode_warna_carabayar);
                    $("td:eq(10)", nRow).css("color", textColor);
                } else if ("' . $jenis . '" == "penunjang") {
                    $("td:eq(10)", nRow).css("background-color", kode_warna_carabayar);
                    $("td:eq(10)", nRow).css("color", textColor);
                } else {
                    $("td:eq(9)", nRow).css("background-color", kode_warna_carabayar);
                    $("td:eq(9)", nRow).css("color", textColor);
                }
                $("td:eq(6)", nRow).attr("style", "white-space: pre-line;overflow-wrap:break-word");
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [3, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\'
                ],
                [4, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStartOp" class="form-control startDateOp"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishOp" readonly="true" class="form-control endDateOp"/><input type="text" style="display:none" class="targetDateOp" col-index=2></div>\'
                ],
                [
                    9,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'ruangan_id',
                            '',
                            $ruanganList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'ruangan_id',
                                'prompt' => \Yii::t('fe', '--pilih ruangan--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    15,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'status_periksa',
                                '',
                                $statusPeriksaList,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'status_periksa',
                                    'prompt' => \Yii::t('fe', '--pilih--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    19,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'carabayar_id',
                            '',
                            $carabayarList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'carabayar_id',
                                'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    20,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name' => 'penjamin_id',
                                'options' => [
                                    'id' => 'penjamin_id',
                                    'class' => 'select2',
                                ],
                                'pluginOptions' => [
                                    'depends' => ['carabayar_id'],
                                    'placeholder' => \Yii::t('fe', '--pilih penjamin--'),
                                    'url' => Url::to(['list-penjamin'])
                                ]
                            ]
                        )

                    )) . '\'
                ],
                [21, 
                \'<input type="text" id="no_rekam_medik" class="form-control" value="" maxlength="6" />\'
            ],
            ],
            {
                3:0,
                23:1,
                22:2,
                21:3,
                19:4,
                19:5,
                20:6,
                16:7,
                18:8
            }
        );
        dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");
        dateRangeHelper(".startDateOp",".endDateOp",".targetDateOp");   
        
        $(".legend-information").css("cursor", "pointer");
        $(".legend-information").each(function (params) {
            var _id = $(this).attr("id");
            $(document).on("click", "#" + _id, function () {
                var _type = $(this).attr("data-type");
                const tableElement = $("#laporan ").DataTable()
                showLoader();
                $(".legend-information").css("border", "1px solid #dddddd");
                if ($(this).attr("selected-filter") == "true") {
                    $("#carabayar_id").prop("disabled", false);
                    $("#penjamin_id").prop("disabled", true);
                    $("#carabayar_id").val("").trigger("change");
                    $("#penjamin_id").val("").trigger("change");
                    _type = 0;
                    $(this).attr("selected-filter", false);
                } else {
                    $("#" + _id).css("border", "2px solid #2ca38b");
                    $(".legend-information").attr("selected-filter", false);
                    $(this).attr("selected-filter", true);
                    $("#carabayar_id").prop("disabled", true);
                    $("#penjamin_id").prop("disabled", false);
                    $("#carabayar_id").val(_type).trigger("change");

                    var penjaminSelect = $("#penjamin_id");
                    $.ajax({
                        type: "GET",
                        dataType: "JSON",
                        url:  "/pendaftaran/informasi-pasien/list-penjamin?type=" + _type
                    }).then(function (data) {
                        $.each(data.output,function(index,obj)
                        {
                            var option = new Option(obj.name, obj.id, true, true);
                            penjaminSelect.append(option).trigger("change");
                        });
                        $("#penjamin_id").val(null).trigger("change");
                    });
                }
                tableElement.ajax.url("/pendaftaran/informasi-pasien/get-data?jenis="+ $("#hide-jenis").val()+"&type=" + _type).load()
            })
        })
    });

    $(document).on("click", "#laporan tbody tr", function () {

        $("#btn-print-sep").attr("disabled", true);
        $("#create-sep-manual").attr("disabled", true);

        var bpjs_id = null;
        var carabayar_id = null;
        var pendaftaran_id = null;
        var pasien_id = null;
        var nama_pasien = null;
        var pasienadmisi_id = null;
        var status_periksa_id = null;
        var pasien_id = null;
        var primary = null;
        var status_bayar = null;
        var pasien_id = null;
        var nosep = null;

        try {
            pasien_id = table.row(".selected").data().pasien_id;
            pendaftaran_id = table.row(".selected").data().pendaftaran_id;
            nama_pasien = table.row(".selected").data().nama_pasien;
            carabayar_id = table.row(".selected").data().carabayar_id ? table.row(".selected").data().carabayar_id : null;
            bpjs_id = table.row(".selected").data().bpjs_id ? table.row(".selected").data().bpjs_id : null;
            pasienadmisi_id = table.row(".selected").data().pasienadmisi_id ? table.row(".selected").data().pasienadmisi_id : null;
            status_periksa_id = table.row(".selected").data().status_periksa_id;
            pasien_id = table.row(".selected").data().pasien_id;
            primary = table.row(".selected").data().primary;
            status_bayar = table.row(".selected").data().status_bayar;
            pasien_id = table.row(".selected").data().pasien_id;
            nosep = table.row(".selected").data().nosep;

            if ($("#hide-jenis").val() == "ranap") {
                norm = table.row(".selected").data().norm;
            } else {
                norm = table.row(".selected").data().no_rekam_medik;
            }
        } catch (e) {
            pendaftaran_id = false;
            bpjs_id = null;
            norm = null;
        }
        
        if (bpjs_id != null ) {
            $("#btn-print-sep").attr("disabled", false);
        }
        
        if (carabayar_id == 6 && bpjs_id == null) {
            var url = $("#create-sep-manual").attr("data-url");
            $("#create-sep").attr("disabled", false);
            $("#create-sep-manual").attr("disabled", false);
            $("#create-sep-manual").attr("data-id", pendaftaran_id);
            $("#create-sep-manual").attr("data-url", url+"&pasienadmisi_id="+pasienadmisi_id+"&pendaftaran_id=" + pendaftaran_id + "&nama_pasien=" + encodeURIComponent(table.row(".selected").data().nama_pasien) + "-");
        }
        
        if(status_periksa_id != 487 && status_bayar != 348) {
            $(".data-edit").attr("disabled", false);
        }
        else {
            $(".data-edit").attr("disabled", true);
        }
        var can_cancel = false;
        try {
            can_cancel = table.row(".selected").data().can_cancel ? table.row(".selected").data().can_cancel : false;
        } catch (e) {
            can_cancel = false;
        }
        
        if (can_cancel) {
            $("#data-batal").attr("disabled", false);
        } else {
            $("#data-batal").attr("disabled", true);
        }

        if (nosep == null && carabayar_id == 6){
            $("#pengajuan-sep").attr("disabled", false);
        } else {
            $("#pengajuan-sep").attr("disabled", true);
        }
        
        if (norm) {
          $("#btn-riwayat-pasien").attr("data-target", "' . Url::home() . '"+"igd/riwayat-pasien/index?norm="+norm);
        } else {
          $("#btn-riwayat-pasien").attr("data-target", null);
        }

        $("#btn-edit-pasien").attr("data-target", "' . Url::home() . '"+"pendaftaran/informasi-pencarian-pasien/update?id="+pasien_id);
        if (status_bayar != 348 && pendaftaran_id != null && pendaftaran_id != false) {
            //check tagihan 
            $.ajax({
                url: "/pendaftaran/informasi-pasien/get-tagihan-sudah-bayar?pendaftaran_id=" + pendaftaran_id,
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if(res.count_pembayaran > 0) {
                        $(".data-edit").attr("disabled", true);
                    }
                },
                error: function(err) {
                    console.log("error cek kunjungan");
                    console.log(err);
                }
            });
        }
    });

    $(document).on("click", "#btn-print-status-pasien", function() {
        if (typeof table.row(".selected").data() === "undefined") {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");

            return true;
        }

        window.open($(this).attr("data-target"), "_blank");
    });
    
    $(document).on("click", "#btn-riwayat-pasien", function() {
        if (typeof table.row(".selected").data() === "undefined") {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");

            return true;
        }

        window.open($(this).attr("data-target"), "_blank");
    });

    $(document).on("click", "#btn-edit-pasien", function() {
        if (typeof table.row(".selected").data() === "undefined") {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
            return true;
        }
    });


    function convertColor(hex, bw) {
        if (hex.indexOf("#") === 0) {
            hex = hex.slice(1);
        }
        // convert 3-digit hex to 6-digits.
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        if (hex.length !== 6) {
            throw new Error("Invalid HEX color.");
        }
        var r = parseInt(hex.slice(0, 2), 16),
            g = parseInt(hex.slice(2, 4), 16),
            b = parseInt(hex.slice(4, 6), 16);
        if (bw) {
            return (r * 0.299 + g * 0.587 + b * 0.114) > 186
                ? "#000000"
                : "#FFFFFF";
        }
        // invert color components
        r = (255 - r).toString(16);
        g = (255 - g).toString(16);
        b = (255 - b).toString(16);
        // pad each with zeros and return
        return "#" + padZero(r) + padZero(g) + padZero(b);
    }

    $(document).on("click", "#re-sync", function() {
        $().docoForm("click", {
            confirmMessage: "Lakukan Sinkronisasi?",
            skipSuccessNotif: true,
            skipErrorNotif: true,
            data: {},
            url: "/pendaftaran/informasi-pasien/re-sync",
            success: function (data) {
                docoNotification("success", "Berhasil", "Sinkronsiasi sedang dilakukan");
            },
            error: function(err) {
                docoNotification("warning", "Peringatan", "Sinkronsiasi sedang dilakukan, harap tunggu sebentar");
            }
        });
    });
', View::POS_END, 'b-index');
?>