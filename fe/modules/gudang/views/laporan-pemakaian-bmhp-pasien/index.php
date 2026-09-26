<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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
                    'excel',
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th style="width: 1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nomor Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Harga Satuan (Rp)");?></th>
                            <th><?=\Yii::t("fe", "Total Harga (Rp)");?></th>
                            <th><?=\Yii::t("fe", "Ditagihkan");?></th>
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
            ajax: baseUrl+"gudang/laporan-pemakaian-bmhp-pasien/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal Transaksi')).'",
                    data: "tgl_transaksi"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nomor Rekam Medik')).'",
                    data: "no_rm",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nomor Pendaftaran')).'",
                    data: "no_pendaftaran",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Pasien')).'",
                    data: "nama_pasien",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Tindakan')).'",
                    data: "tindakan",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Kode Obat')).'",
                    data: "obatalkes_kode",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Obat')).'",
                    data: "obatalkes_nama",
                    searchable: false,
                    orderable: false
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
                    data: "satuan_kecil_nama",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Harga Satuan (Rp)')).'",
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
                },
                {
                    title: "'.(\Yii::t('fe', 'Ditagihkan')).'",
                    data: "is_ditagihkan",
                    searchable: false,
                    orderable: false
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

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });


    });

    ', VIEW::POS_END, "js-kunings");
?>
