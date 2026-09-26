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
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;


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
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="Icon Farmasi">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><strong><?= $this->title; ?></strong></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                $checkRoute = $this->context->checkAksesMenu();
                echo DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh Excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/apotek/laporan-rekapitulasi-penjualan/show-popup?'
                        ]
                    ],
                ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Harga (Rp)");?></th>
                            <th><?=\Yii::t("fe", "Total Harga (Rp)");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
        $("#excel-bgprocess").unbind("click");
        $("#excel-bgprocess").on("click", function (event) {
            var tgl_pelayanan = $(".startDate").val() + " - " + $(".endDate").val();
            var kode_obat = $("#filter_kode_obat").val();
            var nama_obat = $("#filter_transaksi").val();

            _url = encodeURI(baseUrl+"apotek/laporan-rekapitulasi-penjualan/show-popup?tgl_pelayanan="+ tgl_pelayanan 
            + "&kode_obat=" + kode_obat
            + "&nama_obat=" + nama_obat
            )

            $(this).attr("data-url",_url);
        });

        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"], [3, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/laporan-rekapitulasi-penjualan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal')).'",
                    data: "tgl_pelayanan"
                },
                {
                    title: "'.(\Yii::t('fe', 'Kode Obat')).'",
                    data: "kode_obat"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Obat')).'",
                    data: "nama_obat"
                },
                {
                    title: "'.(\Yii::t('fe', 'Qty')).'",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Satuan')).'",
                    data: "satuan_kecil",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Harga (Rp)')).'",
                    data: "harga_netto",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Total Harga (Rp)')).'",
                    data: "total",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
            ]
        ], {
            1:0
        }, true);

        dateRangeHelper(".startDate", ".endDate", ".targetDate");

    });

    ', VIEW::POS_END, "js-kunings");
?>
