<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="Icon Gudang">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><strong><?= $this->title; ?></strong></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'gudang/laporan-pemakaian-bmhp-ruangan/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <div class="container-fluid">
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th style="width: 1">No</th>
                            <th><?=\Yii::t("fe", "Nama Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                            <th><?=\Yii::t("fe", "No Transaksi");?></th>
                            <th><?=\Yii::t("fe", "jenis Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Harga Satuan (Rp)");?></th>
                            <th><?=\Yii::t("fe", "Total Harga (Rp)");?></th>
                            <th><?=\Yii::t("fe", "User");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
    $this->registerJs('
    var table;
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"], [2, "asc"], [4, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/laporan-pemakaian-bmhp-ruangan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Ruangan')).'",
                    data: "ruangan_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal Transaksi')).'",
                    data: "tgl_transaksi",
                    render: (data) => {
                        return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
                    }
                },
                {
                    title: "'.(\Yii::t('fe', 'No Transaksi')).'",
                    data: "no_transaksi",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jenis Obat Alkes')).'",
                    data: "jenisobatalkes_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Kode Obat')).'",
                    data: "kode_obat",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Obat')).'",
                    data: "nama_obat",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Qty')).'",
                    data: "qty_input",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Satuan')).'",
                    data: "satuan_besar",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Harga Satuan (Rp)')).'",
                    data: "harga_netto_konversi",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Total Harga (Rp)')).'",
                    data: "total_harga",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'User')).'",
                    data: "user",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Catatan')).'",
                    data: "catatan",
                    searchable: false,
                    orderable: false
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('ruangan_nama', '', 
                        $listRuangan, 
                        [
                            'class' => 'form-control select2 ruangan_nama', 
                            'prompt' => \Yii::t('fe', ''),
                            'id' => 'filter_ruangan_nama',
                            'prompt' => \Yii::t('fe', '-- Pilih Ruangan --')
                        ]
                    )
                )).'</div>\'
            ],
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
            ],
            [
                4,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('jenisobatalkes_nama', '', 
                        $listJenisObat, 
                        [
                            'class' => 'form-control select2 jenisobatalkes_nama', 
                            'prompt' => \Yii::t('fe', ''),
                            'id' => 'filter_jenisobat',
                            'prompt' => \Yii::t('fe', '-- Pilih Jenis Obat --')
                        ]
                    )
                )).'</div>\'
            ]
        ], {
            2:0,
            1:1,
            4:2,
            5:3,
            6:4,
        });

        dateRangeHelper(".startDate", ".endDate", ".targetDate");

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });


    });

    ', VIEW::POS_END, "js-kunings");
?>
