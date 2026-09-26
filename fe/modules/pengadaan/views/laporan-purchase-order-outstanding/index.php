<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
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
                                'data-url' => Url::home() . 'pengadaan/laporan-purchase-order-outstanding/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-po-outstanding');?>
            </div>
            <div class="panel-body">
                <div class="filter-form"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="legend-index">
                            <?php echo $this->render('../assets/js/filter/filter.php', [
                                'tableName' => 'laporan-po-outstanding',
                                'url' => '/pengadaan/laporan-purchase-order-outstanding',
                                'get' => '/get-data?',
                                'excel' => '/show-popup-excel?',
                                'type' => 'obat',
                            ]); ?>
                        </div>
                    </div>
                </div>
                <table id="laporan-po-outstanding" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Tanggal PR");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Approve");?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi PO");?></th>
                            <th><?=\Yii::t("fe", "Cito ");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <th><?=\Yii::t("fe", "Consignment");?></th>
                            <th><?=\Yii::t("fe", "Manufaktur");?></th>
                            <th><?=\Yii::t("fe", "Kode Supplier");?></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Kode Item");?></th>
                            <th><?=\Yii::t("fe", "Nama Item");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "PO Balance");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "UoM");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                            <th><?=\Yii::t("fe", "PPn (%)");?></th>
                            <th><?=\Yii::t("fe", "Sub Total (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Total setelah PPn (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Catatan 1");?></th>
                            <th><?=\Yii::t("fe", "Catatan 2");?></th>
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
    var belum_po = '.DocoConstants::VAR_BELUM_PO.';

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        $(".DTFC_Cloned").remove();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#laporan-po-outstanding").docoTabel({
            filter: true,
            sorting: [[4, "desc"], [3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-order-outstanding/get-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { title: "'.(\Yii::t("fe", "Tanggal PR")).'", data: "tgl_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "Tanggal Approve")).'", data: "tgl_approve", searchable: false },
                { title: "'.(\Yii::t("fe", "Nomor PR")).'", data: "no_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "Nomor PO")).'", data: "no_po", searchable: false },
                { title: "'.(\Yii::t("fe", "Tanggal PO")).'", data: "tgl_po_dibuat" },
                { title: "'.(\Yii::t("fe", "Tanggal Validasi PO")).'", data: "tgl_validasi", searchable: false },
                { title: "'.(\Yii::t("fe", "Cito ")).'", data: "is_cito", searchable: false },
                { title: "'.(\Yii::t("fe", "Admin")).'", data: "is_admin", searchable: false },
                { title: "'.(\Yii::t("fe", "Consignment")).'", data: "is_consigment", searchable: false },
                { title: "'.(\Yii::t("fe", "Manufaktur")).'", data: "manufaktur_nama", searchable: false },
                { title: "'.(\Yii::t("fe", "Kode Supplier")).'", data: "supplier_kode", searchable: false },
                { title: "'.(\Yii::t("fe", "Supplier")).'", data: "supplier_nama", searchable: false },
                { title: "'.(\Yii::t("fe", "Jenis Obat")).'", data: "jenisobatalkes_nama", searchable: false },
                { title: "'.(\Yii::t("fe", "Kode Item")).'", data: "kode_item", searchable: false },
                { title: "'.(\Yii::t("fe", "Nama Item")).'", data: "nama_item", searchable: false },
                { title: "'.(\Yii::t("fe", "Qty PO")).'", data: "qty_po", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "PO Balance")).'", data: "po_balance", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Satuan")).'", data: "satuan_besar", searchable: false },
                { title: "'.(\Yii::t("fe", "UoM")).'", data: "uom", searchable: false },
                { title: "'.(\Yii::t("fe", "Harga (Rp.)")).'", data: "harga", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Diskon (%)")).'", data: "discount", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "PPn (%)")).'", data: "ppn_persen", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Sub Total (Rp.)")).'", data: "sub_total", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Total setelah PPn (Rp.)")).'", data: "total", searchable: false, class: "text-right" },
                { title: "'.(\Yii::t("fe", "Catatan 1")).'", data: "catatan1", searchable: false },
                { title: "'.(\Yii::t("fe", "Catatan 2")).'", data: "catatan2", searchable: false }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                5,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
        ], {0:5}, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

', View::POS_END, 'b-index');
?>
