<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@sirs.co.id)
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
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form',
                            'id'=>'reset-form',

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
                            'data-url' => Url::home() . 'apotek/laporan-total-rekapitulasi-penjualan-farmasi/show-popup-excel?',
                            'data-width' => '75%',
                            'data-visible' => $checkRoute
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                            <th><?=\Yii::t("fe", "Diskon");?></th>
                            <th><?=\Yii::t("fe", "Total Harga");?></th>
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

<?php $this->registerJs('
    var table;
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
    $(document).on("click", "#reset-form", function() {
        var currentDate = new Date();
        var day = currentDate.getDate();
        var month = currentDate.toLocaleString("id", { month: "short" }); ;
        var year = currentDate.getFullYear();
        
        var formattedDate = day + "-" + month + "-" + year;
    
        $("#rangeDemoStart").val(formattedDate);
        $("#rangeDemoFinish").val(formattedDate);
        table.column(1).search(formattedDate).draw();

    });
    $(document).ready(function(){
        table = $("#example").docoTabel({
            filter: true,
            sorting: [],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/laporan-total-rekapitulasi-penjualan-farmasi/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Tanggal')).'",
                    data: "tgl",
                    visible:false
                },
                {
                    title: "'.(\Yii::t('fe', 'Kode Obat')).'",
                    data: "kode_obat",
                    searchable:false
                },
                {
                    title: "'.(\Yii::t('fe', 'Jenis Obat')).'",
                    data: "jenisobatalkes_nama"
                },
                {
                    title: "'.(\Yii::t('fe', 'Nama Obat')).'",
                    data: "nama_obat",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Qty')).'",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Satuan Kecil')).'",
                    data: "satuan_kecil",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t('fe', 'Diskon (Rp.)')).'",
                    data: "diskon",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t('fe', 'Total Harga (Rp.)')).'",
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
            ],
            [
                3,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenisobatalkes_nama', '',
                        ArrayHelper::map($jenisObatAlkes, 'jenisobatalkes_id', 'jenisobatalkes_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id'=>'filter_jenisobat'
                        ]
                    )
                )).'\'
            ],
        ], {
            1:0,
            3:2
        }, true);

        dateRangeHelper(".startDate", ".endDate", ".targetDate");

    });

    ', VIEW::POS_END, "js-kunings");
?>
