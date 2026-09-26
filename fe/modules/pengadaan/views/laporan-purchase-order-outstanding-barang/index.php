<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
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
                                'data-url' => Url::home() . 'pengadaan/laporan-purchase-order-outstanding-barang/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-po-outstanding-barang');?>
            </div>
            <div class="panel-body">
                <div class="filter-form"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="legend-index">
                            <?php echo $this->render('../assets/js/filter/filter.php', [
                                'tableName' => 'laporan-po-outstanding-barang',
                                'url' => '/pengadaan/laporan-purchase-order-outstanding-barang',
                                'get' => '/get-data?',
                                'excel' => '/show-popup-excel?',
                                'type' => 'barang',
                            ]); ?>
                        </div>
                    </div>
                </div>
                <table id="laporan-po-outstanding-barang" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "PR No.");?></th>
                            <th><?=\Yii::t("fe", "PR Approval Date");?></th>
                            <th><?=\Yii::t("fe", "PO No.");?></th>
                            <th><?=\Yii::t("fe", "PO Create Date");?></th>
                            <th><?=\Yii::t("fe", "PO Approval Date");?></th>
                            <th><?=\Yii::t("fe", "Cito ");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <th><?=\Yii::t("fe", "Supplier Code");?></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Manufacturer");?></th>
                            <th><?=\Yii::t("fe", "Item Code");?></th>
                            <th><?=\Yii::t("fe", "Item Name");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "UOM");?></th>
                            <th><?=\Yii::t("fe", "From UOM");?></th>
                            <th><?=\Yii::t("fe", "Factor");?></th>
                            <th><?=\Yii::t("fe", "To UOM");?></th>
                            <th><?=\Yii::t("fe", "Price");?></th>
                            <th><?=\Yii::t("fe", "Ded %");?></th>
                            <th><?=\Yii::t("fe", "Add %");?></th>
                            <th><?=\Yii::t("fe", "Gross Amount");?></th>
                            <th><?=\Yii::t("fe", "Nett Amount Incl PPn");?></th>
                            <th><?=\Yii::t("fe", "PO Remarks");?></th>
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
        table = $("#laporan-po-outstanding-barang").docoTabel({
            filter: true,
            sorting: [],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-order-outstanding-barang/get-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { title: "'.(\Yii::t("fe", "PR No.")).'", data: "no_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "PR Approval Date")).'", data: "tanggal_verifikasi_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "PO No.")).'", data: "no_po", searchable: false },
                { title: "'.(\Yii::t("fe", "PO Create Date")).'", data: "tanggal_po" },
                { title: "'.(\Yii::t("fe", "PO Approval Date")).'", data: "tanggal_verifikasi_po", searchable: false },
                { title: "'.(\Yii::t("fe", "Cito ")).'", data: "is_cito", searchable: false },
                { title: "'.(\Yii::t("fe", "Admin")).'", data: "is_admin", searchable: false },
                { title: "'.(\Yii::t("fe", "Supplier Code")).'", data: "supplier_kode", searchable: false },
                { title: "'.(\Yii::t("fe", "Supplier")).'", data: "supplier_nama", searchable: false },
                { title: "'.(\Yii::t("fe", "Manufacturer")).'", data: "manufacturer", searchable: false },
                { title: "'.(\Yii::t("fe", "Item Code")).'", data: "item_code", searchable: false },
                { title: "'.(\Yii::t("fe", "Item Name")).'", data: "item_name", searchable: false },
                { title: "'.(\Yii::t("fe", "Qty")).'", data: "qty_po", searchable: false },
                { title: "'.(\Yii::t("fe", "UOM")).'", data: "uom", searchable: false },
                { title: "'.(\Yii::t("fe", "From UOM")).'", data: "from_uom", searchable: false },
                { title: "'.(\Yii::t("fe", "Factor")).'", data: "factor", searchable: false },
                { title: "'.(\Yii::t("fe", "To UOM")).'", data: "to_uom", searchable: false },
                { title: "'.(\Yii::t("fe", "Price")).'", data: "price", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Ded %")).'", data: "deduction_percent", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Add %")).'", data: "addition_percent", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Gross Amount")).'", data: "gross_amount", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Nett Amount Incl PPN")).'", data: "nett_amount", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "PO REMARKS")).'", data: "remarks", searchable: false }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                4,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
        ], {0:4}, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

', View::POS_END, 'b-index');
?>
