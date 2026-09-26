<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    // 'print',
                    'edit' => [
                        'title' => \Yii::t('fe', 'Daftarkan Pasien'),
                        'attributes' => [
                            'id' => 'btn-daftar-ranap',
                            'disabled' => true,
                            'data-options' => false,
                            'data-target' => '/pendaftaran/daftar?pendaftaran_id=',
                            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar'
                        ]
                    ],
                    'cek-kamar'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cek Ketersediaan Kamar'),
                        'icon' => 'fa fa-pencil',
                        'attributes'=>[
                            'id'=>'btn-cek-kamar',
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-width' => '90%',
                            'data-url'=>'/pendaftaran/informasi-pasien-rujuk-ranap/ketersediaan-kamar?pendaftaran_id=',
                            'disabled'=>false
                        ]
                    ],
                    'cetak-spri' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Cetak SPRI'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'btn-cetak-spri',
                            'data-pages'=>'_blank',
                            'data-target' => '/pendaftaran/informasi-pasien-rujuk-ranap/cetak-spri?pendaftaran_id=',
                            'data-conditions' => 'type',
                            'disabled' => true
                        ]
                    ]
                ], '#laporan'); ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan Status</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#99deb9'></span>Bisa Rujuk Rawat Inap</li>
                                    <li><span style='background:#ff4d4d'></span>Expired</li>
                                    <li><span style='background:#52a0ff'></span>Sedang Rawat Inap</li>
                                    <li><span style='background:#ffc038'></span>Batal Rawat Inap</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan Kamar Ruangan Tujuan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#03fce8'></span>Perubahan Kamar Dipilih Oleh Pasien</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal Rujuk Rawat Inap"); ?></th>
                            <th><?= \Yii::t("fe", "No pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Info pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter DPJP"); ?></th>
                            <th><?= \Yii::t("fe", "Kelas Pelayanan"); ?></th>
                            <th><?= \Yii::t("fe", "Info Penjamin"); ?></th>
                            <th><?= \Yii::t("fe", "Kamar Ruangan Tujuan"); ?></th>
                            <th><?= \Yii::t("fe", "Status periksa"); ?></th>
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
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            // scrollY: "40vh",
            ajax: baseUrl+"pendaftaran/informasi-pasien-rujuk-ranap/get-data",
            columnDefs:[
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
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
                {title: "' . (\Yii::t("fe", "Tanggal Rujuk Rawat Inap")) . '", data: "tglrujukranap"}, //2
                {title: "' . (\Yii::t("fe", "No pendaftaran")) . '", data: "no_pendaftaran"}, //3
                {title: "' . (\Yii::t("fe", "Info Pasien")) . '", data: "info_pasien", searchable: false}, //4
                {title: "' . (\Yii::t("fe", "Dokter DPJP")) . '", data: "dpjp_nama",searchable: true}, //5
                {title: "' . (\Yii::t("fe", "Kelas Pelayanan")) . '", data: "kelaspelayanan_nama",searchable: false}, //6
                {title: "' . (\Yii::t("fe", "Info Penjamin")) . '", data: "carabayar_penjamin", searchable: false}, //7
                {title: "' . (\Yii::t('fe', 'Kamar Ruangan Tujuan')) . '", data: "ruangan",searchable: false}, //8
                {title: "' . (\Yii::t("fe", "Status Periksa")) . '", data: "status_periksa_nama",searchable: false}, //9
                {title: "' . (\Yii::t("fe", "Ruangan")) . '", data: "ruangan_id", visible: false}, //10
                {title: "' . (\Yii::t("fe", "Cara bayar")) . '", data: "carabayar_id", visible: false}, //11
                {title: "' . (\Yii::t("fe", "Penjamin")) . '", data: "penjamin_id", visible: false}, //12
                {title: "' . (\Yii::t("fe", "No rekam medik")) . '", data: "no_rekam_medik", visible: false}, //13
                {title: "' . (\Yii::t("fe", "Nama pasien")) . '", data: "nama_pasien", visible: false}, //14
                {title: "' . (\Yii::t("fe", "Status Periksa")) . '", data: "status_periksa_id", visible: false}, //15

            ],
            drawCallback: function(e) {
                var api = this.api();
            },
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 4,
            // }
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var textColor = "#ffffff";

                if(aData.status_rujuk == "rujuk_ranap"){
                    textColor = convertColor("#99deb9", "#99deb9");
                    $("td:eq(4)", nRow).css("background-color", "#99deb9");
                    $("td:eq(4)", nRow).css("color", textColor);
                } else if(aData.status_rujuk == "sedang_ranap"){
                    textColor = convertColor("#52a0ff", "#52a0ff");
                    $("td:eq(4)", nRow).css("background-color", "#52a0ff");
                    $("td:eq(4)", nRow).css("color", textColor);
                } else if(aData.status_rujuk == "expired"){
                    textColor = convertColor("#ff4d4d", "#ff4d4d");
                    $("td:eq(4)", nRow).css("background-color", "#ff4d4d");
                    $("td:eq(4)", nRow).css("color", textColor);
                } else if(aData.status_rujuk == "batal_ranap"){
                    textColor = convertColor("#ffc038", "#ffc038");
                    $("td:eq(4)", nRow).css("background-color", "#ffc038");
                    $("td:eq(4)", nRow).css("color", textColor);
                }

                if(aData.status_ketersediaan_kamar == "user_suggestion"){
                    $("td:eq(8)", nRow).css("background-color", "#03fce8");
                    $("td:eq(8)", nRow).css("color", textColor);
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\'
                ],
                [
                    10,
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
                                'prompt' => \Yii::t('fe', '--Pilih Ruangan--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    11,
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
                                'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    12,
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
                                    'placeholder' => \Yii::t('fe', '--Pilih Penjamin--'),
                                    'url' => Url::to(['/pendaftaran/informasi-pasien/list-penjamin'])
                                ]
                            ]
                        )

                    )) . '\'
                ],
                [
                    15,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'status_periksa_id',
                            '',
                            $statusList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'status_periksa_id',
                                'prompt' => \Yii::t('fe', '--Pilih Status--')
                            ]
                        )
                    )) . '\'
                ],

            ],
            {
                2:0,
                14:1,
                13:2,
                3:3,
                10:4,
                11:5,
                12:6,
                5:7,
                15:8
            }
        );
        dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1"); 
        $("#status_periksa_id").val(433).trigger("change");
        $(".data-filter").trigger("click");
    });

    $(document).on("click", "#laporan tbody tr", function () {
        var status_rujuk = null;
        var pendaftaran_id = null;
        var primary = null;
        var pasien_id = null;

        try {
            status_rujuk = table.row(".selected").data().status_rujuk;
            pasien_id = table.row(".selected").data().pasien_id;
            pendaftaran_id = table.row(".selected").data().pendaftaran_id;
        } catch (e) {
            status_rujuk = null;
            pendaftaran_id = false;
        }
        
        if (status_rujuk == "rujuk_ranap" ) {
            $("#btn-daftar-ranap").attr("disabled", false);
            $("#btn-cetak-spri").attr("disabled", false);
        } else {
            $("#btn-daftar-ranap").attr("disabled", true);
            $("#btn-cetak-spri").attr("disabled", true);
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

', View::POS_END, 'b-index');
?>