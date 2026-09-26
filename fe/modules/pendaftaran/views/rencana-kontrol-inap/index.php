<?php
/**
 * @Author: Fajar
 * @Date:   2022-01-18
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .row-nokontrol {
        background-color: #34bfa3 !important;
        color: #FFFFFF;
        font-weight: bold;
    }

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

</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'print-rujukan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Rencana Kontrol/Inap'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-rencana',
                            'class' => 'btn-print-rencana',
                            // 'data-options' => 'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rencana?rencanakontrol_id=',
                            'disabled' => true,
                            'data-pages' => '_blank',
                        ]
                    ],
                    'add',
                    'edit' => [
                        'title' => \Yii::t('fe', 'Edit'),
                        'attributes' => [
                            'id' => 'data-edit',
                            'data-options' => false,
                            'data-target' => '/pendaftaran/rencana-kontrol-inap/update?id=',
                            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar'
                        ]
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'id' => 'data-hapus',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'pendaftaran/rencana-kontrol-inap/confirm-hapus?rencanakontrol_id=',
                            'data-params' => 'rencanakontrol_id',
                            'disabled' => false

                        ]
                    ],
                    'data-vclaim' => [
                        'title' => \Yii::t('fe', 'Cari Data Vclaim'),
                        'icon' => 'fa fa-search',
                        'attributes' => [
                            'id' => 'data-vclaim',
                            'data-options' => 'link',
                            'target' => '_blank',
                            'data-target' => '/pendaftaran/rencana-kontrol-inap/inf-data-vclaim',
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar'
                        ]
                    ],
                ], '#tb-rencana-kontrol') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#34bfa3;'></span>No SEP Sudah terbit</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="tb-rencana-kontrol" class="table table-striped" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rencana Kontrol") ?></th>
                            <th><?= Yii::t("fe", "RI/RJ") ?></th>
                            <th><?= Yii::t("fe", "No. SEP") ?></th>
                            <th><?= Yii::t("fe", "No. Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "No Surat Kontrol") ?></th>
                            <th><?= Yii::t("fe", "No SPRI") ?></th>
                            <th><?= Yii::t("fe", "Spesialis/Sub Spesialis") ?></th>
                            <th><?= Yii::t("fe", "DPJP Tujuan Kontrol/Inap") ?></th>
                            <th><?= Yii::t("fe", "Jenis Rencana") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var date = "'.date('d-M-Y', strtotime('NOW')).'";
    var table;

    $(document).ready(function() {
        table = $("#tb-rencana-kontrol").docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + "pendaftaran/rencana-kontrol-inap/get-data",
            sorting: [[2, "desc"]],
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            columns: [
                {data: null, searchable: false, orderable: false, defaultContent: ""}, //0
                {title: "'.(\Yii::t("fe", "No")).'", data: "no", searchable: false, orderable: false}, //1
                {title: "'.(\Yii::t("fe", "Tanggal Rencana Kontrol")).'", data: "tgl_rencanakontrol"}, //2
                {title: "'.(\Yii::t("fe", "RI/RJ")).'", data: "jenis_rencana_nama", searchable: false, orderable: false}, //3
                {title: "'.(\Yii::t("fe", "No. SEP")).'", data: "no_sep", orderable: false}, //4
                {title: "'.(\Yii::t("fe", "No. Kartu")).'", data: "no_kartu", orderable: false}, //5
                {title: "'.(\Yii::t("fe", "Nama")).'", data: "nama", searchable: false, orderable: false}, //6
                {title: "'.(\Yii::t("fe", "No Surat Kontrol")).'", data: "nosuratkontrol", searchable: false, orderable: false}, //7
                {title: "'.(\Yii::t("fe", "No SPRI")).'", data: "no_spri", searchable: false, orderable: false}, //8
                {title: "'.(\Yii::t("fe", "Spesialis/Sub Spesialis")).'", data: "nama_spesialis", searchable: false, orderable: false}, //9
                {title: "'.(\Yii::t("fe", "DPJP Tujuan Kontrol/Inap")).'", data: "dokterdpjp_nama", searchable: false, orderable: false}, //10
                {title: "'.(\Yii::t("fe", "Jenis Rencana")) . '", data: "jenis_rencana", visible: false}, //11
                {title: "'.(\Yii::t("fe", "Rencana kontrol ID")) . '", data: "rencanakontrol_id", searchable: false, visible: false}, //12
                {title: "'.(\Yii::t("fe", "No Surat Kontrol/SPRI")) . '", data: "nosuratkontrol", visible: false}, //13
                {title: "'.(\Yii::t("fe", "Dokter")) . '", data: "dokterdpjp_kode", visible: false}, //14
            ],
            drawCallback: function(e) {
                var api = this.api();
                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    if(rowData.no_sep != "" && rowData.no_sep != null) {
                        $(rowNode).addClass("row-nokontrol");
                    }
                    else {
                        $(rowNode).removeClass("row-nokontrol");
                    }
                }
            },
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table , [
            [2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\'
            ],
            [
                11,
                \'' . (preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        'jenis_rencana',
                        '',
                        $jenisRencanaList,
                        [
                            'class' => 'form-control select2',
                            'id' => 'jenis_rencana',
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                )) . '\'
            ],
            [
                14,
                \'' . (preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        'dokterdpjp_kode',
                        '',
                        array(),
                        [
                            'class' => 'form-control select2',
                            'id' => 'dokter_dpjp',
                            'prompt' => \Yii::t('fe', '-- Pilih --')
                        ]
                    )
                )) . '\'
            ],
        ], {
            0:2,
            1:11,
            2:4,
            3:5,
            4:13,
            5:14,
        });

        dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");

        $("#dokter_dpjp").select2InfinityScroll({
            url: "/pendaftaran/rencana-kontrol-inap/all-dokter-list",
        })
    });

    $(document).on("click", "#tb-rencana-kontrol tbody tr", function () {
        var rencanakontrol_id = null;
        $("#data-hapus").attr("disabled", false);
        try {
            rencanakontrol_id = table.row(".selected").data().rencanakontrol_id ? table.row(".selected").data().rencanakontrol_id : null;
            // getInfoPeserta(rencanakontrol_id);
        } catch (e) {
            rencanakontrol_id = null;
        }
        
        if (rencanakontrol_id != null ) {
            $("#btn-print-rencana").attr("disabled", false);
        } else {
            $("#btn-print-rencana").attr("disabled", true);
        }
    });

    function getInfoPeserta(rencanakontrol_id) {  
        $.ajax({
            url: "/pendaftaran/rencana-kontrol-inap/get-info-peserta",
            type: "GET",
            data: {
                rencanakontrol_id: rencanakontrol_id,
            },
            dataType: "JSON",
            success: function(response) {
                var can_delete = response.response.can_delete
                if (can_delete == false) {
                    $("#data-hapus").attr("disabled", true);
                } else {
                    $("#data-hapus").attr("disabled", false);
                }
            }
        })
    }
', View::POS_END, 'index');
?>