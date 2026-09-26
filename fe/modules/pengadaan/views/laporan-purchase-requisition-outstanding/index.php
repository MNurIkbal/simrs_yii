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
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Export Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-table-id' => 'laporan-pr',
                            'class' => 'data-excel',
                            'data-url' => Url::home() . 'pengadaan/laporan-purchase-requisition-outstanding/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-pr'); ?>
            </div>

            <div class="panel-body">
                <div class="filter-form"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="legend-index">
                            <?php echo $this->render('../assets/js/filter/filter.php', [
                                'tableName' => 'laporan-pr',
                                'url' => '/pengadaan/laporan-purchase-requisition-outstanding',
                                'get' => '/get-data?',
                                'excel' => '/show-popup-excel?',
                                'type' => 'obat',
                            ]); ?>
                        </div>
                    </div>
                </div>
                <table id="laporan-pr" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=\Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Tanggal PR");?></th>
                            <th><?=\Yii::t("fe", "Cito ");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <th><?=\Yii::t("fe", "Consignment");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Approve");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "UoM");?></th>
                            <th><?=\Yii::t("fe", "Status PR");?></th>
                            <th><?=\Yii::t("fe", "Manufaktur");?></th>
                            <th><?=\Yii::t("fe", "Status Obat");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                            <th><?=\Yii::t("fe", "Alasan");?></th>
                            <th><?=\Yii::t("fe", "Dibuat Oleh");?></th>
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
        table = $("#laporan-pr").docoTabel({
            filter: true,
            sorting: [[2, "desc"], [1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-requisition-outstanding/get-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor PR")).'", 
                    data: "no_pr", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal PR")).'", 
                    data: "tgl_pr" 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Approve")).'", 
                    data: "tgl_approve", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Cito ")).'", 
                    data: "is_cyto", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Admin")).'", 
                    data: "is_admin", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Consignment")).'", 
                    data: "is_consigment", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Kode Obat")).'", 
                    data: "kode_obat", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nama Obat")).'", 
                    data: "obatalkes_nama", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Jenis Obat")).'", 
                    data: "jenis_obat", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "qty_input", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan")).'", 
                    data: "satuan", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "UoM")).'", 
                    data: "uom", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Status PR")).'", 
                    data: "status_pr", 
                    searchable: false 
                },
                { 
                    title: "' . (\Yii::t("fe", "Manufaktur")) . '", 
                    data: "manufaktur_nama", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Status Obat")).'", 
                    data: "status_obat", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Catatan")).'", 
                    data: "catatan_pr", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Alasan Batal")).'", 
                    data: "alasan", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Dibuat Oleh")).'", 
                    data: "pegawai", 
                    searchable: false 
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ]
        ]);

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });
        
    });
', View::POS_END, 'b-index');
?>
