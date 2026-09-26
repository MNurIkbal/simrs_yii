<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

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
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?php
                    $btn_toolbar = [
                        'search',
                        'reset' => [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target' => $module.'export-excel?'
                        //     ]
                        // ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'apotek/laporan-summary-stok-opname/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#example');?>
            </div>

            <div class="panel-body">
                <div class="filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%;display:none;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=\Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "No. Form SO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Form SO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi SO");?></th>
                            <th><?=\Yii::t("fe", "Di Validasi Oleh");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                            <th><?=\Yii::t("fe", "Konversi");?></th>
                            <th><?=\Yii::t("fe", "Weighted Average");?></th>
                            <th><?=\Yii::t("fe", "System Stock Qty");?></th>
                            <th><?=\Yii::t("fe", "Physical Stock Qty");?></th>
                            <th><?=\Yii::t("fe", "Variance Qty");?></th>
                            <th><?=\Yii::t("fe", "Opening Total Batch Cost");?></th>
                            <th><?=\Yii::t("fe", "Ending Total Batch Cost");?></th>
                            <th><?=\Yii::t("fe", "Selisih Batch Cost");?></th>
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

<?php
$this->registerJs('
    // Global Var
    var table;

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [],
            displayLength: 10,
            processing: true,
            serverSide: true,
            info: false,
            scrollX: true,
            ajax: baseUrl+"apotek/laporan-summary-stok-opname/get-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan")).'", 
                    data: "store"
                },
                { 
                    title: "'.(\Yii::t("fe", "No. Form SO")).'", 
                    data: "noformulir", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Form SO")).'", 
                    data: "tglformulir"
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Validasi SO")).'", 
                    data: "tgl_validasi",
                    searchable: false
                },
                { 
                    title: "'.(\Yii::t("fe", "Di Validasi Oleh")).'", 
                    data: "validasi_oleh", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Jenis Obat Alkes")).'", 
                    data: "jenis_obat"
                },
                { 
                    title: "'.(\Yii::t("fe", "Kode Obat Alkes")).'", 
                    data: "kode_obat", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'", 
                    data: "nama_obat",
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'", 
                    data: "satuan_kecil",
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Konversi")).'", 
                    data: "konversi", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Weighted Average")).'", 
                    data: "weighted_average", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "System Stok Qty")).'", 
                    data: "system_stock_qty", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Physical Stok Qty")).'", 
                    data: "physical_stock_qty", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Variance Qty")).'", 
                    data: "variance_qty", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Opening Total Batch Cost")).'", 
                    data: "opening_total_batch_cost", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Ending Total Batch Cost")).'", 
                    data: "ending_total_batch_cost", 
                    searchable: false,
                    class: "text-right"
                },
                { 
                    title: "'.(\Yii::t("fe", "Selisih Batch Cost")).'", 
                    data: "selisih_batch_cost", 
                    searchable: false,
                    class: "text-right"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=4 readonly="true"></div>\'
            ],
            [
                1,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan_nama', '',
                        ArrayHelper::map($ruangan, 'ruangan_nama', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => 'Pilih Ruangan',
                            'id'=>'filter_ruangan'
                        ]
                    )
                )).'\'
            ],
            [
                6,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenisobatalkes_nama', '',
                        ArrayHelper::map($jenisObatAlkes, 'jenisobatalkes_nama', 'jenisobatalkes_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => 'Pilih Jenis Obatalkes',
                            'id'=>'filter_jenisobat'
                        ]
                    )
                )).'\'
            ],
        ], {
            3:0,
            1:1,
            6:2
        }, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $("#example_wrapper").css("display", "none");
        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });
    });
', View::POS_END, 'b-index');
?>
