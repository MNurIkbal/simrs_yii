<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    th {
        font-weight: 0px !important; 
        font-size: 12px;
    }

    .border-tab {
        border-right: 1px solid white;
    }

    .dataTables_scroll {
    max-height: 99999em !important
    }

    .dataTables_scrollFoot { overflow:visible !important; }

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                   'search' => [
                        'attributes' => [
                            'id' => 'search'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset'
                        ]
                    ],
                    // 'pdf',
                    //'excel',
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'rm/lap-rekap-harian-kinerja-profesional/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],    
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "NO");?></th>
                            <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", "RAWAT INAP (RUANGAN)");?></th>
                            <th colspan="2" class="text-center border-tab"><?=\Yii::t("fe", "JUMLAH TEMPAT TIDUR");?></th>
                            <th rowspan="3" class="text-center border-tab"><?=\Yii::t("fe", " JUMLAH PASIEN SEBELUM");?></th>
                            <th colspan="3" class="text-center border-tab"><?=\Yii::t("fe", "PASIEN MASUK RAWAT");?></th>
                            <th colspan="6" class="text-center border-tab"><?=\Yii::t("fe", "PASIEN KELUAR");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "HP (Pasien Sisa)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "LOS Lama Rawat (Hari)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "ALOS (Hari)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "BOR (%)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "TOI (Hari)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "BTO (KALI)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "NDR (&#8240;)");?></th>
                            <th rowspan="3" class="text-center border-tab" width="1"><?=\Yii::t("fe", "GDR (&#8240;)");?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "KAPASITAS");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "TERSEDIA");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "MASUK");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "PINDAHAN");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "JUMLAH");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "HIDUP");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "DIPINDAHKAN");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "RUJUK RS LAIN");?></th>
                            <th colspan="2" class="text-center border-tab"><?=\Yii::t("fe", "MENINGGAL");?></th>
                            <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "JUMLAH");?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class="text-center border-tab"><?= \Yii::t("fe", "< 48 JAM"); ?></th>
                            <th class="text-center border-tab"><?= \Yii::t("fe", "> 48 JAM"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align:right">Jumlah:</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    var data;

    $(document).ready(function() {
        $(".flex-1").addClass("hidden")
        table = $("#example").DataTable({
            bPaginate: false,
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: baseUrl+"rm/lap-rekap-harian-kinerja-profesional/get-data",
                type: "GET",
                data: function (col) {
                    // for (i = 0; i < col.columns.length; i++) {
                    //     console.log(col.columns[i].data);
                    //     if (col.columns[i].data === "tgl_sensus") { 

                    //     } else if (col.columns[i].data === "ruangan_id") {

                    //     } else {
                    //         col.columns.splice(i, 1); 
                    //     }
                    // }
                }
            },
            columns: [
                {
                    title: "No",
                    data: "kelaspelayanan_nama",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, // 0
                {
                    title: "RAWAT INAP (RUANGAN)",
                    data: "kelaspelayanan_nama",
                    searchable: false,
                    orderable: false
                }, // 1
                {
                    title: "KAPASITAS",
                    data: "jumlah_bed",
                    searchable: false,
                    orderable: false
                }, // 2
                {
                    title: "TERSEDIA",
                    data: "jumlah_bed",
                    searchable: false,
                    orderable: false
                }, // 3
                {
                    title: "JUMLAH PASIEN SEBELUM",
                    data: "pasien_awal",
                    searchable: false,
                    orderable: false
                }, // 4
                {
                    title: "MASUK",
                    data: "pasien_masuk",
                    searchable: false,
                    orderable: false
                }, // 5
                {
                    title: "PINDAHAN",
                    data: "pindah_ke",
                    searchable: false,
                    orderable: false
                }, // 6
                {
                    title: "JUMLAH",
                    data: "jumlah_pasien_masuk",
                    searchable: false,
                    orderable: false
                }, // 7
                {
                    title: "HIDUP",
                    data: "keluar_hidup",
                    searchable: false,
                    orderable: false
                }, // 8
                {
                    title: "DIPINDAHKAN",
                    data: "dipindahkan_dari",
                    searchable: false,
                    orderable: false
                }, // 9
                {
                    title: "RUJUK RS LAIN",
                    data: "rujuk_rs_lain",
                    searchable: false,
                    orderable: false
                }, // 10
                {
                    title: "< 48 JAM",
                    data: "meninggal_kurang_48",
                    searchable: false,
                    orderable: false
                }, // 11
                {
                    title: "> 48 JAM",
                    data: "meninggal_lebih_48",
                    searchable: false,
                    orderable: false
                }, // 12
                {
                    title: "JUMLAH",
                    data: "jumlah_pasien_keluar",
                    searchable: false,
                    orderable: false
                }, // 13
                {
                    title: "HP (Pasien Sisa)",
                    data: "hp",
                    searchable: false,
                    orderable: false
                }, // 14
                {
                    title: "LOS Lama Rawat (Hari)",
                    data: "los",
                    searchable: false,
                    orderable: false
                }, // 15
                {
                    title: "ALOS (Hari)",
                    data: "alos",
                    searchable: false,
                    orderable: false
                }, // 16
                {
                    title: "BOR (%)",
                    data: "bor_today",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return Math.ceil(data);
                    }
                }, // 17
                {
                    title: "TOI (Hari)",
                    data: "toi",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return Math.ceil(data);
                    }
                }, // 18
                {
                    title: "BTO (Kali)",
                    data: "bto",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return Math.ceil(data);
                    }
                }, // 19
                {
                    title: "NDR (&#8240;)",
                    data: "ndr",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return Math.ceil(data);
                    }
                }, // 20
                {
                    title: "GDR (&#8240;)",
                    data: "gdr",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return Math.ceil(data);
                    }
                }, // 21
                {
                    title: "Tanggal",
                    data: "tgl_sensus",
                    searchable: true,
                    orderable: false,
                    visible: false
                }, // 22
                {
                    title: "Ruangan",
                    data: "ruangan_id",
                    searchable: true,
                    orderable: false,
                    visible: false
                }, // 23
            ],
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();

                api.columns().every(function(i) {
                    if (this.visible() && i > 1) { // column 0 and 1 as text jumlah

                        let total =  api
                        .column(i)
                        .data()
                        .reduce((currentValue, previousValue) => parseInt(currentValue) + parseInt(previousValue), 0);

                        api.column(i).footer().innerHTML = total ? total : 0;
                    }
                });
            }
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    22,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [   
                    23, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_id', '', $listRuangan, 
                        ['class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '— Pilih Ruangan —'),
                        'name' => "ruangan_id",
                        ]))).'\'
                ],    
            ], {
                22:0,
                23:1
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });

', View::POS_END, 'b-index');
?>