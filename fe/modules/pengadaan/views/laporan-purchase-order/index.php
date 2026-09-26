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

<style>
.redClass {
    background-color: #ffd2d2!important;
}
</style>

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
                        'excel' => [
                            'attributes' => [
                                'data-target' => $module.'export-excel?actionId='.$this->context->action->id.'&'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-pr');?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="laporan-pr" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi PO");?></th>
                            <th><?=\Yii::t("fe", "Kode Supplier");?></th>
                            <th><?=\Yii::t("fe", "Nama Supplier");?></th>
                            <th><?=\Yii::t("fe", "Manufaktur");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Qty GRN");?></th>
                            <th><?=\Yii::t("fe", "Qty Outstanding");?></th>
                            <th><?=\Yii::t("fe", "UOM");?></th>
                            <th><?=\Yii::t("fe", "From UOM");?></th>
                            <th><?=\Yii::t("fe", "Factor");?></th>
                            <th><?=\Yii::t("fe", "To UOM");?></th>
                            <th><?=\Yii::t("fe", "Harga (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                            <th><?=\Yii::t("fe", "PPn (%)");?></th>
                            <th><?=\Yii::t("fe", "Harga Kotor (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Harga Bersih (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "PO Remarks");?></th>
                            <th><?=\Yii::t("fe", "Status PO");?></th>
                            <th><?=\Yii::t("fe", "Reject Date");?></th>
                            <th><?=\Yii::t("fe", "Reject Remarks");?></th>
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
    var actionId = "'.$this->context->action->id.'";

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#laporan-pr").docoTabel({
            filter: true,
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-order/get-data?actionId="+actionId,
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { title: "'.(\Yii::t("fe", "PR Number")).'", data: "no_pr", searchable: false  },
                { title: "'.(\Yii::t("fe", "PR Verification Date")).'", data: "tanggal_verifikasi_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "PO Number")).'", data: "no_po", searchable: false },
                { title: "'.(\Yii::t("fe", "PO Date")).'", data: "tanggal_po" },
                { title: "'.(\Yii::t("fe", "PO Verification Date")).'", data: "tgl_verifikasi_po", searchable: false },
                { title: "'.(\Yii::t("fe", "Supplier Code")).'", data: "supplier_code", searchable: false },
                { title: "'.(\Yii::t("fe", "Supplier Name")).'", data: "supplier_name", searchable: false },
                { title: "'.(\Yii::t("fe", "Manufacture")).'", data: "manufacturer", searchable: false },
                { title: "'.(\Yii::t("fe", "Item Code")).'", data: "item_code", searchable: false },
                { title: "'.(\Yii::t("fe", "Item Name")).'", data: "item_name", searchable: false },
                { title: "'.(\Yii::t("fe", "Qty PO")).'", data: "qty_po", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "Qty GRN")).'", data: "po_balance", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "Qty Outstanding")).'", data: "qty_outstanding", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "UOM")).'", data: "uom", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "From UOM")).'", data: "from_uom", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "Factor")).'", data: "factor", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "To UOM")).'", data: "to_uom", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "Price (Rp.)")).'", data: "price", searchable: false, class : "text-right" },
                { title: "'.(\Yii::t("fe", "Discount (%)")).'", data: "deduction_percent", searchable: false, class : "text-center" },
                { title: "'.(\Yii::t("fe", "Tax (%)")).'", data: "addition_percent", searchable: false, class : "text-center" },
                { title: "'.(\Yii::t("fe", "Gross Amount")).'", data: "gross_amount", searchable: false },
                { title: "'.(\Yii::t("fe", "Nett Amount")).'", data: "nett_amount", searchable: false },
                { title: "'.(\Yii::t("fe", "PO Remarks")).'", data: "jenis_pr", searchable: false },
                { title: "'.(\Yii::t("fe", "Status PO")).'", data: "status_po" },
                { title: "'.(\Yii::t("fe", "Reject Date")).'", data: "reject_date", searchable: false },
                { title: "'.(\Yii::t("fe", "Reject Remarks")).'", data: "reject_remarks", searchable: false }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                4,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                24,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_po', '', $status_po,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            "col-index" => "2"
                        ]
                    )
                )).'</div>\'
            ]
        ], {0:4, 1:24}, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", "select[name=\'status_po\']", function(){
            $(".data-filter").click();
        });
    });

', View::POS_END, 'b-index');
?>
